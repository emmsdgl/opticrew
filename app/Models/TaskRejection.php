<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Audit row written every time an employee rejects an assigned task.
 *
 * Used to:
 *  - count an employee's rejections inside a window (monthly budget enforcement)
 *  - count rejections of a specific task (per-task ceiling enforcement)
 *  - power admin dashboards / pattern detection
 */
class TaskRejection extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'employee_id',
        'reason',
        'rejected_at',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
