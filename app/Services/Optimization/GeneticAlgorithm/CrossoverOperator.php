<?php

namespace App\Services\Optimization\GeneticAlgorithm;

class CrossoverOperator
{
    /**
     * Perform uniform crossover between two parent schedules
     * Each team's task list has a 50% chance of coming from either parent
     */
    public function crossover(Individual $parentA, Individual $parentB): Individual
    {
        $scheduleA = $parentA->getSchedule();
        $scheduleB = $parentB->getSchedule();
        
        $childSchedule = [];
        
        foreach ($scheduleA as $teamIndex => $teamScheduleA) {
            // Randomly choose which parent to inherit from for this team
            if (rand(0, 1) === 0) {
                $childSchedule[$teamIndex] = [
                    'team' => $teamScheduleA['team'],
                    'tasks' => clone $teamScheduleA['tasks'],
                ];
            } else {
                $teamScheduleB = $scheduleB[$teamIndex];
                $childSchedule[$teamIndex] = [
                    'team' => $teamScheduleB['team'],
                    'tasks' => clone $teamScheduleB['tasks'],
                ];
            }
        }
        
        // Ensure all tasks are assigned (repair if necessary)
        $childSchedule = $this->repairSchedule($childSchedule, $scheduleA);
        
        return new Individual($childSchedule);
    }

    /**
     * Order crossover - preserves task sequences
     */
    public function orderCrossover(Individual $parentA, Individual $parentB): Individual
    {
        $scheduleA = $parentA->getSchedule();
        $scheduleB = $parentB->getSchedule();
        
        $childSchedule = [];
        $teamCount = count($scheduleA);
        
        // Select random crossover points
        $point1 = rand(0, $teamCount - 1);
        $point2 = rand($point1, $teamCount - 1);
        
        // Copy segment from parent A
        for ($i = $point1; $i <= $point2; $i++) {
            $childSchedule[$i] = [
                'team' => $scheduleA[$i]['team'],
                'tasks' => clone $scheduleA[$i]['tasks'],
            ];
        }
        
        // Fill remaining from parent B
        $assignedTasks = collect();
        foreach ($childSchedule as $teamSchedule) {
            $assignedTasks = $assignedTasks->merge($teamSchedule['tasks']->pluck('id'));
        }
        
        foreach ($scheduleB as $teamIndex => $teamScheduleB) {
            if (!isset($childSchedule[$teamIndex])) {
                $childSchedule[$teamIndex] = [
                    'team' => $teamScheduleB['team'],
                    'tasks' => collect(),
                ];
            }
        }
        
        // Add unassigned tasks from parent B
        foreach ($scheduleB as $teamScheduleB) {
            foreach ($teamScheduleB['tasks'] as $task) {
                if (!$assignedTasks->contains($task->id)) {
                    // Assign to random team
                    $randomTeam = array_rand($childSchedule);
                    $childSchedule[$randomTeam]['tasks']->push($task);
                    $assignedTasks->push($task->id);
                }
            }
        }
        
        return new Individual($childSchedule);
    }

    /**
     * Two-point crossover
     */
    public function twoPointCrossover(Individual $parentA, Individual $parentB): Individual
    {
        $scheduleA = $parentA->getSchedule();
        $scheduleB = $parentB->getSchedule();
        
        $childSchedule = [];
        $allTasks = $this->getAllTasks($scheduleA);
        
        // Randomly split tasks into segments
        $shuffledTasks = $allTasks->shuffle();
        $point1 = rand(1, $shuffledTasks->count() - 2);
        $point2 = rand($point1 + 1, $shuffledTasks->count() - 1);
        
        $segment1 = $shuffledTasks->slice(0, $point1);
        $segment2 = $shuffledTasks->slice($point1, $point2 - $point1);
        $segment3 = $shuffledTasks->slice($point2);
        
        // Initialize child schedule
        foreach ($scheduleA as $teamIndex => $teamSchedule) {
            $childSchedule[$teamIndex] = [
                'team' => $teamSchedule['team'],
                'tasks' => collect(),
            ];
        }
        
        // Assign segments to teams
        $this->assignTasksToTeams($segment1, $childSchedule, $scheduleA);
        $this->assignTasksToTeams($segment2, $childSchedule, $scheduleB);
        $this->assignTasksToTeams($segment3, $childSchedule, $scheduleA);
        
        return new Individual($childSchedule);
    }

    /**
     * Repair schedule to ensure all tasks are assigned exactly once
     * ✅ FIX: Assign missing tasks to LEAST LOADED team to maintain balance
     */
    protected function repairSchedule(array $schedule, array $referenceSchedule): array
    {
        $allTasksInReference = $this->getAllTasks($referenceSchedule);
        $assignedTasks = $this->getAllTasks($schedule);

        $assignedIds = $assignedTasks->pluck('id');
        $missingTasks = $allTasksInReference->reject(fn($task) => $assignedIds->contains($task->id));

        // ✅ FIX: Assign missing tasks to LEAST LOADED team (not random!)
        foreach ($missingTasks as $task) {
            // Find team with minimum workload
            $minWorkload = PHP_INT_MAX;
            $selectedTeam = 0;

            foreach ($schedule as $teamIndex => $teamSchedule) {
                $workload = $teamSchedule['tasks']->sum('duration');
                if ($workload < $minWorkload) {
                    $minWorkload = $workload;
                    $selectedTeam = $teamIndex;
                }
            }

            $schedule[$selectedTeam]['tasks']->push($task);
        }

        return $schedule;
    }

    /**
     * Get all tasks from a schedule
     */
    protected function getAllTasks(array $schedule): \Illuminate\Support\Collection
    {
        $allTasks = collect();
        foreach ($schedule as $teamSchedule) {
            $allTasks = $allTasks->merge($teamSchedule['tasks']);
        }
        return $allTasks;
    }

    /**
     * Assign tasks to teams based on reference schedule pattern
     */
    protected function assignTasksToTeams($tasks, array &$targetSchedule, array $referenceSchedule): void
    {
        foreach ($tasks as $task) {
            // Find which team has this task in reference
            $targetTeam = 0;
            foreach ($referenceSchedule as $teamIndex => $teamSchedule) {
                if ($teamSchedule['tasks']->contains('id', $task->id)) {
                    $targetTeam = $teamIndex;
                    break;
                }
            }
            
            $targetSchedule[$targetTeam]['tasks']->push($task);
        }
    }
}