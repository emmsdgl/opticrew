<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePerformance extends Model
{
    use HasFactory;

    protected $table = 'employee_performance';

    protected $fillable = [
        'employee_id',
        'date',
        'tasks_completed',
        'total_performance_score',
        'average_performance',
    ];

    protected $casts = [
        'date' => 'date',
        'tasks_completed' => 'integer',
        'total_performance_score' => 'decimal:4',
        'average_performance' => 'decimal:4',
    ];

    /**
     * Get the employee this performance record belongs to
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Update performance with a new task completion
     *
     * @param float $performanceScore (estimated/actual duration ratio)
     */
    public function addTaskCompletion(float $performanceScore): void
    {
        $this->tasks_completed += 1;
        $this->total_performance_score += $performanceScore;
        $this->average_performance = $this->total_performance_score / $this->tasks_completed;
        $this->save();
    }

    /**
     * Check if performance is above average (faster than estimated)
     */
    public function isAboveAverage(): bool
    {
        return $this->average_performance > 1.0;
    }

    /**
     * Get performance rating as string
     */
    public function getPerformanceRating(): string
    {
        if ($this->average_performance >= 1.2) {
            return 'Excellent';
        } elseif ($this->average_performance >= 1.0) {
            return 'Good';
        } elseif ($this->average_performance >= 0.8) {
            return 'Average';
        } else {
            return 'Needs Improvement';
        }
    }
}
