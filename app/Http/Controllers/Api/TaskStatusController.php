<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\PerformanceFlag;
use App\Services\Alert\AlertService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * TaskStatusController
 *
 * Handles task status updates from the Employee Dashboard
 * Based on pseudocode UPDATE_TASK_STATUS function
 */
class TaskStatusController extends Controller
{
    protected AlertService $alertService;
    protected const ALERT_THRESHOLD_MINUTES = 30;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Start a task (set status to "In Progress")
     *
     * POST /api/tasks/{taskId}/start
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function startTask($taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            $task->update([
                'status' => 'In Progress',
                'started_at' => now()
            ]);

            Log::info("Task started", [
                'task_id' => $task->id,
                'started_at' => $task->started_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task started successfully',
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'started_at' => $task->started_at->toDateTimeString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to start task", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Put task on hold with reason
     * Triggers alert if delay > 30 minutes
     *
     * POST /api/tasks/{taskId}/hold
     * Body: { "reason": "Guest still in cabin" }
     *
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function putTaskOnHold(Request $request, $taskId)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        try {
            $task = Task::findOrFail($taskId);

            $task->update([
                'status' => 'On Hold',
                'on_hold_reason' => $request->reason,
                'on_hold_timestamp' => now()
            ]);

            Log::info("Task put on hold", [
                'task_id' => $task->id,
                'reason' => $request->reason,
                'on_hold_timestamp' => $task->on_hold_timestamp
            ]);

            // ✅ Calculate delay duration
            if ($task->started_at) {
                $delayMinutes = $task->started_at->diffInMinutes(now());

                Log::info("Task delay calculated", [
                    'task_id' => $task->id,
                    'delay_minutes' => $delayMinutes
                ]);

                // ✅ Trigger alert if delay > 30 minutes
                $alertTriggered = false;
                if ($delayMinutes > self::ALERT_THRESHOLD_MINUTES) {
                    $alert = $this->alertService->triggerTaskDelayedAlert($task, [
                        'delay_minutes' => $delayMinutes,
                        'reason' => $request->reason,
                        'task_description' => $task->task_description,
                        'location' => $task->location->location_name ?? 'Unknown',
                        'assigned_team_id' => $task->assigned_team_id
                    ]);

                    $alertTriggered = true;

                    Log::info("Alert triggered for delayed task", [
                        'task_id' => $task->id,
                        'alert_id' => $alert->id,
                        'delay_minutes' => $delayMinutes
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Task put on hold',
                    'data' => [
                        'task_id' => $task->id,
                        'status' => $task->status,
                        'reason' => $task->on_hold_reason,
                        'delay_minutes' => $delayMinutes,
                        'alert_triggered' => $alertTriggered
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Task put on hold',
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'reason' => $task->on_hold_reason
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to put task on hold", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a task
     * Auto-calculates actual duration
     * Creates performance flag if duration exceeded
     *
     * POST /api/tasks/{taskId}/complete
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeTask($taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            if (!$task->started_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task has not been started yet'
                ], 400);
            }

            // ✅ Auto-calculate actual duration
            $actualDurationMinutes = $task->started_at->diffInMinutes(now());

            $task->update([
                'status' => 'Completed',
                'completed_at' => now(),
                'actual_duration' => $actualDurationMinutes
            ]);

            Log::info("Task completed", [
                'task_id' => $task->id,
                'estimated_duration' => $task->estimated_duration_minutes,
                'actual_duration' => $actualDurationMinutes,
                'completed_at' => $task->completed_at
            ]);

            // ✅ Check if actual duration > estimated (create performance flag)
            $performanceFlagged = false;
            if ($actualDurationMinutes > $task->estimated_duration_minutes) {
                $varianceMinutes = $actualDurationMinutes - $task->estimated_duration_minutes;

                PerformanceFlag::create([
                    'task_id' => $task->id,
                    'team_id' => $task->assigned_team_id,
                    'flag_type' => 'duration_exceeded',
                    'estimated_minutes' => $task->estimated_duration_minutes,
                    'actual_minutes' => $actualDurationMinutes,
                    'variance_minutes' => $varianceMinutes,
                    'flagged_at' => now(),
                    'reviewed' => false
                ]);

                $performanceFlagged = true;

                Log::info("Performance flag created", [
                    'task_id' => $task->id,
                    'variance_minutes' => $varianceMinutes
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully',
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'estimated_duration' => $task->estimated_duration_minutes,
                    'actual_duration' => $actualDurationMinutes,
                    'variance_minutes' => $actualDurationMinutes - $task->estimated_duration_minutes,
                    'completed_at' => $task->completed_at->toDateTimeString(),
                    'performance_flagged' => $performanceFlagged
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to complete task", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get task details for employee
     *
     * GET /api/tasks/{taskId}
     *
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskDetails($taskId)
    {
        try {
            $task = Task::with(['location', 'optimizationTeam.members.employee'])
                ->findOrFail($taskId);

            // Get team members
            $teamMembers = [];
            if ($task->optimizationTeam) {
                $teamMembers = $task->optimizationTeam->members()
                    ->with('employee')
                    ->get()
                    ->map(function($member) {
                        return [
                            'id' => $member->employee->id,
                            'name' => $member->employee->full_name,
                            'has_driving_license' => $member->employee->has_driving_license
                        ];
                    })
                    ->toArray();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'task_id' => $task->id,
                    'description' => $task->task_description,
                    'location' => [
                        'id' => $task->location->id ?? null,
                        'name' => $task->location->location_name ?? 'Unknown'
                    ],
                    'status' => $task->status,
                    'scheduled_date' => $task->scheduled_date->toDateString(),
                    'scheduled_time' => $task->scheduled_time,
                    'estimated_duration' => $task->estimated_duration_minutes,
                    'actual_duration' => $task->actual_duration,
                    'arrival_status' => $task->arrival_status,
                    'started_at' => $task->started_at?->toDateTimeString(),
                    'completed_at' => $task->completed_at?->toDateTimeString(),
                    'on_hold_reason' => $task->on_hold_reason,
                    'team_members' => $teamMembers
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get task details", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }
    }

    /**
     * Get tasks assigned to employee's team for a specific date
     *
     * GET /api/employee/tasks?date=2025-10-20
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployeeTasks(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|integer'
        ]);

        try {
            // Find employee's team for this date
            $employeeId = $request->employee_id;
            $date = $request->date;

            // Get tasks assigned to teams where this employee is a member
            $tasks = Task::whereDate('scheduled_date', $date)
                ->whereHas('optimizationTeam.members', function($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId);
                })
                ->with(['location', 'optimizationTeam.members.employee'])
                ->orderBy('scheduled_time')
                ->get()
                ->map(function($task) {
                    return [
                        'task_id' => $task->id,
                        'description' => $task->task_description,
                        'location' => $task->location->location_name ?? 'Unknown',
                        'status' => $task->status,
                        'scheduled_time' => $task->scheduled_time,
                        'estimated_duration' => $task->estimated_duration_minutes,
                        'arrival_status' => $task->arrival_status,
                        'started_at' => $task->started_at?->toDateTimeString(),
                        'completed_at' => $task->completed_at?->toDateTimeString()
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'tasks' => $tasks,
                    'total_tasks' => $tasks->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get employee tasks", [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tasks'
            ], 500);
        }
    }
}
