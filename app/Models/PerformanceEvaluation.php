<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'evaluator_id',
        'evaluation_period_start',
        'evaluation_period_end',
        'status',
        'attendance_score',
        'punctuality_score',
        'task_completion_score',
        'quality_of_work_score',
        'professionalism_score',
        'teamwork_score',
        'overall_rating',
        'strengths',
        'areas_for_improvement',
        'goals_for_next_period',
        'admin_comments',
        'system_metrics',
        'requires_pip',
    ];

    protected $casts = [
        'evaluation_period_start' => 'date',
        'evaluation_period_end' => 'date',
        'overall_rating' => 'decimal:2',
        'system_metrics' => 'array',
        'requires_pip' => 'boolean',
    ];

    const CRITERIA = [
        'attendance_score' => 'Attendance',
        'punctuality_score' => 'Punctuality',
        'task_completion_score' => 'Task Completion',
        'quality_of_work_score' => 'Quality of Work',
        'professionalism_score' => 'Professionalism',
        'teamwork_score' => 'Teamwork',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function improvementPlan()
    {
        return $this->hasOne(PerformanceImprovementPlan::class, 'evaluation_id');
    }

    /**
     * Calculate overall rating from individual criteria scores
     */
    public function calculateOverallRating(): float
    {
        $scores = array_filter([
            $this->attendance_score,
            $this->punctuality_score,
            $this->task_completion_score,
            $this->quality_of_work_score,
            $this->professionalism_score,
            $this->teamwork_score,
        ], fn($s) => $s !== null);

        if (empty($scores)) {
            return 0;
        }

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Get rating label based on overall score
     */
    public function getRatingLabel(): string
    {
        $rating = $this->overall_rating ?? $this->calculateOverallRating();

        if ($rating >= 4.5) return 'Outstanding';
        if ($rating >= 3.5) return 'Exceeds Expectations';
        if ($rating >= 2.5) return 'Meets Expectations';
        if ($rating >= 1.5) return 'Needs Improvement';
        return 'Unsatisfactory';
    }

    /**
     * Get color class for rating
     */
    public function getRatingColor(): string
    {
        $rating = $this->overall_rating ?? $this->calculateOverallRating();

        if ($rating >= 4.5) return 'green';
        if ($rating >= 3.5) return 'blue';
        if ($rating >= 2.5) return 'yellow';
        if ($rating >= 1.5) return 'orange';
        return 'red';
    }
}