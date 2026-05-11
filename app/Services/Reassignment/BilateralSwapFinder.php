<?php

namespace App\Services\Reassignment;

use App\Models\OptimizationTeam;
use App\Models\Task;
use Carbon\Carbon;

/**
 * Finds bilateral team-level swaps for a rejected task (Try 1 of the cascade).
 *
 * Given rejected Task A currently on Team X, search for another Team Y that
 * holds Task B such that swapping is feasible:
 *   - Both teams operate on the same service_date.
 *   - Task A fits in Team Y's day (no time conflict with Y's other tasks).
 *   - Task B fits in Team X's day (no time conflict with X's other tasks).
 *
 * Returns the best candidate (or null), with all candidates ranked by a
 * composite score that prefers minimal disruption to total schedule fitness.
 *
 * Side effects: none. Caller decides whether to commit the swap.
 *
 * See docs/task-rejection-reassignment-policy.md (§4 Try 1).
 */
class BilateralSwapFinder
{
    /**
     * Find the best swap for a rejected task. Returns:
     *   ['swap_partner_team_id' => int, 'swap_partner_task_id' => int, 'score' => float, 'rationale' => string]
     * or null if no feasible swap exists.
     */
    public function findBestSwap(Task $rejectedTask): ?array
    {
        $candidates = $this->findCandidates($rejectedTask);
        if (empty($candidates)) {
            return null;
        }

        // Already sorted by score desc in findCandidates().
        return $candidates[0];
    }

    /**
     * Enumerate all feasible swap (partner_team, partner_task) pairs, ranked.
     */
    public function findCandidates(Task $rejectedTask): array
    {
        if (!$rejectedTask->scheduled_date || !$rejectedTask->assigned_team_id) {
            return [];
        }

        // Original team (where task was rejected).
        $originalTeam = OptimizationTeam::with('tasks')->find($rejectedTask->assigned_team_id);
        if (!$originalTeam) {
            return [];
        }

        // Other teams on the same date that could potentially swap.
        $otherTeams = OptimizationTeam::with('tasks')
            ->where('service_date', $rejectedTask->scheduled_date)
            ->where('id', '!=', $rejectedTask->assigned_team_id)
            ->get();

        $candidates = [];

        foreach ($otherTeams as $otherTeam) {
            foreach ($otherTeam->tasks as $otherTask) {
                // Skip tasks that aren't in a swap-eligible status.
                if (!in_array($otherTask->status, ['Pending', 'Scheduled'], true)) {
                    continue;
                }

                if (!$this->bothFit($rejectedTask, $originalTeam, $otherTask, $otherTeam)) {
                    continue;
                }

                $score = $this->scoreSwap($rejectedTask, $originalTeam, $otherTask, $otherTeam);

                $candidates[] = [
                    'swap_partner_team_id' => $otherTeam->id,
                    'swap_partner_task_id' => $otherTask->id,
                    'swap_partner_team_name' => $otherTeam->team_name,
                    'swap_partner_task_description' => $otherTask->task_description,
                    'score' => $score,
                    'rationale' => "Swap with {$otherTeam->team_name} (task #{$otherTask->id})",
                ];
            }
        }

        // Higher score = better swap.
        usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);
        return $candidates;
    }

    /**
     * Both halves must be feasible:
     *   - Rejected task fits in the partner team's day (no overlap).
     *   - Partner task fits in the original team's day (no overlap).
     */
    protected function bothFit(
        Task $rejectedTask,
        OptimizationTeam $originalTeam,
        Task $partnerTask,
        OptimizationTeam $partnerTeam
    ): bool {
        // Build "if the swap happens" task set for each team.
        $partnerAfterSwap = $partnerTeam->tasks
            ->reject(fn (Task $t) => $t->id === $partnerTask->id)
            ->push($rejectedTask);

        $originalAfterSwap = $originalTeam->tasks
            ->reject(fn (Task $t) => $t->id === $rejectedTask->id)
            ->push($partnerTask);

        return $this->noOverlap($partnerAfterSwap) && $this->noOverlap($originalAfterSwap);
    }

    /**
     * Returns true iff no two tasks in the collection overlap in time.
     * Tasks without a scheduled_time are skipped (treated as already-placed
     * elsewhere and not contributing to the time-conflict check).
     */
    protected function noOverlap($tasks): bool
    {
        $intervals = [];
        foreach ($tasks as $t) {
            $start = $this->taskStart($t);
            if (!$start) {
                continue;
            }
            $duration = (int) ($t->estimated_duration_minutes ?? 60);
            $end = $start->copy()->addMinutes(max(1, $duration));
            $intervals[] = [$start, $end];
        }

        // O(n²) overlap check — n is small (typically ≤ 10 per team per day).
        foreach ($intervals as $i => [$aStart, $aEnd]) {
            foreach ($intervals as $j => [$bStart, $bEnd]) {
                if ($i === $j) {
                    continue;
                }
                if ($aStart->lt($bEnd) && $aEnd->gt($bStart)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Score a swap. Higher = better. Heuristic:
     *   + favour swaps where the partner team has spare capacity
     *     (fewer current tasks → lighter day → swap absorbs disruption easier)
     *   + favour swaps where the partner task is roughly the same duration
     *     (preserves makespan)
     *   - light penalty when the partner team is already understaffed
     */
    protected function scoreSwap(
        Task $rejectedTask,
        OptimizationTeam $originalTeam,
        Task $partnerTask,
        OptimizationTeam $partnerTeam
    ): float {
        $partnerLoad = $partnerTeam->tasks->count();
        $loadFactor = 1.0 / max(1, $partnerLoad);

        $rejDuration = (int) ($rejectedTask->estimated_duration_minutes ?? 60);
        $partnerDuration = (int) ($partnerTask->estimated_duration_minutes ?? 60);
        $durationDelta = abs($rejDuration - $partnerDuration);
        // Closer durations score higher (1.0 at zero delta, decaying).
        $durationFactor = 1.0 / (1.0 + ($durationDelta / 60.0));

        $understaffPenalty = $partnerTeam->activeMemberCount() < 2 ? 0.6 : 1.0;

        return $loadFactor * $durationFactor * $understaffPenalty;
    }

    protected function taskStart(Task $task): ?Carbon
    {
        if (!$task->scheduled_date) {
            return null;
        }
        $rawTime = $task->getRawOriginal('scheduled_time');
        if (!$rawTime) {
            return null;
        }
        $dateStr = $task->scheduled_date instanceof \DateTimeInterface
            ? $task->scheduled_date->format('Y-m-d')
            : (string) $task->scheduled_date;
        return Carbon::parse("{$dateStr} {$rawTime}");
    }
}
