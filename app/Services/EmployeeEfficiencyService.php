<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ✅ STAGE 3: Employee Efficiency Service
 *
 * Recalculates an employee's efficiency after a task is completed, using
 * the 0.7/0.3 exponential moving average from the spec:
 *
 *   newEfficiency = (currentEfficiency × 0.7) + (performanceRatio × 0.3)
 *
 * Where:
 *   contributionRatio = subtasks_completed_by_employee / total_subtasks_for_task
 *   expectedTime      = task.duration × contributionRatio
 *   actualTime        = MAX(completed_at) - MIN(completed_at) for that employee on that task
 *   performanceRatio  = expectedTime / actualTime  (capped at 1.0, floored at 0.0)
 *
 * Constraints:
 *   - Max efficiency = 1.0 (no "faster than estimate" bonus)
 *   - Min efficiency = 0.5 (bad day doesn't destroy the score)
 *   - 0.3 weight means roughly the last 3-4 tasks have the most influence
 */
class EmployeeEfficiencyService
{
    protected const BLEND_NEW = 0.3;
    protected const BLEND_OLD = 0.7;
    protected const MIN_EFFICIENCY = 0.5;
    protected const MAX_EFFICIENCY = 1.0;

    /**
     * Recalculate efficiency for all employees who contributed to a completed task.
     *
     * Called by the TaskCompletionObserver when a task's status flips to "Completed".
     *
     * @param int $taskId
     * @return array Summary of updates: ['employee_id' => ['old' => x, 'new' => y, ...], ...]
     */
    public function recalculateForCompletedTask(int $taskId): array
    {
        // 1. Get the task's duration (the estimated time the work should take)
        $task = DB::table('tasks')
            ->select('id', 'duration', 'estimated_duration_minutes')
            ->where('id', $taskId)
            ->first();

        if (!$task) {
            Log::warning('EmployeeEfficiencyService: task not found', ['task_id' => $taskId]);
            return [];
        }

        $taskDuration = (float) ($task->duration ?? $task->estimated_duration_minutes ?? 60);

        // 2. Get all checklist completions for this task, grouped by employee
        //    completed_by stores user_id, so we join employees via user_id
        $completions = DB::table('task_checklist_completions as tcc')
            ->join('employees as e', function ($join) {
                // completed_by = users.id, employees.user_id = users.id
                $join->on('tcc.completed_by', '=', 'e.user_id');
            })
            ->where('tcc.task_id', $taskId)
            ->where('tcc.is_completed', true)
            ->whereNotNull('tcc.completed_at')
            ->select(
                'e.id as employee_id',
                'e.efficiency as current_efficiency',
                'tcc.completed_at'
            )
            ->get();

        if ($completions->isEmpty()) {
            Log::info('EmployeeEfficiencyService: no checklist completions for task', [
                'task_id' => $taskId,
            ]);
            return [];
        }

        $totalSubtasks = $completions->count();

        // 3. Group by employee
        $byEmployee = $completions->groupBy('employee_id');

        $results = [];

        foreach ($byEmployee as $employeeId => $empCompletions) {
            // Contribution ratio: what fraction of the checklist did this employee do?
            $subtasksDone = $empCompletions->count();
            $contributionRatio = $subtasksDone / $totalSubtasks;

            // Expected time: how long should their share have taken?
            $expectedTime = $taskDuration * $contributionRatio;

            // Actual time: span from their first to last checklist check
            $timestamps = $empCompletions->pluck('completed_at')
                ->map(fn($ts) => strtotime($ts))
                ->filter(fn($ts) => $ts !== false);

            if ($timestamps->count() < 2) {
                // Only 1 checklist item — can't measure a time span.
                // Treat as on-time (ratio = 1.0) so the score doesn't change.
                $performanceRatio = self::MAX_EFFICIENCY;
                $actualTime = $expectedTime;
            } else {
                $actualTime = max(1, $timestamps->max() - $timestamps->min()) / 60; // minutes
                $rawRatio = $expectedTime / $actualTime;
                // Cap at MAX_EFFICIENCY — no faster-than-estimate bonus
                $performanceRatio = min(self::MAX_EFFICIENCY, max(0.0, $rawRatio));
            }

            // Apply the exponential moving average
            $currentEfficiency = (float) ($empCompletions->first()->current_efficiency ?? 1.0);
            $newEfficiency = (self::BLEND_OLD * $currentEfficiency) + (self::BLEND_NEW * $performanceRatio);

            // Clamp to [MIN, MAX]
            $newEfficiency = round(
                max(self::MIN_EFFICIENCY, min(self::MAX_EFFICIENCY, $newEfficiency)),
                4
            );

            // 4. Update the employee's efficiency in the database
            DB::table('employees')
                ->where('id', $employeeId)
                ->update(['efficiency' => $newEfficiency]);

            $results[$employeeId] = [
                'subtasks_done' => $subtasksDone,
                'total_subtasks' => $totalSubtasks,
                'contribution_ratio' => round($contributionRatio, 4),
                'expected_time_min' => round($expectedTime, 2),
                'actual_time_min' => round($actualTime ?? 0, 2),
                'performance_ratio' => round($performanceRatio, 4),
                'old_efficiency' => $currentEfficiency,
                'new_efficiency' => $newEfficiency,
            ];
        }

        Log::info('EmployeeEfficiencyService: recalculated after task completion', [
            'task_id' => $taskId,
            'task_duration' => $taskDuration,
            'total_subtasks' => $totalSubtasks,
            'employees_updated' => $results,
        ]);

        return $results;
    }
}
