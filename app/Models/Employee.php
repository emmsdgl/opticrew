<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // UPDATED: Added new fields for optimization system
    protected $fillable = [
        'user_id',
        'full_name',
        'skills',
        // New fields for optimization
        'is_active',
        'is_day_off',
        'is_busy',
        'efficiency',
        'has_driving_license',
        'years_of_experience',
    ];

    // UPDATED: Added new casts for optimization fields
    protected $casts = [
        'skills' => 'array',  // Existing cast
        // New casts for optimization
        'is_active' => 'boolean',
        'is_day_off' => 'boolean',
        'is_busy' => 'boolean',
        'efficiency' => 'float',
        'has_driving_license' => 'boolean',
        'years_of_experience' => 'integer',
    ];

    // EXISTING RELATIONSHIPS (Keep all of these)
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function performanceHistories()
    {
        return $this->hasManyThrough(
            TaskPerformanceHistory::class,
            TeamMember::class,
            'employee_id',
            'task_id',
            'id',
            'daily_team_id'
        )->join('tasks', 'tasks.id', '=', 'task_performance_histories.task_id')
         ->join('daily_team_assignments', 'daily_team_assignments.id', '=', 'tasks.assigned_team_id');
    }

    // NEW RELATIONSHIP: For day-off tracking
    public function dayOffs()
    {
        return $this->hasMany(DayOff::class);
    }
}