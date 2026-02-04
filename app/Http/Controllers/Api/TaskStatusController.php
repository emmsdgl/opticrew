<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\PerformanceFlag;
use App\Models\Attendance;
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

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Get alert threshold from config
     */
    protected function getAlertThreshold(): int
    {
        return config('optimization.alerts.on_hold_threshold_minutes', 30);
    }

    /**
     * Check if the authenticated user's employee is currently clocked in
     *
     * @param Request $request
     * @return bool
     */
    protected function isEmployeeClockedIn(Request $request): bool
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return false;
        }

        // Check if there's an attendance record for today with clock_in but no clock_out
        $todayAttendance = Attendance::where('employee_id', $user->employee->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->first();

        return $todayAttendance !== null;
    }

    /**
     * Return unauthorized response for employees not clocked in
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function clockInRequiredResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'You must be clocked in to perform this action',
            'error_code' => 'CLOCK_IN_REQUIRED'
        ], 403);
    }

    /**
     * Start a task (set status to "In Progress")
     *
     * POST /api/tasks/{taskId}/start
     *
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function startTask(Request $request, $taskId)
    {
        // Security check: Employee must be clocked in
        if (!$this->isEmployeeClockedIn($request)) {
            return $this->clockInRequiredResponse();
        }

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
        // Security check: Employee must be clocked in
        if (!$this->isEmployeeClockedIn($request)) {
            return $this->clockInRequiredResponse();
        }

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

                // ✅ Trigger alert if delay exceeds threshold
                $alertTriggered = false;
                if ($delayMinutes > $this->getAlertThreshold()) {
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
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeTask(Request $request, $taskId)
    {
        // Security check: Employee must be clocked in
        if (!$this->isEmployeeClockedIn($request)) {
            return $this->clockInRequiredResponse();
        }

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
            $task = Task::with(['location.contractedClient', 'optimizationTeam.members.employee'])
                ->findOrFail($taskId);

            // Get team members
            $teamMembers = [];
            if ($task->optimizationTeam) {
                $teamMembers = $task->optimizationTeam->members()
                    ->with('employee.user')
                    ->get()
                    ->map(function($member) {
                        return [
                            'id' => $member->employee->id,
                            'name' => $member->employee->user->name,
                            'has_driving_license' => $member->employee->has_driving_license
                        ];
                    })
                    ->toArray();
            }

            // Get contracted client name from location
            $clientName = $task->location && $task->location->contractedClient
                ? $task->location->contractedClient->name
                : ($task->location->location_name ?? 'Unknown Client');

            return response()->json([
                'success' => true,
                'data' => [
                    'task_id' => $task->id,
                    'description' => $task->task_description,
                    'location' => [
                        'id' => $task->location->id ?? null,
                        'name' => $task->location->location_name ?? 'Unknown'
                    ],
                    'client_name' => $clientName,
                    'status' => $task->status,
                    'scheduled_date' => $task->scheduled_date->toDateString(),
                    'scheduled_time' => $task->scheduled_time,
                    'estimated_duration' => $task->estimated_duration_minutes,
                    'actual_duration' => $task->actual_duration,
                    'arrival_status' => $task->arrival_status,
                    'started_at' => $task->started_at?->toDateTimeString(),
                    'completed_at' => $task->completed_at?->toDateTimeString(),
                    'on_hold_reason' => $task->on_hold_reason,
                    'team_members' => $teamMembers,
                    'notes' => $task->on_hold_reason
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

    /**
     * Get tasks for schedule (Mobile App)
     * Uses authenticated user's employee_id automatically
     *
     * GET /api/schedule/tasks?date=2025-10-20
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getScheduleTasks(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            // Get authenticated user
            $user = $request->user();

            // Get employee record from user
            if (!$user->employee) {
                return response()->json([
                    'tasks' => []
                ], 200);
            }

            $employeeId = $user->employee->id;
            $date = $request->date;

            // Get tasks assigned to teams where this employee is a member
            $tasks = Task::whereDate('scheduled_date', $date)
                ->whereHas('optimizationTeam.members', function($query) use ($employeeId) {
                    $query->where('employee_id', $employeeId);
                })
                ->with(['location.contractedClient', 'optimizationTeam.members.employee.user'])
                ->orderBy('scheduled_time')
                ->get()
                ->map(function($task) {
                    // Format time (remove seconds)
                    $startTime = $task->scheduled_time
                        ? Carbon::parse($task->scheduled_time)->format('H:i')
                        : '09:00';

                    // Get employee names from team members
                    $employeeNames = 'Unassigned';
                    $companionsCount = 0;
                    if ($task->optimizationTeam && $task->optimizationTeam->members) {
                        $names = $task->optimizationTeam->members
                            ->map(function($member) {
                                return $member->employee && $member->employee->user
                                    ? $member->employee->user->name
                                    : null;
                            })
                            ->filter()
                            ->toArray();

                        if (!empty($names)) {
                            $employeeNames = implode(', ', $names);
                            $companionsCount = count($names);
                        }
                    }

                    // Get company name from location's contracted client
                    $companyName = $task->location && $task->location->contractedClient
                        ? $task->location->contractedClient->name
                        : null;

                    return [
                        'id' => $task->id,
                        'title' => $task->task_description,
                        'name' => $task->task_description, // Alias for title
                        'description' => $task->task_description,
                        'location' => $task->location->location_name ?? null,
                        'company_name' => $companyName,
                        'status' => $task->status,
                        'scheduled_date' => $task->scheduled_date->toDateString(),
                        'scheduled_time' => $task->scheduled_time,
                        'start_time' => $startTime,
                        'duration' => $task->estimated_duration_minutes
                            ? round($task->estimated_duration_minutes / 60, 1)
                            : null,
                        'duration_minutes' => $task->estimated_duration_minutes,
                        'team' => $employeeNames,
                        'companions' => $companionsCount,
                        'arrival_status' => $task->arrival_status,
                    ];
                });

            return response()->json([
                'tasks' => $tasks
            ], 200);

        } catch (\Exception $e) {
            Log::error("Failed to get schedule tasks", [
                'user_id' => $request->user()->id ?? null,
                'date' => $request->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'tasks' => [],
                'error' => 'Failed to retrieve tasks'
            ], 500);
        }
    }

    /**
     * Upload task photo (before/after)
     *
     * POST /api/tasks/{taskId}/photo
     * Body: photo (file), type (before/after)
     *
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadTaskPhoto(Request $request, $taskId)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // Max 5MB
            'type' => 'required|in:before,after'
        ]);

        try {
            $task = Task::findOrFail($taskId);

            // Store the photo
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = 'task_' . $taskId . '_' . $request->type . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('task_photos', $filename, 'public');

                // Update task with photo path (you may need to add these columns to your tasks table)
                $columnName = $request->type === 'before' ? 'before_photo' : 'after_photo';
                $task->update([
                    $columnName => $path
                ]);

                Log::info("Task photo uploaded", [
                    'task_id' => $taskId,
                    'type' => $request->type,
                    'path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => ucfirst($request->type) . ' photo uploaded successfully',
                    'data' => [
                        'task_id' => $taskId,
                        'photo_type' => $request->type,
                        'photo_path' => $path
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo file provided'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Failed to upload task photo", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checklist for a task
     * Returns the company's checklist with completion status for this task
     *
     * GET /api/tasks/{taskId}/checklist
     */
    public function getTaskChecklist($taskId)
    {
        try {
            $task = Task::with('location.contractedClient')->findOrFail($taskId);

            // Get the contracted client's checklist
            $contractedClientId = $task->location->contracted_client_id;

            $checklist = \App\Models\CompanyChecklist::where('contracted_client_id', $contractedClientId)
                ->where('is_active', true)
                ->with(['categories.items'])
                ->first();

            if (!$checklist) {
                return response()->json([
                    'success' => true,
                    'checklist' => null,
                    'message' => 'No checklist configured for this location'
                ]);
            }

            // Get existing completions for this task
            $completions = \App\Models\TaskChecklistCompletion::where('task_id', $taskId)
                ->get()
                ->keyBy('checklist_item_id');

            // Build the response with completion status
            $checklistData = [
                'id' => $checklist->id,
                'name' => $checklist->name,
                'important_reminders' => $checklist->important_reminders,
                'categories' => $checklist->categories->map(function ($category) use ($completions) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'sort_order' => $category->sort_order,
                        'items' => $category->items->map(function ($item) use ($completions) {
                            $completion = $completions->get($item->id);
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'quantity' => $item->quantity,
                                'sort_order' => $item->sort_order,
                                'is_completed' => $completion ? $completion->is_completed : false,
                                'completed_at' => $completion ? $completion->completed_at : null,
                            ];
                        }),
                    ];
                }),
            ];

            // Calculate completion stats
            $totalItems = 0;
            $completedItems = 0;
            foreach ($checklistData['categories'] as $category) {
                foreach ($category['items'] as $item) {
                    $totalItems++;
                    if ($item['is_completed']) {
                        $completedItems++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'checklist' => $checklistData,
                'stats' => [
                    'total' => $totalItems,
                    'completed' => $completedItems,
                    'percentage' => $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to get task checklist", [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load checklist'
            ], 500);
        }
    }

    /**
     * Toggle a checklist item's completion status
     *
     * POST /api/tasks/{taskId}/checklist/{itemId}/toggle
     */
    public function toggleChecklistItem(Request $request, $taskId, $itemId)
    {
        // Security check: Employee must be clocked in
        if (!$this->isEmployeeClockedIn($request)) {
            return $this->clockInRequiredResponse();
        }

        try {
            $task = Task::findOrFail($taskId);
            $item = \App\Models\ChecklistItem::findOrFail($itemId);
            $user = $request->user();

            // Find or create the completion record
            $completion = \App\Models\TaskChecklistCompletion::firstOrNew([
                'task_id' => $taskId,
                'checklist_item_id' => $itemId,
            ]);

            // Toggle the completion status
            $completion->is_completed = !$completion->is_completed;

            if ($completion->is_completed) {
                $completion->completed_by = $user->id;
                $completion->completed_at = now();
            } else {
                $completion->completed_by = null;
                $completion->completed_at = null;
            }

            $completion->save();

            return response()->json([
                'success' => true,
                'item_id' => $itemId,
                'is_completed' => $completion->is_completed,
                'completed_at' => $completion->completed_at,
                'message' => $completion->is_completed ? 'Item marked as completed' : 'Item marked as incomplete'
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to toggle checklist item", [
                'task_id' => $taskId,
                'item_id' => $itemId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update checklist item'
            ], 500);
        }
    }
}
