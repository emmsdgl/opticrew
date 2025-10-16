<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Task;
use App\Models\DailyTeamAssignment;
use App\Models\TeamMember;
use App\Models\EmployeeSchedule;
use App\Models\TaskPerformanceHistory;
use App\Models\OptimizationRun;
use App\Models\OptimizationGeneration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OptimizationService
{
    const TRAVEL_TIME_KAKSLAUTTANEN = 35;
    const TRAVEL_TIME_AIKAMATKAT = 10;
    const MAX_TEAMS_PER_CAR = 3;

    public function run(string $serviceDate, array $newLocationIds, ?int $triggeredByTaskId = null): array
    {
        $optimizationRun = null;
        
        try {
            DB::beginTransaction();

            // === PHASE 0: CREATE OPTIMIZATION RUN RECORD ===
            $optimizationRun = OptimizationRun::create([
                'service_date' => $serviceDate,
                'triggered_by_task_id' => $triggeredByTaskId,
                'status' => 'running',
                'total_tasks' => 0,
                'total_teams' => 0,
                'total_employees' => 0
            ]);

            // === PHASE 1: GATHER ALL WORK ===
            // Get existing "Scheduled" tasks (not started yet)
            $scheduledTasks = Task::where('scheduled_date', $serviceDate)
                ->where('status', 'Scheduled')
                ->get();
            
            $existingLocationIds = $scheduledTasks->pluck('location_id')->toArray();
            $allLocationIds = array_unique(array_merge($existingLocationIds, $newLocationIds));

            if (empty($allLocationIds)) {
                throw new \Exception('No locations selected for scheduling.');
            }

            // Get tasks that are "In Progress" or "Completed" to consider their workload
            $inProgressTasks = Task::where('scheduled_date', $serviceDate)
                ->whereIn('status', ['In Progress', 'Completed'])
                ->with('assignedTeam.members')
                ->get();

            // Delete old "Scheduled" tasks and their team assignments
            $oldTeamIds = $scheduledTasks->pluck('assigned_team_id')->unique()->filter();
            
            Task::where('scheduled_date', $serviceDate)
                ->where('status', 'Scheduled')
                ->delete();
            
            if ($oldTeamIds->isNotEmpty()) {
                DailyTeamAssignment::whereIn('id', $oldTeamIds)->delete();
            }

            // Load locations
            $locationsToClean = Location::whereIn('id', $allLocationIds)
                ->with('contractedClient')
                ->get();
            
            $tasksByClient = $locationsToClean->groupBy('contracted_client_id');
            
            // Get available employees
            $offEmployeeIds = EmployeeSchedule::where('work_date', $serviceDate)
                ->where('is_day_off', true)
                ->pluck('employee_id')
                ->toArray();
            
            // Get employees who are NOT on day off AND NOT currently working on in-progress tasks
            $busyEmployeeIds = $inProgressTasks->flatMap(function($task) {
                return $task->assignedTeam ? $task->assignedTeam->members->pluck('employee_id') : [];
            })->unique()->toArray();

            $availableEmployees = Employee::whereNotIn('id', array_merge($offEmployeeIds, $busyEmployeeIds))->get();

            if ($availableEmployees->isEmpty()) {
                throw new \Exception('No employees are available for scheduling on this date.');
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

            // Update optimization run with employee allocation data
            $optimizationRun->update([
                'total_employees' => $availableEmployees->count(),
                'employee_allocation_data' => $this->formatEmployeeAllocations($employeeAllocations, $tasksByClient)
            ]);

            // Assign cars
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
            $totalTeamsCreated = 0;
            $totalTasksScheduled = 0;
            $allGreedyResults = [];

            foreach ($employeeAllocations as $clientId => $employees) {
                if ($employees->isEmpty()) continue;

                $clientLocations = $tasksByClient[$clientId];
                $clientName = $clientLocations->first()->contractedClient->name;
                
                // Form teams
                $teams = $this->formTeams($employees->values());
                
                if (empty($teams)) {
                    Log::warning("Team formation failed for {$clientName}");
                    continue;
                }

                $totalTeamsCreated += count($teams);

                // Calculate team efficiencies
                $teamEfficiencies = $this->calculateTeamEfficiencies($teams);
                
                // PHASE 3A: GREEDY ALGORITHM
                $greedySchedule = $this->greedyTaskAssignment(
                    $teams, 
                    $clientLocations, 
                    $teamEfficiencies,
                    $clientName
                );

                $allGreedyResults[$clientName] = $this->formatScheduleForLog($greedySchedule, $teams);
                
                // PHASE 3B: GENETIC ALGORITHM
                $travelTime = $this->getTravelTime($clientName);
                $initialWorkloads = array_fill(0, count($teams), 0);
                
                $ga = new GeneticAlgorithmService(
                    collect($teams), 
                    $clientLocations, 
                    $teamEfficiencies, 
                    $initialWorkloads,
                    $greedySchedule,
                    $optimizationRun->id // Pass optimization run ID for logging
                );
                
                $optimalSchedule = $ga->run();

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
                    
                    // Get the final generation number for this optimization
                    $finalGeneration = OptimizationGeneration::where('optimization_run_id', $optimizationRun->id)
                        ->max('generation_number');
                    
                    foreach ($teamData['tasks'] as $taskLocation) {
                        Task::create([
                            'location_id' => $taskLocation->id,
                            'task_description' => "Standard Cleaning",
                            'estimated_duration_minutes' => $taskLocation->base_cleaning_duration_minutes,
                            'scheduled_date' => $serviceDate,
                            'status' => 'Scheduled',
                            'assigned_team_id' => $dailyTeam->id,
                            'optimization_run_id' => $optimizationRun->id,
                            'assigned_by_generation' => $finalGeneration
                        ]);
                        $totalTasksScheduled++;
                    }
                }
            }

            // Update optimization run with final data
            $finalGeneration = \App\Models\OptimizationGeneration::where('optimization_run_id', $optimizationRun->id)
                ->orderBy('best_fitness', 'desc')
                ->first();

            $optimizationRun->update([
                'status' => 'completed',
                'total_tasks' => $totalTasksScheduled,
                'total_teams' => $totalTeamsCreated,
                'greedy_result_data' => $allGreedyResults,
                'final_fitness_score' => $finalGeneration ? $finalGeneration->best_fitness : null,
                'generations_run' => $finalGeneration ? $finalGeneration->generation_number : 0
            ]);

            DB::commit();
            
            return [
                'status' => 'success',
                'message' => $totalTasksScheduled . ' tasks optimized across ' . $totalTeamsCreated . ' teams.',
                'optimization_run_id' => $optimizationRun->id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($optimizationRun) {
                $optimizationRun->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }
            
            return [
                'status' => 'error',
                'message' => 'Optimization Failed: ' . $e->getMessage(),
                'optimization_run_id' => $optimizationRun ? $optimizationRun->id : null
            ];
        }
    }

    /**
     * Format employee allocations for logging
     */
    private function formatEmployeeAllocations(array $employeeAllocations, Collection $tasksByClient): array
    {
        $formatted = [];
        foreach ($employeeAllocations as $clientId => $employees) {
            $clientName = $tasksByClient[$clientId]->first()->contractedClient->name;
            $formatted[$clientName] = [
                'employee_count' => $employees->count(),
                'employees' => $employees->map(fn($e) => [
                    'name' => $e->full_name,
                    'efficiency' => $this->getEmployeeEfficiency($e),
                    'is_driver' => $this->isDriver($e)
                ])->toArray(),
                'total_workload' => $tasksByClient[$clientId]->sum('base_cleaning_duration_minutes')
            ];
        }
        return $formatted;
    }

    /**
     * Allocate employees to clients
     */
    private function allocateEmployeesToClients(Collection $availableEmployees, Collection $sortedClients, float $totalWorkload): array
    {
        $employeesWithScores = $availableEmployees->map(function ($employee) {
            $employee->efficiency = $this->getEmployeeEfficiency($employee);
            return $employee;
        });
    
        $drivers = $employeesWithScores->filter(fn($e) => $this->isDriver($e))->sortByDesc('efficiency');
        $nonDrivers = $employeesWithScores->filter(fn($e) => !$this->isDriver($e))->sortByDesc('efficiency');
        
        if ($drivers->count() < $sortedClients->count()) {
            throw new \Exception('Not enough available drivers to assign one to each client location.');
        }
    
        $employeeAllocations = [];
    
        foreach ($sortedClients as $clientId => $clientLocations) {
            $employeeAllocations[$clientId] = collect([$drivers->shift()]);
        }
        
        $remainingPool = $drivers->concat($nonDrivers)->sortByDesc('efficiency');
    
        while ($remainingPool->isNotEmpty()) {
            $leastEfficientClientId = collect($employeeAllocations)->sortBy(function ($allocatedEmployees) {
                return $allocatedEmployees->sum('efficiency');
            })->keys()->first();
            
            $employeeAllocations[$leastEfficientClientId]->push($remainingPool->shift());
        }
        
        return $employeeAllocations;
    }

    /**
     * Greedy Task Assignment
     */
    private function greedyTaskAssignment(
        array $teams, 
        Collection $locations, 
        array $teamEfficiencies,
        string $clientName
    ): array {
        $sortedLocations = $locations->sortByDesc('base_cleaning_duration_minutes')->values();
        
        $schedule = [];
        $teamWorkloads = [];
        
        foreach ($teams as $index => $team) {
            $schedule[$index] = ['tasks' => collect()];
            $teamWorkloads[$index] = 0;
        }
        
        foreach ($sortedLocations as $location) {
            $teamIndex = array_keys($teamWorkloads, min($teamWorkloads))[0];
            
            $schedule[$teamIndex]['tasks']->push($location);
            
            $efficiency = $teamEfficiencies[$teamIndex] ?? 1.0;
            $predictedDuration = $efficiency > 0 
                ? $location->base_cleaning_duration_minutes / $efficiency 
                : $location->base_cleaning_duration_minutes;
            
            $teamWorkloads[$teamIndex] += $predictedDuration;
        }
        
        return $schedule;
    }

    /**
     * Calculate team efficiencies
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
                $totalEmployeeEfficiency += $this->getEmployeeEfficiency($employee);
            }
            
            $teamEfficiencies[$index] = $totalEmployeeEfficiency / $employeeCount;
        }
        
        return $teamEfficiencies;
    }
    
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
    
            $predictedWorkload = $teamData['tasks']->sum(function($task) use ($teamEfficiency) {
                return $teamEfficiency > 0 ? $task->base_cleaning_duration_minutes / $teamEfficiency : $task->base_cleaning_duration_minutes;
            });
    
            $formatted[] = [
                'team_members' => $teamMembersWithEfficiency,
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
     * Form teams
     */
    private function formTeams($employees): array
    {
        if ($employees->count() < 2) {
            if ($employees->isNotEmpty() && $this->isDriver($employees->first())) { 
                return [$employees]; 
            }
            return [];
        }
    
        $employeesWithScores = $employees->map(function ($employee) {
            $employee->efficiency = $this->getEmployeeEfficiency($employee);
            return $employee;
        });
    
        $drivers = $employeesWithScores->filter(fn($e) => $this->isDriver($e))->sortByDesc('efficiency');
        $nonDrivers = $employeesWithScores->filter(fn($e) => !$this->isDriver($e))->sortByDesc('efficiency');
    
        if ($drivers->isEmpty()) { return []; }
    
        $teams = [];
    
        while ($drivers->isNotEmpty()) {
            $driver = $drivers->shift();
            $newTeam = collect([$driver]);
    
            $remainingEmployees = $drivers->count() + $nonDrivers->count();
            $teamSize = 2;
            if (($remainingEmployees + 1) % 2 != 0 && $remainingEmployees >= 2) {
                $teamSize = 3;
            }
    
            while ($newTeam->count() < $teamSize) {
                if ($nonDrivers->count() >= 2) {
                    if ($newTeam->sum('efficiency') > $teamSize) {
                        $newTeam->push($nonDrivers->pop());
                    } else {
                        $newTeam->push($nonDrivers->shift());
                    }
                } elseif ($nonDrivers->isNotEmpty()) {
                    $newTeam->push($nonDrivers->pop());
                } elseif ($drivers->isNotEmpty()) {
                    $newTeam->push($drivers->pop());
                } else {
                    break;
                }
            }
            $teams[] = $newTeam;
        }
    
        while ($nonDrivers->isNotEmpty()) {
            $smallestTeam = collect($teams)
                ->filter(fn($team) => $team->count() < 3)
                ->sortBy(fn($team) => $team->count())
                ->first();
    
            if ($smallestTeam) {
                $smallestTeam->push($nonDrivers->pop());
            } else {
                break;
            }
        }
    
        return $teams;
    }
    
    private function isDriver($employee): bool
    {
        $skills = json_decode($employee->skills ?? '[]', true);
        return in_array('Driving', $skills);
    }

    private function getEmployeeEfficiency($employee): float
    {
        $teamIds = TeamMember::where('employee_id', $employee->id)->pluck('daily_team_id');
        $taskIds = Task::whereIn('assigned_team_id', $teamIds)->pluck('id');
        $history = TaskPerformanceHistory::whereIn('task_id', $taskIds)->get();
        if ($history->isEmpty()) { return 1.0; }
        $ratioSum = $history->sum(fn($h) => $h->actual_duration_minutes > 0 ? $h->estimated_duration_minutes / $h->actual_duration_minutes : 1);
        return $ratioSum / $history->count();
    }
}