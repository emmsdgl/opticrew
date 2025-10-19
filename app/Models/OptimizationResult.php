<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptimizationResult extends Model
{
    protected $fillable = [
        'service_date',
        'client_id',
        'schedule',
        'fitness_score',
        'generation_count',
    ];

    protected $casts = [
        'service_date' => 'date',
        'schedule' => 'array',
        'fitness_score' => 'float',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invalidTasks()
    {
        return $this->hasMany(InvalidTask::class);
    }
}