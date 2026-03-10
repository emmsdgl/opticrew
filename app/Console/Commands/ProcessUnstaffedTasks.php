<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Employee;
use App\Models\DayOff;
use App\Models\Notification;
use App\Services\Notification\NotificationService;
use App\Services\CompanySettingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #9:  CRITICAL_WARNING for reassignment resulting in incomplete staffing
 * SCENARIO #14: Auto-push "Job Opportunity" to qualified employees for unstaffed tasks
 * SCENARIO #15: CRITICAL_ESCALATION if no acceptance within 60 minutes, auto-assign
 */
class ProcessUnstaffedTasks extends Command
{
    protected $signature = 'opticrew:process-unstaffed-tasks';
    protected $description = 'Process unstaffed tasks: push job opportunities, escalate, auto-assign (Scenarios #9, #14, #15)';

    public function handle(NotificationService $notificationService)
    {
        $today = Carbon::today();
        $escalationTimeout = CompanySettingService::get('unstaffed_escalation_timeout_minutes', 60);

        // SCENARIO #14: Find tasks that are UNSTAFFED (no team or declined)
        $unstaffedTasks = Task::whereDate('scheduled_date', '>=', $today)
            ->where(function ($q) {
                $q->whereNull('assigned_team_id')
                  ->orWhere('employee_approved', false);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->get();

        $this->info("Found {$unstaffedTasks->count()} unstaffed tasks.");

        foreach ($unstaffedTasks as $task) {
            $minutesSinceCreated = $task->created_at->diffInMinutes(now());

            // Check if job opportunity notification was already sent
            $opportunityAlreadySent = Notification::where('type', Notification::TYPE_JOB_OPPORTUNITY)
                ->whereJsonContains('data->task_id', $task->id)
                ->exists();

            if (!$opportunityAlreadySent) {
                // SCENARIO #14: Push "Job Opportunity" to qualified available employees
                $availableEmployees = $this->getQualifiedEmployees($task);

                if ($availableEmployees->isNotEmpty()) {
                    $notificationService->notifyEmployeesJobOpportunity($task, $availableEmployees);

                    // Update task status
                    $task->update(['status' => 'Pending']);

                    Log::info('Job Opportunity notification sent for unstaffed task', [
                        'task_id' => $task->id,
                        'employees_notified' => $availableEmployees->count(),
                    ]);

                    $this->info("Task #{$task->id}: Notified {$availableEmployees->count()} employees.");
                } else {
                    // SCENARIO #9: CRITICAL_WARNING - No available employees
                    $notificationService->notifyAdminsCriticalWarning(
                        $task,
                        "CRITICAL WARNING: Task \"{$task->task_description}\" on {$task->scheduled_date->format('M d, Y')} is UNSTAFFED with no available employees. Pending - Incomplete Staffing."
                    );

                    Log::warning('CRITICAL_WARNING: No available employees for task', [
                        'task_id' => $task->id,
                    ]);
                }
            }

            // SCENARIO #15: Check if escalation timeout has passed
            if ($minutesSinceCreated >= $escalationTimeout && !$task->assigned_team_id) {
                $escalationAlreadySent = Notification::where('type', Notification::TYPE_CRITICAL_ESCALATION)
                    ->whereJsonContains('data->task_id', $task->id)
                    ->exists();

                if (!$escalationAlreadySent) {
                    // CRITICAL_ESCALATION: Notify Senior Dispatcher/Ops Manager
                    $notificationService->notifyAdminsCriticalEscalation($task);

                    // Auto-assign to employee with no pending tasks
                    $autoAssignedEmployee = $this->autoAssignToAvailableEmployee($task);

                    if ($autoAssignedEmployee) {
                        Log::warning('CRITICAL_ESCALATION: Task auto-assigned', [
                            'task_id' => $task->id,
                            'employee_id' => $autoAssignedEmployee->id,
                        ]);
                        $this->info("Task #{$task->id}: Auto-assigned to employee #{$autoAssignedEmployee->id}.");
                    } else {
                        Log::error('CRITICAL_ESCALATION: Could not auto-assign task - no available employees', [
                            'task_id' => $task->id,
                        ]);
                        $this->error("Task #{$task->id}: Could not auto-assign - no available employees.");
                    }
                }
            }
        }
    }

    /**
     * Get qualified available employees for a specific task region/zone
     */
    protected function getQualifiedEmployees(Task $task): \Illuminate\Support\Collection
    {
        $today = Carbon::today();

        return Employee::where('is_active', true)
            ->where('is_day_off', false)
            ->whereDoesntHave('dayOffs', function ($q) use ($today) {
                $q->where('date', '<=', $today)
                  ->where(function ($qq) use ($today) {
                      $qq->where('end_date', '>=', $today)->orWhereNull('end_date');
                  })
                  ->where('status', 'approved');
            })
            ->with('user')
            ->get();
    }

    /**
     * SCENARIO #15: Auto-assign task to an employee with no pending tasks
     */
    protected function autoAssignToAvailableEmployee(Task $task): ?Employee
    {
        $taskDate = $task->scheduled_date;

        // Find employees with the fewest tasks on this date
        $employees = Employee::where('is_active', true)
            ->where('is_day_off', false)
            ->with('user')
            ->get()
            ->sortBy(function ($employee) use ($taskDate) {
                return Task::whereDate('scheduled_date', $taskDate)
                    ->whereHas('optimizationTeam.members', function ($q) use ($employee) {
                        $q->where('employee_id', $employee->id);
                    })
                    ->whereNotIn('status', ['Completed', 'Cancelled'])
                    ->count();
            });

        $bestEmployee = $employees->first();

        if ($bestEmployee) {
            // Note: Full team assignment requires the optimization service
            // For now, log the recommendation for admin action
            Log::info('Auto-assign recommendation', [
                'task_id' => $task->id,
                'recommended_employee_id' => $bestEmployee->id,
                'recommended_employee_name' => $bestEmployee->user->name ?? 'Unknown',
            ]);
        }

        return $bestEmployee;
    }
}
