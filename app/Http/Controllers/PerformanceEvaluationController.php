<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Task;
use App\Models\PerformanceEvaluation;
use App\Models\PerformanceImprovementPlan;
use App\Services\Notification\NotificationService;
use App\Services\PerformanceManagementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceEvaluationController extends Controller
{
    protected $performanceService;

    public function __construct(PerformanceManagementService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Performance management dashboard - list all employees with evaluation status
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $employees = Employee::with(['user'])
            ->where('is_active', true)
            ->get()
            ->map(function ($employee) use ($periodStart, $periodEnd) {
                $evaluation = PerformanceEvaluation::where('employee_id', $employee->id)
                    ->where('evaluation_period_start', $periodStart->toDateString())
                    ->first();

                $employee->current_evaluation = $evaluation;

                $activePip = PerformanceImprovementPlan::where('employee_id', $employee->id)
                    ->where('status', 'active')
                    ->first();

                $employee->has_active_pip = $activePip !== null;
                $employee->pip_pending = !$activePip && $evaluation && $evaluation->requires_pip;

                return $employee;
            });

        $stats = [
            'total_employees' => $employees->count(),
            'evaluated' => $employees->filter(fn($e) => $e->current_evaluation && $e->current_evaluation->status === 'completed')->count(),
            'pending' => $employees->filter(fn($e) => !$e->current_evaluation)->count(),
            'drafts' => $employees->filter(fn($e) => $e->current_evaluation && $e->current_evaluation->status === 'draft')->count(),
            'active_pips' => $employees->filter(fn($e) => $e->has_active_pip || $e->pip_pending)->count(),
        ];

        // --- Employee Efficiency Data ---
        $efficiencyRecords = $employees->map(function ($employee) use ($periodStart, $periodEnd) {
            $totalTasks = Task::whereHas('optimizationTeam.members', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->whereBetween('scheduled_date', [$periodStart, $periodEnd])
                ->whereNull('deleted_at')
                ->count();

            $completedTasks = Task::whereHas('optimizationTeam.members', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->whereBetween('scheduled_date', [$periodStart, $periodEnd])
                ->whereNull('deleted_at')
                ->where('status', 'Completed')
                ->count();

            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
            $efficiencyScore = $completionRate;
            $status = $efficiencyScore >= 70 ? 'High' : ($efficiencyScore < 50 ? 'Low' : 'Medium');

            return [
                'name' => $employee->user->name ?? 'Unknown',
                'email' => $employee->user->email ?? '',
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $completionRate,
                'efficiency_score' => $efficiencyScore,
                'status' => $status,
            ];
        })->sortByDesc('efficiency_score')->values();

        return view('admin.reports.performance.index', compact('employees', 'stats', 'month', 'year', 'periodStart', 'periodEnd', 'efficiencyRecords'));
    }

    /**
     * Show evaluation form for a specific employee
     */
    public function create(Request $request, $employeeId)
    {
        $employee = Employee::with('user')->findOrFail($employeeId);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        // Check if evaluation already exists
        $evaluation = PerformanceEvaluation::where('employee_id', $employee->id)
            ->where('evaluation_period_start', $periodStart->toDateString())
            ->first();

        // Get previous evaluations for context
        $previousEvaluations = PerformanceEvaluation::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->orderBy('evaluation_period_end', 'desc')
            ->take(3)
            ->get();

        return view('admin.reports.performance.evaluate', compact(
            'employee', 'evaluation', 'previousEvaluations',
            'month', 'year', 'periodStart', 'periodEnd'
        ));
    }

    /**
     * Auto-fill evaluation scores via AJAX
     */
    public function autoFill(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $autoFillData = $this->performanceService->generateAutoFill($employee, $periodStart, $periodEnd);

        return response()->json($autoFillData);
    }

    /**
     * Store or update evaluation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'evaluation_period_start' => 'required|date',
            'evaluation_period_end' => 'required|date|after:evaluation_period_start',
            'attendance_score' => 'required|integer|min:1|max:5',
            'punctuality_score' => 'required|integer|min:1|max:5',
            'task_completion_score' => 'required|integer|min:1|max:5',
            'quality_of_work_score' => 'required|integer|min:1|max:5',
            'professionalism_score' => 'required|integer|min:1|max:5',
            'teamwork_score' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string|max:2000',
            'areas_for_improvement' => 'nullable|string|max:2000',
            'goals_for_next_period' => 'nullable|string|max:2000',
            'admin_comments' => 'nullable|string|max:2000',
            'requires_pip' => 'boolean',
            'system_metrics' => 'nullable|json',
            'status' => 'required|in:draft,completed',
        ]);

        $scores = [
            $validated['attendance_score'],
            $validated['punctuality_score'],
            $validated['task_completion_score'],
            $validated['quality_of_work_score'],
            $validated['professionalism_score'],
            $validated['teamwork_score'],
        ];
        $validated['overall_rating'] = round(array_sum($scores) / count($scores), 2);
        $validated['evaluator_id'] = Auth::id();
        $validated['requires_pip'] = $request->has('requires_pip');

        if (!empty($validated['system_metrics'])) {
            $validated['system_metrics'] = json_decode($validated['system_metrics'], true);
        }

        $evaluation = PerformanceEvaluation::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'evaluation_period_start' => $validated['evaluation_period_start'],
            ],
            $validated
        );

        // If PIP was unchecked, remove any existing PIP for this evaluation
        if (!$validated['requires_pip']) {
            $existingPip = PerformanceImprovementPlan::where('evaluation_id', $evaluation->id)->first();
            if ($existingPip) {
                // Notify employee that PIP was cancelled
                $this->notifyEmployeePipStatusChanged($existingPip->load('employee'), 'cancelled');
                $existingPip->delete();
            }
        }

        // If completed with PIP required, auto-generate and redirect to PIP form for review
        if ($validated['status'] === 'completed' && !empty($validated['requires_pip'])) {
            $existingPip = PerformanceImprovementPlan::where('evaluation_id', $evaluation->id)->first();
            if (!$existingPip) {
                $pipData = $this->performanceService->generatePipData($evaluation->load('employee'));

                return redirect()->route('admin.reports.performance.pip.create', $evaluation->id)
                    ->with('success', 'Evaluation completed. Please review the pre-filled Performance Improvement Plan below.')
                    ->with('pip_prefill', $pipData);
            }
        }

        $message = $validated['status'] === 'completed'
            ? 'Evaluation completed successfully.'
            : 'Evaluation saved as draft.';

        return redirect()->route('admin.reports.performance.index', [
            'month' => Carbon::parse($validated['evaluation_period_start'])->month,
            'year' => Carbon::parse($validated['evaluation_period_start'])->year,
        ])->with('success', $message);
    }

    /**
     * Show evaluation details
     */
    public function show($id)
    {
        $evaluation = PerformanceEvaluation::with(['employee.user', 'evaluator', 'improvementPlan'])->findOrFail($id);

        return view('admin.reports.performance.show', compact('evaluation'));
    }

    /**
     * Auto-fill ALL pending employees for a month
     */
    public function autoFillAll(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $employees = Employee::where('is_active', true)
            ->whereDoesntHave('performanceEvaluations', function ($q) use ($periodStart) {
                $q->where('evaluation_period_start', $periodStart->toDateString());
            })
            ->get();

        $created = 0;
        foreach ($employees as $employee) {
            $autoFill = $this->performanceService->generateAutoFill($employee, $periodStart, $periodEnd);

            PerformanceEvaluation::create([
                'employee_id' => $employee->id,
                'evaluator_id' => Auth::id(),
                'evaluation_period_start' => $periodStart->toDateString(),
                'evaluation_period_end' => $periodEnd->toDateString(),
                'status' => 'draft',
                'attendance_score' => $autoFill['scores']['attendance_score'],
                'punctuality_score' => $autoFill['scores']['punctuality_score'],
                'task_completion_score' => $autoFill['scores']['task_completion_score'],
                'quality_of_work_score' => $autoFill['scores']['quality_of_work_score'],
                'professionalism_score' => $autoFill['scores']['professionalism_score'],
                'teamwork_score' => $autoFill['scores']['teamwork_score'],
                'overall_rating' => round(array_sum($autoFill['scores']) / count($autoFill['scores']), 2),
                'strengths' => $autoFill['strengths'],
                'areas_for_improvement' => $autoFill['areas_for_improvement'],
                'goals_for_next_period' => $autoFill['goals_for_next_period'],
                'system_metrics' => $autoFill['metrics'],
                'requires_pip' => round(array_sum($autoFill['scores']) / count($autoFill['scores']), 2) <= 2.0,
            ]);
            $created++;
        }

        return redirect()->back()->with('success', "Auto-filled evaluations for {$created} employees as drafts. Please review each one.");
    }

    /**
     * Complete all draft evaluations that have all feedback fields filled in.
     */
    public function completeAll(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();

        $drafts = PerformanceEvaluation::where('evaluation_period_start', $periodStart->toDateString())
            ->where('status', 'draft')
            ->get();

        $completed = 0;
        $skipped = 0;

        foreach ($drafts as $evaluation) {
            // Check that all required feedback fields are filled
            if (empty($evaluation->strengths) || empty($evaluation->areas_for_improvement) || empty($evaluation->goals_for_next_period)) {
                $skipped++;
                continue;
            }

            $evaluation->update(['status' => 'completed']);
            $completed++;

            // Auto-create PIP for employees that need it
            if ($evaluation->requires_pip) {
                $existingPip = PerformanceImprovementPlan::where('evaluation_id', $evaluation->id)->exists();
                if (!$existingPip) {
                    $pipData = $this->performanceService->generatePipData($evaluation->load('employee'));
                    $newPip = PerformanceImprovementPlan::create([
                        'employee_id' => $evaluation->employee_id,
                        'evaluation_id' => $evaluation->id,
                        'created_by' => Auth::id(),
                        'title' => $pipData['title'],
                        'description' => $pipData['description'],
                        'areas_to_improve' => $pipData['areas_to_improve'],
                        'action_items' => $pipData['action_items'],
                        'start_date' => $pipData['start_date'],
                        'end_date' => $pipData['end_date'],
                        'status' => 'active',
                    ]);
                    $this->notifyEmployeePipAssigned($newPip);
                }
            }
        }

        $message = "Completed {$completed} evaluation(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} — missing Strengths, Areas for Improvement, or Goals.";
        }

        return redirect()->back()->with('success', $message);
    }

    // ---- PIP Methods ----

    public function createPip($evaluationId)
    {
        $evaluation = PerformanceEvaluation::with(['employee.user'])->findOrFail($evaluationId);

        return view('admin.reports.performance.pip-form', compact('evaluation'));
    }

    public function storePip(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'evaluation_id' => 'nullable|exists:performance_evaluations,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'areas_to_improve' => 'required|array|min:1',
            'areas_to_improve.*.area' => 'required|string',
            'areas_to_improve.*.details' => 'required|string',
            'action_items' => 'required|array|min:1',
            'action_items.*.description' => 'required|string',
            'action_items.*.target_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Add pending status to action items
        $actionItems = collect($validated['action_items'])->map(function ($item) {
            $item['status'] = 'pending';
            return $item;
        })->toArray();

        $pip = PerformanceImprovementPlan::create([
            'employee_id' => $validated['employee_id'],
            'evaluation_id' => $validated['evaluation_id'],
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'areas_to_improve' => $validated['areas_to_improve'],
            'action_items' => $actionItems,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'active',
        ]);

        // Notify employee
        $this->notifyEmployeePipAssigned($pip);

        return redirect()->route('admin.reports.performance.show', $pip->evaluation_id ?? $pip->id)
            ->with('success', 'Performance Improvement Plan created successfully.');
    }

    public function showPip($id)
    {
        $pip = PerformanceImprovementPlan::with(['employee.user', 'evaluation', 'creator'])->findOrFail($id);

        return view('admin.reports.performance.pip-show', compact('pip'));
    }

    public function updatePipStatus(Request $request, $id)
    {
        $pip = PerformanceImprovementPlan::with('employee.user')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,completed,extended,cancelled',
            'outcome_notes' => 'nullable|string|max:2000',
        ]);

        $oldStatus = $pip->status;
        $pip->update($validated);

        // Notify employee of status change
        if ($oldStatus !== $validated['status']) {
            $this->notifyEmployeePipStatusChanged($pip, $validated['status']);
        }

        return redirect()->back()->with('success', 'PIP status updated.');
    }

    // ---- Notification Helpers ----

    protected function notifyEmployeePipAssigned(PerformanceImprovementPlan $pip): void
    {
        $employee = Employee::with('user')->find($pip->employee_id);
        if (!$employee || !$employee->user) return;

        $areas = collect($pip->areas_to_improve)->pluck('area')->implode(', ');
        $notificationService = app(NotificationService::class);

        $notificationService->create(
            $employee->user,
            Notification::TYPE_PIP_ASSIGNED,
            'Performance Improvement Plan Assigned',
            "You have been placed on a Performance Improvement Plan to help you improve in: {$areas}. The plan runs from {$pip->start_date->format('M d')} to {$pip->end_date->format('M d, Y')}. Please review the details and action items.",
            [
                'pip_id' => $pip->id,
                'areas' => $areas,
                'start_date' => $pip->start_date->toDateString(),
                'end_date' => $pip->end_date->toDateString(),
            ]
        );
    }

    protected function notifyEmployeePipStatusChanged(PerformanceImprovementPlan $pip, string $newStatus): void
    {
        $employee = $pip->employee ?? Employee::with('user')->find($pip->employee_id);
        if (!$employee || !$employee->user) return;

        $messages = [
            'completed' => 'Congratulations! You have successfully completed your Performance Improvement Plan. Great job on meeting the improvement goals.',
            'extended' => "Your Performance Improvement Plan has been extended to {$pip->end_date->format('M d, Y')}. Please continue working on the action items.",
            'cancelled' => 'Your Performance Improvement Plan has been cancelled. Please speak with your supervisor if you have any questions.',
        ];

        $message = $messages[$newStatus] ?? "Your Performance Improvement Plan status has been updated to: {$newStatus}.";

        $notificationService = app(NotificationService::class);
        $notificationService->create(
            $employee->user,
            Notification::TYPE_PIP_STATUS_UPDATED,
            'Improvement Plan Update: ' . ucfirst($newStatus),
            $message,
            [
                'pip_id' => $pip->id,
                'new_status' => $newStatus,
            ]
        );
    }
}