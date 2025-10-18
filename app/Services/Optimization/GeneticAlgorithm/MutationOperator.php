<?php

namespace App\Services\Optimization\GeneticAlgorithm;

class MutationOperator
{
    /**
     * Swap mutation - randomly swap tasks between two teams
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
     * Move a random task from one team to another
     */
    protected function insertMutation(array $schedule): array
    {
        $teamIndices = array_keys($schedule);
        
        if (count($teamIndices) < 2) {
            return $schedule;
        }
        
        // Find team with tasks
        $sourceTeamIndex = null;
        foreach ($teamIndices as $index) {
            if ($schedule[$index]['tasks']->isNotEmpty()) {
                $sourceTeamIndex = $index;
                break;
            }
        }
        
        if ($sourceTeamIndex === null) {
            return $schedule;
        }
        
        // Select random task from source team
        $sourceTasks = $schedule[$sourceTeamIndex]['tasks'];
        $taskIndex = rand(0, $sourceTasks->count() - 1);
        $task = $sourceTasks[$taskIndex];
        
        // Remove task from source
        $schedule[$sourceTeamIndex]['tasks'] = $sourceTasks->forget($taskIndex)->values();
        
        // Add to random different team
        $availableTeams = array_diff($teamIndices, [$sourceTeamIndex]);
        $targetTeamIndex = $availableTeams[array_rand($availableTeams)];
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