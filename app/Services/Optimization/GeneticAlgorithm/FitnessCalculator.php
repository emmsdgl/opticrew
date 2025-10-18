<?php

namespace App\Services\Optimization\GeneticAlgorithm;

class FitnessCalculator
{
    public function calculate(Individual $individual, array $teamEfficiencies): float
    {
        $schedule = $individual->getSchedule();
        $workloads = [];

        foreach ($schedule as $teamIndex => $teamSchedule) {
            $efficiency = $teamEfficiencies[$teamIndex];
            $totalWorkload = 0;

            foreach ($teamSchedule['tasks'] as $task) {
                $predictedDuration = $task->duration / $efficiency;
                $totalWorkload += $predictedDuration;
            }

            $workloads[] = $totalWorkload;
        }

        // Calculate standard deviation
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;

        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }

        $stdDev = sqrt($variance / count($workloads));

        // Fitness: higher is better (lower std_dev = better balance)
        return 1 / (1 + $stdDev);
    }
}