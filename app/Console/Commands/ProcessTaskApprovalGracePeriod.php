<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\OptimizationTeamMember;
use App\Models\User;
use App\Services\Notification\NotificationService;
use App\Services\CompanySettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #19: Task Approval Grace Period
 * If employee doesn't approve the task within the grace period,
 * task is flagged as UNSTAFFED and admin must manually assign.
 *
 * SCENARIO #21: Task Reassignment for late clock-in
 * If employee is running late and hasn't clocked in by the time their task
 * should start, the system notifies admin and may auto-reassign.
 *
 * IMMINENT-DECLINE AUTO-REASSIGN: when a declined task is starting within
 * the grace period (default 30 min), don't wait for admin — find a free
 * employee and swap them in so the task isn't left "inactive" at start time.
 * Long-lead declines stay manual (admin retains control).
 */
class ProcessTaskApprovalGracePeriod extends Command
{
    protected $signature = 'opticrew:process-task-grace-periods';
    protected $description = 'Process task approval grace period, late clock-in, and imminent-decline auto-reassign (Scenarios #19, #21)';

    public function handle(NotificationService $notificationService)
    {
        $gracePeriod = CompanySettingService::get('task_approval_grace_period_minutes', 30);
        $today = Carbon::today();

        // SCENARIO #19: Find tasks that were assigned but not approved within grace period
        // Only flag tasks whose scheduled date has already passed (past tasks).
        // Today's and future tasks stay in "To Be Approved" so employees can still act on them.
        $expiredApprovalTasks = Task::whereNull('employee_approved')
            ->whereNotNull('assigned_team_id')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->whereDate('scheduled_date', '<', $today)
            ->get()
            ->filter(function ($task) use ($gracePeriod) {
                // Check if grace period has passed since assignment
                $assignedAt = $task->updated_at; // When team was last assigned
                return $assignedAt && $assignedAt->diffInMinutes(now()) >= $gracePeriod;
            });

        foreach ($expiredApprovalTasks as $task) {
            // Check if we already sent a notification
            $alreadyNotified = Notification::where('type', Notification::TYPE_TASK_UNSTAFFED)
                ->whereJsonContains('data->task_id', $task->id)
                ->exists();

            if (!$alreadyNotified) {
                // Flag task as UNSTAFFED
                $task->update([
                    'employee_approved' => false,
                    'status' => 'Pending',
                ]);

                // Notify admins
                $notificationService->notifyAdminsCriticalWarning(
                    $task,
                    "Task \"{$task->task_description}\" was not approved within {$gracePeriod} minutes. Task is now UNSTAFFED. Admin should manually assign."
                );

                Log::warning('Task approval grace period expired', [
                    'task_id' => $task->id,
                    'grace_period_minutes' => $gracePeriod,
                ]);

                $this->warn("Task #{$task->id}: Approval expired. Marked as UNSTAFFED.");
            }
        }

        // SCENARIO #21: Check for late clock-ins on today's tasks
        $this->processLateClockIns($notificationService, $today);

        // IMMINENT-DECLINE AUTO-REASSIGN
        $reassignedCount = $this->processImminentDeclines($notificationService, $today, $gracePeriod);

        $this->info("Processed {$expiredApprovalTasks->count()} expired approval tasks; {$reassignedCount} imminent-decline auto-reassigns.");
    }

    /**
     * Find tasks that were declined and are starting within the imminent
     * window (now to now + grace), and auto-reassign each to an employee
     * with no pending tasks today. If no replacement is available, fall
     * back to a CRITICAL_ESCALATION alert so admin sees the danger.
     */
    protected function processImminentDeclines(NotificationService $ns, Carbon $today, int $gracePeriod): int
    {
        $now = Carbon::now();
        $cutoffEnd = $now->copy()->addMinutes($gracePeriod);

        $imminent = Task::whereDate('scheduled_date', $today)
            ->where('employee_approved', false)
            ->whereNotNull('scheduled_time')
            ->whereNotNull('assigned_team_id')
            ->whereNotNull('approved_by')
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->get()
            ->filter(function ($task) use ($now, $cutoffEnd) {
                $start = Carbon::parse($task->scheduled_date)->setTimeFromTimeString(
                    Carbon::parse($task->scheduled_time)->format('H:i:s')
                );
                return $start->gte($now) && $start->lte($cutoffEnd);
            });

        $reassigned = 0;

        foreach ($imminent as $task) {
            $declinerUser = User::find($task->approved_by);
            $decliner = $declinerUser?->employee;
            if (!$decliner) {
                Log::warning('Imminent decline auto-reassign: decliner has no employee record', [
                    'task_id' => $task->id,
                    'approved_by_user_id' => $task->approved_by,
                ]);
                continue;
            }

            $replacement = $this->findAvailableReplacement($decliner->id, $today);

            if (!$replacement) {
                $ns->notifyAdminsCriticalEscalation(
                    $task,
                    "Task \"{$task->task_description}\" is starting within {$gracePeriod} min and was declined, but no available replacement was found. Manual intervention required."
                );
                Log::warning('Imminent decline auto-reassign skipped: no replacement available', [
                    'task_id' => $task->id,
                    'decliner_employee_id' => $decliner->id,
                ]);
                continue;
            }

            try {
                DB::transaction(function () use ($task, $decliner, $replacement) {
                    OptimizationTeamMember::where('optimization_team_id', $task->assigned_team_id)
                        ->where('employee_id', $decliner->id)
                        ->delete();

                    $alreadyOnTeam = OptimizationTeamMember::where('optimization_team_id', $task->assigned_team_id)
                        ->where('employee_id', $replacement->id)
                        ->exists();
                    if (!$alreadyOnTeam) {
                        OptimizationTeamMember::create([
                            'optimization_team_id' => $task->assigned_team_id,
                            'employee_id' => $replacement->id,
                        ]);
                    }

                    $task->update([
                        'employee_approved' => true,
                        'employee_approved_at' => Carbon::now(),
                        'approved_by' => $replacement->user_id,
                        'status' => 'Scheduled',
                    ]);
                });

                $ns->notifyAdminDeclineAutoReassigned($task, $decliner, $replacement);

                Log::warning('Imminent decline auto-reassigned', [
                    'task_id' => $task->id,
                    'team_id' => $task->assigned_team_id,
                    'decliner_employee_id' => $decliner->id,
                    'replacement_employee_id' => $replacement->id,
                ]);

                $this->warn("Task #{$task->id}: imminent decline → auto-reassigned to employee #{$replacement->id}.");
                $reassigned++;
            } catch (\Throwable $e) {
                Log::error('Imminent decline auto-reassign failed', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $reassigned;
    }

    /**
     * Find an active employee with no pending/scheduled tasks today, not on
     * approved leave, not soft-deleted. Excludes the decliner. Returns null
     * if nobody qualifies.
     */
    protected function findAvailableReplacement(int $excludeEmployeeId, Carbon $date): ?Employee
    {
        $busyTeamIds = Task::whereDate('scheduled_date', $date)
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->whereNotNull('assigned_team_id')
            ->pluck('assigned_team_id')
            ->unique()
            ->toArray();

        $busyEmployeeIds = empty($busyTeamIds)
            ? []
            : OptimizationTeamMember::whereIn('optimization_team_id', $busyTeamIds)
                ->pluck('employee_id')
                ->unique()
                ->toArray();

        $busyEmployeeIds[] = $excludeEmployeeId;

        return Employee::where('is_active', true)
            ->whereNotIn('id', $busyEmployeeIds)
            ->whereDoesntHave('dayOffs', fn($q) =>
                $q->where('date', '<=', $date)
                  ->where(function ($sub) use ($date) {
                      $sub->where('end_date', '>=', $date)
                          ->orWhereNull('end_date');
                  })
            )
            ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
            ->first();
    }

    /**
     * SCENARIO #21 & #22: Handle late clock-in reassignment
     *
     * If an employee hasn't clocked in and their task is about to start:
     * - Admin gets a notification
     * - If employee clocks in late, and another team has already started their task,
     *   the late employee stays with their original schedule (minus already-started tasks)
     * - Compensation only applies to tasks the compensated employee actually started/ended
     */
    protected function processLateClockIns(NotificationService $notificationService, $today)
    {
        $now = Carbon::now();

        // Get tasks scheduled for today that are about to start (within 30 minutes)
        $upcomingTasks = Task::whereDate('scheduled_date', $today)
            ->where('status', 'Pending')
            ->where('employee_approved', true)
            ->whereNotNull('assigned_team_id')
            ->with(['optimizationTeam.members.employee.user'])
            ->get();

        foreach ($upcomingTasks as $task) {
            if (!$task->scheduled_time) continue;

            $scheduledStart = Carbon::parse($task->scheduled_date)->setTimeFromTimeString(
                Carbon::parse($task->scheduled_time)->format('H:i:s')
            );

            $minutesUntilStart = $now->diffInMinutes($scheduledStart, false);

            // Check if task should have started already (employee is late)
            if ($minutesUntilStart <= 0 && $minutesUntilStart >= -30) {
                // Task should have started - check if team members are clocked in
                if (!$task->optimizationTeam) continue;

                foreach ($task->optimizationTeam->members as $member) {
                    if (!$member->employee) continue;

                    $isClockedIn = \App\Models\Attendance::where('employee_id', $member->employee_id)
                        ->whereDate('clock_in', $today)
                        ->whereNull('clock_out')
                        ->exists();

                    if (!$isClockedIn) {
                        // Employee hasn't clocked in - they're late
                        $alreadyNotified = Notification::where('type', Notification::TYPE_LAST_MINUTE_DECLINE)
                            ->whereJsonContains('data->task_id', $task->id)
                            ->whereJsonContains('data->employee_name', $member->employee->user->name ?? '')
                            ->exists();

                        if (!$alreadyNotified) {
                            $employeeName = $member->employee->user->name ?? 'Employee';
                            $minutesLate = abs($minutesUntilStart);

                            // SCENARIO #22: structured late-clock-in notification (replaces
                            // the generic CRITICAL_WARNING) so admin UI can render action buttons.
                            $notificationService->notifyAdminsLateClockIn(
                                $task,
                                $member->employee,
                                $scheduledStart,
                                $minutesLate
                            );

                            Log::warning('Employee late for task - not clocked in', [
                                'task_id' => $task->id,
                                'team_id' => $task->assigned_team_id,
                                'employee_id' => $member->employee_id,
                                'scheduled_time' => $scheduledStart->format('H:i'),
                                'minutes_late' => $minutesLate,
                            ]);

                            $this->warn("Task #{$task->id}: Employee {$employeeName} is {$minutesLate} min late.");
                        }
                    }
                }
            }
        }
    }
}
