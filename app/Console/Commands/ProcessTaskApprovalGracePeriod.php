<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Notification;
use App\Services\Notification\NotificationService;
use App\Services\CompanySettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #19: Task Approval Grace Period
 * If employee doesn't approve the task within the grace period,
 * task is flagged as UNSTAFFED and admin must manually assign.
 *
 * SCENARIO #21: Task Reassignment for late clock-in
 * If employee is running late and hasn't clocked in by the time their task
 * should start, the system notifies admin and may auto-reassign.
 */
class ProcessTaskApprovalGracePeriod extends Command
{
    protected $signature = 'opticrew:process-task-grace-periods';
    protected $description = 'Process task approval grace period and late clock-in reassignment (Scenarios #19, #21)';

    public function handle(NotificationService $notificationService)
    {
        $gracePeriod = CompanySettingService::get('task_approval_grace_period_minutes', 30);
        $today = Carbon::today();

        // SCENARIO #19: Find tasks that were assigned but not approved within grace period
        $expiredApprovalTasks = Task::whereNull('employee_approved')
            ->whereNotNull('assigned_team_id')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
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

        $this->info("Processed {$expiredApprovalTasks->count()} expired approval tasks.");
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

                            $notificationService->notifyAdminsCriticalWarning(
                                $task,
                                "Employee {$employeeName} has not clocked in for task \"{$task->task_description}\" (scheduled at {$scheduledStart->format('g:i A')}). Consider reassignment."
                            );

                            Log::warning('Employee late for task - not clocked in', [
                                'task_id' => $task->id,
                                'employee_id' => $member->employee_id,
                                'scheduled_time' => $scheduledStart->format('H:i'),
                            ]);

                            $this->warn("Task #{$task->id}: Employee {$employeeName} is late (not clocked in).");
                        }
                    }
                }
            }
        }
    }
}
