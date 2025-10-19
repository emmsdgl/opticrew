<?php

namespace App\Http\Controllers;

use App\Services\Optimization\ScenarioManager\ScenarioManager;
use App\Models\OptimizationResult;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScenarioController extends Controller
{
    protected ScenarioManager $scenarioManager;

    public function __construct(ScenarioManager $scenarioManager)
    {
        $this->scenarioManager = $scenarioManager;
    }

    /**
     * Analyze a what-if scenario
     */
    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'scenario_type' => 'required|string|in:employee_absence,emergency_tasks,vehicle_breakdown,time_constraint',
            'parameters' => 'required|array',
        ]);

        try {
            // Get original schedule
            $originalSchedule = $this->getOriginalSchedule($validated['service_date']);

            if (empty($originalSchedule)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No schedule found for the given date. Please optimize first.',
                ], 404);
            }

            // Run scenario analysis
            $result = $this->scenarioManager->analyze(
                $originalSchedule,
                $validated['scenario_type'],
                $validated['parameters']
            );

            // Optionally save scenario analysis
            $this->saveScenarioAnalysis($validated, $result);

            return response()->json([
                'status' => 'success',
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Scenario analysis failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Compare multiple scenarios
     */
    public function compareScenarios(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'scenarios' => 'required|array|min:2',
            'scenarios.*.type' => 'required|string',
            'scenarios.*.parameters' => 'required|array',
        ]);

        try {
            $originalSchedule = $this->getOriginalSchedule($validated['service_date']);

            if (empty($originalSchedule)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No schedule found for the given date.',
                ], 404);
            }

            $results = [];
            foreach ($validated['scenarios'] as $index => $scenario) {
                $results["scenario_" . ($index + 1)] = $this->scenarioManager->analyze(
                    $originalSchedule,
                    $scenario['type'],
                    $scenario['parameters']
                );
            }

            // Generate comparison report
            $comparison = $this->generateComparison($results);

            return response()->json([
                'status' => 'success',
                'scenarios' => $results,
                'comparison' => $comparison,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Scenario comparison failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available scenario types
     */
    public function getScenarioTypes(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'scenario_types' => [
                [
                    'type' => 'employee_absence',
                    'name' => 'Employee Absence',
                    'description' => 'Simulate what happens when an employee is absent',
                    'required_parameters' => ['employee_id'],
                ],
                [
                    'type' => 'emergency_tasks',
                    'name' => 'Emergency Tasks',
                    'description' => 'Add urgent tasks to existing schedule',
                    'required_parameters' => ['emergency_tasks'],
                ],
                [
                    'type' => 'vehicle_breakdown',
                    'name' => 'Vehicle Breakdown',
                    'description' => 'Handle vehicle unavailability',
                    'required_parameters' => ['vehicle_id'],
                ],
                [
                    'type' => 'time_constraint',
                    'name' => 'Time Constraint',
                    'description' => 'Client needs earlier completion',
                    'required_parameters' => ['client_id', 'deadline'],
                ],
            ],
        ]);
    }

    protected function getOriginalSchedule(string $serviceDate): array
    {
        $results = OptimizationResult::whereDate('service_date', $serviceDate)->get();

        $schedule = [];
        foreach ($results as $result) {
            $schedule[$result->client_id] = [
                'client_id' => $result->client_id,
                'schedule' => json_decode($result->schedule, true),
                'fitness' => $result->fitness_score,
            ];
        }

        return $schedule;
    }

    protected function saveScenarioAnalysis(array $validated, array $result): void
    {
        \App\Models\ScenarioAnalysis::create([
            'service_date' => $validated['service_date'],
            'scenario_type' => $validated['scenario_type'],
            'parameters' => json_encode($validated['parameters']),
            'modified_schedule' => json_encode($result['modified_schedule']),
            'impact_analysis' => json_encode($result['impact_analysis']),
            'recommendations' => json_encode($result['recommendations']),
        ]);
    }

    protected function generateComparison(array $scenarios): array
    {
        $comparison = [
            'best_scenario' => null,
            'worst_scenario' => null,
            'summary' => [],
        ];

        $fitnessScores = [];
        foreach ($scenarios as $name => $scenario) {
            $fitness = $scenario['impact_analysis']['scenario_fitness'] ?? 0;
            $fitnessScores[$name] = $fitness;

            $comparison['summary'][] = [
                'scenario' => $name,
                'fitness' => $fitness,
                'feasible' => $scenario['impact_analysis']['feasible'] ?? false,
                'affected_teams' => $scenario['impact_analysis']['affected_teams'] ?? 0,
            ];
        }

        if (!empty($fitnessScores)) {
            $comparison['best_scenario'] = array_keys($fitnessScores, max($fitnessScores))[0];
            $comparison['worst_scenario'] = array_keys($fitnessScores, min($fitnessScores))[0];
        }

        return $comparison;
    }
}