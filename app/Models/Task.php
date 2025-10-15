<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'location_id',
        'client_id',
        'task_description',
        'estimated_duration_minutes',
        'scheduled_date',
        'status',
        'assigned_team_id',
        'started_at', // <-- ADD THIS LINE
    ];

    // --- YOUR RELATIONSHIPS ARE ALREADY HERE, WHICH IS GOOD ---
    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    // public function team()
    // {
    //     return $this->belongsTo(DailyTeamAssignment::class, 'assigned_team_id');
    // }

    // public function performanceHistory()
    // {
    //     return $this->hasOne(TaskPerformanceHistory::class);
    // }
}