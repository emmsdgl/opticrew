<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * @deprecated This service is deprecated and will be removed in a future version.
 * Use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer instead.
 *
 * This file is kept for backward compatibility only.
 * All new code should use the modular GeneticAlgorithmOptimizer in the Optimization namespace.
 */
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
    private ?int $optimizationRunId;

    public function __construct(
        Collection $teams, 
        Collection $tasks, 
        array $teamEfficiencies, 
        array $initialWorkloads = [],
        ?array $seedSchedule = null,
        ?int $optimizationRunId = null // ADD THIS
    ) {
        $this->teams = $teams;
        $this->tasks = $tasks;
        $this->teamEfficiencies = $teamEfficiencies;
        $this->initialWorkloads = $initialWorkloads;
        $this->seedSchedule = $seedSchedule;
        $this->optimizationRunId = $optimizationRunId; // ADD THIS

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
    
        // Log generation 0
        $population = $this->calculatePopulationFitness($population);
        $this->logGeneration(0, $population, false);
    
        for ($i = 1; $i <= $this->generations; $i++) {
            $population = $this->calculatePopulationFitness($population);
    
            $currentBestFitness = $population[0]['fitness'];
            $isImprovement = $currentBestFitness > $bestFitnessSoFar;
            
            if ($isImprovement) {
                $bestFitnessSoFar = $currentBestFitness;
                $generationsWithoutImprovement = 0;
            } else {
                $generationsWithoutImprovement++;
            }
    
            // Log every 10th generation or when there's improvement
            if ($i % 10 == 0 || $isImprovement) {
                $this->logGeneration($i, $population, $isImprovement);
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
        
        // Log final generation if not already logged
        if ($i % 10 != 0) {
            $this->logGeneration($i, $population, false);
        }
        
        return $population[0];
    }

    private function logGeneration(int $generationNumber, array $population, bool $isImprovement): void
    {
        if (!$this->optimizationRunId) {
            return; // Can't log without optimization run ID
        }
    
        $fitnesses = array_map(fn($schedule) => $schedule['fitness'], $population);
        $bestFitness = max($fitnesses);
        $averageFitness = array_sum($fitnesses) / count($fitnesses);
        $worstFitness = min($fitnesses);
    
        // Format best schedule for storage
        $bestScheduleData = $this->formatScheduleForStorage($population[0]);
    
        \App\Models\OptimizationGeneration::create([
            'optimization_run_id' => $this->optimizationRunId,
            'generation_number' => $generationNumber,
            'best_fitness' => round($bestFitness, 4),
            'average_fitness' => round($averageFitness, 4),
            'worst_fitness' => round($worstFitness, 4),
            'is_improvement' => $isImprovement,
            'best_schedule_data' => json_encode($bestScheduleData),
            'population_summary' => json_encode([
                'population_size' => count($population),
                'fitness_range' => [
                    'min' => round($worstFitness, 4),
                    'max' => round($bestFitness, 4),
                    'avg' => round($averageFitness, 4)
                ]
            ])
        ]);
    }
    
    // ADD THIS HELPER METHOD to format schedule:
    
    private function formatScheduleForStorage(array $schedule): array
    {
        $formatted = [];
        foreach ($schedule as $teamIndex => $teamData) {
            if (!is_int($teamIndex)) continue;
            
            $formatted[] = [
                'team_index' => $teamIndex,
                'task_count' => $teamData['tasks']->count(),
                'task_ids' => $teamData['tasks']->pluck('id')->toArray(),
                'total_duration' => $teamData['tasks']->sum('base_cleaning_duration_minutes')
            ];
        }
        return $formatted;
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