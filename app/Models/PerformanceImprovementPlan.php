<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceImprovementPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'evaluation_id',
        'created_by',
        'title',
        'description',
        'areas_to_improve',
        'action_items',
        'start_date',
        'end_date',
        'status',
        'outcome_notes',
    ];

    protected $casts = [
        'areas_to_improve' => 'array',
        'action_items' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function evaluation()
    {
        return $this->belongsTo(PerformanceEvaluation::class, 'evaluation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->end_date->isPast();
    }

    public function getProgressPercentage(): int
    {
        if (empty($this->action_items)) return 0;

        $completed = collect($this->action_items)->where('status', 'completed')->count();
        return round(($completed / count($this->action_items)) * 100);
    }
}