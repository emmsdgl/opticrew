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

        // Calculate workload for each team and apply penalties
        foreach ($schedule as $teamIndex => $teamSchedule) {
            $efficiency = $teamEfficiencies[$teamIndex];
            $totalWorkload = 0; // In minutes
            $totalHours = 0;    // Total working hours (duration + travel)

            foreach ($teamSchedule['tasks'] as $task) {
                // Workload (adjusted by efficiency)
                $predictedDuration = $task->duration / $efficiency;
                $totalWorkload += $predictedDuration;

                // Total hours (duration + travel time)
                $totalHours += ($task->duration + $task->travel_time) / 60;
            }

            $workloads[] = $totalWorkload;

            // ✅ PENALTY 1: Exceeding 12-hour limit
            if ($totalHours > self::MAX_HOURS_PER_DAY) {
                $overtime = $totalHours - self::MAX_HOURS_PER_DAY;
                $penalty += $overtime * 10; // Heavy penalty
            }

            // ✅ PENALTY 2: Missing 3PM deadline (simplified)
            if ($totalWorkload > self::DEADLINE_TIME_MINUTES) {
                $overDeadline = $totalWorkload - self::DEADLINE_TIME_MINUTES;
                $penalty += $overDeadline * 0.005; // 5 points per 1000 minutes over
            }
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
        $fitness = (1 / (1 + $stdDev)) - $penalty;

        // Ensure fitness is never negative
        if ($fitness < 0) {
            $fitness = 0.001;
        }

        return $fitness;
    }
}