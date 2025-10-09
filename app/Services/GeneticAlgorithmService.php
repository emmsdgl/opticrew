<?php

namespace App\Services;

use Illuminate\Support\Collection;

class GeneticAlgorithmService
{
    private Collection $teams;
    private Collection $tasks;
    private array $teamEfficiencies;
    private int $populationSize;
    private int $generations;
    private float $mutationRate;
    private array $initialWorkloads;
    private ?array $seedSchedule; // The greedy result

    public function __construct(
        Collection $teams, 
        Collection $tasks, 
        array $teamEfficiencies, 
        array $initialWorkloads = [],
        ?array $seedSchedule = null
    ) {
        $this->teams = $teams;
        $this->tasks = $tasks;
        $this->teamEfficiencies = $teamEfficiencies;
        $this->initialWorkloads = $initialWorkloads;
        $this->seedSchedule = $seedSchedule;

        $this->populationSize = 20;
        $this->generations = 100;
        $this->mutationRate = 0.10;
    }

    public function run(): array
    {
        $population = $this->initializePopulation();
        $bestFitnessSoFar = 0;
        $generationsWithoutImprovement = 0;
        $patience = 15;

        for ($i = 0; $i < $this->generations; $i++) {
            $population = $this->calculatePopulationFitness($population);

            $currentBestFitness = $population[0]['fitness'];
            if ($currentBestFitness > $bestFitnessSoFar) {
                $bestFitnessSoFar = $currentBestFitness;
                $generationsWithoutImprovement = 0;
            } else {
                $generationsWithoutImprovement++;
            }

            if ($generationsWithoutImprovement >= $patience) {
                break; // Early stopping
            }

            $newPopulation = [$population[0]]; // Elitism
            while (count($newPopulation) < $this->populationSize) {
                $parent1 = $this->selection($population);
                $parent2 = $this->selection($population);
                $child = $this->crossover($parent1, $parent2);
                $child = $this->mutate($child);
                $newPopulation[] = $child;
            }
            $population = $newPopulation;
        }

        $population = $this->calculatePopulationFitness($population);
        return $population[0];
    }

    /**
     * IMPROVED: Initialize population with greedy schedule as seed
     */
    private function initializePopulation(): array
    {
        $population = [];
        
        // If we have a seed (greedy result), add it as the first individual
        if ($this->seedSchedule !== null) {
            $population[] = $this->seedSchedule;
        }
        
        // Fill the rest with random schedules
        while (count($population) < $this->populationSize) {
            $schedule = [];
            $shuffledTasks = $this->tasks->shuffle();
            
            foreach ($this->teams as $index => $team) {
                $schedule[$index] = ['tasks' => collect()];
            }
            
            foreach ($shuffledTasks as $task) {
                $randomTeamIndex = array_rand($schedule);
                $schedule[$randomTeamIndex]['tasks']->push($task);
            }
            
            $population[] = $schedule;
        }
        
        return $population;
    }

    private function calculatePopulationFitness(array $population): array
    {
        foreach ($population as &$schedule) {
            $schedule['fitness'] = $this->calculateFitness($schedule);
        }
        usort($population, fn($a, $b) => $b['fitness'] <=> $a['fitness']);
        return $population;
    }

    /**
     * Calculate fitness based on workload balance
     */
    private function calculateFitness(array $schedule): float
    {
        $predictedWorkloads = $this->initialWorkloads;

        foreach ($schedule as $teamIndex => $teamData) {
            if (is_int($teamIndex)) {
                $teamEfficiency = $this->teamEfficiencies[$teamIndex] ?? 1.0;
                
                $predictedTaskWorkload = $teamData['tasks']->sum(function($task) use ($teamEfficiency) {
                    return $teamEfficiency > 0 
                        ? $task->base_cleaning_duration_minutes / $teamEfficiency 
                        : $task->base_cleaning_duration_minutes;
                });
                
                $predictedWorkloads[$teamIndex] += $predictedTaskWorkload;
            }
        }

        if (count($predictedWorkloads) < 2) {
            return 1; // Single team is perfectly balanced
        }

        $mean = array_sum($predictedWorkloads) / count($predictedWorkloads);
        $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $predictedWorkloads)) / count($predictedWorkloads);
        $stdDev = sqrt($variance);

        return 1 / (1 + $stdDev);
    }
    
    private function selection(array $population): array
    {
        $tournament = collect($population)->random(5)->all();
        usort($tournament, fn($a, $b) => $b['fitness'] <=> $a['fitness']);
        return $tournament[0];
    }
    
    private function crossover(array $parent1, array $parent2): array
    {
        $childSchedule = [];
        $allTasks = $this->tasks->keyBy('id');
        
        $parent1Tasks = collect();
        foreach($parent1 as $index => $data) {
            if(is_int($index)) {
                $parent1Tasks = $parent1Tasks->merge(
                    $data['tasks']->map(fn($t) => ['task_id' => $t->id, 'teamIndex' => $index])
                );
            }
        }
        
        $parent2Tasks = collect();
        foreach($parent2 as $index => $data) {
            if(is_int($index)) {
                $parent2Tasks = $parent2Tasks->merge(
                    $data['tasks']->map(fn($t) => ['task_id' => $t->id, 'teamIndex' => $index])
                );
            }
        }

        $start = rand(0, max(0, $parent1Tasks->count() - 1));
        $end = rand($start, $parent1Tasks->count() - 1);
        $childSlice = $parent1Tasks->slice($start, $end - $start)->keyBy('task_id');
        
        $childTasks = $childSlice->toArray();
        foreach($parent2Tasks as $taskData) {
            if(!isset($childTasks[$taskData['task_id']])) {
                $childTasks[$taskData['task_id']] = $taskData;
            }
        }

        foreach ($this->teams as $index => $team) {
            $childSchedule[$index] = ['tasks' => collect()];
        }

        foreach($childTasks as $taskData) {
            $childSchedule[$taskData['teamIndex']]['tasks']->push($allTasks[$taskData['task_id']]);
        }
        
        return $childSchedule;
    }
    
    private function mutate(array $schedule): array
    {
        if ((rand(0, 100) / 100) < $this->mutationRate) {
            $teamIndices = array_keys(array_filter($schedule, fn($k) => is_int($k), ARRAY_FILTER_USE_KEY));
            
            if (count($teamIndices) >= 2) {
                $teamIndex1 = $teamIndices[array_rand($teamIndices)];
                $teamIndex2 = $teamIndices[array_rand($teamIndices)];

                if ($teamIndex1 !== $teamIndex2 && 
                    $schedule[$teamIndex1]['tasks']->isNotEmpty() && 
                    $schedule[$teamIndex2]['tasks']->isNotEmpty()) {
                    
                    $task1 = $schedule[$teamIndex1]['tasks']->pop();
                    $task2 = $schedule[$teamIndex2]['tasks']->pop();
                    $schedule[$teamIndex1]['tasks']->push($task2);
                    $schedule[$teamIndex2]['tasks']->push($task1);
                }
            }
        }
        return $schedule;
    }
}