<?php
/**
 * Traditional Genetic Algorithm (No Preprocessing)
 *
 * Standard GA without rule-based filtering:
 * - Direct task assignment to employees
 * - No validation or preprocessing
 * - Purely random initialization (no greedy seed)
 * - No elitism
 */

class TraditionalGA
{
    private $config;
    private $fitnessHistory = [];

    public function __construct($config = [])
    {
        $this->config = array_merge([
            'population_size' => 50,
            'max_generations' => 100,
            'mutation_rate' => 0.1,
            'crossover_rate' => 0.8,
            'patience' => 15,
            'max_work_hours' => 12 * 60,
        ], $config);
    }

    public function optimize($tasks, $employees, $clients)
    {
        $this->fitnessHistory = [];

        if (empty($tasks) || empty($employees)) {
            return [
                'best_schedule' => [],
                'best_fitness' => 0,
                'generations' => 0,
                'fitness_history' => [],
                'teams' => [],
            ];
        }

        $teams = $this->createSimpleTeams($employees, $clients);
        $population = $this->initializePopulation($tasks, $teams);

        $bestFitness = -INF;
        $bestSchedule = null;
        $generationsWithoutImprovement = 0;
        $convergenceGeneration = null;

        for ($generation = 0; $generation < $this->config['max_generations']; $generation++) {
            $fitnessScores = [];
            foreach ($population as $idx => $schedule) {
                $fitnessScores[$idx] = $this->calculateFitness($schedule, $tasks, $teams);
            }

            $currentBest = max($fitnessScores);
            $currentBestIdx = array_search($currentBest, $fitnessScores);

            $this->fitnessHistory[] = [
                'generation' => $generation,
                'best' => $currentBest,
                'average' => array_sum($fitnessScores) / count($fitnessScores),
                'worst' => min($fitnessScores),
            ];

            if ($currentBest > $bestFitness) {
                $bestFitness = $currentBest;
                $bestSchedule = $population[$currentBestIdx];
                $generationsWithoutImprovement = 0;
                $convergenceGeneration = $generation;
            } else {
                $generationsWithoutImprovement++;
            }

            if ($this->config['patience'] > 0 && $generationsWithoutImprovement >= $this->config['patience']) {
                break;
            }

            // No elitism - pure selection
            $selected = $this->selection($population, $fitnessScores);

            $newPopulation = [];
            while (count($newPopulation) < $this->config['population_size']) {
                $parent1 = $selected[array_rand($selected)];
                $parent2 = $selected[array_rand($selected)];

                if (mt_rand() / mt_getrandmax() < $this->config['crossover_rate']) {
                    $offspring = $this->crossover($parent1, $parent2);
                } else {
                    $offspring = $parent1;
                }

                if (mt_rand() / mt_getrandmax() < $this->config['mutation_rate']) {
                    $offspring = $this->mutate($offspring, $tasks, $teams);
                }

                $newPopulation[] = $offspring;
            }

            $population = $newPopulation;
        }

        return [
            'best_schedule' => $bestSchedule ?? [],
            'best_fitness' => max(0, $bestFitness),
            'generations' => $generation + 1,
            'convergence_generation' => $convergenceGeneration,
            'fitness_history' => $this->fitnessHistory,
            'teams' => $teams,
        ];
    }

    private function createSimpleTeams($employees, $clients)
    {
        $teams = [];
        $teamId = 1;
        $employeeChunks = array_chunk($employees, 2);

        foreach ($clients as $client) {
            foreach ($employeeChunks as $chunk) {
                $teams[] = [
                    'team_id' => $teamId++,
                    'client_id' => $client['id'],
                    'members' => $chunk,
                    'team_efficiency' => $this->calculateTeamEfficiency($chunk),
                ];
            }
        }

        return $teams;
    }

    private function calculateTeamEfficiency($teamMembers)
    {
        $efficiencies = array_map(fn($emp) => $emp['efficiency'] ?? 1.0, $teamMembers);
        return array_sum($efficiencies) / count($efficiencies);
    }

    private function initializePopulation($tasks, $teams)
    {
        $population = [];
        // All random - no greedy seed
        for ($i = 0; $i < $this->config['population_size']; $i++) {
            $population[] = $this->generateRandomSchedule($tasks, $teams);
        }
        return $population;
    }

    private function generateRandomSchedule($tasks, $teams)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);

        $shuffledTasks = $tasks;
        shuffle($shuffledTasks);

        foreach ($shuffledTasks as $task) {
            $randomTeam = $teams[array_rand($teams)];
            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            if ($teamWorkloads[$randomTeam['team_id']] + $taskDuration <= $this->config['max_work_hours'] * 1.2) {
                $schedule[] = [
                    'task_id' => $task['id'],
                    'team_id' => $randomTeam['team_id'],
                    'client_id' => $task['client_id'],
                ];
                $teamWorkloads[$randomTeam['team_id']] += $taskDuration;
            }
        }

        return $schedule;
    }

    private function calculateFitness($schedule, $tasks, $teams)
    {
        if (empty($schedule)) return 0.001;

        $teamWorkloads = [];
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['id']] = $task;
        }

        foreach ($schedule as $assignment) {
            $taskId = $assignment['task_id'];
            $teamId = $assignment['team_id'];
            if (!isset($taskMap[$taskId])) continue;

            $duration = $taskMap[$taskId]['duration'] + ($taskMap[$taskId]['travel_time'] ?? 0);

            if (!isset($teamWorkloads[$teamId])) $teamWorkloads[$teamId] = 0;
            $teamWorkloads[$teamId] += $duration;
        }

        if (empty($teamWorkloads)) return 0.001;

        $mean = array_sum($teamWorkloads) / count($teamWorkloads);
        $variance = 0;
        foreach ($teamWorkloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }
        $stdDev = sqrt($variance / count($teamWorkloads));

        // Normalize by mean (coefficient of variation) so fitness measures
        // proportional balance, not absolute minutes
        $normalizedStdDev = $mean > 0 ? $stdDev / $mean : 0;
        $fitness = 1 / (1 + $normalizedStdDev);

        // Penalties
        $violationPenalty = 1.0;
        foreach ($teamWorkloads as $workload) {
            if ($workload > $this->config['max_work_hours']) {
                $violationPenalty *= 0.3;
            }
        }
        $fitness *= $violationPenalty;

        $completionRate = count($schedule) / count($tasks);
        $fitness *= pow($completionRate, 4);

        if ($completionRate < 1.0) {
            $unassignedTasks = count($tasks) - count($schedule);
            $fitness *= (1.0 / (1.0 + $unassignedTasks * 5.0));
        }

        return max(0.001, $fitness);
    }

    private function selection($population, $fitnessScores)
    {
        $selected = [];
        $tournamentSize = 3;

        for ($i = 0; $i < count($population) / 2; $i++) {
            $tournament = [];
            for ($j = 0; $j < $tournamentSize; $j++) {
                $idx = array_rand($population);
                $tournament[$idx] = $fitnessScores[$idx];
            }
            arsort($tournament);
            $winner = array_key_first($tournament);
            $selected[] = $population[$winner];
        }

        return $selected;
    }

    private function crossover($parent1, $parent2)
    {
        if (empty($parent1) || empty($parent2)) return $parent1;

        $maxPoint = min(count($parent1), count($parent2)) - 1;
        if ($maxPoint < 1) return $parent1;

        $crossoverPoint = mt_rand(1, $maxPoint);

        $offspring = array_merge(
            array_slice($parent1, 0, $crossoverPoint),
            array_slice($parent2, $crossoverPoint)
        );

        $seen = [];
        $offspring = array_filter($offspring, function ($assignment) use (&$seen) {
            $taskId = $assignment['task_id'];
            if (in_array($taskId, $seen)) return false;
            $seen[] = $taskId;
            return true;
        });

        return array_values($offspring);
    }

    private function mutate($schedule, $tasks, $teams)
    {
        if (empty($schedule)) return $schedule;

        $mutated = $schedule;
        $mutationPoint = array_rand($mutated);
        $newTeam = $teams[array_rand($teams)];
        $mutated[$mutationPoint]['team_id'] = $newTeam['team_id'];

        return $mutated;
    }
}
