<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceEvaluation;
use App\Models\PerformanceImprovementPlan;
use Illuminate\Http\Request;

class PerformanceController extends Controller
{
    /**
     * Get active PIPs for the authenticated employee
     */
    public function getActivePips(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['pips' => []], 200);
        }

        $pips = PerformanceImprovementPlan::with(['evaluation', 'creator'])
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($pip) {
                return [
                    'id' => $pip->id,
                    'title' => $pip->title,
                    'description' => $pip->description,
                    'status' => $pip->status,
                    'start_date' => $pip->start_date->toDateString(),
                    'end_date' => $pip->end_date->toDateString(),
                    'is_overdue' => $pip->isOverdue(),
                    'progress' => $pip->getProgressPercentage(),
                    'areas_to_improve' => $pip->areas_to_improve,
                    'action_items' => $pip->action_items,
                    'outcome_notes' => $pip->outcome_notes,
                    'created_by' => $pip->creator->name ?? 'Admin',
                    'created_at' => $pip->created_at->toDateString(),
                    'evaluation' => $pip->evaluation ? [
                        'period' => $pip->evaluation->evaluation_period_start->format('F Y'),
                        'overall_rating' => $pip->evaluation->overall_rating,
                        'rating_label' => $pip->evaluation->getRatingLabel(),
                    ] : null,
                ];
            });

        return response()->json(['pips' => $pips], 200);
    }

    /**
     * Get PIP details
     */
    public function getPipDetails(Request $request, $pipId)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $pip = PerformanceImprovementPlan::with(['evaluation', 'creator'])
            ->where('employee_id', $employee->id)
            ->where('id', $pipId)
            ->first();

        if (!$pip) {
            return response()->json(['error' => 'PIP not found'], 404);
        }

        return response()->json([
            'pip' => [
                'id' => $pip->id,
                'title' => $pip->title,
                'description' => $pip->description,
                'status' => $pip->status,
                'start_date' => $pip->start_date->toDateString(),
                'end_date' => $pip->end_date->toDateString(),
                'is_overdue' => $pip->isOverdue(),
                'progress' => $pip->getProgressPercentage(),
                'days_remaining' => $pip->end_date->isPast()
                    ? -$pip->end_date->diffInDays(now())
                    : $pip->end_date->diffInDays(now()),
                'duration_days' => $pip->start_date->diffInDays($pip->end_date),
                'areas_to_improve' => $pip->areas_to_improve,
                'action_items' => $pip->action_items,
                'outcome_notes' => $pip->outcome_notes,
                'created_by' => $pip->creator->name ?? 'Admin',
                'created_at' => $pip->created_at->toDateString(),
                'evaluation' => $pip->evaluation ? [
                    'period' => $pip->evaluation->evaluation_period_start->format('F Y'),
                    'overall_rating' => number_format($pip->evaluation->overall_rating, 1),
                    'rating_label' => $pip->evaluation->getRatingLabel(),
                    'scores' => [
                        'Attendance' => $pip->evaluation->attendance_score,
                        'Punctuality' => $pip->evaluation->punctuality_score,
                        'Task Completion' => $pip->evaluation->task_completion_score,
                        'Quality of Work' => $pip->evaluation->quality_of_work_score,
                        'Professionalism' => $pip->evaluation->professionalism_score,
                        'Teamwork' => $pip->evaluation->teamwork_score,
                    ],
                ] : null,
            ],
        ], 200);
    }

    /**
     * Get latest evaluation summary for the employee
     */
    public function getLatestEvaluation(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['evaluation' => null], 200);
        }

        $evaluation = PerformanceEvaluation::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->orderByDesc('evaluation_period_start')
            ->first();

        if (!$evaluation) {
            return response()->json(['evaluation' => null], 200);
        }

        return response()->json([
            'evaluation' => [
                'period' => $evaluation->evaluation_period_start->format('F Y'),
                'overall_rating' => number_format($evaluation->overall_rating, 1),
                'rating_label' => $evaluation->getRatingLabel(),
                'scores' => [
                    'Attendance' => $evaluation->attendance_score,
                    'Punctuality' => $evaluation->punctuality_score,
                    'Task Completion' => $evaluation->task_completion_score,
                    'Quality of Work' => $evaluation->quality_of_work_score,
                    'Professionalism' => $evaluation->professionalism_score,
                    'Teamwork' => $evaluation->teamwork_score,
                ],
                'strengths' => $evaluation->strengths,
                'areas_for_improvement' => $evaluation->areas_for_improvement,
                'goals_for_next_period' => $evaluation->goals_for_next_period,
                'requires_pip' => $evaluation->requires_pip,
            ],
        ], 200);
    }
}
