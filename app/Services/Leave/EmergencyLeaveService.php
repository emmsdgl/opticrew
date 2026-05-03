<?php

namespace App\Services\Leave;

use App\Models\DayOff;
use App\Models\Employee;
use App\Models\Task;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #13: Active Recovery — when an emergency leave overlaps with already-assigned
 * tasks, immediately push job-opportunity notifications to qualified replacement employees
 * and re-evaluate the affected teams' staffing (instead of waiting for the next
 * ProcessUnstaffedTasks cron tick).
 *
 * Extracted from LeaveRequestController so both the mobile API and the web form
 * (EmployeeRequestsController) can use the same logic.
 */
class EmergencyLeaveService
{
    public function __construct(private NotificationService $notificationService) {}

    /**
     * Returns the number of tasks that triggered recovery (0 if none affected).
     */
    public function triggerActiveRecovery(DayOff $leave, Employee $employee): int
    {
        $startDate = Carbon::parse($leave->date);
        $endDate = $leave->end_date ? Carbon::parse($leave->end_date) : $startDate;

        $tasks = Task::whereBetween('scheduled_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereHas('optimizationTeam.members', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->with('optimizationTeam')
            ->get();

        if ($tasks->isEmpty()) {
            return 0;
        }

        $notifiedTeamIds = [];
        $availableReplacements = Employee::where('is_active', true)
            ->where('is_day_off', false)
            ->where('id', '!=', $employee->id)
            ->whereDoesntHave('dayOffs', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'approved')
                  ->where('date', '<=', $endDate->toDateString())
                  ->where(function ($qq) use ($startDate) {
                      $qq->where('end_date', '>=', $startDate->toDateString())
                         ->orWhereNull('end_date');
                  });
            })
            ->with('user')
            ->get();

        foreach ($tasks as $task) {
            if ($availableReplacements->isNotEmpty()) {
                $this->notificationService->notifyEmployeesJobOpportunity($task, $availableReplacements);
            }

            $team = $task->optimizationTeam;
            if ($team && !in_array($team->id, $notifiedTeamIds, true)) {
                if ($team->evaluateStaffing()) {
                    $this->notificationService->notifyAdminsTeamIncompleteStaffing($team);
                }
                $notifiedTeamIds[] = $team->id;
            }
        }

        Log::info('Active Recovery initiated for emergency leave', [
            'leave_id' => $leave->id,
            'employee_id' => $employee->id,
            'tasks_count' => $tasks->count(),
            'replacements_notified' => $availableReplacements->count(),
        ]);

        return $tasks->count();
    }
}
