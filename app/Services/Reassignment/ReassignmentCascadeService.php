<?php

namespace App\Services\Reassignment;

use App\Models\ClientAppointment;
use App\Models\OptimizationTeam;
use App\Models\Task;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates the rejection cascade:
 *   Try 1 (auto bilateral swap) → Try 2a (auto mid-day free-slot)
 *   → Try 2b (admin-mediated stretch — surfaces candidates only)
 *   → Try 3 (manual — handled by AdminTaskReassignmentController)
 *
 * The orchestrator is invoked AFTER an employee rejection has been
 * recorded (Task.status='Rejected', audit row written). It attempts to
 * resolve the rejection automatically; if it cannot, it leaves the task
 * in 'Rejected' status for admin to handle manually.
 *
 * Notifications fire for every successful auto-resolution so admins can
 * see what happened in their notification feed.
 *
 * See docs/task-rejection-reassignment-policy.md.
 */
class ReassignmentCascadeService
{
    public function __construct(
        protected BilateralSwapFinder $swapFinder,
        protected MidDayGapFinder $gapFinder,
        protected StretchCandidateRanker $stretchRanker,
        protected NotificationService $notifications
    ) {}

    /**
     * Run the auto cascade for a freshly-rejected task. Returns:
     *   [
     *     'resolved' => 'try_1' | 'try_2a' | null,
     *     'stretch_candidates' => array  // only populated if not auto-resolved
     *     'message' => string
     *   ]
     *
     * Caller is responsible for the per-task ceiling check — if the ceiling
     * was already reached, the cascade should NOT be run (admin handles).
     */
    public function runAutoCascade(Task $rejectedTask): array
    {
        // Try 1: bilateral swap
        $swap = $this->swapFinder->findBestSwap($rejectedTask);
        if ($swap) {
            $this->executeSwap($rejectedTask, $swap);
            return [
                'resolved' => 'try_1',
                'stretch_candidates' => [],
                'message' => "Auto-resolved via bilateral swap with team #{$swap['swap_partner_team_id']} (task #{$swap['swap_partner_task_id']}).",
            ];
        }

        // Try 2a: mid-day gap
        $receiver = $this->gapFinder->findBestReceiver($rejectedTask);
        if ($receiver) {
            $this->executeFreeSlot($rejectedTask, $receiver);
            return [
                'resolved' => 'try_2a',
                'stretch_candidates' => [],
                'message' => "Auto-resolved by placing into team #{$receiver['receiver_team_id']}'s mid-day gap.",
            ];
        }

        // Try 2b: stretch candidates surfaced (admin must take action).
        $stretchCandidates = $this->stretchRanker->rank($rejectedTask);

        return [
            'resolved' => null,
            'stretch_candidates' => $stretchCandidates,
            'message' => count($stretchCandidates) > 0
                ? 'No auto-resolution found. ' . count($stretchCandidates) . ' stretch candidate(s) available — admin can offer.'
                : 'No auto-resolution and no stretch candidates available. Admin must handle manually (Try 3).',
        ];
    }

    /**
     * Execute a bilateral swap atomically. Both tasks change assigned_team_id;
     * the rejected task's status is restored from 'Rejected' to 'Scheduled'.
     */
    protected function executeSwap(Task $rejectedTask, array $swap): void
    {
        $partnerTeam = OptimizationTeam::with('members.employee.user')->find($swap['swap_partner_team_id']);
        $partnerTask = Task::find($swap['swap_partner_task_id']);

        if (!$partnerTeam || !$partnerTask) {
            return;
        }

        $originalTeamId = $rejectedTask->assigned_team_id;

        DB::transaction(function () use ($rejectedTask, $partnerTask, $partnerTeam, $originalTeamId) {
            // Rejected task moves to the partner team.
            $rejectedTask->update([
                'assigned_team_id' => $partnerTeam->id,
                'status' => 'Scheduled',
                'reassigned_at' => now(),
                'reassignment_reason' => 'Auto-resolved via bilateral swap (Try 1).',
                'employee_approved' => null,
                'employee_approved_at' => null,
            ]);

            // Partner task moves to the original (rejecting) team.
            $partnerTask->update([
                'assigned_team_id' => $originalTeamId,
                'reassigned_at' => now(),
                'reassignment_reason' => 'Bilateral swap counterpart (Try 1).',
                'employee_approved' => null,
                'employee_approved_at' => null,
            ]);
        });

        // Notify each newly-assigned team member on both sides.
        $this->notifySwapMembers($partnerTeam, $rejectedTask);

        $originalTeam = OptimizationTeam::with('members.employee.user')->find($originalTeamId);
        if ($originalTeam) {
            $this->notifySwapMembers($originalTeam, $partnerTask);
        }

        Log::info('Reassignment cascade Try 1 (bilateral swap) succeeded', [
            'rejected_task_id' => $rejectedTask->id,
            'partner_task_id' => $partnerTask->id,
            'original_team_id' => $originalTeamId,
            'partner_team_id' => $partnerTeam->id,
            'score' => $swap['score'] ?? null,
        ]);
    }

    /**
     * Execute a free-slot placement: assign the rejected task to the receiver
     * team without taking anything from them in return.
     */
    protected function executeFreeSlot(Task $rejectedTask, array $receiver): void
    {
        $receiverTeam = OptimizationTeam::with('members.employee.user')->find($receiver['receiver_team_id']);
        if (!$receiverTeam) {
            return;
        }

        DB::transaction(function () use ($rejectedTask, $receiverTeam) {
            $rejectedTask->update([
                'assigned_team_id' => $receiverTeam->id,
                'status' => 'Scheduled',
                'reassigned_at' => now(),
                'reassignment_reason' => 'Auto-resolved via mid-day free-slot placement (Try 2a).',
                'employee_approved' => null,
                'employee_approved_at' => null,
            ]);
        });

        // Find a matching appointment for richer notifications.
        $appointment = null;
        if ($rejectedTask->client_id && $rejectedTask->scheduled_date) {
            $appointment = ClientAppointment::where('client_id', $rejectedTask->client_id)
                ->whereDate('service_date', $rejectedTask->scheduled_date)
                ->first();
        }

        foreach ($receiverTeam->members as $member) {
            $employeeUser = $member->employee?->user ?? null;
            if ($employeeUser) {
                $this->notifications->notifyEmployeeTaskAssigned($employeeUser, $rejectedTask, $appointment);
            }
        }

        Log::info('Reassignment cascade Try 2a (mid-day gap) succeeded', [
            'rejected_task_id' => $rejectedTask->id,
            'receiver_team_id' => $receiverTeam->id,
            'score' => $receiver['score'] ?? null,
        ]);
    }

    /**
     * Notify each member of a team that a task has been assigned/swapped to them.
     */
    protected function notifySwapMembers(OptimizationTeam $team, Task $task): void
    {
        $appointment = null;
        if ($task->client_id && $task->scheduled_date) {
            $appointment = ClientAppointment::where('client_id', $task->client_id)
                ->whereDate('service_date', $task->scheduled_date)
                ->first();
        }

        foreach ($team->members as $member) {
            $employeeUser = $member->employee?->user ?? null;
            if ($employeeUser) {
                $this->notifications->notifyEmployeeTaskAssigned($employeeUser, $task, $appointment);
            }
        }
    }
}
