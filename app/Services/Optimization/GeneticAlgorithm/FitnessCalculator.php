<?php

namespace App\Services\Optimization\GeneticAlgorithm;

use Illuminate\Support\Facades\Log;

class FitnessCalculator
{
    protected const MAX_HOURS_PER_DAY = 12;
    protected const DEADLINE_TIME_MINUTES = 15 * 60; // 3 PM = 15:00 = 900 minutes

    /**
     * Calculate fitness score for a schedule
     *
     * MULTIPLICATIVE FITNESS FUNCTION (Aligned with Simulation Model)
     *
     * Formula: fitness = baseFitness × constraintMultiplier × completionMultiplier × taskBalanceMultiplier
     *
     * Fitness components:
     * 1. ✅ Base: 1/(1+workloadStdDev) - Reward balanced workloads
     * 2. ✅ Constraint Multiplier: 0.5 if 12-hour violated, 0.7-1.0 if deadline missed
     * 3. ✅ Completion Multiplier: (assigned/total)^4 - Heavy penalty for unassigned tasks
     * 4. ✅ Task Balance Multiplier: Exponential decay based on task count stdDev
     *
     * @param Individual $individual
     * @param array $teamEfficiencies
     * @param int|null $totalTaskCount Total number of tasks to be assigned (for completion rate)
     * @return float Fitness score (higher is better, range 0-1)
     */
    public function calculate(Individual $individual, array $teamEfficiencies, ?int $totalTaskCount = null): float
    {
        $schedule = $individual->getSchedule();
        $workloads = [];
        $penaltyDetails = []; // Track penalty breakdown

        // Calculate workload for each team and apply penalties
        foreach ($schedule as $teamIndex => $teamSchedule) {
            $efficiency = $teamEfficiencies[$teamIndex];
            $totalWorkload = 0; // In minutes
            $totalHours = 0;    // Total working hours (duration only, travel added once)
            $taskCount = count($teamSchedule['tasks']);
            $arrivalTaskWorkload = 0; // Workload for ONLY arrival tasks (3PM deadline)

            // ✅ Calculate one-time travel time based on client destination
            $travelTimeMinutes = 0;
            if ($taskCount > 0) {
                $firstTask = $teamSchedule['tasks'][0];
                // Get contracted client ID from first task's location
                if ($firstTask->location && $firstTask->location->contracted_client_id) {
                    $clientId = $firstTask->location->contracted_client_id;
                    // Kakslauttanen (ID=1): 30 min, Aikamatkat (ID=2): 15 min
                    $travelTimeMinutes = ($clientId == 1) ? 30 : 15;
                }
            }

            foreach ($teamSchedule['tasks'] as $task) {
                // Workload (adjusted by efficiency)
                $predictedDuration = $task->duration / $efficiency;
                $totalWorkload += $predictedDuration;

                // ✅ Track ONLY arrival tasks for 3PM deadline
                if ($task->arrival_status) {
                    $arrivalTaskWorkload += $predictedDuration;
                }

                // Total hours (duration only - travel added once below)
                $totalHours += $task->duration / 60;
            }

            // Add one-time travel to total hours
            $totalHours += $travelTimeMinutes / 60;

            $workloads[] = $totalWorkload;
            $arrivalTaskCount = collect($teamSchedule['tasks'])->where('arrival_status', true)->count();

            $teamDetails = [
                'team_index' => $teamIndex + 1,
                'efficiency' => $efficiency,
                'task_count' => $taskCount,
                'arrival_task_count' => $arrivalTaskCount,
                'workload_minutes' => round($totalWorkload, 2),
                'arrival_workload_minutes' => round($arrivalTaskWorkload, 2),
                'travel_time_minutes' => $travelTimeMinutes,
                'total_hours' => round($totalHours, 2),
                'violations' => []
            ];

            // Track CONSTRAINT VIOLATIONS for multiplicative penalties
            // ✅ VIOLATION 1: Exceeding 12-hour limit (applies to ALL tasks)
            if ($totalHours > self::MAX_HOURS_PER_DAY) {
                $overtime = $totalHours - self::MAX_HOURS_PER_DAY;
                $teamDetails['violations'][] = [
                    'type' => '12-hour_limit',
                    'overtime_hours' => round($overtime, 2),
                    'description' => 'Team exceeds 12-hour daily work limit'
                ];
            }

            // ✅ VIOLATION 2: Missing 3PM deadline (ONLY for arrival/urgent tasks)
            if ($arrivalTaskCount > 0 && $arrivalTaskWorkload > self::DEADLINE_TIME_MINUTES) {
                $overDeadline = $arrivalTaskWorkload - self::DEADLINE_TIME_MINUTES;
                $teamDetails['violations'][] = [
                    'type' => '3pm_deadline_arrival',
                    'arrival_tasks' => $arrivalTaskCount,
                    'over_deadline_minutes' => round($overDeadline, 2),
                    'description' => 'Arrival tasks (urgent) cannot finish before 3PM'
                ];
            }

            $penaltyDetails[] = $teamDetails;
        }

        // ✅ RULE 6: Balanced task distribution (BOTH workload AND task count)
        if (count($workloads) === 0) {
            return 0.001; // Avoid division by zero
        }

        // Calculate workload standard deviation
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;

        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }

        $stdDev = sqrt($variance / count($workloads));

        // ✅ Calculate task count standard deviation (PRIMARY metric for fairness)
        $taskCounts = array_map(fn($details) => $details['task_count'], $penaltyDetails);
        $taskMean = array_sum($taskCounts) / count($taskCounts);
        $taskVariance = 0;

        foreach ($taskCounts as $count) {
            $taskVariance += pow($count - $taskMean, 2);
        }

        $taskStdDev = sqrt($taskVariance / count($taskCounts));

        // ============================================================
        // MULTIPLICATIVE FITNESS CALCULATION (MATCHES SIMULATION)
        // ============================================================

        // 1️⃣ BASE FITNESS: Workload balance (lower stdDev = higher fitness)
        $baseFitness = 1 / (1 + $stdDev);

        // 2️⃣ CONSTRAINT MULTIPLIER: Check for hard constraint violations
        $constraintMultiplier = $this->calculateConstraintMultiplier($penaltyDetails);

        // 3️⃣ COMPLETION MULTIPLIER: Ensure all tasks are assigned (MATCH SIMULATION)
        $completionMultiplier = $this->calculateCompletionMultiplier($schedule, $totalTaskCount);

        // 4️⃣ TASK BALANCE MULTIPLIER: Penalize uneven task distribution
        // Use exponential decay: 1 / (1 + taskStdDev * weight)
        // Higher weight = stronger penalty for imbalance
        $taskBalanceMultiplier = 1 / (1 + $taskStdDev * 5.0);

        // FINAL FITNESS: Multiply all components
        $fitness = $baseFitness * $constraintMultiplier * $completionMultiplier * $taskBalanceMultiplier;

        // Ensure fitness is in valid range
        $fitness = max(0.001, min(1.0, $fitness));

        // ✅ Log detailed fitness breakdown
        Log::info("Fitness calculation breakdown (MULTIPLICATIVE)", [
            'workloads' => array_map(fn($w) => round($w, 2), $workloads),
            'task_counts' => $taskCounts,
            'mean_workload' => round($mean, 2),
            'workload_std_dev' => round($stdDev, 4),
            'task_std_dev' => round($taskStdDev, 4),
            '1_base_fitness' => round($baseFitness, 4),
            '2_constraint_multiplier' => round($constraintMultiplier, 4),
            '3_completion_multiplier' => round($completionMultiplier, 4),
            '4_task_balance_multiplier' => round($taskBalanceMultiplier, 4),
            'final_fitness' => round($fitness, 4),
            'team_details' => $penaltyDetails
        ]);

        return $fitness;
    }

    /**
     * Calculate constraint violation multiplier (MULTIPLICATIVE PENALTIES)
     *
     * @param array $teamDetails
     * @return float Multiplier between 0.01 and 1.0
     */
    private function calculateConstraintMultiplier(array $teamDetails): float
    {
        $multiplier = 1.0;
        $violations = [];

        foreach ($teamDetails as $team) {
            if (empty($team['violations'])) {
                continue;
            }

            foreach ($team['violations'] as $violation) {
                if ($violation['type'] === '12-hour_limit') {
                    // MATCH SIMULATION: 12-hour violation = multiply by 0.5 (HEAVY PENALTY)
                    $multiplier *= 0.5;
                    $violations[] = [
                        'team' => $team['team_index'],
                        'type' => '12-hour limit exceeded',
                        'overtime' => $violation['overtime_hours'] . ' hours',
                        'penalty' => '×0.5 (50% fitness reduction)'
                    ];
                }

                if ($violation['type'] === '3pm_deadline_arrival') {
                    // 3PM deadline violation: Proportional penalty (0.7 to 1.0)
                    // Max 30% reduction for severe violations
                    $overDeadlineMinutes = $violation['over_deadline_minutes'];
                    $penaltyFactor = min(0.3, $overDeadlineMinutes / 720); // Max 30% penalty
                    $multiplier *= (1.0 - $penaltyFactor);
                    $violations[] = [
                        'team' => $team['team_index'],
                        'type' => '3PM deadline missed',
                        'over_deadline' => round($overDeadlineMinutes, 2) . ' minutes',
                        'penalty' => '×' . round(1.0 - $penaltyFactor, 4)
                    ];
                }
            }
        }

        // Ensure multiplier never goes below minimum threshold
        $multiplier = max(0.01, $multiplier);

        if (!empty($violations)) {
            Log::warning("Constraint violations detected", [
                'violations' => $violations,
                'final_multiplier' => round($multiplier, 4)
            ]);
        }

        return $multiplier;
    }

    /**
     * Calculate task completion multiplier (MATCHES SIMULATION MODEL)
     *
     * Formula: (assignedTasks / totalTasks) ^ 4
     *
     * This heavily penalizes incomplete schedules:
     * - 100% complete: 1.0^4 = 1.00 (no penalty)
     * - 95% complete: 0.95^4 = 0.81 (19% penalty)
     * - 90% complete: 0.90^4 = 0.66 (34% penalty)
     * - 80% complete: 0.80^4 = 0.41 (59% penalty)
     * - 50% complete: 0.50^4 = 0.06 (94% penalty)
     *
     * @param array $schedule
     * @param int|null $totalTaskCount
     * @return float Multiplier between 0.0001 and 1.0
     */
    private function calculateCompletionMultiplier(array $schedule, ?int $totalTaskCount = null): float
    {
        // Count assigned tasks in schedule
        $assignedTaskCount = 0;
        foreach ($schedule as $teamSchedule) {
            $assignedTaskCount += count($teamSchedule['tasks']);
        }

        // If total task count not provided, assume all tasks are assigned
        if ($totalTaskCount === null || $totalTaskCount === 0) {
            Log::info("Completion multiplier: Total task count not provided, assuming 100% completion");
            return 1.0;
        }

        // Calculate completion rate
        $completionRate = $assignedTaskCount / $totalTaskCount;

        // Apply power-4 penalty (matches simulation model)
        $completionMultiplier = pow($completionRate, 4);

        // Log if not complete
        if ($completionRate < 1.0) {
            $unassignedCount = $totalTaskCount - $assignedTaskCount;
            Log::warning("Incomplete task assignment detected", [
                'total_tasks' => $totalTaskCount,
                'assigned_tasks' => $assignedTaskCount,
                'unassigned_tasks' => $unassignedCount,
                'completion_rate' => round($completionRate, 4),
                'completion_multiplier' => round($completionMultiplier, 4),
                'fitness_impact' => round((1.0 - $completionMultiplier) * 100, 2) . '% reduction'
            ]);
        }

        // Ensure minimum threshold
        return max(0.0001, $completionMultiplier);
    }
}