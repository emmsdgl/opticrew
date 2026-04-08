<?php
/**
 * Traditional Genetic Algorithm (Multi-Objective + Constraints Embedded)
 *
 * Standard GA implementation with:
 *  (a) Rule-based constraints integrated DIRECTLY into the GA internals
 *      (NOT via the RuleBasedPreprocessor class — that is preserved untouched)
 *  (b) Multi-objective fitness mirroring EnhancedHybridGA:
 *        1. Workload balance     (coefficient of variation)
 *        2. Task sequencing      (arrivals first per team)
 *        3. Makespan optimization (relative to ideal balanced split)
 *        4. Idle time / zigzag    (single-client per team)
 *      Combined via weighted geometric mean.
 *
 * Embedded rule-based constraints:
 *   1. Task validation: tasks missing location_id / scheduled_date are skipped
 *   2. Task priority: arrival_status DESC determines initial scheduling order
 *   3. Team size: minimum 2, maximum 3 members per team
 *   4. Driver-per-team: each team must contain at least one licensed driver
 *   5. Driver-first composition: driver placed first, then non-drivers
 *   6. 12-hour limit per team (max_work_hours = 720 minutes)
 *   7. Target utilization: 85% of available capacity
 *   8. Workday window: 08:00 – 20:00 (12-hour daily window)
 *   9. Client matching: tasks must go to teams that serve their client
 *
 * Layers of enforcement:
 *   - createSimpleTeams()      : structural constraints (team size, driver presence)
 *   - generateRandomSchedule() : hard 12-hour limit at construction time
 *   - calculateFitness()       : multi-objective score + penalty multipliers
 *   - mutate()                 : adjacent swap (for sequencing) + team reassignment
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
            // Embedded rule-based constraints
            'max_work_hours' => 12 * 60,         // 12 hours per day per team
            'min_team_size' => 2,                // smallest valid team
            'max_team_size' => 3,                // largest valid team
            'require_driver_per_team' => true,   // at least 1 licensed driver
            'target_utilization_rate' => 0.85,   // 85% utilization target
            'work_start_minutes' => 480,         // 08:00 = 480 min from midnight
            'work_end_minutes' => 1200,          // 20:00 = 1200 min from midnight
            // Multi-objective fitness weights (must sum to 1.0)
            'weight_balance' => 0.40,
            'weight_sequencing' => 0.20,
            'weight_makespan' => 0.20,
            'weight_idle_time' => 0.20,
        ], $config);
    }

    public function optimize($tasks, $employees, $clients)
    {
        $this->fitnessHistory = [];

        // Start the "solution time" timer — measures only the GA work
        $solutionStartTime = microtime(true);

        if (empty($tasks) || empty($employees)) {
            return [
                'best_schedule' => [],
                'best_fitness' => 0,
                'generations' => 0,
                'fitness_history' => [],
                'teams' => [],
            ];
        }

        // ─── Embedded Rule 1: Task Validation ───
        // Drop tasks missing location_id or scheduled_date.
        $tasks = array_values(array_filter($tasks, function ($t) {
            return !empty($t['location_id']) && !empty($t['scheduled_date']);
        }));

        // ─── Embedded Rule 2: Task Priority Sort ───
        // Tasks with arrival_status = 1 are queued first.
        usort($tasks, function ($a, $b) {
            return ($b['arrival_status'] ?? 0) <=> ($a['arrival_status'] ?? 0);
        });

        if (empty($tasks)) {
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

        // ─── GA WORK COMPLETE — capture the "solution time" here ───
        $solutionTimeMs = (microtime(true) - $solutionStartTime) * 1000;

        return [
            'best_schedule' => $bestSchedule ?? [],
            'best_fitness' => max(0, $bestFitness),
            'generations' => $generation + 1,
            'convergence_generation' => $convergenceGeneration,
            'fitness_history' => $this->fitnessHistory,
            'teams' => $teams,
            'solution_time_ms' => $solutionTimeMs,
        ];
    }

    /**
     * Build teams INSIDE the GA, with all rule-based constraints embedded:
     *   - Min team size: 2
     *   - Max team size: 3
     *   - Driver-per-team: every team must contain ≥1 licensed driver
     *   - Driver placed first in member list
     *
     * No external preprocessor is invoked; this is the GA's internal logic.
     */
    private function createSimpleTeams($employees, $clients)
    {
        $minSize = $this->config['min_team_size'];
        $maxSize = $this->config['max_team_size'];
        $requireDriver = $this->config['require_driver_per_team'];

        // Split into drivers and non-drivers
        $drivers = [];
        $nonDrivers = [];
        foreach ($employees as $emp) {
            if (!empty($emp['has_driving_license']) && (int) $emp['has_driving_license'] === 1) {
                $drivers[] = $emp;
            } else {
                $nonDrivers[] = $emp;
            }
        }

        // If we require a driver per team but have none, the GA can't proceed.
        if ($requireDriver && empty($drivers)) {
            return [];
        }

        // Determine team sizes: prefer trios; fall back to pairs depending on remainder
        $totalEmployees = count($drivers) + count($nonDrivers);
        $teamsOfThree = intdiv($totalEmployees, 3);
        $remainder = $totalEmployees % 3;
        $teamsOfTwo = 0;

        if ($remainder === 1) {
            $teamsOfThree = max(0, $teamsOfThree - 1);
            $teamsOfTwo = 2;
        } elseif ($remainder === 2) {
            $teamsOfTwo = 1;
        }

        // Build the actual team rosters once — same physical teams will be
        // mirrored per client, just like the rule-based version.
        $available = $employees;
        $physicalTeams = [];

        $buildOne = function (&$pool, int $size) use (&$drivers, &$nonDrivers) {
            $team = [];

            // Driver placed first
            $driverIdx = null;
            foreach ($pool as $idx => $emp) {
                if (!empty($emp['has_driving_license']) && (int) $emp['has_driving_license'] === 1) {
                    $driverIdx = $idx;
                    break;
                }
            }
            if ($driverIdx !== null) {
                $team[] = $pool[$driverIdx];
                array_splice($pool, $driverIdx, 1);
            } elseif (!empty($pool)) {
                $team[] = array_shift($pool);
            }

            // Fill remaining slots — non-drivers first, then extra drivers
            while (count($team) < $size && !empty($pool)) {
                $nonDriverIdx = null;
                foreach ($pool as $idx => $emp) {
                    if (empty($emp['has_driving_license']) || (int) $emp['has_driving_license'] === 0) {
                        $nonDriverIdx = $idx;
                        break;
                    }
                }
                if ($nonDriverIdx !== null) {
                    $team[] = $pool[$nonDriverIdx];
                    array_splice($pool, $nonDriverIdx, 1);
                } else {
                    $team[] = array_shift($pool);
                }
            }

            return $team;
        };

        for ($i = 0; $i < $teamsOfThree; $i++) {
            $team = $buildOne($available, $maxSize);
            if (count($team) >= $minSize) {
                $physicalTeams[] = $team;
            }
        }
        for ($i = 0; $i < $teamsOfTwo; $i++) {
            $team = $buildOne($available, $minSize);
            if (count($team) >= $minSize) {
                $physicalTeams[] = $team;
            }
        }

        // Mirror each physical team across every client (same shape as preprocessor output)
        $teams = [];
        $teamId = 1;
        foreach ($physicalTeams as $physical) {
            foreach ($clients as $client) {
                $teams[] = [
                    'team_id' => $teamId,
                    'client_id' => $client['id'],
                    'members' => $physical,
                    'team_efficiency' => $this->calculateTeamEfficiency($physical),
                ];
            }
            $teamId++;
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
        $teamTaskCounts = array_fill_keys(array_column($teams, 'team_id'), 0);

        // Random task order — but the global arrival_status sort already happened
        // in optimize(), so arrivals tend to land first across the population.
        $shuffledTasks = $tasks;
        shuffle($shuffledTasks);

        foreach ($shuffledTasks as $task) {
            $randomTeam = $teams[array_rand($teams)];
            $taskDuration = $task['duration'] + ($task['travel_time'] ?? 0);

            // ─── Embedded Rule: Hard 12-Hour Limit ───
            // Reject any assignment that would exceed the 12-hour daily cap.
            if ($teamWorkloads[$randomTeam['team_id']] + $taskDuration <= $this->config['max_work_hours']) {
                $schedule[] = [
                    'task_id' => $task['id'],
                    'team_id' => $randomTeam['team_id'],
                    'client_id' => $task['client_id'],
                    'order' => $teamTaskCounts[$randomTeam['team_id']],
                ];
                $teamWorkloads[$randomTeam['team_id']] += $taskDuration;
                $teamTaskCounts[$randomTeam['team_id']]++;
            }
        }

        return $schedule;
    }

    private function calculateFitness($schedule, $tasks, $teams)
    {
        if (empty($schedule)) return 0.001;

        $taskMap = [];
        foreach ($tasks as $task) {
            $taskMap[$task['id']] = $task;
        }

        // Quick team lookup (members, client_id, efficiency) for structural checks
        $teamLookup = [];
        $teamEfficiency = [];
        foreach ($teams as $team) {
            $teamLookup[$team['team_id']] = $team;
            $teamEfficiency[$team['team_id']] = $team['team_efficiency'] ?? 1.0;
        }

        // Group assignments by team and sort by 'order' for sequencing fitness
        $teamAssignments = [];
        foreach ($schedule as $assignment) {
            $teamAssignments[$assignment['team_id']][] = $assignment;
        }
        foreach ($teamAssignments as &$assigns) {
            usort($assigns, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        }
        unset($assigns);

        // ─── Multi-objective scores (mirroring EnhancedHybridGA) ───
        $balanceFitness    = $this->calcBalanceFitness($teamAssignments, $taskMap, $teams);
        $sequencingFitness = $this->calcSequencingFitness($teamAssignments, $taskMap);
        $makespanFitness   = $this->calcMakespanFitness($teamAssignments, $taskMap, $teamEfficiency);
        $idleFitness       = $this->calcIdleTimeFitness($teamAssignments);

        // Weighted geometric mean — fitness can reach 1.0 when all 4 objectives are at max
        $w = $this->config;
        $balanceFitness    = max(0.0001, $balanceFitness);
        $sequencingFitness = max(0.0001, $sequencingFitness);
        $makespanFitness   = max(0.0001, $makespanFitness);
        $idleFitness       = max(0.0001, $idleFitness);

        $fitness = pow($balanceFitness,    $w['weight_balance'])
                 * pow($sequencingFitness, $w['weight_sequencing'])
                 * pow($makespanFitness,   $w['weight_makespan'])
                 * pow($idleFitness,       $w['weight_idle_time']);

        // ─── Compute team workloads for the rule-based penalties below ───
        $teamWorkloads = [];
        $usedTeamIds = [];
        foreach ($schedule as $assignment) {
            $taskId = $assignment['task_id'];
            $teamId = $assignment['team_id'];
            if (!isset($taskMap[$taskId])) continue;
            $duration = $taskMap[$taskId]['duration'] + ($taskMap[$taskId]['travel_time'] ?? 0);
            if (!isset($teamWorkloads[$teamId])) $teamWorkloads[$teamId] = 0;
            $teamWorkloads[$teamId] += $duration;
            $usedTeamIds[$teamId] = true;
        }
        $meanWorkload = !empty($teamWorkloads) ? array_sum($teamWorkloads) / count($teamWorkloads) : 0;

        // ─── Embedded Rule: 12-hour limit (hard penalty) ───
        foreach ($teamWorkloads as $workload) {
            if ($workload > $this->config['max_work_hours']) {
                $fitness *= 0.3;
            }
        }

        // ─── Embedded Rule: Driver-per-team + Team-size constraints ───
        $minSize = $this->config['min_team_size'];
        $maxSize = $this->config['max_team_size'];
        foreach (array_keys($usedTeamIds) as $teamId) {
            $team = $teamLookup[$teamId] ?? null;
            if (!$team) {
                $fitness *= 0.2;
                continue;
            }
            $members = $team['members'] ?? [];
            $size = count($members);

            if ($size < $minSize || $size > $maxSize) {
                $fitness *= 0.4;
            }

            if (!empty($this->config['require_driver_per_team'])) {
                $hasDriver = false;
                foreach ($members as $m) {
                    if (!empty($m['has_driving_license']) && (int) $m['has_driving_license'] === 1) {
                        $hasDriver = true;
                        break;
                    }
                }
                if (!$hasDriver) {
                    $fitness *= 0.3;
                }
            }
        }

        // ─── Embedded Rule: Client matching ───
        $clientMismatch = 0;
        foreach ($schedule as $assignment) {
            $task = $taskMap[$assignment['task_id']] ?? null;
            $team = $teamLookup[$assignment['team_id']] ?? null;
            if ($task && $team && isset($task['client_id']) && isset($team['client_id'])) {
                if ($task['client_id'] != $team['client_id']) {
                    $clientMismatch++;
                }
            }
        }
        if ($clientMismatch > 0) {
            $fitness *= 1.0 / (1.0 + $clientMismatch * 0.5);
        }

        // ─── Embedded Rule: Target utilization (85%) ───
        $target = $this->config['target_utilization_rate'] * $this->config['max_work_hours'];
        if ($target > 0 && $meanWorkload > 0) {
            $utilizationGap = abs($meanWorkload - $target) / $target;
            $fitness *= 1.0 / (1.0 + $utilizationGap);
        }

        // ─── Task completion penalty (power-of-4) ───
        $completionRate = count($schedule) / count($tasks);
        $fitness *= pow($completionRate, 4);

        if ($completionRate < 1.0) {
            $unassignedTasks = count($tasks) - count($schedule);
            $fitness *= (1.0 / (1.0 + $unassignedTasks * 5.0));
        }

        return max(0.001, $fitness);
    }

    // ─── Multi-Objective Sub-Fitnesses (mirroring EnhancedHybridGA) ───

    /**
     * Objective 1: Workload Balance — coefficient of variation across team workloads.
     * Returns 1.0 when every team has identical load, decreasing as teams diverge.
     */
    private function calcBalanceFitness($teamAssignments, $taskMap, $teams)
    {
        $workloads = [];
        foreach ($teamAssignments as $teamId => $assigns) {
            $total = 0;
            foreach ($assigns as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if ($task) {
                    $total += $task['duration'] + ($task['travel_time'] ?? 0);
                }
            }
            $workloads[$teamId] = $total;
        }
        if (empty($workloads)) return 0;

        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        foreach ($workloads as $wl) {
            $variance += pow($wl - $mean, 2);
        }
        $stdDev = sqrt($variance / count($workloads));
        $cv = $mean > 0 ? $stdDev / $mean : 0;
        $balance = 1 / (1 + $cv);

        // Team utilization bonus: penalize using fewer teams than available
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
     * Objective 2: Task Sequencing — arrivals should appear at the front of each team's queue.
     */
    private function calcSequencingFitness($teamAssignments, $taskMap)
    {
        $totalScore = 0;
        $teamCount = 0;

        foreach ($teamAssignments as $assigns) {
            if (empty($assigns)) continue;
            $teamCount++;

            $arrivalFlags = [];
            foreach ($assigns as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                $arrivalFlags[] = ($task && ($task['arrival_status'] ?? 0) == 1) ? 1 : 0;
            }

            $totalArrivals = array_sum($arrivalFlags);
            if ($totalArrivals === 0 || $totalArrivals === count($arrivalFlags)) {
                $totalScore += 1.0;
                continue;
            }

            $correctlyPlaced = 0;
            for ($i = 0; $i < $totalArrivals; $i++) {
                if (isset($arrivalFlags[$i]) && $arrivalFlags[$i] === 1) {
                    $correctlyPlaced++;
                }
            }
            $totalScore += $correctlyPlaced / $totalArrivals;
        }

        return $teamCount > 0 ? $totalScore / $teamCount : 0;
    }

    /**
     * Objective 3: Makespan — score relative to the ideal balanced split.
     * Score = ideal_makespan / actual_makespan, capped to [0, 1].
     */
    private function calcMakespanFitness($teamAssignments, $taskMap, $teamEfficiency)
    {
        if (empty($teamAssignments)) return 0;

        $teamCompletionTimes = [];
        $totalWork = 0;
        foreach ($teamAssignments as $teamId => $assigns) {
            $eff = $teamEfficiency[$teamId] ?? 1.0;
            $totalTime = 0;
            foreach ($assigns as $a) {
                $task = $taskMap[$a['task_id']] ?? null;
                if ($task) {
                    $base = $task['duration'] + ($task['travel_time'] ?? 0);
                    $totalTime += $base / max(0.1, $eff);
                }
            }
            $teamCompletionTimes[$teamId] = $totalTime;
            $totalWork += $totalTime;
        }

        if (empty($teamCompletionTimes) || $totalWork <= 0) return 1.0;

        $makespan = max($teamCompletionTimes);
        $idealMakespan = $totalWork / count($teamCompletionTimes);
        $ratio = $idealMakespan / max(1, $makespan);
        return max(0.0, min(1.0, $ratio));
    }

    /**
     * Objective 4: Idle Time / Zigzag Reduction — single-client per team is ideal.
     */
    private function calcIdleTimeFitness($teamAssignments)
    {
        if (empty($teamAssignments)) return 0;

        $teamCount = 0;
        $totalScore = 0;

        foreach ($teamAssignments as $assigns) {
            if (empty($assigns)) continue;
            $teamCount++;

            $clientIds = [];
            foreach ($assigns as $a) {
                $cid = $a['client_id'] ?? null;
                if ($cid !== null && !in_array($cid, $clientIds)) {
                    $clientIds[] = $cid;
                }
            }

            if (count($clientIds) <= 1) {
                $totalScore += 1.0;
                continue;
            }

            // Penalize each client switch within the sequence
            $switches = 0;
            $prevClient = null;
            foreach ($assigns as $a) {
                $cid = $a['client_id'] ?? null;
                if ($prevClient !== null && $cid !== $prevClient) {
                    $switches++;
                }
                $prevClient = $cid;
            }

            $maxSwitches = count($assigns) - 1;
            $switchRate = $maxSwitches > 0 ? $switches / $maxSwitches : 0;
            $totalScore += 1 / (1 + $switchRate * 2);
        }

        return $teamCount > 0 ? $totalScore / $teamCount : 0;
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

        // 50/50 — adjacent swap (sequencing) OR team reassignment (exploration)
        if (mt_rand(0, 1) === 0) {
            // Adjacent swap within a single team
            $byTeam = [];
            foreach ($mutated as $idx => $a) {
                $byTeam[$a['team_id']][] = $idx;
            }
            $eligible = array_filter($byTeam, fn($idxs) => count($idxs) >= 2);
            if (empty($eligible)) {
                return $this->teamReassignMutation($mutated, $teams);
            }
            $teamIds = array_keys($eligible);
            $teamId = $teamIds[array_rand($teamIds)];
            $indices = $eligible[$teamId];
            usort($indices, fn($a, $b) => ($mutated[$a]['order'] ?? 0) <=> ($mutated[$b]['order'] ?? 0));
            $pairIdx = mt_rand(0, count($indices) - 2);
            $idx1 = $indices[$pairIdx];
            $idx2 = $indices[$pairIdx + 1];
            $tempOrder = $mutated[$idx1]['order'] ?? 0;
            $mutated[$idx1]['order'] = $mutated[$idx2]['order'] ?? 0;
            $mutated[$idx2]['order'] = $tempOrder;
            return $mutated;
        }

        return $this->teamReassignMutation($mutated, $teams);
    }

    private function teamReassignMutation($schedule, $teams)
    {
        if (empty($schedule)) return $schedule;
        $mutated = $schedule;
        $mutationPoint = array_rand($mutated);
        $newTeam = $teams[array_rand($teams)];
        $mutated[$mutationPoint]['team_id'] = $newTeam['team_id'];
        return $mutated;
    }
}
