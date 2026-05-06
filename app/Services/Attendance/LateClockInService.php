<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OptimizationTeam;
use App\Models\OptimizationTeamMember;
use App\Models\Task;
use App\Services\CompanySettingService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #21: Late clock-in placement on the busiest team.
 *
 * When an employee clocks in after their first scheduled task's start time
 * (plus a grace window), the system removes them from their original team
 * for the day and places them on whichever team has the most PENDING tasks
 * remaining today. Admins are notified so they can confirm or reassign.
 *
 * Same-day urgent leave (Scenario #18) and emergency leave (Scenario #12)
 * are handled by separate paths and do NOT enter this code.
 */
class LateClockInService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Inspect a freshly-created Attendance row and, if the employee is late,
     * remove them from their original team for today and slot them onto the
     * team with the most pending tasks. Idempotent and safe to call on any
     * clock-in (returns early if nothing to do).
     */
    public function handleLateClockIn(Attendance $attendance): array
    {
        $employee = Employee::with('user')->find($attendance->employee_id);
        if (!$employee) {
            return ['was_late' => false, 'reason' => 'employee_not_found'];
        }

        $today = Carbon::parse($attendance->clock_in)->startOfDay();
        $clockInAt = Carbon::parse($attendance->clock_in);

        // Find this employee's earliest scheduled task today
        $firstTask = Task::whereDate('scheduled_date', $today)
            ->whereNotNull('assigned_team_id')
            ->whereNotNull('scheduled_time')
            ->whereIn('status', ['Pending', 'Scheduled', 'In Progress'])
            ->whereHas('optimizationTeam.members', fn($q) =>
                $q->where('employee_id', $employee->id)
            )
            ->orderBy('scheduled_time')
            ->first();

        if (!$firstTask) {
            return ['was_late' => false, 'reason' => 'no_scheduled_task_today'];
        }

        $scheduledStart = Carbon::parse($firstTask->scheduled_date)
            ->setTimeFromTimeString(Carbon::parse($firstTask->scheduled_time)->format('H:i:s'));

        $gracePeriod = (int) CompanySettingService::get('task_approval_grace_period_minutes', 30);
        $lateCutoff = $scheduledStart->copy()->addMinutes($gracePeriod);

        if ($clockInAt->lte($lateCutoff)) {
            return [
                'was_late' => false,
                'reason' => 'within_grace_period',
                'scheduled_start' => $scheduledStart->toIso8601String(),
                'grace_period_minutes' => $gracePeriod,
            ];
        }

        $minutesLate = (int) $clockInAt->diffInMinutes($scheduledStart);

        return DB::transaction(function () use ($attendance, $employee, $today, $minutesLate) {
            // Only consider teams whose optimization run is LIVE (not soft-deleted),
            // otherwise we'd pick up orphan rows from runs that were superseded by
            // a re-optimization (e.g. after a leave approval).
            $previousMembership = OptimizationTeamMember::where('employee_id', $employee->id)
                ->whereHas('team.optimizationRun', fn($q) => $q->whereDate('service_date', $today))
                ->first();
            $previousTeamId = $previousMembership?->optimization_team_id;

            $busiestTeam = $this->findBusiestTeam($today);

            if (!$busiestTeam) {
                $attendance->update([
                    'is_late' => true,
                    'minutes_late' => $minutesLate,
                ]);
                $this->notificationService->notifyAdminLateNoWork($employee, $minutesLate);
                Log::info('Late clock-in detected but no remaining work to assign', [
                    'employee_id' => $employee->id,
                    'attendance_id' => $attendance->id,
                    'minutes_late' => $minutesLate,
                ]);

                return [
                    'was_late' => true,
                    'minutes_late' => $minutesLate,
                    'reassigned_to_team_id' => null,
                    'reason' => 'no_busiest_team_found',
                ];
            }

            if ($previousTeamId && $previousTeamId !== $busiestTeam->id) {
                $previousMembership->delete();
                Log::info('Late employee removed from original team', [
                    'employee_id' => $employee->id,
                    'previous_team_id' => $previousTeamId,
                ]);
            }

            $alreadyOnBusiest = OptimizationTeamMember::where('optimization_team_id', $busiestTeam->id)
                ->where('employee_id', $employee->id)
                ->exists();

            if (!$alreadyOnBusiest) {
                OptimizationTeamMember::create([
                    'optimization_team_id' => $busiestTeam->id,
                    'employee_id' => $employee->id,
                ]);
            }

            $attendance->update([
                'is_late' => true,
                'minutes_late' => $minutesLate,
                'reassigned_to_team_id' => $busiestTeam->id,
            ]);

            $pendingTaskCount = Task::where('assigned_team_id', $busiestTeam->id)
                ->whereDate('scheduled_date', $today)
                ->whereIn('status', ['Pending', 'Scheduled'])
                ->count();

            $this->notificationService->notifyAdminLateReassignment(
                $employee,
                $busiestTeam,
                $minutesLate,
                $pendingTaskCount,
                $previousTeamId
            );

            Log::info('Late employee reassigned to busiest team', [
                'employee_id' => $employee->id,
                'attendance_id' => $attendance->id,
                'minutes_late' => $minutesLate,
                'previous_team_id' => $previousTeamId,
                'new_team_id' => $busiestTeam->id,
                'new_team_index' => $busiestTeam->team_index,
                'pending_tasks_on_new_team' => $pendingTaskCount,
            ]);

            return [
                'was_late' => true,
                'minutes_late' => $minutesLate,
                'reassigned_to_team_id' => $busiestTeam->id,
                'new_team_index' => $busiestTeam->team_index,
                'previous_team_id' => $previousTeamId,
                'pending_task_count' => $pendingTaskCount,
            ];
        });
    }

    /**
     * Find the team for the given date with the highest count of PENDING/SCHEDULED
     * tasks. Skips teams whose optimization run was soft-deleted (orphans from a
     * superseded re-optimization). Returns null if no eligible team exists.
     */
    protected function findBusiestTeam(Carbon $date): ?OptimizationTeam
    {
        $teamsWithPending = Task::whereDate('scheduled_date', $date)
            ->whereNotNull('assigned_team_id')
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->select('assigned_team_id', DB::raw('COUNT(*) as pending_count'))
            ->groupBy('assigned_team_id')
            ->orderByDesc('pending_count')
            ->get();

        foreach ($teamsWithPending as $row) {
            $team = OptimizationTeam::with('optimizationRun')->find($row->assigned_team_id);
            if (!$team || !$team->optimizationRun) continue;
            return $team;
        }

        return null;
    }
}
