<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\Optimization\OptimizationService;
use Illuminate\Support\Facades\Log;

/**
 * ✅ STAGE 2.5: Task Approval Observer (Strict Timeline Mode)
 *
 * Watches every Task update. When the employee_approved field changes
 * (true ↔ false ↔ null), it triggers OptimizationService::recomputeTeamTimetable
 * for the affected team so the timeline reflects only confirmed work.
 *
 * Why an observer?
 *  - It runs automatically — no controller needs to remember to call it.
 *  - It catches every approve/decline path (web UI, API, mobile, scheduled commands).
 *  - It's synchronous, so the next page load already shows the recomputed times.
 *
 * Why this is safe:
 *  - We use saveQuietly() inside recomputeTeamTimetable() so the recompute itself
 *    doesn't trigger this observer again (no infinite loop).
 *  - We only act when assigned_team_id and scheduled_date are present (skip
 *    tasks that haven't been optimized yet).
 *  - Wrapped in a DB transaction inside the service.
 */
class TaskApprovalObserver
{
    public function __construct(protected OptimizationService $optimizationService)
    {
    }

    /**
     * Fires after a Task is updated.
     */
    public function updated(Task $task): void
    {
        // Only react when the approval field actually changed
        if (!$task->wasChanged('employee_approved')) {
            return;
        }

        // Skip tasks that aren't part of an optimized schedule
        if (empty($task->assigned_team_id) || empty($task->scheduled_date)) {
            return;
        }

        $serviceDate = \Carbon\Carbon::parse($task->scheduled_date)->format('Y-m-d');

        try {
            $this->optimizationService->recomputeTeamTimetable(
                (int) $task->assigned_team_id,
                $serviceDate
            );
        } catch (\Throwable $e) {
            // Don't break the approve/decline flow if recompute hiccups —
            // log it and let the manager re-optimize manually if needed.
            Log::error('TaskApprovalObserver: recompute failed', [
                'task_id' => $task->id,
                'team_id' => $task->assigned_team_id,
                'service_date' => $serviceDate,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
