<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskRejection;
use App\Models\PerformanceFlag;
use App\Models\Attendance;
use App\Models\Feedback;
use App\Services\Alert\AlertService;
use App\Services\Notification\NotificationService;
use App\Services\Reassignment\MassRejectionDetector;
use App\Services\Reassignment\ReassignmentCascadeService;
use Illuminate\Support\Facades\DB;
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
    protected NotificationService $notificationService;
    protected ReassignmentCascadeService $cascade;
    protected MassRejectionDetector $massDetector;

    public function __construct(
        AlertService $alertService,
        NotificationService $notificationService,
        ReassignmentCascadeService $cascade,
        MassRejectionDetector $massDetector
    ) {
        $this->alertService = $alertService;
        $this->notificationService = $notificationService;
        $this->cascade = $cascade;
        $this->massDetector = $massDetector;
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
     * Reject a task (preference-based rejection — "I'd prefer a different task").
     *
     * POST /api/tasks/{taskId}/reject
     * Body: { "reason": "<key from config('rejection.allowed_reasons')>" }
     *
     * Enforces:
     *   - Task must be Pending or Scheduled.
     *   - Reject window: must be at least N hours before task start
     *     (config: rejection.window_hours_before_start, default 24).
     *     Past the window, employees must use the emergency-leave flow.
     *   - Monthly budget: employee may reject at most N times per calendar month
     *     (config: rejection.monthly_budget, default 3).
     *   - Reason must be one of the keys in config('rejection.allowed_reasons').
     *
     * On success:
     *   - Task: status='Rejected', employee_approved=false, rejection_reason set,
     *     rejection_count incremented.
     *   - Audit row written to task_rejections.
     *   - Admins notified (notifyAdminsTaskRejected).
     *   - If the task hits the per-task ceiling, admins+managers receive a
     *     high-priority follow-up notification.
     *
     * Cascade (Try 1 / 2a / 2b / 3) is intentionally NOT invoked here yet —
     * see docs/task-rejection-reassignment-policy.md §6 for the implementation
     * roadmap. For now, rejection is visible to admin via notification and the
     * existing TaskApprovalObserver still runs.
     */
    public function rejectTask(Request $request, $taskId)
    {
        $allowedReasonKeys = array_keys(config('rejection.allowed_reasons', []));

        $validated = $request->validate([
            'reason' => 'required|string|in:' . implode(',', $allowedReasonKeys),
            'reason_text' => 'nullable|string|max:500',
        ]);

        try {
            $task = Task::findOrFail($taskId);

            // Status gate.
            if (!in_array($task->status, ['Scheduled', 'Pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending or scheduled tasks can be rejected.',
                    'error_code' => 'INVALID_STATUS',
                ], 400);
            }

            // Resolve the rejecting employee from the authenticated user.
            $user = $request->user();
            $employee = $user ? $user->employee : null;
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record linked to the current user.',
                    'error_code' => 'NO_EMPLOYEE',
                ], 403);
            }

            // Window check: at least window_hours_before_start before task start.
            $windowHours = (int) config('rejection.window_hours_before_start', 24);
            $taskStart = $this->resolveTaskStart($task);
            if ($taskStart && now()->diffInMinutes($taskStart, false) < ($windowHours * 60)) {
                return response()->json([
                    'success' => false,
                    'message' => "Rejection window closed. Rejections must arrive at least {$windowHours}h before task start. For genuine impediments, please file an emergency leave.",
                    'error_code' => 'WINDOW_CLOSED',
                    'task_start' => $taskStart->toIso8601String(),
                    'window_hours' => $windowHours,
                ], 422);
            }

            // Budget check.
            $budgetRemaining = $employee->rejectionBudgetRemaining();
            if ($budgetRemaining <= 0) {
                $budget = (int) config('rejection.monthly_budget', 3);
                return response()->json([
                    'success' => false,
                    'message' => "Monthly rejection budget used ({$budget}/{$budget}). For genuine impediments, please file an emergency leave.",
                    'error_code' => 'BUDGET_EXHAUSTED',
                    'budget' => $budget,
                    'budget_remaining' => 0,
                ], 422);
            }

            // Compose human-readable reason: combine the picked-list label with optional free text.
            $reasonLabel = config("rejection.allowed_reasons.{$validated['reason']}", $validated['reason']);
            $reasonText = trim((string) ($validated['reason_text'] ?? ''));
            $reasonForRecord = $reasonText !== ''
                ? "{$reasonLabel}: {$reasonText}"
                : $reasonLabel;

            $newRejectionCount = ($task->rejection_count ?? 0) + 1;
            $ceiling = (int) config('rejection.per_task_ceiling', 3);

            // Atomically update the task and write the audit row.
            DB::transaction(function () use ($task, $employee, $reasonForRecord, $newRejectionCount) {
                $task->update([
                    'status' => 'Rejected',
                    'employee_approved' => false,
                    'employee_approved_at' => now(),
                    'rejection_reason' => $reasonForRecord,
                    'rejection_count' => $newRejectionCount,
                ]);

                TaskRejection::create([
                    'task_id' => $task->id,
                    'employee_id' => $employee->id,
                    'reason' => $reasonForRecord,
                    'rejected_at' => now(),
                ]);
            });

            // Refresh from DB so the notification sees the latest rejection_count.
            $task->refresh();

            // Notifications: always tell admins this happened.
            $employeeName = trim(($user->name ?? '') ?: ($user->email ?? "Employee #{$employee->id}"));
            $newBudgetRemaining = max(0, $budgetRemaining - 1);

            $this->notificationService->notifyAdminsTaskRejected(
                $task,
                $employeeName,
                $reasonForRecord,
                $newBudgetRemaining,
                $newRejectionCount
            );

            // Per-task ceiling: high-priority follow-up to admin + manager.
            $ceilingReached = $newRejectionCount >= $ceiling;
            if ($ceilingReached) {
                $this->notificationService->notifyAdminsTaskRejectionCeilingReached($task);
            }

            // Mass-rejection meta-trigger: if too many rejections in the
            // window, pause the per-rejection cascade and let admin decide
            // whether to re-run optimization globally.
            $massSnapshot = $this->massDetector->evaluate();
            if ($massSnapshot['tripped']) {
                $this->notificationService->notifyAdminsMassRejectionTripped($massSnapshot);
            }

            // Auto cascade (Try 1 → Try 2a → surface Try 2b candidates).
            // Skip if the per-task ceiling is already reached OR if mass
            // rejection has tripped — those cases require admin attention.
            $cascadeResult = ['resolved' => null, 'stretch_candidates' => [], 'message' => 'Cascade skipped.'];
            if (!$ceilingReached && !$massSnapshot['tripped']) {
                $cascadeResult = $this->cascade->runAutoCascade($task);

                if ($cascadeResult['resolved']) {
                    // Notify admins that the cascade auto-resolved (FYI).
                    $resolutionLabel = $cascadeResult['resolved'] === 'try_1'
                        ? 'Try 1 (bilateral swap)'
                        : 'Try 2a (mid-day free-slot)';
                    $this->notificationService->notifyAdminsCascadeAutoResolved(
                        $task,
                        $resolutionLabel,
                        $cascadeResult['message']
                    );
                }
            }

            Log::info('Task rejected by employee', [
                'task_id' => $task->id,
                'employee_id' => $employee->id,
                'reason' => $reasonForRecord,
                'rejection_count' => $newRejectionCount,
                'ceiling_reached' => $ceilingReached,
                'mass_rejection_tripped' => $massSnapshot['tripped'],
                'budget_remaining' => $newBudgetRemaining,
                'cascade_resolved' => $cascadeResult['resolved'],
            ]);

            // User-facing message reflects the most relevant outcome.
            $message = match (true) {
                $massSnapshot['tripped'] =>
                    'Task rejected. Mass-rejection threshold tripped — admin will re-optimize.',
                $ceilingReached =>
                    'Task rejected. Per-task ceiling reached — admin will handle manually.',
                $cascadeResult['resolved'] === 'try_1' =>
                    'Task rejected and auto-resolved via bilateral swap.',
                $cascadeResult['resolved'] === 'try_2a' =>
                    'Task rejected and auto-resolved by placing into another team\'s mid-day gap.',
                count($cascadeResult['stretch_candidates']) > 0 =>
                    'Task rejected. No auto-resolution — admin can offer the task to a stretch candidate.',
                default =>
                    'Task rejected. No auto-resolution available — admin will handle manually.',
            };

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->fresh()->status, // 'Rejected' or 'Scheduled' if cascade resolved
                    'rejection_count' => $newRejectionCount,
                    'ceiling' => $ceiling,
                    'ceiling_reached' => $ceilingReached,
                    'budget_remaining' => $newBudgetRemaining,
                    'mass_rejection_tripped' => $massSnapshot['tripped'],
                    'cascade' => [
                        'resolved' => $cascadeResult['resolved'],
                        'stretch_candidate_count' => count($cascadeResult['stretch_candidates']),
                        'detail' => $cascadeResult['message'],
                    ],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to reject task', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject task: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Combine task.scheduled_date + task.scheduled_time into a single Carbon
     * representing when the task is supposed to start. Returns null if either
     * field is missing — in which case the window check is skipped (we can't
     * compare against an unknown start time).
     */
    protected function resolveTaskStart(Task $task): ?Carbon
    {
        if (!$task->scheduled_date) {
            return null;
        }

        $dateStr = $task->scheduled_date instanceof \DateTimeInterface
            ? $task->scheduled_date->format('Y-m-d')
            : (string) $task->scheduled_date;

        $rawTime = $task->getRawOriginal('scheduled_time');
        if (!$rawTime) {
            // No time-of-day — fall back to start-of-day for the window check.
            return Carbon::parse($dateStr)->startOfDay();
        }

        return Carbon::parse("{$dateStr} {$rawTime}");
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
                ->map(function($task) use ($employeeId) {
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

                    // Check if this employee has already rated this task
                    $hasRated = Feedback::where('task_id', $task->id)
                        ->where('employee_id', $employeeId)
                        ->where('user_type', 'employee')
                        ->exists();

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
                        'has_rated' => $hasRated,
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

    /**
     * Submit employee feedback for a completed task
     *
     * POST /api/tasks/{taskId}/feedback
     * Body: {
     *   "rating": 5,
     *   "tags": ["Task clarity", "Enough Time"],
     *   "report_type": "Equipment Issue" (optional),
     *   "comment": "Detailed review text" (optional)
     * }
     *
     * @param Request $request
     * @param int $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitTaskFeedback(Request $request, $taskId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'report_type' => 'nullable|string|max:100',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $task = Task::findOrFail($taskId);
            $user = $request->user();

            // Ensure task is completed
            if ($task->status !== 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Feedback can only be submitted for completed tasks'
                ], 400);
            }

            // Get employee record
            $employee = $user->employee;
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee record not found'
                ], 403);
            }

            // Check if feedback already exists for this task by this employee
            $existingFeedback = Feedback::where('task_id', $taskId)
                ->where('employee_id', $employee->id)
                ->where('user_type', 'employee')
                ->first();

            if ($existingFeedback) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted feedback for this task'
                ], 409);
            }

            // Create feedback record using the new modal feedback fields
            $feedback = Feedback::create([
                'task_id' => $taskId,
                'employee_id' => $employee->id,
                'user_type' => 'employee',
                'rating' => $request->rating,
                'keywords' => $request->tags ?? [],
                'feedback_text' => $request->comment,
                'service_type' => $request->report_type, // Store report type if provided
            ]);

            Log::info("Employee task feedback submitted", [
                'task_id' => $taskId,
                'employee_id' => $employee->id,
                'rating' => $request->rating,
                'tags' => $request->tags,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully',
                'data' => [
                    'feedback_id' => $feedback->id,
                    'task_id' => $taskId,
                    'rating' => $feedback->rating,
                    'tags' => $feedback->keywords,
                    'comment' => $feedback->feedback_text,
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error("Failed to submit task feedback", [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback: ' . $e->getMessage()
            ], 500);
        }
    }
}
