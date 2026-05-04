<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Task;
use App\Models\UrgentLeave;
use App\Services\CompanySettingService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #18: Auto-escalation when admin doesn't respond to an Urgent Leave
 * within `reassignment_grace_period_minutes` (default 30).
 *
 * Escalation 1: Auto-assign the urgent leave to the employee with the fewest
 *   pending tasks today. Admin still sets the compensation amount manually.
 */
class ProcessUrgentLeaveGrace extends Command
{
    protected $signature = 'opticrew:process-urgent-leave-grace';
    protected $description = 'Auto-assign replacements for Urgent Leaves where the admin grace period has expired (Scenario #18)';

    public function handle(NotificationService $notificationService): int
    {
        $graceMinutes = (int) CompanySettingService::get('reassignment_grace_period_minutes', 30);
        $cutoff = Carbon::now()->subMinutes($graceMinutes);

        $expired = UrgentLeave::where('status', UrgentLeave::STATUS_AWAITING_ADMIN)
            ->where('triggered_at', '<=', $cutoff)
            ->with('employee.user')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No Urgent Leaves past the grace window.');
            return self::SUCCESS;
        }

        $assignedCount = 0;
        foreach ($expired as $leave) {
            $today = Carbon::today();
            $replacement = Employee::where('is_active', true)
                ->where('is_day_off', false)
                ->where('id', '!=', $leave->employee_id)
                ->whereDoesntHave('dayOffs', function ($q) use ($today) {
                    $q->where('status', 'approved')
                      ->whereDate('date', $today);
                })
                ->with('user')
                ->get()
                ->sortBy(function ($emp) use ($today) {
                    return Task::whereDate('scheduled_date', $today)
                        ->whereHas('optimizationTeam.members', function ($q) use ($emp) {
                            $q->where('employee_id', $emp->id);
                        })
                        ->whereNotIn('status', ['Completed', 'Cancelled'])
                        ->count();
                })
                ->first();

            if (!$replacement) {
                Log::warning('Urgent Leave grace expired but no replacement available', [
                    'urgent_leave_id' => $leave->id,
                ]);
                continue;
            }

            $leave->update([
                'replacement_employee_id' => $replacement->id,
                'status' => UrgentLeave::STATUS_AUTO_ASSIGNED,
                'escalation_level' => 1,
                'auto_escalation_at' => now(),
            ]);

            // SCENARIO #18: Actually swap the team membership for today's tasks
            $teamsAffected = UrgentLeave::reassignTodaysTasks($leave->fresh());

            $employeeName = $leave->employee->fullName ?? ($leave->employee->user->name ?? 'Employee');
            $replacementName = $replacement->fullName ?? ($replacement->user->name ?? 'Employee');

            $notificationService->notifyAdminsUrgentLeaveAutoAssigned($leave, $employeeName, $replacementName);

            Log::warning('Urgent Leave auto-assigned by grace expiration', [
                'urgent_leave_id' => $leave->id,
                'employee_id' => $leave->employee_id,
                'replacement_employee_id' => $replacement->id,
                'teams_reassigned' => $teamsAffected,
            ]);

            $assignedCount++;
        }

        $this->info("Processed {$expired->count()} expired Urgent Leaves; auto-assigned {$assignedCount}.");
        return self::SUCCESS;
    }
}
