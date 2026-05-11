<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientAppointment;
use App\Models\OptimizationTeam;
use App\Models\Task;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AdminTaskReassignmentController
 *
 * Implements Try 3 (Manual) of the rejection cascade — see
 * docs/task-rejection-reassignment-policy.md.
 *
 * Lets an admin look up rejected tasks, see candidate teams to reassign to,
 * and commit a reassignment. The auto cascade (Try 1 / 2a / 2b) is not yet
 * implemented; this is the human-fallback path.
 */
class AdminTaskReassignmentController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * GET /api/admin/tasks/rejected
     *
     * List tasks currently in 'Rejected' status. Admins land here from
     * rejection notifications, or from a future "Rejected Tasks" panel.
     */
    public function listRejected(Request $request)
    {
        $tasks = Task::where('status', 'Rejected')
            ->with([
                'location',
                'client',
                'optimizationTeam',
                'rejections' => function ($q) {
                    $q->latest('rejected_at');
                },
                'rejections.employee.user',
            ])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->limit(100)
            ->get();

        $ceiling = (int) config('rejection.per_task_ceiling', 3);

        $payload = $tasks->map(function (Task $task) use ($ceiling) {
            return [
                'task_id' => $task->id,
                'task_description' => $task->task_description,
                'scheduled_date' => $task->scheduled_date?->format('Y-m-d'),
                'scheduled_time' => $task->getRawOriginal('scheduled_time'),
                'estimated_duration_minutes' => $task->estimated_duration_minutes,
                'location' => $task->location?->location_name,
                'client_name' => $task->client
                    ? trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? ''))
                    : null,
                'previous_team_id' => $task->assigned_team_id,
                'previous_team_name' => $task->optimizationTeam?->team_name,
                'rejection_count' => (int) ($task->rejection_count ?? 0),
                'rejection_ceiling' => $ceiling,
                'ceiling_reached' => (int) ($task->rejection_count ?? 0) >= $ceiling,
                'last_rejection' => optional($task->rejections->first(), function ($r) {
                    return [
                        'reason' => $r->reason,
                        'rejected_at' => $r->rejected_at?->toIso8601String(),
                        'employee_name' => $r->employee?->user?->name,
                    ];
                }),
                'rejection_history' => $task->rejections->map(fn ($r) => [
                    'reason' => $r->reason,
                    'rejected_at' => $r->rejected_at?->toIso8601String(),
                    'employee_name' => $r->employee?->user?->name,
                ])->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $payload,
            'count' => $payload->count(),
        ]);
    }

    /**
     * GET /api/admin/tasks/{taskId}/reassignment-options
     *
     * For a single rejected task, return the list of OptimizationTeams that
     * could potentially take it. Filter rules:
     *   - Same service_date as the task.
     *   - Not the team that was previously assigned (avoids re-rejection loop).
     *   - Active members ≥ 2 preferred (we still surface understaffed teams,
     *     but flag them so admin sees the warning).
     *
     * No hard scoring/ranking — admin makes the call. We attach metadata
     * (member count, current task load) so the picker UI can render context.
     */
    public function reassignmentOptions(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->status !== 'Rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Reassignment options are only available for tasks in Rejected status.',
                'error_code' => 'INVALID_STATUS',
                'current_status' => $task->status,
            ], 400);
        }

        $teams = OptimizationTeam::where('service_date', $task->scheduled_date)
            ->where('id', '!=', $task->assigned_team_id)
            ->with(['members.employee.user', 'tasks'])
            ->get();

        $payload = $teams->map(function (OptimizationTeam $team) {
            $activeCount = $team->activeMemberCount();
            return [
                'team_id' => $team->id,
                'team_name' => $team->team_name,
                'service_date' => $team->service_date?->format('Y-m-d'),
                'staffing_status' => $team->staffing_status,
                'active_member_count' => $activeCount,
                'understaffed' => $activeCount < 2,
                'current_task_count' => $team->tasks->count(),
                'members' => $team->members->map(function ($m) {
                    return [
                        'employee_id' => $m->employee_id,
                        'name' => $m->employee?->user?->name,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'task' => [
                'id' => $task->id,
                'description' => $task->task_description,
                'scheduled_date' => $task->scheduled_date?->format('Y-m-d'),
                'scheduled_time' => $task->getRawOriginal('scheduled_time'),
                'previous_team_id' => $task->assigned_team_id,
            ],
            'options' => $payload,
            'count' => $payload->count(),
        ]);
    }

    /**
     * POST /api/admin/tasks/{taskId}/reassign
     * Body: { "team_id": <int>, "note": "<optional admin note>" }
     *
     * Commits a manual reassignment of a Rejected task to a different team.
     * Effects:
     *   - task.assigned_team_id replaced
     *   - task.status: 'Rejected' → 'Scheduled'
     *   - task.reassigned_at, reassignment_reason set
     *   - Each member of the new team is notified (notifyEmployeeTaskAssigned)
     *
     * Note: rejection_count and rejection_reason are intentionally preserved
     * as historical record. The per-task ceiling still applies on subsequent
     * rejections, so a task that already hit the ceiling cannot bounce again.
     */
    public function reassign(Request $request, $taskId)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:optimization_teams,id',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $task = Task::findOrFail($taskId);

            if ($task->status !== 'Rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only tasks in Rejected status can be reassigned through this endpoint.',
                    'error_code' => 'INVALID_STATUS',
                    'current_status' => $task->status,
                ], 400);
            }

            $newTeam = OptimizationTeam::with(['members.employee.user'])
                ->findOrFail($validated['team_id']);

            // Sanity: same service_date — admin shouldn't accidentally move a
            // task across days (would silently break the schedule for that day).
            if ($task->scheduled_date && $newTeam->service_date
                && $task->scheduled_date->format('Y-m-d') !== $newTeam->service_date->format('Y-m-d')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected team is for a different service date.',
                    'error_code' => 'DATE_MISMATCH',
                    'task_date' => $task->scheduled_date->format('Y-m-d'),
                    'team_date' => $newTeam->service_date->format('Y-m-d'),
                ], 422);
            }

            $note = trim((string) ($validated['note'] ?? ''));
            $reasonText = $note !== ''
                ? "Manual reassignment by admin: {$note}"
                : 'Manual reassignment by admin (post-rejection).';

            $previousTeamId = $task->assigned_team_id;

            DB::transaction(function () use ($task, $newTeam, $reasonText) {
                $task->update([
                    'assigned_team_id' => $newTeam->id,
                    'status' => 'Scheduled',
                    'reassigned_at' => now(),
                    'reassignment_reason' => $reasonText,
                    // Reset employee approval state — the new team's members
                    // get a fresh decision window.
                    'employee_approved' => null,
                    'employee_approved_at' => null,
                ]);
            });

            $task->refresh();

            // Look up an associated appointment for richer notifications
            // (cleanest match: same client + same service date).
            $appointment = null;
            if ($task->client_id && $task->scheduled_date) {
                $appointment = ClientAppointment::where('client_id', $task->client_id)
                    ->whereDate('service_date', $task->scheduled_date)
                    ->first();
            }

            // Notify each newly-assigned team member.
            $notifiedCount = 0;
            foreach ($newTeam->members as $member) {
                $employeeUser = $member->employee?->user ?? null;
                if ($employeeUser) {
                    $this->notificationService->notifyEmployeeTaskAssigned(
                        $employeeUser,
                        $task,
                        $appointment
                    );
                    $notifiedCount++;
                }
            }

            Log::info('Task manually reassigned by admin', [
                'task_id' => $task->id,
                'previous_team_id' => $previousTeamId,
                'new_team_id' => $newTeam->id,
                'admin_user_id' => Auth::id(),
                'note' => $note,
                'notified_employees' => $notifiedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Task reassigned to {$newTeam->team_name}. {$notifiedCount} employee(s) notified.",
                'data' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'previous_team_id' => $previousTeamId,
                    'new_team_id' => $newTeam->id,
                    'new_team_name' => $newTeam->team_name,
                    'notified_employees' => $notifiedCount,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to reassign task', [
                'task_id' => $taskId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reassign task: ' . $e->getMessage(),
            ], 500);
        }
    }
}
