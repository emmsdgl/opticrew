<?php

namespace App\Services\Optimization\GeneticAlgorithm;

/**
 * Mutation Operator - Enhanced Implementation (Production)
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │ PRODUCTION vs SIMULATION COMPARISON                                     │
 * ├─────────────────────────────────────────────────────────────────────────┤
 * │                                                                         │
 * │ PRODUCTION (This Implementation):                                       │
 * │ ✓ 3 Mutation Types: Swap, Insert (balance-aware), Scramble             │
 * │ ✓ Rate: 20% per individual (matches simulation after alignment)        │
 * │ ✓ Strategy: Random selection among 3 types for diversity               │
 * │                                                                         │
 * │ SIMULATION MODEL:                                                       │
 * │ ✓ 1 Mutation Type: Simple team reassignment                            │
 * │ ✓ Rate: 20% per individual                                             │
 * │ ✓ Strategy: Move random task to random team (same client)              │
 * │                                                                         │
 * │ WHY DIFFERENT?                                                          │
 * │ Production uses enhanced operators based on scheduling literature       │
 * │ (Syswerda, 1989). Both approaches are valid; production version        │
 * │ provides better balance optimization for real-world scenarios.          │
 * │                                                                         │
 * └─────────────────────────────────────────────────────────────────────────┘
 *
 * @package App\Services\Optimization\GeneticAlgorithm
 */
class MutationOperator
{
    /**
     * Apply mutation to an individual
     *
     * Randomly selects one of three mutation types:
     * 1. Swap: Exchange tasks between two teams
     * 2. Insert: Move task from most loaded to least loaded (balance-aware)
     * 3. Scramble: Reorder tasks within a team
     *
     * @param Individual $individual
     * @return Individual Mutated individual
     */
    public function mutate(Individual $individual): Individual
    {
        $schedule = $individual->getSchedule();
        $mutatedSchedule = $schedule;
        
        // Randomly select mutation type
        $mutationType = rand(1, 3);
        
        switch ($mutationType) {
            case 1:
                $mutatedSchedule = $this->swapMutation($mutatedSchedule);
                break;
            case 2:
                $mutatedSchedule = $this->insertMutation($mutatedSchedule);
                break;
            case 3:
                $mutatedSchedule = $this->scrambleMutation($mutatedSchedule);
                break;
        }
        
        return new Individual($mutatedSchedule);
    }

    /**
     * Swap random tasks between two random teams
     */
    protected function swapMutation(array $schedule): array
    {
        $teamIndices = array_keys($schedule);
        
        if (count($teamIndices) < 2) {
            return $schedule;
        }
        
        // Select two random teams
        shuffle($teamIndices);
        $team1Index = $teamIndices[0];
        $team2Index = $teamIndices[1];
        
        $team1Tasks = $schedule[$team1Index]['tasks'];
        $team2Tasks = $schedule[$team2Index]['tasks'];
        
        if ($team1Tasks->isEmpty() || $team2Tasks->isEmpty()) {
            return $schedule;
        }
        
        // Swap random tasks
        $task1Index = rand(0, $team1Tasks->count() - 1);
        $task2Index = rand(0, $team2Tasks->count() - 1);
        
        $task1 = $team1Tasks[$task1Index];
        $task2 = $team2Tasks[$task2Index];
        
        $schedule[$team1Index]['tasks'][$task1Index] = $task2;
        $schedule[$team2Index]['tasks'][$task2Index] = $task1;
        
        return $schedule;
    }

    /**
     * Move a task from MOST loaded team to LEAST loaded team (balance-preserving)
     * ✅ FIX: This mutation now improves balance instead of destroying it
     */
    protected function insertMutation(array $schedule): array
    {
        $teamIndices = array_keys($schedule);

        if (count($teamIndices) < 2) {
            return $schedule;
        }

        // ✅ Find MOST loaded team (source)
        $maxWorkload = -1;
        $sourceTeamIndex = null;
        foreach ($teamIndices as $index) {
            if ($schedule[$index]['tasks']->isEmpty()) {
                continue;
            }
            $workload = $schedule[$index]['tasks']->sum('duration');
            if ($workload > $maxWorkload) {
                $maxWorkload = $workload;
                $sourceTeamIndex = $index;
            }
        }

        if ($sourceTeamIndex === null) {
            return $schedule;
        }

        // ✅ Find LEAST loaded team (target)
        $minWorkload = PHP_INT_MAX;
        $targetTeamIndex = null;
        foreach ($teamIndices as $index) {
            if ($index === $sourceTeamIndex) {
                continue; // Skip source team
            }
            $workload = $schedule[$index]['tasks']->sum('duration');
            if ($workload < $minWorkload) {
                $minWorkload = $workload;
                $targetTeamIndex = $index;
            }
        }

        if ($targetTeamIndex === null) {
            return $schedule;
        }

        // Select random task from MOST loaded team
        $sourceTasks = $schedule[$sourceTeamIndex]['tasks'];
        $taskIndex = rand(0, $sourceTasks->count() - 1);
        $task = $sourceTasks[$taskIndex];

        // Move task from MOST loaded to LEAST loaded
        $schedule[$sourceTeamIndex]['tasks'] = $sourceTasks->forget($taskIndex)->values();
        $schedule[$targetTeamIndex]['tasks']->push($task);

        return $schedule;
    }

    /**
     * Scramble tasks within a random team
     */
    protected function scrambleMutation(array $schedule): array
    {
        $teamIndices = array_keys($schedule);
        $randomTeamIndex = $teamIndices[array_rand($teamIndices)];
        
        $tasks = $schedule[$randomTeamIndex]['tasks'];
        
        if ($tasks->count() < 2) {
            return $schedule;
        }
        
        // Shuffle tasks within the team
        $schedule[$randomTeamIndex]['tasks'] = $tasks->shuffle();
        
        return $schedule;
    }

    /**
     * Inversion mutation - reverse sequence of tasks in a team
     */
    protected function inversionMutation(array $schedule): array
    {
        $teamIndices = array_keys($schedule);
        $randomTeamIndex = $teamIndices[array_rand($teamIndices)];
        
        $tasks = $schedule[$randomTeamIndex]['tasks'];
        
        if ($tasks->count() < 2) {
            return $schedule;
        }
        
        // Reverse task order
        $schedule[$randomTeamIndex]['tasks'] = $tasks->reverse()->values();
        
        return $schedule;
    }

    /**
     * Multi-swap mutation - swap multiple tasks
     */
    public function heavyMutate(Individual $individual): Individual
    {
        $schedule = $individual->getSchedule();
        
        // Perform multiple mutations
        $mutationCount = rand(2, 4);
        for ($i = 0; $i < $mutationCount; $i++) {
            $schedule = $this->swapMutation($schedule);
        }
        
        return new Individual($schedule);
    }
}