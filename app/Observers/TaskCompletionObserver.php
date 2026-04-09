<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\EmployeeEfficiencyService;
use Illuminate\Support\Facades\Log;

/**
 * ✅ STAGE 3: Task Completion Observer
 *
 * When a task's status flips to "Completed", triggers the employee efficiency
 * recalculation for every employee who contributed checklist items to that task.
 *
 * This is the feedback loop described in the spec:
 *   "After each completed task, this formula recalculates and updates that value."
 *
 * The updated efficiency is then used by the NEXT optimization run — teams with
 * consistently faster employees get tighter schedules (effective_duration = base / eff),
 * and slower employees get more breathing room.
 */
class TaskCompletionObserver
{
    public function __construct(protected EmployeeEfficiencyService $efficiencyService)
    {
    }

    public function updated(Task $task): void
    {
        // Only react when status just changed TO "Completed"
        if (!$task->wasChanged('status')) {
            return;
        }

        if ($task->status !== 'Completed') {
            return;
        }

        try {
            $this->efficiencyService->recalculateForCompletedTask($task->id);
        } catch (\Throwable $e) {
            // Don't break the completion flow if efficiency calc hiccups
            Log::error('TaskCompletionObserver: efficiency recalculation failed', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
