<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizationTeam extends Model
{
    protected $fillable = [
        'optimization_run_id',
        'team_index',
        'service_date',
        'car_id',
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

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_team_id');
    }
}