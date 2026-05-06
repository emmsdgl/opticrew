<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrgentLeave extends Model
{
    public const STATUS_AWAITING_ADMIN = 'awaiting_admin';
    public const STATUS_AUTO_ASSIGNED = 'auto_assigned';
    public const STATUS_MANUALLY_ASSIGNED = 'manually_assigned';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'triggered_at',
        'clock_out_at',
        'reason',
        'status',
        'replacement_employee_id',
        'compensation_amount',
        'escalation_level',
        'auto_escalation_at',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'auto_escalation_at' => 'datetime',
        'processed_at' => 'datetime',
        'compensation_amount' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'replacement_employee_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * SCENARIO #18: Swap the leaving employee out of every team they're on for today
     * and replace with the assigned replacement. Returns the number of teams affected.
     * Re-evaluates each team's staffing afterwards (Scenario #9 integration).
     */
    public static function reassignTodaysTasks(self $leave): int
    {
        if (!$leave->replacement_employee_id) {
            return 0;
        }

        $today = ($leave->triggered_at ?? now())->copy()->toDateString();

        $teams = OptimizationTeam::whereDate('service_date', $today)
            ->whereHas('members', function ($q) use ($leave) {
                $q->where('employee_id', $leave->employee_id);
            })
            ->with('members')
            ->get();

        $affected = 0;
        $notificationService = app(\App\Services\Notification\NotificationService::class);

        foreach ($teams as $team) {
            // Skip if the replacement is already on this team — would create a duplicate
            $alreadyOnTeam = $team->members->contains('employee_id', $leave->replacement_employee_id);
            if ($alreadyOnTeam) {
                \Illuminate\Support\Facades\Log::info('Urgent Leave reassignment skipped — replacement already on team', [
                    'urgent_leave_id' => $leave->id,
                    'team_id' => $team->id,
                ]);
                continue;
            }

            $member = $team->members->firstWhere('employee_id', $leave->employee_id);
            if (!$member) continue;

            $member->employee_id = $leave->replacement_employee_id;
            $member->save();
            $affected++;

            // Refresh and re-evaluate staffing — fires CRITICAL_WARNING if team is now solo
            $team->refresh();
            if ($team->evaluateStaffing()) {
                $notificationService->notifyAdminsTeamIncompleteStaffing($team);
            }
        }

        return $affected;
    }

    /**
     * SCENARIO #22: Pro-rata compensation rule.
     *
     * The "agreed" compensation_amount is what admin promised to pay if the
     * replacement covers the entire urgent leave. But the replacement is
     * actually paid only for tasks they completed — if the late employee
     * comes back and finishes some tasks themselves, the replacement gets
     * a smaller share.
     *
     * compensation_per_task = compensation_amount / total_tasks_on_team_that_day
     * earned = compensation_per_task × tasks_completed_by_replacement
     *
     * Returns 0 for cancelled, uncompensated, or pre-completion urgent leaves.
     */
    public function effectiveCompensation(): float
    {
        if (
            $this->status === self::STATUS_CANCELLED ||
            !$this->compensation_amount ||
            !$this->replacement_employee_id
        ) {
            return 0.0;
        }

        $date = $this->triggered_at?->toDateString();
        if (!$date) {
            return 0.0;
        }

        $replacement = $this->replacement;
        if (!$replacement) {
            return 0.0;
        }

        // Find the team the replacement is on for that date (set by reassignTodaysTasks).
        $teamId = OptimizationTeamMember::whereHas('team', fn($q) =>
                $q->whereDate('service_date', $date)
            )
            ->where('employee_id', $replacement->id)
            ->value('optimization_team_id');

        if (!$teamId) {
            return 0.0;
        }

        $totalTasks = Task::where('assigned_team_id', $teamId)
            ->whereDate('scheduled_date', $date)
            ->count();

        if ($totalTasks === 0) {
            return 0.0;
        }

        $completedByReplacement = Task::where('assigned_team_id', $teamId)
            ->whereDate('scheduled_date', $date)
            ->where('status', 'Completed')
            ->where('completed_by', $replacement->user_id)
            ->count();

        if ($completedByReplacement === 0) {
            return 0.0;
        }

        return round((float) $this->compensation_amount * ($completedByReplacement / $totalTasks), 2);
    }
}
