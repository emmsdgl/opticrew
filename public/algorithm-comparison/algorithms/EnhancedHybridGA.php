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
            // Workload balance now has the largest weight to inherit HybridGA's strength
            'weight_balance' => 0.40,
            'weight_sequencing' => 0.20,
            'weight_makespan' => 0.20,
            'weight_idle_time' => 0.20,
            // Makespan: base minutes per area unit, upgrade bonus
            'base_duration_per_unit' => 10,
            'area_upgrade_bonus_minutes' => 10,
            // Time slot allocation: default workday start (8:00 AM = 480 minutes)
            'service_start_minutes' => 480,
            // Subtask simulation (per-task checklist for performance tracking)
            'subtasks_per_task' => 4,
            // Employee efficiency formula
            'efficiency_min' => 0.5,
            'efficiency_max' => 1.0,
            'efficiency_blend_weight' => 0.3, // new ratio influence (old weight = 0.7)
        ], $config);
    }

    public function optimize($tasks, $employeeAllocations, $teams)
    {
        $this->fitnessHistory = [];

        // Start the "solution time" timer — measures only the GA work,
        // NOT the extended report (timetable, makespan comparison, subtasks, etc.)
        $solutionStartTime = microtime(true);

        if (empty($tasks) || empty($teams)) {
            return [
                'best_schedule' => [],
                'best_fitness' => 0,
                'generations' => 0,
                'convergence_generation' => null,
                'fitness_history' => [],
                'solution_time_ms' => (microtime(true) - $solutionStartTime) * 1000,
            ];
        }

        // Enforce the one-driver-per-team policy: drop any team that came in
        // without at least one licensed driver among its members.
        $teams = array_values(array_filter($teams, function ($team) {
            $members = $team['members'] ?? [];
            foreach ($members as $m) {
                if (!empty($m['has_driving_license']) && (int) $m['has_driving_license'] === 1) {
                    return true;
                }
            }
            return false;
        }));

        if (empty($teams)) {
            return [
                'best_schedule' => [],
                'best_fitness' => 0,
                'generations' => 0,
                'convergence_generation' => null,
                'fitness_history' => [],
                'extended_report' => ['error' => 'No teams with at least one driver — driver-per-team policy could not be satisfied.'],
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

            // Capture the entire population's fitness for this generation
            $sortedScores = $fitnessScores;
            rsort($sortedScores); // descending: best first
            $this->fitnessHistory[] = [
                'generation' => $generation,
                'best' => $currentBest,
                'average' => array_sum($fitnessScores) / count($fitnessScores),
                'worst' => min($fitnessScores),
                'population_size' => count($fitnessScores),
                'individual_fitnesses' => array_map(fn($f) => round($f, 6), $sortedScores),
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
                    // 3 mutation types — pick one at random
                    $mt = mt_rand(0, 2);
                    if ($mt === 0) {
                        // Adjacent swap (sequencing improvement)
                        $offspring = $this->adjacentSwapMutation($offspring, $taskMap);
                    } elseif ($mt === 1) {
                        // Random team reassignment (exploration)
                        $offspring = $this->teamReassignMutation($offspring, $tasks, $teams);
                    } else {
                        // Balance-aware reassignment (HybridGA-style):
                        // move a task from the heaviest team to the lightest team
                        $offspring = $this->balanceMutation($offspring, $tasks, $teams, $taskMap);
                    }
                }

                $newPopulation[] = $offspring;
            }

            $population = array_slice($newPopulation, 0, $this->config['population_size']);
        }

        // ─── GA WORK COMPLETE — capture the "solution time" here ───
        // This excludes everything below (timetable, makespan comparison, subtasks,
        // employee performance, per-client breakdown), which are reporting overhead.
        $solutionTimeMs = (microtime(true) - $solutionStartTime) * 1000;

        // ─── Build extended report data for the comparison page ───
        $report = [];
        if ($bestSchedule) {
            // 1. Time slot allocation (timetable per task per team)
            $timetable = $this->buildTimetable($bestSchedule, $taskMap, $teamEfficiency, $teams);

            // 2. Makespan of the optimized sequence
            $makespan = $this->calculateMakespan($timetable);
            $makespanScore = $this->calculateMakespanScoreFromMinutes($makespan);

            // 3. Comparison sequence with deliberately longer makespan
            //    (reverse order + worst-fit team) so the GA's improvement is visible
            $longSchedule = $this->generateLongMakespanSchedule($bestSchedule, $tasks, $teams, $taskMap);
            $longTimetable = $this->buildTimetable($longSchedule, $taskMap, $teamEfficiency, $teams);
            $longMakespan = $this->calculateMakespan($longTimetable);
            $longMakespanScore = $this->calculateMakespanScoreFromMinutes($longMakespan);

            // 4. Subtask simulation + per-employee performance
            $subtaskSimulation = $this->simulateSubtaskCompletions($timetable, $teams, $taskMap);
            $employeePerformance = $this->calculateEmployeePerformance($subtaskSimulation, $teams);

            // 5. Per-client breakdown: timetable grouped by client + makespan per client
            $clientBreakdown = $this->buildClientBreakdown($timetable, $teams);

            $report = [
                'timetable' => $timetable,
                'makespan_minutes' => $makespan,
                'makespan_score' => $makespanScore,
                'comparison_long_makespan' => [
                    'timetable' => $longTimetable,
                    'makespan_minutes' => $longMakespan,
                    'makespan_score' => $longMakespanScore,
                    'improvement_minutes' => $longMakespan - $makespan,
                    'improvement_percent' => $longMakespan > 0 ? round((($longMakespan - $makespan) / $longMakespan) * 100, 2) : 0,
                ],
                'client_breakdown' => $clientBreakdown,
                'subtask_simulation' => $subtaskSimulation,
                'employee_performance' => $employeePerformance,
                'service_start_minutes' => $this->config['service_start_minutes'],
                // Per-generation history (best/avg/worst + every individual's fitness)
                'fitness_per_generation' => $this->fitnessHistory,
                'population_size' => (int) $this->config['population_size'],
                'total_generations_run' => $generation + 1,
                'convergence_generation' => $convergenceGeneration,
            ];
        }

        return [
            'best_schedule' => $bestSchedule ?? [],
            'best_fitness' => max(0, $bestFitness),
            'generations' => $generation + 1,
            'convergence_generation' => $convergenceGeneration,
            'fitness_history' => $this->fitnessHistory,
            'extended_report' => $report,
            // GA-only solve time (excludes extended_report building)
            'solution_time_ms' => $solutionTimeMs,
        ];
    }

    // ─── Population Initialization ───

    private function initializePopulation($tasks, $teams, $taskMap)
    {
        $population = [];
        $popSize = $this->config['population_size'];

        // 1) The pure HybridGA-style greedy seed (longest task → least loaded team)
        $greedySeed = $this->generateSequenceAwareGreedySchedule($tasks, $teams, $taskMap);
        $population[] = $greedySeed;

        // 2) Half the population = balanced seeds (variations of the greedy schedule)
        //    These start from HybridGA's strong balance baseline and then mutate slightly
        //    so the GA has a diverse but well-balanced starting set.
        $balancedCount = (int) ($popSize * 0.5);
        for ($i = 1; $i < $balancedCount; $i++) {
            $variant = $this->shuffleSchedulesPreservingBalance($greedySeed);
            $population[] = $variant;
        }

        // 3) Remaining half = random sequence-aware schedules for diversity
        while (count($population) < $popSize) {
            $population[] = $this->generateRandomSequenceSchedule($tasks, $teams, $taskMap);
        }

        return $population;
    }

    /**
     * Build a slight variation of a balanced schedule by randomly reordering
     * tasks within a few teams. The team assignment (and therefore the workload
     * balance) is preserved — only the sequence inside each team changes.
     */
    private function shuffleSchedulesPreservingBalance($schedule)
    {
        if (empty($schedule)) return $schedule;

        $variant = $schedule;
        $byTeam = [];
        foreach ($variant as $idx => $a) {
            $byTeam[$a['team_id']][] = $idx;
        }

        // Pick 1-3 random teams and shuffle their internal task order
        $teamIds = array_keys($byTeam);
        if (empty($teamIds)) return $variant;
        $numShuffles = min(count($teamIds), mt_rand(1, 3));

        for ($s = 0; $s < $numShuffles; $s++) {
            $teamId = $teamIds[array_rand($teamIds)];
            $indices = $byTeam[$teamId];
            if (count($indices) < 2) continue;

            // Reassign random orders within this team
            $newOrders = range(0, count($indices) - 1);
            shuffle($newOrders);
            foreach ($indices as $i => $idx) {
                $variant[$idx]['order'] = $newOrders[$i];
            }
        }

        return $variant;
    }

    /**
     * Greedy seed combining HybridGA's balance strategy with sequencing awareness:
     *  - When arrivals exist: sort arrivals first, then largest-duration first
     *  - When no arrivals: pure HybridGA-style largest-first (no arrival sort)
     *  - For each task, find the LEAST-LOADED team that can serve its client
     *    AND won't exceed the 12-hour limit
     *  - Second pass (bin-packing): try to fit any unassigned tasks into any team with capacity
     */
    private function generateSequenceAwareGreedySchedule($tasks, $teams, $taskMap)
    {
        $schedule = [];
        $teamWorkloads = array_fill_keys(array_column($teams, 'team_id'), 0);
        $teamTaskLists = array_fill_keys(array_column($teams, 'team_id'), []);
        $assignedTaskIds = [];

        // Detect whether the dataset has any arrivals to optimize for
        $hasArrivals = false;
        foreach ($tasks as $t) {
            if (!empty($t['arrival_status']) && (int) $t['arrival_status'] === 1) {
                $hasArrivals = true;
                break;
            }
        }

        // Sort: if arrivals exist, prioritize them; otherwise pure largest-first (HybridGA mode)
        $sortedTasks = $tasks;
        usort($sortedTasks, function ($a, $b) use ($hasArrivals) {
            if ($hasArrivals) {
                $arrivalA = $a['arrival_status'] ?? 0;
                $arrivalB = $b['arrival_status'] ?? 0;
                if ($arrivalB !== $arrivalA) return $arrivalB <=> $arrivalA;
            }
            $durationA = $a['duration'] + ($a['travel_time'] ?? 0);
            $durationB = $b['duration'] + ($b['travel_time'] ?? 0);
            return $durationB <=> $durationA;
        });

        // First pass: HybridGA-style "largest task → least-loaded team that fits"
        foreach ($sortedTasks as $task) {
            if (isset($assignedTaskIds[$task['id']])) continue;

            $clientTeams = array_filter($teams, fn($team) => $team['client_id'] == $task['client_id']);
            if (empty($clientTeams)) continue;

            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            // Find the least-loaded team that can still fit this task
            $minTeam = null;
            $minWorkload = INF;
            foreach ($clientTeams as $team) {
                $teamId = $team['team_id'];
                if ($teamWorkloads[$teamId] + $taskDuration > $this->config['max_work_hours']) {
                    continue; // Hard 12-hour limit — skip this team
                }
                if ($teamWorkloads[$teamId] < $minWorkload) {
                    $minWorkload = $teamWorkloads[$teamId];
                    $minTeam = $team;
                }
            }

            if ($minTeam) {
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

        // Second pass: bin-packing for unassigned (sort SHORTEST first to fit gaps)
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

        // ─── Multi-objective combiner (weighted GEOMETRIC mean) ───
        // This is multiplicative like HybridGA, so the score CAN reach 1.0 when
        // every objective is at its maximum. Each objective is raised to its weight.
        //   fitness = balance^w1 * sequencing^w2 * makespan^w3 * idle^w4
        // (Weights still sum to 1.0 so the result stays in [0, 1].)
        $balanceFitness = max(0.0001, $balanceFitness);
        $sequencingFitness = max(0.0001, $sequencingFitness);
        $makespanFitness = max(0.0001, $makespanFitness);
        $idleFitness = max(0.0001, $idleFitness);

        $fitness = pow($balanceFitness, $w['weight_balance'])
                 * pow($sequencingFitness, $w['weight_sequencing'])
                 * pow($makespanFitness, $w['weight_makespan'])
                 * pow($idleFitness, $w['weight_idle_time']);

        // ─── Global Penalties (HybridGA-style) ───

        // Constraint violation: max work hours — multiply by 0.5 for each overworked team
        // (matches HybridGA, severe enough that balance becomes the dominant signal)
        $teamWorkloads = $this->getTeamWorkloads($teamAssignments, $taskMap);
        foreach ($teamWorkloads as $workload) {
            if ($workload > $this->config['max_work_hours']) {
                $fitness *= 0.5;
            }
        }

        // Task completion penalty (power of 4) — heavily penalizes incomplete schedules
        // 100% complete = 1.0, 90% = 0.66, 80% = 0.41, 50% = 0.06
        $completionRate = count($schedule) / count($tasks);
        $fitness *= pow($completionRate, 4);

        // Additional unassigned-task penalty (matches HybridGA exactly)
        if ($completionRate < 1.0) {
            $unassigned = count($tasks) - count($schedule);
            $fitness *= (1.0 / (1.0 + $unassigned * 5.0));
        }

        // (Team utilization is already factored into calculateBalanceFitness now)

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
     * Objective 1: Workload Balance — uses HybridGA's coefficient-of-variation approach
     * for fair, proportional balance across teams.
     *
     * Returns 1.0 when every team has identical workload, decreasing as teams diverge.
     * Also includes the team-utilization bonus from HybridGA: penalize using fewer
     * teams than available so the algorithm spreads the work properly.
     */
    private function calculateBalanceFitness($teamAssignments, $taskMap, $teams)
    {
        $workloads = $this->getTeamWorkloads($teamAssignments, $taskMap);
        if (empty($workloads)) return 0;

        // Coefficient of variation: stdDev / mean
        // This is proportional, so a 50/50 split scores the same regardless of total minutes
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        foreach ($workloads as $wl) {
            $variance += pow($wl - $mean, 2);
        }
        $stdDev = sqrt($variance / count($workloads));
        $cv = $mean > 0 ? $stdDev / $mean : 0;
        $balance = 1 / (1 + $cv);

        // Team utilization: penalize when fewer teams are used than available
        // (matches HybridGA's late penalty but rolled into the balance objective)
        $uniqueTeamIds = [];
        foreach ($teams as $team) {
            $uniqueTeamIds[$team['team_id']] = true;
        }
        $totalAvailable = count($uniqueTeamIds);
        $teamsUsed = count($workloads);
        if ($totalAvailable > 0 && $teamsUsed < $totalAvailable) {
            $balance *= pow($teamsUsed / $totalAvailable, 2);
        }

        return $balance;
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
     *
     * Scoring is RELATIVE to the theoretical minimum makespan:
     *   ideal_makespan = total_work / number_of_teams
     * If the actual makespan equals the ideal, score = 1.0 (perfectly balanced).
     * Score drops linearly as makespan exceeds the ideal.
     */
    private function calculateMakespanFitness($teamAssignments, $taskMap, $teamEfficiency)
    {
        if (empty($teamAssignments)) return 0;

        $teamCompletionTimes = [];
        $totalWork = 0;
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
            $totalWork += $totalTime;
        }

        if (empty($teamCompletionTimes) || $totalWork <= 0) return 1.0;

        $makespan = max($teamCompletionTimes);
        $teamCount = count($teamCompletionTimes);
        $idealMakespan = $totalWork / $teamCount;

        // Score: 1.0 when actual makespan equals the ideal balanced split.
        // Drops as the gap between actual and ideal grows.
        // ratio = idealMakespan / actualMakespan, range (0, 1]
        $ratio = $idealMakespan / max(1, $makespan);
        return max(0.0, min(1.0, $ratio));
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

    /**
     * Balance-aware mutation (HybridGA-style):
     * Identify the heaviest-loaded team and the lightest-loaded team that serve
     * the same client, then move a task from the heavy team to the light team.
     * This actively reduces workload imbalance with each mutation.
     */
    private function balanceMutation($schedule, $tasks, $teams, $taskMap)
    {
        if (empty($schedule)) return $schedule;

        $mutated = $schedule;

        // Compute current per-team workload
        $teamLoads = [];
        foreach ($mutated as $idx => $a) {
            $task = $taskMap[$a['task_id']] ?? null;
            if (!$task) continue;
            $duration = ($task['duration'] ?? 0) + ($task['travel_time'] ?? 0);
            $teamLoads[$a['team_id']] = ($teamLoads[$a['team_id']] ?? 0) + $duration;
        }

        if (count($teamLoads) < 2) return $mutated;

        // Find heaviest and lightest teams
        arsort($teamLoads);
        $heavyTeamId = array_key_first($teamLoads);
        $lightTeamId = array_key_last($teamLoads);
        if ($heavyTeamId === $lightTeamId) return $mutated;
        if (($teamLoads[$heavyTeamId] - $teamLoads[$lightTeamId]) < 30) {
            // Already pretty balanced; nothing meaningful to do
            return $mutated;
        }

        // Pick a task currently on the heavy team
        $heavyIndices = [];
        foreach ($mutated as $idx => $a) {
            if ($a['team_id'] === $heavyTeamId) {
                $heavyIndices[] = $idx;
            }
        }
        if (empty($heavyIndices)) return $mutated;

        $candidateIdx = $heavyIndices[array_rand($heavyIndices)];
        $candidate = $mutated[$candidateIdx];
        $task = $taskMap[$candidate['task_id']] ?? null;
        if (!$task) return $mutated;

        // The light team must serve the same client as the task
        $lightTeamRecord = null;
        foreach ($teams as $t) {
            if ($t['team_id'] === $lightTeamId && $t['client_id'] == $task['client_id']) {
                $lightTeamRecord = $t;
                break;
            }
        }
        if (!$lightTeamRecord) return $mutated; // Can't legally move

        // Make sure adding this task doesn't push the light team over the 12-hour limit
        $taskDuration = ($task['duration'] ?? 0) + ($task['travel_time'] ?? 0);
        if (($teamLoads[$lightTeamId] + $taskDuration) > $this->config['max_work_hours']) {
            return $mutated;
        }

        // Perform the move
        $mutated[$candidateIdx]['team_id'] = $lightTeamId;
        // Append at end of the light team's existing sequence
        $maxOrder = -1;
        foreach ($mutated as $a) {
            if ($a['team_id'] === $lightTeamId) {
                $maxOrder = max($maxOrder, $a['order'] ?? 0);
            }
        }
        $mutated[$candidateIdx]['order'] = $maxOrder + 1;

        return $mutated;
    }

    // ─── Extended Reporting (timetable, makespan, subtasks, performance) ───

    /**
     * Build a timetable: for each team, list its tasks with start_min and end_min
     * derived from a fixed service start time + sequential durations adjusted by team efficiency.
     * This is the "time slot allocation per task".
     */
    private function buildTimetable($schedule, $taskMap, $teamEfficiency, $teams)
    {
        // Group assignments by team
        $teamAssignments = [];
        foreach ($schedule as $assignment) {
            $teamAssignments[$assignment['team_id']][] = $assignment;
        }

        // Sort each team's tasks by chromosome order
        foreach ($teamAssignments as &$assignments) {
            usort($assignments, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        }
        unset($assignments);

        // Build a quick team lookup so we can attach team metadata
        $teamLookup = [];
        foreach ($teams as $team) {
            $teamLookup[$team['team_id']] = $team;
        }

        $serviceStart = $this->config['service_start_minutes'];
        $timetable = [];

        foreach ($teamAssignments as $teamId => $assignments) {
            $eff = $teamEfficiency[$teamId] ?? 1.0;
            $cursor = $serviceStart;
            $rows = [];

            foreach ($assignments as $idx => $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if (!$task) continue;

                $base = (int) ($task['duration'] + ($task['travel_time'] ?? 0));
                // Variable effective duration: base / efficiency
                // (faster team finishes sooner; slower team takes longer)
                $effective = (int) round($base / max(0.1, $eff));

                $startMin = $cursor;
                $endMin = $cursor + $effective;
                $cursor = $endMin;

                $rows[] = [
                    'sequence' => $idx + 1,
                    'task_id' => $a['task_id'],
                    'client_id' => $a['client_id'] ?? null,
                    'arrival_status' => (int) ($task['arrival_status'] ?? 0),
                    'base_duration' => $base,
                    'effective_duration' => $effective,
                    'start_min' => $startMin,
                    'end_min' => $endMin,
                    'start_label' => $this->minutesToClock($startMin),
                    'end_label' => $this->minutesToClock($endMin),
                ];
            }

            $timetable[] = [
                'team_id' => $teamId,
                'efficiency' => round($eff, 4),
                'members' => $teamLookup[$teamId]['members'] ?? [],
                'tasks' => $rows,
                'team_finish_min' => empty($rows) ? $serviceStart : end($rows)['end_min'],
                'team_finish_label' => empty($rows) ? $this->minutesToClock($serviceStart) : end($rows)['end_label'],
            ];
        }

        return $timetable;
    }

    /**
     * Makespan = the latest team_finish_min across all teams.
     */
    private function calculateMakespan($timetable)
    {
        if (empty($timetable)) return 0;
        $finishes = array_map(fn($team) => $team['team_finish_min'] ?? 0, $timetable);
        return max($finishes);
    }

    /**
     * Convert a makespan (in minutes) to a normalized score 0..1 (higher is better).
     */
    private function calculateMakespanScoreFromMinutes($makespanMinutes)
    {
        $serviceStart = $this->config['service_start_minutes'];
        $maxAcceptable = $serviceStart + $this->config['max_work_hours']; // e.g. 8:00 + 12h = 20:00
        if ($makespanMinutes <= $serviceStart) return 1.0;
        if ($makespanMinutes >= $maxAcceptable) return 0.1;
        $usedFraction = ($makespanMinutes - $serviceStart) / ($maxAcceptable - $serviceStart);
        return round(max(0.1, min(1.0, 1.0 - $usedFraction)), 4);
    }

    /**
     * Build a deliberately worse schedule for makespan comparison:
     * - Concentrate tasks on one team while others stay light
     * - Reverse the chromosome order so arrivals land at the end
     * The result is a sequence whose makespan is much higher than the optimized one.
     */
    private function generateLongMakespanSchedule($bestSchedule, $tasks, $teams, $taskMap)
    {
        if (empty($bestSchedule)) return $bestSchedule;

        // Group by team
        $teamGroups = [];
        foreach ($bestSchedule as $a) {
            $teamGroups[$a['team_id']][] = $a;
        }

        if (count($teamGroups) <= 1) {
            // Single team: just reverse the order so arrivals are last
            $reordered = $bestSchedule;
            $orderMax = count($reordered) - 1;
            foreach ($reordered as $idx => &$a) {
                $a['order'] = $orderMax - $idx;
            }
            unset($a);
            return $reordered;
        }

        // Multi team: pick the team with the most tasks and pile everything onto it.
        // This deliberately wrecks balance and produces a long makespan.
        $sortedTeams = $teamGroups;
        uksort($sortedTeams, function ($a, $b) use ($teamGroups) {
            return count($teamGroups[$b]) <=> count($teamGroups[$a]);
        });
        $heavyTeamId = array_key_first($sortedTeams);

        // Move every task that legally can move (same client_id) to the heavy team
        $bad = [];
        $orderCounter = 0;
        // First add the original heavy team's tasks in REVERSE order
        $heavyTasks = $teamGroups[$heavyTeamId];
        $heavyTasks = array_reverse($heavyTasks);
        foreach ($heavyTasks as $a) {
            $a['order'] = $orderCounter++;
            $bad[] = $a;
        }
        // Then try to merge other teams' tasks onto the heavy team if same client
        foreach ($teamGroups as $teamId => $assignments) {
            if ($teamId === $heavyTeamId) continue;
            foreach ($assignments as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if (!$task) continue;
                // The heavy team must serve this client; if not, leave it where it was
                $heavyTeamRecord = null;
                foreach ($teams as $t) {
                    if ($t['team_id'] === $heavyTeamId) { $heavyTeamRecord = $t; break; }
                }
                if ($heavyTeamRecord && $heavyTeamRecord['client_id'] == $task['client_id']) {
                    $a['team_id'] = $heavyTeamId;
                    $a['order'] = $orderCounter++;
                    $bad[] = $a;
                } else {
                    // Keep on its original team but at the end of the queue
                    $a['order'] = 9999;
                    $bad[] = $a;
                }
            }
        }

        return $bad;
    }

    /**
     * For each task in the timetable, generate up to N subtasks (max 4) and randomly
     * assign each subtask to a team member. Each subtask gets a simulated start/end
     * timestamp based on equal slicing of the task's effective duration.
     */
    private function simulateSubtaskCompletions($timetable, $teams, $taskMap)
    {
        $maxSubtasks = (int) $this->config['subtasks_per_task'];
        $simulation = [];

        $teamLookup = [];
        foreach ($teams as $t) {
            $teamLookup[$t['team_id']] = $t;
        }

        foreach ($timetable as $teamRow) {
            $teamId = $teamRow['team_id'];
            $members = $teamRow['members'] ?? [];
            if (empty($members)) continue;

            foreach ($teamRow['tasks'] as $taskRow) {
                $task = $taskMap[$taskRow['task_id']] ?? null;
                if (!$task) continue;

                // Number of subtasks for this task: between 2 and max (deterministic per task id seed)
                mt_srand((int) $taskRow['task_id'] * 7919); // stable per task
                $count = mt_rand(2, max(2, $maxSubtasks));
                $sliceMinutes = max(1, (int) floor($taskRow['effective_duration'] / $count));

                $subtasks = [];
                for ($i = 0; $i < $count; $i++) {
                    $member = $members[array_rand($members)];
                    $startMin = $taskRow['start_min'] + ($i * $sliceMinutes);
                    // Add small random jitter so the actual time isn't exactly the slice
                    $jitter = mt_rand(-2, 4); // can finish a bit early or late
                    $endMin = min($taskRow['end_min'] + 5, $startMin + $sliceMinutes + $jitter);

                    $subtasks[] = [
                        'subtask_id' => $taskRow['task_id'] . '-' . ($i + 1),
                        'name' => 'Subtask ' . ($i + 1),
                        'completed_by' => $member['id'] ?? null,
                        'completed_by_name' => $this->memberDisplayName($member),
                        'start_min' => $startMin,
                        'end_min' => $endMin,
                        'start_label' => $this->minutesToClock($startMin),
                        'end_label' => $this->minutesToClock($endMin),
                    ];
                }
                mt_srand(); // reset RNG to wall-clock seed for the rest of the run

                $simulation[] = [
                    'task_id' => $taskRow['task_id'],
                    'team_id' => $teamId,
                    'task_start_min' => $taskRow['start_min'],
                    'task_end_min' => $taskRow['end_min'],
                    'task_start_label' => $taskRow['start_label'],
                    'task_end_label' => $taskRow['end_label'],
                    'estimated_duration' => $taskRow['effective_duration'],
                    'subtasks' => $subtasks,
                ];
            }
        }

        return $simulation;
    }

    /**
     * Calculate per-employee performance using the formulas:
     *   contribution_ratio = subtasks_done_by_employee / total_subtasks_in_task
     *   expected_time      = task_duration * contribution_ratio
     *   actual_time        = (last subtask end) - (first subtask start) for that employee
     *   performance_ratio  = expected / actual  (capped at efficiency_max)
     *   new_efficiency     = (current * 0.7) + (performance_ratio * 0.3)
     * Returns a per-employee summary across all tasks they touched.
     */
    private function calculateEmployeePerformance($subtaskSimulation, $teams)
    {
        $employees = [];

        // Seed every team member at default efficiency 1.0
        foreach ($teams as $team) {
            foreach (($team['members'] ?? []) as $member) {
                $eid = $member['id'] ?? null;
                if (!$eid) continue;
                if (!isset($employees[$eid])) {
                    $employees[$eid] = [
                        'employee_id' => $eid,
                        'name' => $this->memberDisplayName($member),
                        'team_id' => $team['team_id'],
                        'starting_efficiency' => 1.0,
                        'current_efficiency' => 1.0,
                        'tasks_touched' => 0,
                        'task_breakdowns' => [],
                    ];
                }
            }
        }

        $blendNew = (float) $this->config['efficiency_blend_weight']; // 0.3
        $blendOld = 1.0 - $blendNew;                                   // 0.7
        $minEff = (float) $this->config['efficiency_min'];
        $maxEff = (float) $this->config['efficiency_max'];

        foreach ($subtaskSimulation as $taskSim) {
            $totalSubtasks = count($taskSim['subtasks']);
            if ($totalSubtasks === 0) continue;

            // Group subtasks by employee
            $byEmployee = [];
            foreach ($taskSim['subtasks'] as $st) {
                $eid = $st['completed_by'];
                if (!$eid) continue;
                $byEmployee[$eid][] = $st;
            }

            foreach ($byEmployee as $eid => $stList) {
                if (!isset($employees[$eid])) continue;

                $contributionRatio = round(count($stList) / $totalSubtasks, 4);
                $expectedTime = round($taskSim['estimated_duration'] * $contributionRatio, 2);

                $starts = array_column($stList, 'start_min');
                $ends = array_column($stList, 'end_min');
                $actualTime = max(1, max($ends) - min($starts));

                $rawRatio = $expectedTime / $actualTime;
                // Cap at max efficiency (no faster-than-estimate bonus)
                $performanceRatio = round(min($maxEff, $rawRatio), 4);

                $oldEff = $employees[$eid]['current_efficiency'];
                $newEff = ($oldEff * $blendOld) + ($performanceRatio * $blendNew);
                $newEff = round(max($minEff, min($maxEff, $newEff)), 4);

                $employees[$eid]['task_breakdowns'][] = [
                    'task_id' => $taskSim['task_id'],
                    'subtasks_done' => count($stList),
                    'total_subtasks' => $totalSubtasks,
                    'contribution_ratio' => $contributionRatio,
                    'expected_time' => $expectedTime,
                    'actual_time' => $actualTime,
                    'performance_ratio' => $performanceRatio,
                    'efficiency_before' => $oldEff,
                    'efficiency_after' => $newEff,
                ];

                $employees[$eid]['current_efficiency'] = $newEff;
                $employees[$eid]['tasks_touched']++;
            }
        }

        // Final pass: round numeric fields for display, drop employees with 0 tasks
        $result = [];
        foreach ($employees as $emp) {
            if ($emp['tasks_touched'] === 0) continue;
            $emp['current_efficiency'] = round($emp['current_efficiency'], 4);
            $result[] = $emp;
        }

        return $result;
    }

    /**
     * Group the timetable by client_id and compute a per-client makespan.
     * Returns an array of clients, each with their teams and makespan stats.
     */
    private function buildClientBreakdown($timetable, $teams)
    {
        // team_id -> client_id lookup (each team belongs to exactly one client)
        $teamToClient = [];
        $clientNames = [];
        foreach ($teams as $t) {
            $teamToClient[$t['team_id']] = $t['client_id'];
            // best-effort client display name
            if (!isset($clientNames[$t['client_id']])) {
                $clientNames[$t['client_id']] = $t['client_name'] ?? ('Client #' . $t['client_id']);
            }
        }

        // Group timetable rows under their client
        $byClient = [];
        foreach ($timetable as $teamRow) {
            $clientId = $teamToClient[$teamRow['team_id']] ?? 'unknown';
            if (!isset($byClient[$clientId])) {
                $byClient[$clientId] = [
                    'client_id' => $clientId,
                    'client_name' => $clientNames[$clientId] ?? ('Client #' . $clientId),
                    'teams' => [],
                    'team_count' => 0,
                    'task_count' => 0,
                ];
            }
            $byClient[$clientId]['teams'][] = $teamRow;
            $byClient[$clientId]['team_count']++;
            $byClient[$clientId]['task_count'] += count($teamRow['tasks'] ?? []);
        }

        // Compute per-client makespan (max team finish time within that client) + score
        $result = [];
        foreach ($byClient as $entry) {
            $finishes = array_map(fn($team) => $team['team_finish_min'] ?? 0, $entry['teams']);
            $clientMakespan = !empty($finishes) ? max($finishes) : 0;
            $entry['makespan_minutes'] = $clientMakespan;
            $entry['makespan_label'] = $this->minutesToClock($clientMakespan);
            $entry['makespan_score'] = $this->calculateMakespanScoreFromMinutes($clientMakespan);
            $result[] = $entry;
        }

        // Stable sort by client_id for consistent display
        usort($result, fn($a, $b) => ($a['client_id'] <=> $b['client_id']));

        return $result;
    }

    /**
     * Convert minutes (since midnight) to a 24-hour clock label "HH:MM".
     */
    private function minutesToClock($minutes)
    {
        $minutes = (int) $minutes;
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }

    private function memberDisplayName($member)
    {
        if (is_string($member)) return $member;
        if (!is_array($member)) return 'Member';
        if (isset($member['name']) && $member['name']) return $member['name'];
        if (isset($member['first_name']) || isset($member['last_name'])) {
            return trim(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''));
        }
        if (isset($member['id'])) return 'Employee #' . $member['id'];
        return 'Member';
    }
}