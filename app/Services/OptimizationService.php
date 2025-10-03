<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Task;
use App\Models\DailyTeamAssignment;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

class OptimizationService
{
    public function run(string $serviceDate, array $locationIds): array
    {
        // --- PHASE 1: PRE-PROCESSING & VALIDATION ---
        $availableEmployees = Employee::whereDoesntHave('schedules', function ($query) use ($serviceDate) {
            $query->where('work_date', $serviceDate)->where('is_day_off', true);
        })->get();

        if ($availableEmployees->isEmpty()) {
            return ['status' => 'error', 'message' => 'No employees are scheduled to work on this date.'];
        }

        // We get the locations and SORT them by duration, LONGEST FIRST. This is key for the greedy algorithm.
        $locationsToClean = Location::whereIn('id', $locationIds)
            ->orderBy('base_cleaning_duration_minutes', 'desc')
            ->get();
        
        // We only support single-client optimization for now.
        $clientId = $locationsToClean->first()->contracted_client_id;


        DB::beginTransaction();
        try {
            // --- PHASE 2: TEAM FORMATION & WORKLOAD BALANCING ---

            // 1. Form Teams based on your rules (this function is already smart)
            $teams = $this->formTeams($availableEmployees);
            if (empty($teams)) {
                throw new \Exception('Could not form any valid teams (check for available drivers).');
            }

            // 2. Prepare for Assignment: Create database records for the teams
            $createdTeams = [];
            $teamWorkloads = []; // This array will track the total minutes for each team
            
            $availableCar = Car::where('is_available', true)->first(); // Simple car assignment

            foreach ($teams as $teamMembers) {
                $dailyTeam = DailyTeamAssignment::create([
                    'assignment_date' => $serviceDate,
                    'contracted_client_id' => $clientId,
                    'car_id' => $availableCar ? $availableCar->id : null,
                ]);

                foreach ($teamMembers as $employee) {
                    TeamMember::create([
                        'daily_team_id' => $dailyTeam->id,
                        'employee_id' => $employee->id,
                    ]);
                }
                $createdTeams[] = $dailyTeam;
                $teamWorkloads[$dailyTeam->id] = 0; // Initialize workload for each team at 0
            }

            // 3. THE GREEDY BALANCING ALGORITHM
            foreach ($locationsToClean as $location) {
                // Find the team that currently has the LEAST amount of work.
                $teamWithLeastWorkId = array_keys($teamWorkloads, min($teamWorkloads))[0];
                
                // Assign the current task (which is the longest remaining) to that team.
                Task::create([
                    'location_id' => $location->id,
                    'task_description' => "Standard Cleaning",
                    'estimated_duration_minutes' => $location->base_cleaning_duration_minutes,
                    'scheduled_date' => $serviceDate,
                    'status' => 'Scheduled',
                    'assigned_team_id' => $teamWithLeastWorkId, // Assign to the best team
                ]);

                // Update that team's total workload.
                $teamWorkloads[$teamWithLeastWorkId] += $location->base_cleaning_duration_minutes;
            }

            DB::commit();
            return ['status' => 'success', 'message' => $locationsToClean->count() . ' tasks have been optimally assigned to ' . count($createdTeams) . ' teams.'];

        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => 'An error occurred during assignment: ' . $e->getMessage()];
        }
    }

    /**
     * Forms teams from a collection of employees (this logic is already correct).
     */
    private function formTeams($employees): array
    {
        // ... (The formTeams function from the previous step is correct, no changes needed here) ...
        $drivers = $employees->filter(function ($employee) {
            return in_array('Driving', json_decode($employee->skills ?? '[]'));
        })->shuffle();

        $cleaners = $employees->filter(function ($employee) {
            return !in_array('Driving', json_decode($employee->skills ?? '[]'));
        })->shuffle();

        if ($drivers->isEmpty()) {
            return [];
        }

        $teams = [];
        $employeePool = $employees->shuffle();

        while (!$employeePool->isEmpty()) {
            $teamSize = ($employeePool->count() % 2 != 0 && $employeePool->count() > 2) ? 3 : 2;
            if ($employeePool->count() <= 2) {
                $teamSize = $employeePool->count();
            }

            // Find a driver for the team
            $driver = $employeePool->first(function($emp) {
                return in_array('Driving', json_decode($emp->skills ?? '[]'));
            });

            if (!$driver) break; // Stop if no more drivers can be found

            $newTeam = collect([$driver]);
            $employeePool = $employeePool->reject(fn($emp) => $emp->id === $driver->id);

            // Find cleaners for the rest of the team
            $neededCleaners = $teamSize - 1;
            $teamCleaners = $employeePool->take($neededCleaners);
            $newTeam = $newTeam->concat($teamCleaners);
            $employeePool = $employeePool->slice($neededCleaners);

            $teams[] = $newTeam;
        }

        return $teams;
    }



    public function runForSimulation(string $serviceDate, array $locationIds, $availableEmployees)
    {
        $locationsToClean = Location::whereIn('id', $locationIds)
            ->orderBy('base_cleaning_duration_minutes', 'desc')
            ->get();

        $teams = $this->formTeams($availableEmployees);
        if (empty($teams)) {
            return []; // Return empty if no teams can be formed
        }

        $teamWorkloads = [];
        foreach ($teams as $index => $team) {
            $teamWorkloads[$index] = 0;
        }

        // Greedy Balancing Algorithm
        foreach ($locationsToClean as $location) {
            $teamWithLeastWorkId = array_keys($teamWorkloads, min($teamWorkloads))[0];
            $teamWorkloads[$teamWithLeastWorkId] += $location->base_cleaning_duration_minutes;
        }

        // --- RETURN DETAILED METRICS ---
        
        // Calculate workload standard deviation (a measure of balance)
        $workloadStdDev = $this->calculateStandardDeviation(array_values($teamWorkloads));
        
        // Calculate fitness (higher is better, so inverse of std dev)
        $fitness = 1 / (1 + $workloadStdDev);

        // Calculate total workforce cost (simplified example)
        // Assumes an average hourly rate. A real system would use individual rates.
        $totalHours = array_sum($teamWorkloads) / 60;
        $averageHourlyRate = 15; // Example rate in EUR
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