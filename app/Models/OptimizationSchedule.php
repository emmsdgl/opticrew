<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptimizationSchedule extends Model
{
    protected $fillable = [
        'optimization_generation_id',
        'schedule_index',
        'fitness_score',
        'team_assignments',
        'workload_distribution',
        'is_elite',
        'is_final_result',
        'created_by'
    ];

    protected $casts = [
        'fitness_score' => 'decimal:4',
        'team_assignments' => 'array',
        'workload_distribution' => 'array',
        'is_elite' => 'boolean',
        'is_final_result' => 'boolean'
    ];

    public function generation()
    {
        return $this->belongsTo(OptimizationGeneration::class, 'optimization_generation_id');
    }
}