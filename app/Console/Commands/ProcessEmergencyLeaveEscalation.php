<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DayOff;
use App\Models\Task;
use App\Models\Employee;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #11: Automated Escalation Protocol for Emergency Leave
 *
 * Escalation 1 (Less than 24 hrs): Send "Urgent Reminder" to Manager with One-Click Approve/Deny
 * Escalation 2 (Less than 12 hrs): Notify Dispatcher or Supervisor - "Emergency leave pending"
 * Escalation 3 (Less than 4 hrs): System prevents "Ghost Assignment" by placing Hard Lock
 *   - Block employee clock-in
 *   - Notify Dispatcher that job is UNSTAFFED
 */
class ProcessEmergencyLeaveEscalation extends Command
{
    protected $signature = 'opticrew:escalate-emergency-leaves';
    protected $description = 'Process emergency leave escalation protocol (Scenario #11)';

    public function handle(NotificationService $notificationService)
    {
        $pendingEmergencyLeaves = DayOff::where('is_emergency', true)
            ->where('status', 'pending')
            ->where('date', '>=', Carbon::today())
            ->where('auto_escalation_locked', false)
            ->with('employee.user')
            ->get();

        foreach ($pendingEmergencyLeaves as $leave) {
            $hoursUntilLeave = Carbon::now()->diffInHours(Carbon::parse($leave->date), false);
            $employeeName = $leave->employee->fullName ?? $leave->employee->user->name ?? 'Employee';
            $currentLevel = $leave->escalation_level;

            if ($hoursUntilLeave <= 4 && $currentLevel < 3) {
                // ESCALATION 3: Hard Lock
                $leave->update([
                    'escalation_level' => 3,
                    'escalation_notified_at' => now(),
                    'auto_escalation_locked' => true,
                ]);

                $notificationService->notifyManagerEmergencyLeave($leave, $employeeName, 3);

                // Mark tasks as UNSTAFFED for this employee on the leave date
                $this->markTasksUnstaffed($leave);

                Log::warning('Emergency Leave Escalation 3: Hard Lock applied', [
                    'leave_id' => $leave->id,
                    'employee' => $employeeName,
                    'hours_until_leave' => $hoursUntilLeave,
                ]);

            } elseif ($hoursUntilLeave <= 12 && $currentLevel < 2) {
                // ESCALATION 2: Notify Dispatcher
                $leave->update([
                    'escalation_level' => 2,
                    'escalation_notified_at' => now(),
                ]);

                $notificationService->notifyManagerEmergencyLeave($leave, $employeeName, 2);

                Log::info('Emergency Leave Escalation 2: Dispatcher notified', [
                    'leave_id' => $leave->id,
                    'employee' => $employeeName,
                ]);

            } elseif ($hoursUntilLeave <= 24 && $currentLevel < 1) {
                // ESCALATION 1: Urgent Reminder
                $leave->update([
                    'escalation_level' => 1,
                    'escalation_notified_at' => now(),
                ]);

                $notificationService->notifyManagerEmergencyLeave($leave, $employeeName, 1);

                Log::info('Emergency Leave Escalation 1: Urgent reminder sent', [
                    'leave_id' => $leave->id,
                    'employee' => $employeeName,
                ]);
            }
        }

        $this->info("Processed {$pendingEmergencyLeaves->count()} emergency leave requests.");
    }

    /**
     * Mark assigned tasks as needing reassignment when employee is on emergency leave.
     * SCENARIO #9: also re-evaluate the affected teams; if any drops below 2 active
     * members, fire a CRITICAL_WARNING and persist the "incomplete_staffing" status.
     */
    protected function markTasksUnstaffed(DayOff $leave)
    {
        $tasks = Task::whereDate('scheduled_date', $leave->date)
            ->whereHas('optimizationTeam.members', function ($q) use ($leave) {
                $q->where('employee_id', $leave->employee_id);
            })
            ->with('optimizationTeam')
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->get();

        $notifiedTeamIds = [];
        $notificationService = app(\App\Services\Notification\NotificationService::class);

        foreach ($tasks as $task) {
            Log::warning('Task marked as understaffed due to emergency leave', [
                'task_id' => $task->id,
                'employee_id' => $leave->employee_id,
                'leave_id' => $leave->id,
            ]);

            $team = $task->optimizationTeam;
            if ($team && !in_array($team->id, $notifiedTeamIds, true)) {
                if ($team->evaluateStaffing()) {
                    $notificationService->notifyAdminsTeamIncompleteStaffing($team);
                }
                $notifiedTeamIds[] = $team->id;
            }
        }
    }
}
