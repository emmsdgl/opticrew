<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Task;
use App\Models\DailyTeamAssignment;
use App\Models\TeamMember;
use App\Models\SchedulingLog;
use App\Models\EmployeeSchedule;
use App\Models\TaskPerformanceHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OptimizationService
{
    // Travel times (one-way in minutes)
    const TRAVEL_TIME_KAKSLAUTTANEN = 35; // 30-40 mins, using average
    const TRAVEL_TIME_AIKAMATKAT = 10;
    
    // Maximum teams per car
    const MAX_TEAMS_PER_CAR = 3;

    public function run(string $serviceDate, array $newLocationIds): array
    {
        $log = [
            'service_date' => $serviceDate,
            'inputs' => ['location_ids' => $newLocationIds],
            'steps' => [],
        ];
    
        try {
            DB::beginTransaction();
    
            // === PHASE 1: GATHER ALL WORK ===
            $existingTaskLocationIds = Task::where('scheduled_date', $serviceDate)
                ->where('status', 'Scheduled')
                ->pluck('location_id')
                ->toArray();
            
            $allLocationIds = array_unique(array_merge($existingTaskLocationIds, $newLocationIds));
    
            if (empty($allLocationIds)) {
                throw new \Exception('No locations selected for scheduling.');
            }
    
            // Cleanup old records
            $oldTeamIds = Task::where('scheduled_date', $serviceDate)
                ->where('status', 'Scheduled')
                ->pluck('assigned_team_id')
                ->unique();
            
            Task::where('scheduled_date', $serviceDate)
                ->where('status', 'Scheduled')
                ->delete();
            
            if ($oldTeamIds->isNotEmpty()) {
                DailyTeamAssignment::whereIn('id', $oldTeamIds)->delete();
            }
            
            SchedulingLog::where('schedule_date', $serviceDate)->delete();
    
            // Load locations with client relationship
            $locationsToClean = Location::whereIn('id', $allLocationIds)
                ->with('contractedClient')
                ->get();
            
            $tasksByClient = $locationsToClean->groupBy('contracted_client_id');
            
            // Get available employees
            $offEmployeeIds = EmployeeSchedule::where('work_date', $serviceDate)
                ->where('is_day_off', true)
                ->pluck('employee_id')
                ->toArray();
            
            $availableEmployees = Employee::whereNotIn('id', $offEmployeeIds)->get();
    
            $log['steps'][] = [
                'title' => 'Available Employees', 
                'count' => $availableEmployees->count(), 
                'data' => $availableEmployees->pluck('full_name')->toArray()
            ];
            
            if ($availableEmployees->isEmpty()) {
                throw new \Exception('No employees are scheduled to work on this date.');
            }
    
            // === PHASE 2: STRATEGIC ALLOCATION ===
            $totalWorkload = $locationsToClean->sum('base_cleaning_duration_minutes');
            $sortedClients = $tasksByClient->sortByDesc(fn($locations) => 
                $locations->sum('base_cleaning_duration_minutes')
            );
            
            $employeeAllocations = $this->allocateEmployeesToClients(
                $availableEmployees, 
                $sortedClients, 
                $totalWorkload
            );
            
            // Log allocation details
            foreach ($employeeAllocations as $clientId => $employees) {
                $clientName = $tasksByClient[$clientId]->first()->contractedClient->name;
                $log['steps'][] = [
                    'title' => "Employee Allocation for {$clientName}",
                    'count' => $employees->count(),
                    'data' => $employees->pluck('full_name')->toArray()
                ];
            }
    
            // Assign cars (one per client location)
            $availableCars = Car::where('is_available', 1)->get();
            if ($availableCars->count() < $sortedClients->count()) {
                throw new \Exception('Not enough available cars for all client locations.');
            }
            
            $carAssignments = [];
            $carIndex = 0;
            foreach ($employeeAllocations as $clientId => $employees) {
                if ($employees->isNotEmpty()) {
                    $carAssignments[$clientId] = $availableCars->get($carIndex);
                    $carIndex++;
                }
            }
            
            // === PHASE 3 & 4: GREEDY + GA OPTIMIZATION ===
            $logSteps = [];
    
            foreach ($employeeAllocations as $clientId => $employees) {
                if ($employees->isEmpty()) continue;
    
                $clientLocations = $tasksByClient[$clientId];
                $clientName = $clientLocations->first()->contractedClient->name;
                
                // Form teams for this client
                $teams = $this->formTeams($employees->values());
                
                if (empty($teams)) {
                    $logSteps[] = [
                        'title' => "⚠️ Team Formation Failed for {$clientName}",
                        'error' => 'Could not form valid teams',
                        'employees' => $employees->pluck('full_name')->toArray()
                    ];
                    continue;
                }
    
                $logSteps[] = [
                    'title' => "Team Formation for {$clientName}",
                    'count' => count($teams),
                    'data' => collect($teams)->map(fn($team) => 
                        $team->pluck('full_name')->toArray()
                    )->toArray()
                ];
    
                // Calculate team efficiencies
                $teamEfficiencies = $this->calculateTeamEfficiencies($teams);
                
                // PHASE 3A: GREEDY ALGORITHM (Initial Assignment)
                $greedySchedule = $this->greedyTaskAssignment(
                    $teams, 
                    $clientLocations, 
                    $teamEfficiencies,
                    $clientName
                );
                
                $logSteps[] = [
                    'title' => "Greedy Algorithm Result for {$clientName}",
                    'data' => $this->formatScheduleForLog($greedySchedule, $teams)
                ];
                
                // PHASE 3B: GENETIC ALGORITHM (Refinement)
                $travelTime = $this->getTravelTime($clientName);
                $initialWorkloads = array_fill(0, count($teams), 0); // No travel in workload
                
                $ga = new GeneticAlgorithmService(
                    collect($teams), 
                    $clientLocations, 
                    $teamEfficiencies, 
                    $initialWorkloads,
                    $greedySchedule // Pass greedy result as seed
                );
                
                $optimalSchedule = $ga->run();
    
                $logSteps[] = [
                    'title' => "Genetic Algorithm Result for {$clientName}",
                    'fitness_score' => round($optimalSchedule['fitness'], 4),
                    'data' => $this->formatScheduleForLog($optimalSchedule, $teams)
                ];
                
                // Create database records
                foreach ($optimalSchedule as $teamIndex => $teamData) {
                    if (!is_int($teamIndex)) continue;
                    
                    $dailyTeam = DailyTeamAssignment::create([
                        'assignment_date' => $serviceDate,
                        'contracted_client_id' => $clientId,
                        'car_id' => $carAssignments[$clientId]->id ?? null,
                    ]);
    
                    foreach ($teams[$teamIndex] as $employee) {
                        TeamMember::create([
                            'daily_team_id' => $dailyTeam->id, 
                            'employee_id' => $employee->id
                        ]);
                    }
                    
                    foreach ($teamData['tasks'] as $taskLocation) {
                        Task::create([
                            'location_id' => $taskLocation->id,
                            'task_description' => "Standard Cleaning",
                            'estimated_duration_minutes' => $taskLocation->base_cleaning_duration_minutes,
                            'scheduled_date' => $serviceDate,
                            'status' => 'Scheduled',
                            'assigned_team_id' => $dailyTeam->id,
                        ]);
                    }
                }
            }
    
            $log['steps'] = array_merge($log['steps'], $logSteps);
            SchedulingLog::create([
                'schedule_date' => $serviceDate, 
                'log_data' => json_encode($log)
            ]);
    
            DB::commit();
            return [
                'status' => 'success', 
                'message' => $locationsToClean->count() . ' total tasks have been optimized across ' . 
                            count($employeeAllocations) . ' locations.'
            ];
    
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error', 
                'message' => 'Optimization Failed: ' . $e->getMessage()
            ];
        }

        // TEMPORARY DEBUG
        Log::info("Teams formed for {$clientName}:", [
            'employee_count' => $employees->count(),
            'team_count' => count($teams),
            'teams' => collect($teams)->map(fn($t) => [
                'size' => $t->count(),
                'members' => $t->pluck('full_name')->toArray(),
                'has_driver' => $t->contains(fn($e) => $this->isDriver($e))
            ])->toArray()
        ]);

    }

    /**
     * IMPROVED: Allocate employees to clients with minimum 2 per client
     */
    private function allocateEmployeesToClients(Collection $availableEmployees, Collection $sortedClients, float $totalWorkload): array
    {
        // === NEW EFFICIENCY-AWARE LOGIC ===
        // 1. Calculate efficiency for every employee and sort them.
        $employeesWithScores = $availableEmployees->map(function ($employee) {
            $employee->efficiency = $this->getEmployeeEfficiency($employee);
            return $employee;
        });
    
        $drivers = $employeesWithScores->filter(fn($e) => $this->isDriver($e))->sortByDesc('efficiency');
        $nonDrivers = $employeesWithScores->filter(fn($e) => !$this->isDriver($e))->sortByDesc('efficiency');
        // ===================================
        
        if ($drivers->count() < $sortedClients->count()) {
            throw new \Exception('Not enough available drivers to assign one to each client location.');
        }
    
        $employeeAllocations = [];
    
        // First pass: Initialize each client's allocation with one driver.
        // Give the BEST drivers to the clients with the MOST work.
        foreach ($sortedClients as $clientId => $clientLocations) {
            $employeeAllocations[$clientId] = collect([$drivers->shift()]); // .shift() takes the best driver
        }
        
        // Second pass: Distribute the remaining pool (drivers + non-drivers)
        $remainingPool = $drivers->concat($nonDrivers)->sortByDesc('efficiency');
    
        // Distribute remaining employees one by one, giving the best available person
        // to the client group that is currently the "least efficient" to balance the teams.
        while ($remainingPool->isNotEmpty()) {
            // Find the client group with the lowest total efficiency score right now.
            $leastEfficientClientId = collect($employeeAllocations)->sortBy(function ($allocatedEmployees) {
                return $allocatedEmployees->sum('efficiency');
            })->keys()->first();
            
            // Give the best remaining employee to that group.
            $employeeAllocations[$leastEfficientClientId]->push($remainingPool->shift());
        }
        
        return $employeeAllocations;
    }

    /**
     * PHASE 3A: Greedy Task Assignment
     */
    private function greedyTaskAssignment(
        array $teams, 
        Collection $locations, 
        array $teamEfficiencies,
        string $clientName
    ): array {
        // Sort locations by duration (longest first)
        $sortedLocations = $locations->sortByDesc('base_cleaning_duration_minutes')->values();
        
        // Initialize schedule
        $schedule = [];
        $teamWorkloads = [];
        
        foreach ($teams as $index => $team) {
            $schedule[$index] = ['tasks' => collect()];
            $teamWorkloads[$index] = 0;
        }
        
        // Greedy assignment: Give longest task to team with least workload
        foreach ($sortedLocations as $location) {
            $teamIndex = array_keys($teamWorkloads, min($teamWorkloads))[0];
            
            $schedule[$teamIndex]['tasks']->push($location);
            
            // Calculate predicted workload using efficiency
            $efficiency = $teamEfficiencies[$teamIndex] ?? 1.0;
            $predictedDuration = $efficiency > 0 
                ? $location->base_cleaning_duration_minutes / $efficiency 
                : $location->base_cleaning_duration_minutes;
            
            $teamWorkloads[$teamIndex] += $predictedDuration;
        }
        
        return $schedule;
    }

    /**
     * Calculate team efficiency from historical data
     */
    private function calculateTeamEfficiencies(array $teams): array
    {
        $teamEfficiencies = [];
        
        foreach ($teams as $index => $team) {
            $totalEmployeeEfficiency = 0;
            $employeeCount = $team->count();

            if ($employeeCount === 0) {
                $teamEfficiencies[$index] = 1.0;
                continue;
            }

            foreach ($team as $employee) {
                $teamIds = TeamMember::where('employee_id', $employee->id)
                    ->pluck('daily_team_id');
                
                $taskIds = Task::whereIn('assigned_team_id', $teamIds)
                    ->pluck('id');
                
                $history = TaskPerformanceHistory::whereIn('task_id', $taskIds)->get();
                
                $employeeEfficiency = 1.0; // Default 100%
                
                if ($history->isNotEmpty()) {
                    $ratioSum = $history->sum(function($h) {
                        return $h->actual_duration_minutes > 0 
                            ? $h->estimated_duration_minutes / $h->actual_duration_minutes 
                            : 1;
                    });
                    $employeeEfficiency = $ratioSum / $history->count();
                }
                
                $totalEmployeeEfficiency += $employeeEfficiency;
            }
            
            // Team efficiency is average of member efficiencies
            $teamEfficiencies[$index] = $totalEmployeeEfficiency / $employeeCount;
        }
        
        return $teamEfficiencies;
    }
    
    /**
     * Get travel time for a client
     */
    private function getTravelTime(string $clientName): int
    {
        return ($clientName === 'Kakslauttanen') 
            ? self::TRAVEL_TIME_KAKSLAUTTANEN 
            : self::TRAVEL_TIME_AIKAMATKAT;
    }
    
    /**
     * Format schedule for logging
     */
    private function formatScheduleForLog(array $schedule, array $teams): array
    {
        $formatted = [];
        foreach ($schedule as $teamIndex => $teamData) {
            if (!is_int($teamIndex)) continue;
    
            $team = $teams[$teamIndex];
            
            // --- NEW: Prepare detailed member data ---
            $teamMembersWithEfficiency = [];
            $totalEfficiency = 0;
            if ($team->isNotEmpty()) {
                foreach ($team as $employee) {
                    $efficiency = $this->getEmployeeEfficiency($employee);
                    $teamMembersWithEfficiency[] = [
                        'name' => $employee->full_name,
                        'efficiency' => $efficiency,
                    ];
                    $totalEfficiency += $efficiency;
                }
                $teamEfficiency = $totalEfficiency / $team->count();
            } else {
                $teamEfficiency = 1.0;
            }
            // --- End of new section ---
    
            // Calculate the PREDICTED workload for this team's tasks
            $predictedWorkload = $teamData['tasks']->sum(function($task) use ($teamEfficiency) {
                return $teamEfficiency > 0 ? $task->base_cleaning_duration_minutes / $teamEfficiency : $task->base_cleaning_duration_minutes;
            });
    
            $formatted[] = [
                'team_members' => $teamMembersWithEfficiency, // UPDATED: Pass the detailed data
                'assigned_tasks' => $teamData['tasks']->pluck('location_name')->toArray(),
                'total_tasks' => $teamData['tasks']->count(),
                'estimated_duration' => $teamData['tasks']->sum('base_cleaning_duration_minutes'),
                'team_efficiency' => round($teamEfficiency * 100) . '%',
                'predicted_workload' => round($predictedWorkload),
            ];
        }
        return $formatted;
    }

    /**
     * IMPROVED: Form teams with better driver distribution
     */
    private function formTeams($employees): array
    {
        if ($employees->count() < 2) {
            if ($employees->isNotEmpty() && $this->isDriver($employees->first())) { return [$employees]; }
            return [];
        }
    
        // === NEW HETEROGENEOUS LOGIC ===
        // 1. Calculate the efficiency score for every employee in the pool.
        $employeesWithScores = $employees->map(function ($employee) {
            $employee->efficiency = $this->getEmployeeEfficiency($employee);
            return $employee;
        });
    
        // 2. Separate into drivers and non-drivers, then SORT them from most efficient to least efficient.
        $drivers = $employeesWithScores->filter(fn($e) => $this->isDriver($e))->sortByDesc('efficiency');
        $nonDrivers = $employeesWithScores->filter(fn($e) => !$this->isDriver($e))->sortByDesc('efficiency');
        // ===================================
    
        if ($drivers->isEmpty()) { return []; }
    
        $teams = [];
    
        while ($drivers->isNotEmpty()) {
            // 3. Start a new team with the CURRENT BEST available driver.
            $driver = $drivers->shift(); // .shift() takes the first (best) item.
            $newTeam = collect([$driver]);
    
            $remainingEmployees = $drivers->count() + $nonDrivers->count();
            $teamSize = 2;
            if (($remainingEmployees + 1) % 2 != 0 && $remainingEmployees >= 2) {
                $teamSize = 3;
            }
    
            // 4. Fill the team by pairing the BEST with the WORST.
            while ($newTeam->count() < $teamSize) {
                if ($nonDrivers->count() >= 2) {
                    // If we have options, pair best with worst.
                    if ($newTeam->sum('efficiency') > $teamSize) { // If the team is already fast...
                        $newTeam->push($nonDrivers->pop()); //...add the slowest non-driver.
                    } else {
                        $newTeam->push($nonDrivers->shift()); //...add the fastest non-driver.
                    }
                } elseif ($nonDrivers->isNotEmpty()) {
                    $newTeam->push($nonDrivers->pop());
                } elseif ($drivers->isNotEmpty()) {
                    $newTeam->push($drivers->pop()); // Add the slowest driver if no non-drivers left
                } else {
                    break;
                }
            }
            $teams[] = $newTeam;
        }
    
        // 4. Distribute any remaining non-drivers to the smallest teams.
        while ($nonDrivers->isNotEmpty()) {
            // Find the smallest team that can still accept a member.
            $smallestTeam = collect($teams)
                ->filter(fn($team) => $team->count() < 3)
                ->sortBy(fn($team) => $team->count())
                ->first();
    
            if ($smallestTeam) {
                $smallestTeam->push($nonDrivers->pop());
            } else {
                // All teams are full (all are trios). Stop.
                break;
            }
        }
    
        return $teams;
    }
    
    /**
     * Check if employee has driving skill
     */
    private function isDriver($employee): bool
    {
        $skills = json_decode($employee->skills ?? '[]', true);
        return in_array('Driving', $skills);
    }

    // You also need this new helper function inside the same class.
    private function getEmployeeEfficiency($employee): float
    {
        $teamIds = \App\Models\TeamMember::where('employee_id', $employee->id)->pluck('daily_team_id');
        $taskIds = \App\Models\Task::whereIn('assigned_team_id', $teamIds)->pluck('id');
        $history = \App\Models\TaskPerformanceHistory::whereIn('task_id', $taskIds)->get();
        if ($history->isEmpty()) { return 1.0; }
        $ratioSum = $history->sum(fn($h) => $h->actual_duration_minutes > 0 ? $h->estimated_duration_minutes / $h->actual_duration_minutes : 1);
        return $ratioSum / $history->count();
    }

    /**
     * Simulation method (unchanged)
     */
    public function runForSimulation(string $serviceDate, array $locationIds, $availableEmployees)
    {
        $locationsToClean = Location::whereIn('id', $locationIds)
            ->orderBy('base_cleaning_duration_minutes', 'desc')
            ->get();

        $teams = $this->formTeams($availableEmployees);
        if (empty($teams)) {
            return [];
        }

        $teamWorkloads = array_fill(0, count($teams), 0);

        foreach ($locationsToClean as $location) {
            $teamWithLeastWorkId = array_keys($teamWorkloads, min($teamWorkloads))[0];
            $teamWorkloads[$teamWithLeastWorkId] += $location->base_cleaning_duration_minutes;
        }

        $workloadStdDev = $this->calculateStandardDeviation(array_values($teamWorkloads));
        $fitness = 1 / (1 + $workloadStdDev);
        $totalHours = array_sum($teamWorkloads) / 60;
        $averageHourlyRate = 15;
        $totalCost = $totalHours * $averageHourlyRate;

        return [
            'fitness' => $fitness,
            'workload_std_dev' => round($workloadStdDev, 2) . ' minutes',
            'total_cost' => '€' . round($totalCost, 2),
        ];
    }
    
    private function calculateStandardDeviation(array $values): float
    {
        if (count($values) < 2) {
            return 0.0;
        }
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $values)) / (count($values) - 1);
        return sqrt($variance);
    }
}