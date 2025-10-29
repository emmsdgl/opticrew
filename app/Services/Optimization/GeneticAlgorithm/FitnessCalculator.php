<?php

namespace App\Services\Optimization\GeneticAlgorithm;

class FitnessCalculator
{
    protected const MAX_HOURS_PER_DAY = 12;
    protected const DEADLINE_TIME_MINUTES = 15 * 60; // 3 PM = 15:00 = 900 minutes

    /**
     * Calculate fitness score for a schedule
     *
     * Fitness components (from pseudocode):
     * 1. ✅ Penalize exceeding 12-hour limit (RULE 7)
     * 2. ✅ Penalize missing 3PM deadline
     * 3. ✅ Reward balanced task distribution (low standard deviation)
     *
     * @param Individual $individual
     * @param array $teamEfficiencies
     * @return float Fitness score (higher is better)
     */
    public function calculate(Individual $individual, array $teamEfficiencies): float
    {
        $schedule = $individual->getSchedule();
        $workloads = [];
        $penalty = 0;
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

            $teamPenalty = 0;
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
                'penalties' => []
            ];

            // ✅ PENALTY 1: Exceeding 12-hour limit (applies to ALL tasks)
            if ($totalHours > self::MAX_HOURS_PER_DAY) {
                $overtime = $totalHours - self::MAX_HOURS_PER_DAY;
                $overtimePenalty = $overtime * 10;
                $penalty += $overtimePenalty;
                $teamPenalty += $overtimePenalty;
                $teamDetails['penalties'][] = [
                    'type' => '12-hour_limit',
                    'overtime_hours' => round($overtime, 2),
                    'penalty_value' => round($overtimePenalty, 4),
                    'description' => 'Team exceeds 12-hour daily work limit'
                ];
            }

            // ✅ PENALTY 2: Missing 3PM deadline (ONLY for arrival/urgent tasks)
            if ($arrivalTaskCount > 0 && $arrivalTaskWorkload > self::DEADLINE_TIME_MINUTES) {
                $overDeadline = $arrivalTaskWorkload - self::DEADLINE_TIME_MINUTES;
                $deadlinePenalty = $overDeadline * 0.005;
                $penalty += $deadlinePenalty;
                $teamPenalty += $deadlinePenalty;
                $teamDetails['penalties'][] = [
                    'type' => '3pm_deadline_arrival',
                    'arrival_tasks' => $arrivalTaskCount,
                    'over_deadline_minutes' => round($overDeadline, 2),
                    'penalty_value' => round($deadlinePenalty, 4),
                    'description' => 'Arrival tasks (urgent) cannot finish before 3PM'
                ];
            }

            $teamDetails['total_team_penalty'] = round($teamPenalty, 4);
            $penaltyDetails[] = $teamDetails;
        }

        // ✅ REWARD: Balanced task distribution (low standard deviation)
        if (count($workloads) === 0) {
            return 0.001; // Avoid division by zero
        }

        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;

        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }

        $stdDev = sqrt($variance / count($workloads));

        // Fitness: Higher is better (lower std_dev = better balance)
        // Subtract penalties
        $baseFitness = 1 / (1 + $stdDev);
        $fitness = $baseFitness - $penalty;

        // Ensure fitness is never negative
        if ($fitness < 0) {
            $fitness = 0.001;
        }

        // ✅ Log detailed fitness breakdown
        \Log::info("Fitness calculation breakdown", [
            'workloads' => array_map(fn($w) => round($w, 2), $workloads),
            'mean_workload' => round($mean, 2),
            'std_dev' => round($stdDev, 2),
            'base_fitness' => round($baseFitness, 4),
            'total_penalty' => round($penalty, 4),
            'final_fitness' => round($fitness, 4),
            'team_details' => $penaltyDetails
        ]);

        return $fitness;
    }
}