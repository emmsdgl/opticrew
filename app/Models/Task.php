<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'client_id',
        'task_description',
        'rate_type',              // Rate type: Normal or Student
        'estimated_duration_minutes',
        'scheduled_date',
        'status',
        'employee_approved',      // Employee approval status
        'employee_approved_at',   // When employee approved/declined
        'assigned_team_id',
        'started_by',             // User who started the task
        'started_at',
        'completed_by',           // User who completed the task
        // Optimization system fields
        'scheduled_time',
        'duration',              // You might use this or estimated_duration_minutes
        'travel_time',
        'latitude',
        'longitude',
        'required_equipment',
        'required_skills',
        // New pseudocode fields
        'arrival_status',         // RULE 3: Priority for arriving guests
        'on_hold_reason',         // Reason for being on hold
        'on_hold_timestamp',      // When task was put on hold
        'actual_duration',        // Auto-calculated actual time taken
        'completed_at',           // When task was completed
        'reassigned_at',          // When task was reassigned
        'reassignment_reason',    // Why task was reassigned
        'optimization_run_id',    // Link to optimization run
        'assigned_by_generation', // GA generation that assigned this task
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'employee_approved_at' => 'datetime',
        'on_hold_timestamp' => 'datetime',
        'reassigned_at' => 'datetime',
        // Optimization casts
        'duration' => 'integer',
        'travel_time' => 'integer',
        'estimated_duration_minutes' => 'integer',
        'actual_duration' => 'integer',
        'required_equipment' => 'array',
        'required_skills' => 'array',
        // Boolean casts
        'arrival_status' => 'boolean',
        'employee_approved' => 'boolean',
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

    /**
     * Get the optimization team assigned to this task
     */
    public function optimizationTeam()
    {
        return $this->belongsTo(OptimizationTeam::class, 'assigned_team_id');
    }

    /**
     * Get the optimization run this task belongs to
     */
    public function optimizationRun()
    {
        return $this->belongsTo(OptimizationRun::class, 'optimization_run_id');
    }

    /**
     * Get performance flags for this task
     */
    public function performanceFlags()
    {
        return $this->hasMany(PerformanceFlag::class);
    }

    /**
     * Get alerts for this task
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the review for this task
     */
    public function review()
    {
        return $this->hasOne(TaskReview::class);
    }

    /**
     * Check if task has been reviewed
     */
    public function hasReview()
    {
        return $this->review()->exists();
    }

    /**
     * Get the checklist completions for this task
     */
    public function checklistCompletions()
    {
        return $this->hasMany(TaskChecklistCompletion::class);
    }

    /**
     * Get the user who assigned this task
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who started this task
     */
    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * Get the user who completed this task
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}