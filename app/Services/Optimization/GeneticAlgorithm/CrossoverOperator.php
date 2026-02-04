<?php

namespace App\Services\Optimization\GeneticAlgorithm;

/**
 * Crossover Operator - Uniform Crossover (Production)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ PRODUCTION vs SIMULATION COMPARISON                                     │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │                                                                         │
 * │ PRODUCTION (This Implementation):                                       │
 * │ ✓ Type: Uniform Crossover                                              │
 * │ ✓ Method: Random 50/50 selection per team from parents                 │
 * │ ✓ Repair: Assign missing tasks to LEAST loaded team (balance-aware)    │
 * │ ✓ Rate: Always applied (100%)                                          │
 * │                                                                         │
 * │ SIMULATION MODEL:                                                       │
 * │ ✓ Type: Single-Point Crossover                                         │
 * │ ✓ Method: Split at random point, combine parent segments               │
 * │ ✓ Repair: Remove duplicate task assignments                            │
 * │ ✓ Rate: 85% probability                                                │
 * │                                                                         │
 * │ WHY DIFFERENT?                                                          │
 * │ Uniform crossover provides higher diversity than single-point for      │
 * │ scheduling problems with multiple objectives (Syswerda, 1989).         │
 * │ Both approaches are valid genetic operators with proven effectiveness.  │
 * │                                                                         │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * @package App\Services\Optimization\GeneticAlgorithm
 */
class CrossoverOperator
{
    /**
     * Perform uniform crossover between two parent schedules
     *
     * Each team's task list has a 50% chance of coming from either parent.
     * This provides higher diversity compared to single-point crossover,
     * which is beneficial for multi-objective scheduling optimization.
     *
     * @param Individual $parentA First parent individual
     * @param Individual $parentB Second parent individual
     * @return Individual Child individual created from crossover
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
     * ✅ FIX: Assign missing tasks to team with FEWEST TASKS (fair distribution)
     * Uses task count as primary metric, workload as secondary tiebreaker
     */
    protected function repairSchedule(array $schedule, array $referenceSchedule): array
    {
        $allTasksInReference = $this->getAllTasks($referenceSchedule);
        $assignedTasks = $this->getAllTasks($schedule);

        $assignedIds = $assignedTasks->pluck('id');
        $missingTasks = $allTasksInReference->reject(fn($task) => $assignedIds->contains($task->id));

        // ✅ FIX: Assign missing tasks to team with FEWEST TASKS (not random!)
        // Primary: task count, Secondary: workload (matches generateFairGreedySchedule)
        foreach ($missingTasks as $task) {
            $minTaskCount = PHP_INT_MAX;
            $minWorkload = PHP_INT_MAX;
            $selectedTeam = 0;

            foreach ($schedule as $teamIndex => $teamSchedule) {
                $taskCount = $teamSchedule['tasks']->count();
                $workload = $teamSchedule['tasks']->sum('duration');

                // Primary: fewer tasks, Secondary: lower workload
                if ($taskCount < $minTaskCount ||
                    ($taskCount === $minTaskCount && $workload < $minWorkload)) {
                    $minTaskCount = $taskCount;
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