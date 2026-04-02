<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\Feedback;
use App\Models\PerformanceEvaluation;
use App\Models\PerformanceFlag;
use App\Models\Task;
use Carbon\Carbon;

class PerformanceManagementService
{
    /**
     * Generate auto-fill scores and metrics for an employee evaluation.
     * Pulls from attendance, task performance, feedback, and performance flags.
     */
    public function generateAutoFill(Employee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        $metrics = [
            'attendance' => $this->getAttendanceMetrics($employee, $periodStart, $periodEnd),
            'task_performance' => $this->getTaskMetrics($employee, $periodStart, $periodEnd),
            'feedback' => $this->getFeedbackMetrics($employee, $periodStart, $periodEnd),
            'performance_flags' => $this->getFlagMetrics($employee, $periodStart, $periodEnd),
        ];

        $scores = $this->calculateScores($metrics);

        return [
            'scores' => $scores,
            'metrics' => $metrics,
            'strengths' => $this->generateStrengths($scores, $metrics),
            'areas_for_improvement' => $this->generateAreasForImprovement($scores, $metrics),
            'goals_for_next_period' => $this->generateGoals($scores, $metrics),
        ];
    }

    protected function getAttendanceMetrics(Employee $employee, Carbon $start, Carbon $end): array
    {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$start, $end])
            ->get();

        $totalDays = $attendances->count();
        $totalHours = $attendances->sum('hours_worked');

        // Check for late clock-ins (after 8:00 AM as a general measure)
        $lateDays = $attendances->filter(function ($a) {
            return $a->clock_in && Carbon::parse($a->clock_in)->format('H:i') > '08:15';
        })->count();

        // Working days in period (weekdays)
        $expectedDays = $start->diffInWeekdays($end);

        return [
            'total_days_present' => $totalDays,
            'expected_days' => $expectedDays,
            'attendance_rate' => $expectedDays > 0 ? round(($totalDays / $expectedDays) * 100, 1) : 0,
            'total_hours' => round($totalHours, 1),
            'late_days' => $lateDays,
            'punctuality_rate' => $totalDays > 0 ? round((($totalDays - $lateDays) / $totalDays) * 100, 1) : 0,
        ];
    }

    protected function getTaskMetrics(Employee $employee, Carbon $start, Carbon $end): array
    {
        // Daily performance records
        $performances = EmployeePerformance::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $totalTasks = $performances->sum('tasks_completed');
        $avgPerformance = $performances->avg('average_performance') ?? 0;

        // Tasks completed through optimization teams
        $completedTasks = Task::where('status', 'Completed')
            ->whereBetween('completed_at', [$start, $end])
            ->whereHas('assignedEmployees', function ($q) use ($employee) {
                $q->where('employees.id', $employee->id);
            })
            ->count();

        return [
            'tasks_completed' => $totalTasks ?: $completedTasks,
            'average_performance_score' => round($avgPerformance, 2),
            'performance_rating' => $this->getPerformanceLabel($avgPerformance),
        ];
    }

    protected function getFeedbackMetrics(Employee $employee, Carbon $start, Carbon $end): array
    {
        $feedbacks = Feedback::where('employee_id', $employee->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return [
            'total_feedbacks' => $feedbacks->count(),
            'avg_rating' => round($feedbacks->avg('rating') ?? 0, 1),
            'avg_punctuality' => round($feedbacks->avg('punctuality_rating') ?? 0, 1),
            'avg_professionalism' => round($feedbacks->avg('professionalism_rating') ?? 0, 1),
            'avg_quality' => round($feedbacks->avg('quality_rating') ?? 0, 1),
            'positive_keywords' => $feedbacks->pluck('keywords')->flatten()->filter()->countBy()->sortDesc()->take(5)->toArray(),
        ];
    }

    protected function getFlagMetrics(Employee $employee, Carbon $start, Carbon $end): array
    {
        $flags = PerformanceFlag::where('employee_id', $employee->id)
            ->whereBetween('flagged_at', [$start, $end])
            ->get();

        return [
            'total_flags' => $flags->count(),
            'reviewed_flags' => $flags->where('reviewed', true)->count(),
            'avg_variance_pct' => $flags->count() > 0 ? round($flags->avg(fn($f) => $f->getVariancePercentage()), 1) : 0,
        ];
    }

    /**
     * Convert raw metrics into 1-5 scores
     */
    protected function calculateScores(array $metrics): array
    {
        $att = $metrics['attendance'];
        $task = $metrics['task_performance'];
        $fb = $metrics['feedback'];
        $flags = $metrics['performance_flags'];

        return [
            'attendance_score' => $this->ratePercentage($att['attendance_rate']),
            'punctuality_score' => $this->ratePercentage($att['punctuality_rate']),
            'task_completion_score' => $this->rateTaskPerformance($task['average_performance_score']),
            'quality_of_work_score' => $this->rateFeedbackScore($fb['avg_quality'], $flags['total_flags']),
            'professionalism_score' => $this->rateFeedbackScore($fb['avg_professionalism']),
            'teamwork_score' => $this->rateTeamwork($fb['avg_rating'], $task['average_performance_score']),
        ];
    }

    protected function ratePercentage(float $pct): int
    {
        if ($pct >= 95) return 5;
        if ($pct >= 85) return 4;
        if ($pct >= 75) return 3;
        if ($pct >= 60) return 2;
        return 1;
    }

    protected function rateTaskPerformance(float $avgScore): int
    {
        // avgScore is estimated/actual ratio: >1 means faster than estimated
        if ($avgScore >= 1.2) return 5;
        if ($avgScore >= 1.0) return 4;
        if ($avgScore >= 0.8) return 3;
        if ($avgScore >= 0.6) return 2;
        return $avgScore > 0 ? 1 : 3; // Default to 3 if no data
    }

    protected function rateFeedbackScore(float $avgRating, int $flags = 0): int
    {
        if ($avgRating == 0) return 3; // No data defaults to neutral

        $score = min(5, max(1, round($avgRating)));
        // Reduce by 1 if many flags
        if ($flags > 3) $score = max(1, $score - 1);

        return $score;
    }

    protected function rateTeamwork(float $avgRating, float $taskPerf): int
    {
        if ($avgRating == 0 && $taskPerf == 0) return 3;

        // Combine feedback rating and task performance
        $combined = $avgRating > 0 ? $avgRating : 3;
        if ($taskPerf >= 1.0) $combined = min(5, $combined + 0.5);

        return min(5, max(1, round($combined)));
    }

    protected function getPerformanceLabel(float $score): string
    {
        if ($score >= 1.2) return 'Excellent';
        if ($score >= 1.0) return 'Good';
        if ($score >= 0.8) return 'Average';
        if ($score > 0) return 'Needs Improvement';
        return 'No Data';
    }

    protected function generateStrengths(array $scores, array $metrics): string
    {
        $strengths = [];
        $labels = [
            'attendance_score' => 'Attendance',
            'punctuality_score' => 'Punctuality',
            'task_completion_score' => 'Task Completion',
            'quality_of_work_score' => 'Quality of Work',
            'professionalism_score' => 'Professionalism',
            'teamwork_score' => 'Teamwork',
        ];

        foreach ($scores as $key => $score) {
            if ($score >= 4) {
                $strengths[] = $labels[$key] ?? $key;
            }
        }

        if (empty($strengths)) {
            return 'Employee shows consistent performance across evaluated areas.';
        }

        return 'Employee demonstrates strong performance in: ' . implode(', ', $strengths) . '.';
    }

    protected function generateAreasForImprovement(array $scores, array $metrics): string
    {
        $areas = [];
        $labels = [
            'attendance_score' => 'Attendance consistency',
            'punctuality_score' => 'Punctuality and time management',
            'task_completion_score' => 'Task completion efficiency',
            'quality_of_work_score' => 'Quality of work delivered',
            'professionalism_score' => 'Professional conduct',
            'teamwork_score' => 'Team collaboration',
        ];

        foreach ($scores as $key => $score) {
            if ($score <= 2) {
                $areas[] = $labels[$key] ?? $key;
            }
        }

        if (empty($areas)) {
            return 'No critical areas identified. Continue maintaining current performance standards.';
        }

        return 'Areas requiring attention: ' . implode(', ', $areas) . '.';
    }

    protected function generateGoals(array $scores, array $metrics): string
    {
        $goals = [
            'attendance_score' => 'Maintain at least 90% attendance rate throughout the next evaluation period.',
            'punctuality_score' => 'Arrive on time for all scheduled shifts with no more than 1 late arrival per month.',
            'task_completion_score' => 'Complete 100% of assigned tasks within the estimated time each day.',
            'quality_of_work_score' => 'Achieve an average client quality rating of at least 4 out of 5.',
            'professionalism_score' => 'Maintain professional conduct in all client and team interactions.',
            'teamwork_score' => 'Actively collaborate with team members and assist when own tasks are completed.',
        ];

        $selectedGoals = [];

        // Add goals for low-scoring areas (priority)
        foreach ($scores as $key => $score) {
            if ($score <= 2 && isset($goals[$key])) {
                $selectedGoals[] = $goals[$key];
            }
        }

        // Add goals for mid-scoring areas if no low ones
        if (empty($selectedGoals)) {
            foreach ($scores as $key => $score) {
                if ($score <= 3 && isset($goals[$key])) {
                    $selectedGoals[] = $goals[$key];
                }
            }
        }

        // If all scores are high
        if (empty($selectedGoals)) {
            return 'Continue maintaining high performance standards. Consider mentoring newer team members to share best practices.';
        }

        return "Goals for next month:\n- " . implode("\n- ", $selectedGoals);
    }

    /**
     * Generate a pre-filled PIP based on evaluation scores.
     * Returns arrays for areas_to_improve and action_items.
     */
    public function generatePipData(PerformanceEvaluation $evaluation): array
    {
        $criteriaDetails = [
            'attendance_score' => [
                'area' => 'Attendance',
                'details' => 'Employee has not been consistently present during scheduled work days. Frequent absences affect team productivity and service delivery.',
                'actions' => [
                    'Maintain at least 90% attendance rate over the next 30 days',
                    'Notify supervisor at least 24 hours in advance for any planned absence',
                    'Provide documentation for any unplanned absences within 48 hours',
                ],
            ],
            'punctuality_score' => [
                'area' => 'Punctuality',
                'details' => 'Employee has been arriving late or not meeting task deadlines consistently. Timeliness is essential for smooth operations and client satisfaction.',
                'actions' => [
                    'Arrive on time for all scheduled shifts with zero late arrivals for the next 30 days',
                    'Clock in at least 5 minutes before the scheduled start time',
                    'Complete all assigned tasks within the estimated time frame',
                ],
            ],
            'task_completion_score' => [
                'area' => 'Task Completion',
                'details' => 'Employee has not been completing assigned tasks efficiently. Tasks are either left incomplete or take significantly longer than expected.',
                'actions' => [
                    'Complete 100% of assigned tasks each day without leaving any unfinished',
                    'Follow the task checklist for each assignment to ensure thoroughness',
                    'Ask for guidance immediately if a task is unclear rather than delaying',
                ],
            ],
            'quality_of_work_score' => [
                'area' => 'Quality of Work',
                'details' => 'The quality of cleaning services delivered has not met expected standards. Clients have provided feedback indicating room for improvement.',
                'actions' => [
                    'Follow the detailed cleaning checklist for every assignment without skipping steps',
                    'Do a final walkthrough before marking any task as complete',
                    'Achieve an average client quality rating of at least 3.5 out of 5 over the next month',
                ],
            ],
            'professionalism_score' => [
                'area' => 'Professionalism',
                'details' => 'Employee needs to improve professional conduct, including communication with clients and team members, appearance, and workplace behavior.',
                'actions' => [
                    'Maintain a respectful and courteous tone in all client and team interactions',
                    'Follow the company dress code and grooming standards at all times',
                    'Respond to messages and instructions from supervisors within a reasonable time',
                ],
            ],
            'teamwork_score' => [
                'area' => 'Teamwork',
                'details' => 'Employee has not been collaborating effectively with team members. Good teamwork is essential for completing group tasks on time and maintaining morale.',
                'actions' => [
                    'Actively assist team members when your own tasks are completed early',
                    'Communicate clearly with team partners about task division at the start of each assignment',
                    'Participate constructively in any team discussions or briefings',
                ],
            ],
        ];

        $areasToImprove = [];
        $actionItems = [];
        $startDate = now();

        foreach ($criteriaDetails as $field => $detail) {
            $score = $evaluation->$field;
            if ($score !== null && $score <= 2) {
                $areasToImprove[] = [
                    'area' => $detail['area'],
                    'details' => $detail['details'],
                ];

                foreach ($detail['actions'] as $index => $action) {
                    $actionItems[] = [
                        'description' => $action,
                        'target_date' => $startDate->copy()->addDays(($index + 1) * 10)->toDateString(),
                        'status' => 'pending',
                    ];
                }
            }
        }

        // If no criteria scored <= 2 but PIP was still checked, use criteria <= 3
        if (empty($areasToImprove)) {
            foreach ($criteriaDetails as $field => $detail) {
                $score = $evaluation->$field;
                if ($score !== null && $score <= 3) {
                    $areasToImprove[] = [
                        'area' => $detail['area'],
                        'details' => $detail['details'],
                    ];

                    $actionItems[] = [
                        'description' => $detail['actions'][0],
                        'target_date' => $startDate->copy()->addDays(30)->toDateString(),
                        'status' => 'pending',
                    ];
                }
            }
        }

        $employee = $evaluation->employee;

        return [
            'title' => 'Performance Improvement Plan - ' . $employee->fullName,
            'description' => 'Based on the ' . $evaluation->evaluation_period_start->format('F Y') . ' performance evaluation, '
                . $employee->fullName . ' has been identified for a structured improvement plan to address the areas listed below.',
            'areas_to_improve' => $areasToImprove,
            'action_items' => $actionItems,
            'start_date' => $startDate->toDateString(),
            'end_date' => $startDate->copy()->addDays(30)->toDateString(),
        ];
    }
}