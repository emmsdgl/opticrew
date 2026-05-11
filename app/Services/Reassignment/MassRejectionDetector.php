<?php

namespace App\Services\Reassignment;

use App\Models\Employee;
use App\Models\TaskRejection;
use Carbon\Carbon;

/**
 * Detects whether a mass-rejection event has occurred — i.e., enough
 * rejections have arrived in a short window that the per-rejection cascade
 * should pause and a global re-optimization should be considered.
 *
 * Trigger (whichever is higher):
 *   - At least N absolute rejections in a rolling W-hour window, OR
 *   - At least P% of currently-assigned employees have rejected in W hours.
 *
 * The thresholds N, W, and P are tunable via config('rejection.mass_rejection.*').
 *
 * This detector is read-only: it inspects the task_rejections table and
 * returns a snapshot. The caller decides what to do (notify admins,
 * pause cascade, etc.). See docs/task-rejection-reassignment-policy.md (§4 mass-rejection).
 */
class MassRejectionDetector
{
    /**
     * Inspect recent rejections and return whether the threshold is tripped,
     * with diagnostic data. Returns:
     *   [
     *     'tripped' => bool,
     *     'window_hours' => int,
     *     'rejection_count' => int,
     *     'rejecting_employee_count' => int,
     *     'assigned_employee_count' => int,
     *     'percent_rejecting' => float,
     *     'min_count_threshold' => int,
     *     'min_percent_threshold' => int,
     *     'reason' => string,  // human-readable description of why tripped (or not)
     *   ]
     */
    public function evaluate(): array
    {
        $config = config('rejection.mass_rejection', []);
        $minCount = (int) ($config['min_count'] ?? 3);
        $minPercent = (int) ($config['min_percent'] ?? 25);
        $windowHours = (int) ($config['window_hours'] ?? 4);

        $cutoff = Carbon::now()->subHours($windowHours);

        // All rejections in the window.
        $recentRejections = TaskRejection::where('rejected_at', '>=', $cutoff)->get();
        $rejectionCount = $recentRejections->count();
        $rejectingEmployeeCount = $recentRejections->pluck('employee_id')->unique()->count();

        // Currently-assigned employees: anyone who is a member of an
        // OptimizationTeam that has at least one upcoming or in-progress
        // task. Cheap proxy: count active employees overall.
        // Refining this is a later improvement; for now, a stable baseline.
        $assignedEmployeeCount = Employee::where('is_active', true)->count();

        $percentRejecting = $assignedEmployeeCount > 0
            ? round(($rejectingEmployeeCount / $assignedEmployeeCount) * 100, 2)
            : 0.0;

        // "Whichever is higher" semantics: trip if EITHER threshold met.
        // (The doc says "whichever is higher" referring to which of the two
        //  thresholds resolves to a higher absolute number — implementation-wise,
        //  treating either as sufficient to trip is the safer reading.)
        $countTripped = $rejectionCount >= $minCount;
        $percentTripped = $percentRejecting >= $minPercent;
        $tripped = $countTripped || $percentTripped;

        $reason = match (true) {
            $countTripped && $percentTripped =>
                "Mass-rejection: {$rejectionCount} rejections AND {$percentRejecting}% of assigned employees in {$windowHours}h.",
            $countTripped =>
                "Mass-rejection: {$rejectionCount} rejections (≥{$minCount}) in {$windowHours}h.",
            $percentTripped =>
                "Mass-rejection: {$percentRejecting}% of assigned employees rejecting (≥{$minPercent}%) in {$windowHours}h.",
            default =>
                "Below thresholds ({$rejectionCount} rejections, {$percentRejecting}% of workforce, in {$windowHours}h).",
        };

        return [
            'tripped' => $tripped,
            'window_hours' => $windowHours,
            'rejection_count' => $rejectionCount,
            'rejecting_employee_count' => $rejectingEmployeeCount,
            'assigned_employee_count' => $assignedEmployeeCount,
            'percent_rejecting' => $percentRejecting,
            'min_count_threshold' => $minCount,
            'min_percent_threshold' => $minPercent,
            'reason' => $reason,
        ];
    }
}
