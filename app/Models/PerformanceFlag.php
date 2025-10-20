<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'employee_id',
        'team_id',
        'flag_type',
        'estimated_minutes',
        'actual_minutes',
        'variance_minutes',
        'flagged_at',
        'reviewed',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'reviewed' => 'boolean',
        'estimated_minutes' => 'integer',
        'actual_minutes' => 'integer',
        'variance_minutes' => 'integer',
    ];

    /**
     * Get the task that was flagged
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the employee related to this flag
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who reviewed this flag
     */
    public function reviewedByUser()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Mark flag as reviewed
     */
    public function markAsReviewed(int $userId, ?string $notes = null): void
    {
        $this->update([
            'reviewed' => true,
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
            'review_notes' => $notes,
        ]);
    }

    /**
     * Get variance percentage
     */
    public function getVariancePercentage(): float
    {
        if ($this->estimated_minutes == 0) {
            return 0;
        }
        return ($this->variance_minutes / $this->estimated_minutes) * 100;
    }
}
