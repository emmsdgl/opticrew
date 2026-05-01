<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DayOff;

class OptimizationTeam extends Model
{
    public const STAFFING_FULL = 'fully_staffed';
    public const STAFFING_INCOMPLETE = 'incomplete_staffing';

    protected $fillable = [
        'optimization_run_id',
        'team_index',
        'service_date',
        'car_id',
        'staffing_status',
    ];

    protected $casts = [
        'service_date' => 'date',
    ];

    public function optimizationRun()
    {
        return $this->belongsTo(OptimizationRun::class);
    }

    public function members()
    {
        return $this->hasMany(OptimizationTeamMember::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            OptimizationTeamMember::class,
            'optimization_team_id', // Foreign key on optimization_team_members table
            'id',                   // Foreign key on employees table
            'id',                   // Local key on optimization_teams table
            'employee_id'           // Local key on optimization_team_members table
        );
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_team_id');
    }

    /**
     * Get the team name attribute (Team 1, Team 2, etc.)
     */
    public function getTeamNameAttribute()
    {
        return 'Team ' . $this->team_index;
    }

    /**
     * SCENARIO #9: count members who are actually available on this team's service_date.
     * Excludes employees with a locked emergency leave or an approved standard leave on that date.
     */
    public function activeMemberCount(): int
    {
        $employeeIds = $this->members()->pluck('employee_id');
        if ($employeeIds->isEmpty()) {
            return 0;
        }

        $unavailable = DayOff::whereIn('employee_id', $employeeIds)
            ->whereDate('date', $this->service_date)
            ->where(function ($q) {
                $q->where('status', 'approved')
                  ->orWhere(function ($q2) {
                      $q2->where('is_emergency', true)->where('auto_escalation_locked', true);
                  });
            })
            ->pluck('employee_id')
            ->unique();

        return $employeeIds->diff($unavailable)->count();
    }

    /**
     * SCENARIO #9: re-evaluate staffing_status. Returns true if the team transitioned
     * from fully_staffed → incomplete_staffing (so callers can fire a CRITICAL_WARNING).
     */
    public function evaluateStaffing(): bool
    {
        $previous = $this->staffing_status;
        $newStatus = $this->activeMemberCount() < 2
            ? self::STAFFING_INCOMPLETE
            : self::STAFFING_FULL;

        if ($newStatus !== $previous) {
            $this->staffing_status = $newStatus;
            $this->save();
        }

        return $previous === self::STAFFING_FULL && $newStatus === self::STAFFING_INCOMPLETE;
    }
}