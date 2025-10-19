<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvalidTask extends Model
{
    protected $fillable = [
        'optimization_result_id',
        'task_id',
        'rejection_reason',
        'task_details',
    ];

    protected $casts = [
        'task_details' => 'array',
    ];

    public function optimizationResult(): BelongsTo
    {
        return $this->belongsTo(OptimizationResult::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}