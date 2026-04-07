<?php
/**
 * Enhanced Hybrid Genetic Algorithm (Multi-Objective)
 *
 * Builds on the Rule-Based + GA Hybrid with 4 fitness objectives:
 * 1. Balanced Workload — equal task distribution across teams
 * 2. Task Sequencing — arrivals first, order-aware fitness, adjacent swap mutation
 * 3. Makespan Optimization — minimize total completion time using team efficiency
 * 4. Idle Time Reduction — avoid zigzagging, cluster tasks per client per team
 *
 * Key differences from HybridGA:
 * - Chromosome encodes task ORDER within each team (not just assignment)
 * - Adjacent swap mutation for incremental sequence improvements
 * - Multi-objective weighted fitness function
 * - Team efficiency factor based on experience/skills diversity
 */

class EnhancedHybridGA
{
    private $config;
    private $fitnessHistory = [];

    public function __construct($config = [])
    {
        $this->config = array_merge([
            'population_size' => 50,
            'max_generations' => 100,
            'mutation_rate' => 0.15,
            'crossover_rate' => 0.8,
            'elite_percentage' => 0.1,
            'patience' => 15,
            'max_work_hours' => 12 * 60,
            // Multi-objective weights (must sum to 1.0)
            'weight_balance' => 0.25,
            'weight_sequencing' => 0.25,
            'weight_makespan' => 0.25,
            'weight_idle_time' => 0.25,
            // Makespan: base minutes per area unit, upgrade bonus
            'base_duration_per_unit' => 10,
            'area_upgrade_bonus_minutes' => 10,
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

        // Build task map for quick lookups
        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['id']] = $task;
        }

        // Build team efficiency map
        $teamEfficiency = [];
        foreach ($teams as $team) {
            $teamEfficiency[$team['team_id']] = $team['team_efficiency'] ?? 1.0;
        }

        $population = $this->initializePopulation($tasks, $teams, $taskMap);

        $bestFitness = -INF;
        $bestSchedule = null;
        $generationsWithoutImprovement = 0;
        $convergenceGeneration = null;

        for ($generation = 0; $generation < $this->config['max_generations']; $generation++) {
            $fitnessScores = [];
            foreach ($population as $idx => $schedule) {
                $fitnessScores[$idx] = $this->calculateMultiObjectiveFitness(
                    $schedule, $tasks, $teams, $taskMap, $teamEfficiency
                );
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

            // Elitism
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
                    $offspring = $this->orderAwareCrossover($parent1, $parent2, $taskMap);
                } else {
                    $offspring = $parent1;
                }

                if (mt_rand() / mt_getrandmax() < $this->config['mutation_rate']) {
                    // 50% chance of adjacent swap mutation (sequencing), 50% team reassignment
                    if (mt_rand(0, 1) === 0) {
                        $offspring = $this->adjacentSwapMutation($offspring, $taskMap);
                    } else {
                        $offspring = $this->teamReassignMutation($offspring, $tasks, $teams);
                    }
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

    // ─── Population Initialization ───

    private function initializePopulation($tasks, $teams, $taskMap)
    {
        $population = [];

        // Greedy seed: arrivals first, then by duration (longest first), balanced across teams
        $population[] = $this->generateSequenceAwareGreedySchedule($tasks, $teams, $taskMap);

        // Fill rest with random but sequence-aware solutions
        for ($i = 1; $i < $this->config['population_size']; $i++) {
            $population[] = $this->generateRandomSequenceSchedule($tasks, $teams, $taskMap);
        }

        return $population;
    }

    private function generateSequenceAwareGreedySchedule($tasks, $teams, $taskMap)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);
        $teamTaskLists = array_fill_keys(array_column($teams, 'team_id'), []);
        $assignedTaskIds = [];

        // Sort: arrivals first, then by duration descending
        $sortedTasks = $tasks;
        usort($sortedTasks, function ($a, $b) {
            $arrivalA = $a['arrival_status'] ?? 0;
            $arrivalB = $b['arrival_status'] ?? 0;
            if ($arrivalB !== $arrivalA) return $arrivalB <=> $arrivalA;
            $durationA = $a['duration'] + ($a['travel_time'] ?? 0);
            $durationB = $b['duration'] + ($b['travel_time'] ?? 0);
            return $durationB <=> $durationA;
        });

        // Assign tasks: least-loaded team for the client, preserving arrival order
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
                    $order = count($teamTaskLists[$minTeam['team_id']]);
                    $schedule[] = [
                        'task_id' => $task['id'],
                        'team_id' => $minTeam['team_id'],
                        'client_id' => $task['client_id'],
                        'order' => $order,
                    ];
                    $teamWorkloads[$minTeam['team_id']] += $taskDuration;
                    $teamTaskLists[$minTeam['team_id']][] = $task['id'];
                    $assignedTaskIds[$task['id']] = true;
                }
            }
        }

        // Second pass: bin-packing for remaining
        $unassignedTasks = array_filter($tasks, fn($task) => !isset($assignedTaskIds[$task['id']]));
        foreach ($unassignedTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;
            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);
            foreach ($clientTeams as $team) {
                $teamId = $team['team_id'];
                if ($this->config['max_work_hours'] - $teamWorkloads[$teamId] >= $taskDuration) {
                    $order = count($teamTaskLists[$teamId]);
                    $schedule[] = [
                        'task_id' => $task['id'],
                        'team_id' => $teamId,
                        'client_id' => $task['client_id'],
                        'order' => $order,
                    ];
                    $teamWorkloads[$teamId] += $taskDuration;
                    $teamTaskLists[$teamId][] = $task['id'];
                    $assignedTaskIds[$task['id']] = true;
                    break;
                }
            }
        }

        return $schedule;
    }

    private function generateRandomSequenceSchedule($tasks, $teams, $taskMap)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);
        $teamTaskCounts = array_fill_keys(array_column($teams, 'team_id'), 0);
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
                    'order' => $teamTaskCounts[$randomTeam['team_id']],
                ];
                $teamWorkloads[$randomTeam['team_id']] += $taskDuration;
                $teamTaskCounts[$randomTeam['team_id']]++;
                $assignedTaskIds[$task['id']] = true;
            }
        }

        // Second pass
        $unassigned = array_filter($tasks, fn($t) => !isset($assignedTaskIds[$t['id']]));
        foreach ($unassigned as $task) {
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
                        'order' => $teamTaskCounts[$teamId],
                    ];
                    $teamWorkloads[$teamId] += $taskDuration;
                    $teamTaskCounts[$teamId]++;
                    $assignedTaskIds[$task['id']] = true;
                    break;
                }
            }
        }

        return $schedule;
    }

    // ─── Multi-Objective Fitness Function ───

    private function calculateMultiObjectiveFitness($schedule, $tasks, $teams, $taskMap, $teamEfficiency)
    {
        if (empty($schedule)) return 0;

        $w = $this->config;

        // Group assignments by team
        $teamAssignments = [];
        foreach ($schedule as $assignment) {
            $teamId = $assignment['team_id'];
            if (!isset($teamAssignments[$teamId])) {
                $teamAssignments[$teamId] = [];
            }
            $teamAssignments[$teamId][] = $assignment;
        }

        // Sort each team's tasks by order
        foreach ($teamAssignments as &$assignments) {
            usort($assignments, function ($a, $b) {
                return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
            });
        }
        unset($assignments);

        // ─── Objective 1: Balanced Workload ───
        $balanceFitness = $this->calculateBalanceFitness($teamAssignments, $taskMap, $teams);

        // ─── Objective 2: Task Sequencing (arrivals first) ───
        $sequencingFitness = $this->calculateSequencingFitness($teamAssignments, $taskMap);

        // ─── Objective 3: Makespan Optimization ───
        $makespanFitness = $this->calculateMakespanFitness($teamAssignments, $taskMap, $teamEfficiency);

        // ─── Objective 4: Idle Time / Zigzag Reduction ───
        $idleFitness = $this->calculateIdleTimeFitness($teamAssignments, $taskMap);

        // Weighted sum
        $fitness = ($w['weight_balance'] * $balanceFitness)
                 + ($w['weight_sequencing'] * $sequencingFitness)
                 + ($w['weight_makespan'] * $makespanFitness)
                 + ($w['weight_idle_time'] * $idleFitness);

        // ─── Global Penalties ───

        // Constraint violation: max work hours
        $teamWorkloads = $this->getTeamWorkloads($teamAssignments, $taskMap);
        foreach ($teamWorkloads as $workload) {
            if ($workload > $this->config['max_work_hours']) {
                $fitness *= 0.5;
            }
        }

        // Task completion penalty (power of 4)
        $completionRate = count($schedule) / count($tasks);
        $fitness *= pow($completionRate, 4);

        if ($completionRate < 1.0) {
            $unassigned = count($tasks) - count($schedule);
            $fitness *= (1.0 / (1.0 + $unassigned * 5.0));
        }

        // Team utilization encouragement
        $uniqueTeamIds = [];
        foreach ($teams as $team) {
            $uniqueTeamIds[$team['team_id']] = true;
        }
        $teamsUsed = count($teamAssignments);
        $totalAvailable = count($uniqueTeamIds);
        if ($teamsUsed < $totalAvailable) {
            $fitness *= pow($teamsUsed / $totalAvailable, 2);
        }

        return $fitness;
    }

    private function getTeamWorkloads($teamAssignments, $taskMap)
    {
        $workloads = [];
        foreach ($teamAssignments as $teamId => $assignments) {
            $total = 0;
            foreach ($assignments as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if ($task) {
                    $total += $task['duration'] + ($task['travel_time'] ?? 0);
                }
            }
            $workloads[$teamId] = $total;
        }
        return $workloads;
    }

    /**
     * Objective 1: Workload Balance
     * Same approach as original — coefficient of variation
     */
    private function calculateBalanceFitness($teamAssignments, $taskMap, $teams)
    {
        $workloads = $this->getTeamWorkloads($teamAssignments, $taskMap);
        if (empty($workloads)) return 0;

        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        foreach ($workloads as $wl) {
            $variance += pow($wl - $mean, 2);
        }
        $stdDev = sqrt($variance / count($workloads));
        $cv = $mean > 0 ? $stdDev / $mean : 0;

        return 1 / (1 + $cv);
    }

    /**
     * Objective 2: Task Sequencing — arrivals should come first in each team's queue
     * Score: for each team, count how many arrival tasks appear before non-arrival tasks.
     * Perfect = all arrivals grouped at the start.
     */
    private function calculateSequencingFitness($teamAssignments, $taskMap)
    {
        $totalScore = 0;
        $totalTeamsWithTasks = 0;

        foreach ($teamAssignments as $teamId => $assignments) {
            if (empty($assignments)) continue;
            $totalTeamsWithTasks++;

            $taskIds = array_map(fn($a) => $a['task_id'], $assignments);
            $arrivalFlags = [];
            foreach ($taskIds as $tid) {
                $task = $taskMap[$tid] ?? null;
                $arrivalFlags[] = ($task && ($task['arrival_status'] ?? 0) == 1) ? 1 : 0;
            }

            $totalArrivals = array_sum($arrivalFlags);
            if ($totalArrivals === 0 || $totalArrivals === count($arrivalFlags)) {
                // No sequencing conflict possible
                $totalScore += 1.0;
                continue;
            }

            // Count how many arrivals are in the first N positions (where N = total arrivals)
            $correctlyPlaced = 0;
            for ($i = 0; $i < $totalArrivals; $i++) {
                if (isset($arrivalFlags[$i]) && $arrivalFlags[$i] === 1) {
                    $correctlyPlaced++;
                }
            }

            $totalScore += $correctlyPlaced / $totalArrivals;
        }

        return $totalTeamsWithTasks > 0 ? $totalScore / $totalTeamsWithTasks : 0;
    }

    /**
     * Objective 3: Makespan — minimize the maximum team completion time
     * Adjusted by team efficiency: effective_duration = duration / efficiency
     * Lower makespan = higher fitness
     */
    private function calculateMakespanFitness($teamAssignments, $taskMap, $teamEfficiency)
    {
        if (empty($teamAssignments)) return 0;

        $teamCompletionTimes = [];
        foreach ($teamAssignments as $teamId => $assignments) {
            $eff = $teamEfficiency[$teamId] ?? 1.0;
            $totalTime = 0;
            foreach ($assignments as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if ($task) {
                    $baseDuration = $task['duration'] + ($task['travel_time'] ?? 0);
                    // Efficiency adjusts duration: higher efficiency = faster
                    $effectiveDuration = $baseDuration / max(0.1, $eff);
                    $totalTime += $effectiveDuration;
                }
            }
            $teamCompletionTimes[$teamId] = $totalTime;
        }

        if (empty($teamCompletionTimes)) return 0;

        $makespan = max($teamCompletionTimes);
        $maxPossible = $this->config['max_work_hours'];

        // Normalize: 1.0 when makespan is 0, approaches 0 as makespan grows
        return 1 / (1 + ($makespan / max(1, $maxPossible)));
    }

    /**
     * Objective 4: Idle Time / Zigzag Reduction
     * Penalize teams that switch between different clients.
     * Ideal: each team works on ONE client (travels once, stays until done).
     * Score: proportion of teams with single-client assignment.
     */
    private function calculateIdleTimeFitness($teamAssignments, $taskMap)
    {
        if (empty($teamAssignments)) return 0;

        $totalTeams = 0;
        $totalScore = 0;

        foreach ($teamAssignments as $teamId => $assignments) {
            if (empty($assignments)) continue;
            $totalTeams++;

            // Count unique clients this team serves
            $clientIds = [];
            foreach ($assignments as $a) {
                $clientId = $a['client_id'] ?? null;
                if ($clientId && !in_array($clientId, $clientIds)) {
                    $clientIds[] = $clientId;
                }
            }

            $uniqueClients = count($clientIds);
            if ($uniqueClients <= 1) {
                $totalScore += 1.0; // Perfect: no zigzagging
            } else {
                // Count client switches in the sequence (consecutive different clients)
                $switches = 0;
                $prevClient = null;
                foreach ($assignments as $a) {
                    $cid = $a['client_id'] ?? null;
                    if ($prevClient !== null && $cid !== $prevClient) {
                        $switches++;
                    }
                    $prevClient = $cid;
                }

                // Penalize based on number of switches
                $maxSwitches = count($assignments) - 1;
                $switchRate = $maxSwitches > 0 ? $switches / $maxSwitches : 0;
                $totalScore += 1 / (1 + $switchRate * 2);
            }
        }

        return $totalTeams > 0 ? $totalScore / $totalTeams : 0;
    }

    // ─── Genetic Operators ───

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

    /**
     * Order-aware crossover: preserves task ordering within teams.
     * Takes team assignments from parent1 for first half of teams,
     * fills remaining from parent2, avoiding duplicates.
     */
    private function orderAwareCrossover($parent1, $parent2, $taskMap)
    {
        if (empty($parent1) || empty($parent2)) return $parent1;

        // Group by team for both parents
        $teams1 = [];
        foreach ($parent1 as $a) {
            $teams1[$a['team_id']][] = $a;
        }
        $teams2 = [];
        foreach ($parent2 as $a) {
            $teams2[$a['team_id']][] = $a;
        }

        $allTeamIds = array_unique(array_merge(array_keys($teams1), array_keys($teams2)));
        $teamIdList = array_values($allTeamIds);
        $crossoverPoint = (int)(count($teamIdList) / 2);

        $offspring = [];
        $usedTasks = [];

        // Take first half of teams from parent1
        for ($i = 0; $i < $crossoverPoint; $i++) {
            $tid = $teamIdList[$i];
            if (isset($teams1[$tid])) {
                foreach ($teams1[$tid] as $a) {
                    if (!isset($usedTasks[$a['task_id']])) {
                        $offspring[] = $a;
                        $usedTasks[$a['task_id']] = true;
                    }
                }
            }
        }

        // Take second half from parent2
        for ($i = $crossoverPoint; $i < count($teamIdList); $i++) {
            $tid = $teamIdList[$i];
            if (isset($teams2[$tid])) {
                foreach ($teams2[$tid] as $a) {
                    if (!isset($usedTasks[$a['task_id']])) {
                        $offspring[] = $a;
                        $usedTasks[$a['task_id']] = true;
                    }
                }
            }
        }

        return $offspring;
    }

    /**
     * Adjacent Swap Mutation: swaps two adjacent tasks within a team's sequence.
     * This makes small, incremental improvements to task ordering.
     * If the swap puts arrivals earlier, it's a beneficial mutation.
     */
    private function adjacentSwapMutation($schedule, $taskMap)
    {
        if (count($schedule) < 2) return $schedule;

        $mutated = $schedule;

        // Group by team
        $teamIndices = [];
        foreach ($mutated as $idx => $a) {
            $teamIndices[$a['team_id']][] = $idx;
        }

        // Pick a random team with at least 2 tasks
        $eligibleTeams = array_filter($teamIndices, fn($indices) => count($indices) >= 2);
        if (empty($eligibleTeams)) return $mutated;

        $teamId = array_rand($eligibleTeams);
        $indices = $eligibleTeams[$teamId];

        // Sort indices by order
        usort($indices, function ($a, $b) use ($mutated) {
            return ($mutated[$a]['order'] ?? 0) <=> ($mutated[$b]['order'] ?? 0);
        });

        // Pick a random adjacent pair and swap their orders
        $pairIdx = mt_rand(0, count($indices) - 2);
        $idx1 = $indices[$pairIdx];
        $idx2 = $indices[$pairIdx + 1];

        // Swap order values
        $tempOrder = $mutated[$idx1]['order'] ?? 0;
        $mutated[$idx1]['order'] = $mutated[$idx2]['order'] ?? 0;
        $mutated[$idx2]['order'] = $tempOrder;

        return $mutated;
    }

    /**
     * Team Reassignment Mutation: moves a task to a different team (same as original HybridGA mutate).
     */
    private function teamReassignMutation($schedule, $tasks, $teams)
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