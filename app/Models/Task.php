<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'client_id',
        'task_description',
        'estimated_duration_minutes',
        'scheduled_date',
        'status',
        'assigned_team_id',
        'started_at',
        // New fields for optimization system
        'scheduled_time',
        'duration',              // You might use this or estimated_duration_minutes
        'travel_time',
        'latitude',
        'longitude',
        'required_equipment',
        'required_skills',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        // New casts for optimization
        'duration' => 'integer',
        'travel_time' => 'integer',
        'estimated_duration_minutes' => 'integer',
        'required_equipment' => 'array',
        'required_skills' => 'array',
    ];

    // EXISTING RELATIONSHIPS (Keep all of these)
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class, 'contracted_client_id');
    }

    public function team()
    {
        return $this->belongsTo(DailyTeamAssignment::class, 'assigned_team_id');
    }

    // Uncomment if you need this
    // public function performanceHistory()
    // {
    //     return $this->hasOne(TaskPerformanceHistory::class);
    // }
}