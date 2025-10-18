<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptimizationRun extends Model
{
    protected $fillable = [
        'service_date',
        'total_tasks',
        'total_employees',
        'total_teams',
        'final_fitness_score',
        'generations_run',
        'status',
        'employee_allocation_data',
        'greedy_result_data',
    ];

    protected $casts = [
        'service_date' => 'date',
        'employee_allocation_data' => 'array',
        'greedy_result_data' => 'array',
        'final_fitness_score' => 'decimal:4'
    ];

    public function generations()
    {
        return $this->hasMany(OptimizationGeneration::class);
    }

    public function triggeredByTask()
    {
        return $this->belongsTo(Task::class, 'triggered_by_task_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
