<?php
/**
 * Rule-Based Preprocessor (Hybrid Algorithm - Step 1)
 *
 * Filters and validates tasks according to business rules:
 * - Tasks must have location_id and scheduled_date
 * - Prioritizes tasks with arrival_status = 1
 * - Groups tasks by client
 * - Assigns employees ensuring at least one driver per team
 */

class RuleBasedPreprocessor
{
    private $rules = [
        'max_work_hours' => 12,
        'work_start_time' => '08:00:00',
        'work_end_time' => '20:00:00',
        'min_team_size' => 2,
        'max_team_size' => 3,
        'require_driver_per_team' => true,
        'target_utilization_rate' => 0.85,
    ];

    public function preprocess($tasks, $employees, $clients)
    {
        // Step 1: Validate and sort tasks
        list($validTasks, $invalidTasks) = $this->validateTasks($tasks);

        if (empty($validTasks)) {
            return [
                'valid_tasks' => [],
                'invalid_tasks' => $invalidTasks,
                'employee_allocations' => [],
                'teams' => [],
                'error' => 'No valid tasks found',
            ];
        }

        // Step 2: Calculate optimal workforce
        $optimalWorkforce = $this->calculateOptimalWorkforce($validTasks, $employees);

        // Step 3: Group tasks by client
        $tasksByClient = $this->groupTasksByClient($validTasks);

        // Step 4: Allocate employees to clients
        $employeeAllocations = $this->allocateEmployeesToClients($tasksByClient, $optimalWorkforce);

        // Step 5: Form teams
        $teams = $this->formTeams($employeeAllocations);

        return [
            'valid_tasks' => $validTasks,
            'invalid_tasks' => $invalidTasks,
            'employee_allocations' => $employeeAllocations,
            'teams' => $teams,
            'optimal_workforce_size' => count($optimalWorkforce),
            'tasks_by_client' => $tasksByClient,
        ];
    }

    private function validateTasks($tasks)
    {
        $valid = [];
        $invalid = [];

        usort($tasks, function ($a, $b) {
            return ($b['arrival_status'] ?? 0) <=> ($a['arrival_status'] ?? 0);
        });

        foreach ($tasks as $task) {
            if (!empty($task['location_id']) && !empty($task['scheduled_date'])) {
                $valid[] = $task;
            } else {
                $invalid[] = $task;
            }
        }

        return [$valid, $invalid];
    }

    private function calculateOptimalWorkforce($tasks, $employees)
    {
        $totalWorkHours = 0;
        foreach ($tasks as $task) {
            $taskHours = ($task['duration'] + ($task['travel_time'] ?? 0)) / 60;
            $totalWorkHours += $taskHours;
        }

        $adjustedWorkHours = $totalWorkHours / $this->rules['target_utilization_rate'];
        $minWorkforce = ceil($adjustedWorkHours / $this->rules['max_work_hours']);
        $minWorkforce = max($minWorkforce, count($employees));

        $drivers = array_filter($employees, fn($emp) => $emp['has_driving_license'] == 1);
        $nonDrivers = array_filter($employees, fn($emp) => $emp['has_driving_license'] == 0);

        $selectedEmployees = array_merge(array_values($drivers), array_values($nonDrivers));

        return $selectedEmployees;
    }

    private function groupTasksByClient($tasks)
    {
        $grouped = [];
        foreach ($tasks as $task) {
            $clientId = $task['client_id'] ?? 'unassigned';
            if (!isset($grouped[$clientId])) {
                $grouped[$clientId] = [];
            }
            $grouped[$clientId][] = $task;
        }
        return $grouped;
    }

    private function allocateEmployeesToClients($tasksByClient, $workforce)
    {
        $allocations = [];
        foreach ($tasksByClient as $clientId => $clientTasks) {
            $allocations[$clientId] = $workforce;
        }
        return $allocations;
    }

    private function formTeams($employeeAllocations)
    {
        $teams = [];
        $teamId = 1;

        $allEmployees = [];
        foreach ($employeeAllocations as $employees) {
            foreach ($employees as $emp) {
                $allEmployees[$emp['id']] = $emp;
            }
        }
        $allEmployees = array_values($allEmployees);

        $drivers = [];
        $nonDrivers = [];
        foreach ($allEmployees as $emp) {
            if ($emp['has_driving_license'] == 1) {
                $drivers[] = $emp;
            } else {
                $nonDrivers[] = $emp;
            }
        }

        $totalEmployees = count($drivers) + count($nonDrivers);
        if ($totalEmployees == 0) return $teams;

        $teamsOf3 = intdiv($totalEmployees, 3);
        $remainder = $totalEmployees % 3;

        if ($remainder == 1) {
            $teamsOf3 = max(0, $teamsOf3 - 1);
            $teamsOf2 = 2;
        } elseif ($remainder == 2) {
            $teamsOf2 = 1;
        } else {
            $teamsOf2 = 0;
        }

        $allAvailable = $allEmployees;
        $clientIds = array_keys($employeeAllocations);

        for ($i = 0; $i < $teamsOf3; $i++) {
            $team = $this->buildTeam($allAvailable, 3);
            if (count($team) >= $this->rules['min_team_size']) {
                foreach ($clientIds as $clientId) {
                    $teams[] = [
                        'team_id' => $teamId,
                        'client_id' => $clientId,
                        'members' => $team,
                        'team_efficiency' => $this->calculateTeamEfficiency($team),
                    ];
                }
                $teamId++;
            }
        }

        for ($i = 0; $i < $teamsOf2; $i++) {
            $team = $this->buildTeam($allAvailable, 2);
            if (count($team) >= $this->rules['min_team_size']) {
                foreach ($clientIds as $clientId) {
                    $teams[] = [
                        'team_id' => $teamId,
                        'client_id' => $clientId,
                        'members' => $team,
                        'team_efficiency' => $this->calculateTeamEfficiency($team),
                    ];
                }
                $teamId++;
            }
        }

        return $teams;
    }

    private function buildTeam(&$allAvailable, $targetSize)
    {
        $team = [];

        $driverIndex = null;
        foreach ($allAvailable as $index => $emp) {
            if ($emp['has_driving_license'] == 1) {
                $driverIndex = $index;
                break;
            }
        }

        if ($driverIndex !== null) {
            $team[] = $allAvailable[$driverIndex];
            unset($allAvailable[$driverIndex]);
            $allAvailable = array_values($allAvailable);
        } elseif (count($allAvailable) > 0) {
            $team[] = array_shift($allAvailable);
        }

        while (count($team) < $targetSize && count($allAvailable) > 0) {
            $nonDriverIndex = null;
            foreach ($allAvailable as $index => $emp) {
                if ($emp['has_driving_license'] == 0) {
                    $nonDriverIndex = $index;
                    break;
                }
            }

            if ($nonDriverIndex !== null) {
                $team[] = $allAvailable[$nonDriverIndex];
                unset($allAvailable[$nonDriverIndex]);
                $allAvailable = array_values($allAvailable);
            } else {
                $team[] = array_shift($allAvailable);
            }
        }

        return $team;
    }

    private function calculateTeamEfficiency($teamMembers)
    {
        $efficiencies = array_map(fn($emp) => $emp['efficiency'] ?? 1.0, $teamMembers);
        return array_sum($efficiencies) / count($efficiencies);
    }
}
