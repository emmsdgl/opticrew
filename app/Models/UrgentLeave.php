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
}
