<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptimizationGeneration extends Model
{
    protected $fillable = [
        'optimization_run_id',
        'generation_number',
        'best_fitness',
        'average_fitness',
        'worst_fitness',
        'is_improvement',
        'best_schedule_data',
        'population_summary'
    ];

    protected $casts = [
        'best_fitness' => 'decimal:4',
        'average_fitness' => 'decimal:4',
        'worst_fitness' => 'decimal:4',
        'is_improvement' => 'boolean',
        'best_schedule_data' => 'array',
        'population_summary' => 'array'
    ];

    public function optimizationRun()
    {
        return $this->belongsTo(OptimizationRun::class);
    }

    public function schedules()
    {
        return $this->hasMany(OptimizationSchedule::class);
    }
}