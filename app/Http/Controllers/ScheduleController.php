<?php

namespace App\Http\Controllers;

use App\Services\Optimization\OptimizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ScheduleController extends Controller
{
    protected OptimizationService $optimizationService;

    public function __construct(OptimizationService $optimizationService)
    {
        $this->optimizationService = $optimizationService;
    }

    /**
     * Optimize schedule for a given date
     */
    public function optimize(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'location_ids' => 'nullable|array',
            'location_ids.*' => 'integer|exists:locations,id',
            'triggered_by_task_id' => 'nullable|integer|exists:tasks,id',
        ]);

        try {
            $result = $this->optimizationService->optimizeSchedule(
                $validated['service_date'],
                $validated['location_ids'] ?? [],
                $validated['triggered_by_task_id'] ?? null
            );

            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Optimization failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get optimization results for a date
     */
    public function getSchedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'client_id' => 'nullable|integer|exists:clients,id',
        ]);

        $query = \App\Models\OptimizationResult::whereDate('service_date', $validated['service_date']);

        if (isset($validated['client_id'])) {
            $query->where('client_id', $validated['client_id']);
        }

        $results = $query->with('client')->get();

        return response()->json([
            'status' => 'success',
            'schedules' => $results,
        ]);
    }

    /**
     * Get optimization statistics
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
        ]);

        $results = \App\Models\OptimizationResult::whereDate('service_date', $validated['service_date'])
            ->get();

        $statistics = [
            'total_clients' => $results->count(),
            'average_fitness' => $results->avg('fitness_score'),
            'total_tasks' => $results->sum(function ($result) {
                $schedule = json_decode($result->schedule, true);
                $taskCount = 0;
                foreach ($schedule as $teamSchedule) {
                    $taskCount += count($teamSchedule['tasks'] ?? []);
                }
                return $taskCount;
            }),
        ];

        return response()->json([
            'status' => 'success',
            'statistics' => $statistics,
        ]);
    }
}