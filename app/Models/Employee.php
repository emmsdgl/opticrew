<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection; // Add this at the top
use App\Models\TaskPerformanceHistory; // Add this at the top
use App\Models\TeamMember;
use App\Models\DailyTeamAssignment;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Your fillable property should be here
    protected $fillable = [
        'user_id',
        'full_name',
        'skills',
    ];

    // ADD THIS FUNCTION
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
    
    // You might also have a user() relationship here, which is also fine.
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
            TaskPerformanceHistory::class, // The final model we want to get
            TeamMember::class,             // The intermediate model we start with
            'employee_id',                 // Foreign key on TeamMember table...
            'task_id',                     // Foreign key on TaskPerformanceHistory table...
            'id',                          // Local key on Employee table...
            'daily_team_id'                // Local key on TeamMember table that connects to the NEXT model in the chain
        )->join('tasks', 'tasks.id', '=', 'task_performance_histories.task_id')
         ->join('daily_team_assignments', 'daily_team_assignments.id', '=', 'tasks.assigned_team_id');
    }
}