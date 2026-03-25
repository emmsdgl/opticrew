<?php
/**
 * Hybrid Genetic Algorithm (Rule-Based Preprocessing + GA with Elitism)
 *
 * This is OptiCrew's actual algorithm:
 * - Uses RuleBasedPreprocessor to filter/validate/form teams
 * - Greedy seed + random population initialization
 * - Elitism preserves best solutions across generations
 * - Fitness: workload balance + constraint compliance + task completion
 */

class HybridGA
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
            'elite_percentage' => 0.1,
            'patience' => 15,
            'max_work_hours' => 12 * 60,
        ], $config);
    }

    public function optimize($tasks, $employeeAllocations, $teams)
    {
        $this->fitnessHistory = [];

        if (empty($tasks) || empty($teams)) {
            return [
                'best_schedule' => [],
                'best_fitness' => 0,
                'generations' => 0,
                'convergence_generation' => null,
                'fitness_history' => [],
            ];
        }

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

            // Selection
            $selected = $this->selection($population, $fitnessScores);

            // Elitism: preserve best solutions
            $eliteCount = max(1, (int)($this->config['population_size'] * $this->config['elite_percentage']));
            arsort($fitnessScores);
            $eliteIndices = array_keys(array_slice($fitnessScores, 0, $eliteCount, true));
            $elites = [];
            foreach ($eliteIndices as $idx) {
                $elites[] = $population[$idx];
            }

            $newPopulation = $elites;

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

            $population = array_slice($newPopulation, 0, $this->config['population_size']);
        }

        return [
            'best_schedule' => $bestSchedule ?? [],
            'best_fitness' => max(0, $bestFitness),
            'generations' => $generation + 1,
            'convergence_generation' => $convergenceGeneration,
            'fitness_history' => $this->fitnessHistory,
        ];
    }

    private function initializePopulation($tasks, $teams)
    {
        $population = [];

        // Greedy seed (Rule-Based advantage)
        $greedySolution = $this->generateGreedySchedule($tasks, $teams);
        $population[] = $greedySolution;

        // Fill rest with random solutions
        for ($i = 1; $i < $this->config['population_size']; $i++) {
            $population[] = $this->generateRandomSchedule($tasks, $teams);
        }

        return $population;
    }

    private function generateGreedySchedule($tasks, $teams)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);
        $assignedTaskIds = [];

        // Sort by duration (longest first)
        $sortedTasks = $tasks;
        usort($sortedTasks, function ($a, $b) {
            $durationA = $a['duration'] + ($a['travel_time'] ?? 0);
            $durationB = $b['duration'] + ($b['travel_time'] ?? 0);
            return $durationB <=> $durationA;
        });

        // First pass: assign largest tasks to least loaded team
        foreach ($sortedTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;

            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $minTeam = null;
            $minWorkload = INF;

            foreach ($clientTeams as $team) {
                $teamId = $team['team_id'];
                if ($teamWorkloads[$teamId] < $minWorkload) {
                    $minWorkload = $teamWorkloads[$teamId];
                    $minTeam = $team;
                }
            }

            if ($minTeam) {
                $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);
                if ($teamWorkloads[$minTeam['team_id']] + $taskDuration <= $this->config['max_work_hours']) {
                    $schedule[] = [
                        'task_id' => $task['id'],
                        'team_id' => $minTeam['team_id'],
                        'client_id' => $task['client_id'],
                    ];
                    $teamWorkloads[$minTeam['team_id']] += $taskDuration;
                    $assignedTaskIds[$task['id']] = true;
                }
            }
        }

        // Second pass: bin-packing for remaining tasks
        $unassignedTasks = array_filter($tasks, fn($task) => !isset($assignedTaskIds[$task['id']]));
        usort($unassignedTasks, function ($a, $b) {
            return ($a['duration'] + ($a['travel_time'] ?? 0)) <=> ($b['duration'] + ($b['travel_time'] ?? 0));
        });

        foreach ($unassignedTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;

            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            foreach ($clientTeams as $team) {
                $teamId = $team['team_id'];
                $remainingCapacity = $this->config['max_work_hours'] - $teamWorkloads[$teamId];
                if ($taskDuration <= $remainingCapacity) {
                    $schedule[] = [
                        'task_id' => $task['id'],
                        'team_id' => $teamId,
                        'client_id' => $task['client_id'],
                    ];
                    $teamWorkloads[$teamId] += $taskDuration;
                    $assignedTaskIds[$task['id']] = true;
                    break;
                }
            }
        }

        return $schedule;
    }

    private function generateRandomSchedule($tasks, $teams)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);
        $assignedTaskIds = [];

        $shuffledTasks = $tasks;
        shuffle($shuffledTasks);

        foreach ($shuffledTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;

            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $clientTeams = array_values($clientTeams);
            $randomTeam = $clientTeams[array_rand($clientTeams)];
            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            if ($teamWorkloads[$randomTeam['team_id']] + $taskDuration <= $this->config['max_work_hours']) {
                $schedule[] = [
                    'task_id' => $task['id'],
                    'team_id' => $randomTeam['team_id'],
                    'client_id' => $task['client_id'],
                ];
                $teamWorkloads[$randomTeam['team_id']] += $taskDuration;
                $assignedTaskIds[$task['id']] = true;
            }
        }

        // Second pass for unassigned
        $unassignedTasks = array_filter($tasks, fn($task) => !isset($assignedTaskIds[$task['id']]));
        shuffle($unassignedTasks);

        foreach ($unassignedTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;

            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            foreach ($clientTeams as $team) {
                $teamId = $team['team_id'];
                if ($this->config['max_work_hours'] - $teamWorkloads[$teamId] >= $taskDuration) {
                    $schedule[] = [
                        'task_id' => $task['id'],
                        'team_id' => $teamId,
                        'client_id' => $task['client_id'],
                    ];
                    $teamWorkloads[$teamId] += $taskDuration;
                    $assignedTaskIds[$task['id']] = true;
                    break;
                }
            }
        }

        return $schedule;
    }

    private function calculateFitness($schedule, $tasks, $teams)
    {
        if (empty($schedule)) return 0;

        $teamWorkloads = [];
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['id']] = $task;
        }

        foreach ($schedule as $assignment) {
            $taskId = $assignment['task_id'];
            $teamId = $assignment['team_id'];
            if (!isset($taskMap[$taskId])) continue;

            $task = $taskMap[$taskId];
            $duration = $task['duration'] + ($task['travel_time'] ?? 0);

            if (!isset($teamWorkloads[$teamId])) {
                $teamWorkloads[$teamId] = 0;
            }
            $teamWorkloads[$teamId] += $duration;
        }

        if (empty($teamWorkloads)) return 0;

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

        // Constraint violation penalties
        foreach ($teamWorkloads as $workload) {
            if ($workload > $this->config['max_work_hours']) {
                $fitness *= 0.5;
            }
        }

        // Task completion penalty (power of 4)
        $completionRate = count($schedule) / count($tasks);
        $fitness *= pow($completionRate, 4);

        if ($completionRate < 1.0) {
            $unassignedTasks = count($tasks) - count($schedule);
            $fitness *= (1.0 / (1.0 + $unassignedTasks * 5.0));
        }

        // Team utilization encouragement
        $uniqueTeamIds = [];
        foreach ($teams as $team) {
            $uniqueTeamIds[$team['team_id']] = true;
        }
        $totalAvailableTeams = count($uniqueTeamIds);
        $teamsUsed = count($teamWorkloads);

        if ($teamsUsed < $totalAvailableTeams) {
            $teamUtilizationRate = $teamsUsed / $totalAvailableTeams;
            $fitness *= pow($teamUtilizationRate, 2);
        }

        return $fitness;
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

        $assignment = $mutated[$mutationPoint];
        $taskId = $assignment['task_id'];

        $task = null;
        foreach ($tasks as $t) {
            if ($t['id'] == $taskId) {
                $task = $t;
                break;
            }
        }
        if (!$task) return $mutated;

        $clientTeams = array_values(array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']));
        if (!empty($clientTeams)) {
            $newTeam = $clientTeams[array_rand($clientTeams)];
            $mutated[$mutationPoint]['team_id'] = $newTeam['team_id'];
        }

        return $mutated;
    }
}
