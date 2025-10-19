<?php

namespace App\Services\Optimization\ScenarioManager;

use App\Services\Optimization\ScenarioManager\Scenarios\EmployeeAbsenceScenario;
use App\Services\Optimization\ScenarioManager\Scenarios\EmergencyTaskScenario;
use App\Services\Optimization\ScenarioManager\Scenarios\VehicleBreakdownScenario;
use App\Services\Optimization\ScenarioManager\Scenarios\TimeConstraintScenario;

class ScenarioManager
{
    protected ImpactAnalyzer $impactAnalyzer;

    public function __construct(ImpactAnalyzer $impactAnalyzer)
    {
        $this->impactAnalyzer = $impactAnalyzer;
    }

    public function analyze(array $originalSchedule, string $scenarioType, array $parameters): array
    {
        $baselineFitness = $this->calculateOverallFitness($originalSchedule);

        $scenario = $this->createScenario($scenarioType);
        $modifiedResult = $scenario->handle($originalSchedule, $parameters);

        $impactAnalysis = $this->impactAnalyzer->analyze(
            $baselineFitness,
            $modifiedResult,
            $originalSchedule
        );

        return [
            'original_schedule' => $originalSchedule,
            'modified_schedule' => $modifiedResult['schedule'],
            'impact_analysis' => $impactAnalysis,
            'recommendations' => $this->generateRecommendations($impactAnalysis),
        ];
    }

    protected function createScenario(string $type)
    {
        return match ($type) {
            'employee_absence' => new EmployeeAbsenceScenario(),
            'emergency_tasks' => new EmergencyTaskScenario(),
            'vehicle_breakdown' => new VehicleBreakdownScenario(),
            'time_constraint' => new TimeConstraintScenario(),
            default => throw new \InvalidArgumentException("Unknown scenario type: {$type}"),
        };
    }

    protected function calculateOverallFitness(array $schedule): float
    {
        // Implement fitness calculation across all clients
        $totalFitness = 0;
        foreach ($schedule as $clientSchedule) {
            $totalFitness += $clientSchedule->getFitness() ?? 0;
        }
        return $totalFitness / count($schedule);
    }

    protected function generateRecommendations(array $analysis): array
    {
        $recommendations = [];

        if ($analysis['fitness_difference'] < -0.1) {
            $recommendations[] = 'Consider hiring additional staff';
            $recommendations[] = 'Review task priorities and deadlines';
        }

        if ($analysis['reassignments_needed'] > 10) {
            $recommendations[] = 'Significant schedule disruption - notify affected clients';
        }

        if (!$analysis['feasible']) {
            $recommendations[] = 'CRITICAL: Schedule is not feasible - immediate action required';
        }

        return $recommendations;
    }
}