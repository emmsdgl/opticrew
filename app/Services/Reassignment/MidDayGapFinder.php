<?php

namespace App\Services\Reassignment;

use App\Models\OptimizationTeam;
use App\Models\Task;
use Carbon\Carbon;

/**
 * Finds a team with a *mid-day gap* that can absorb a rejected task without
 * extending its workday end (Try 2a of the cascade).
 *
 * "Mid-day gap" means the rejected task's time slot fits between two of the
 * receiving team's existing tasks — so day-end doesn't move and no overtime
 * is created.
 *
 * If only end-of-day capacity exists, this finder returns null and the
 * cascade should fall through to Try 2b (stretch with compensation).
 *
 * See docs/task-rejection-reassignment-policy.md (§4 Try 2a).
 */
class MidDayGapFinder
{
    /**
     * Find the best mid-day-gap receiver for a rejected task. Returns:
     *   ['receiver_team_id' => int, 'score' => float, 'rationale' => string]
     * or null if no mid-day gap fits.
     */
    public function findBestReceiver(Task $rejectedTask): ?array
    {
        $candidates = $this->findCandidates($rejectedTask);
        if (empty($candidates)) {
            return null;
        }
        return $candidates[0];
    }

    /**
     * Enumerate all teams whose schedule has a mid-day gap that the rejected
     * task fits into, ranked by suitability.
     */
    public function findCandidates(Task $rejectedTask): array
    {
        $start = $this->taskStart($rejectedTask);
        if (!$start) {
            return [];
        }

        $duration = (int) ($rejectedTask->estimated_duration_minutes ?? 60);
        $end = $start->copy()->addMinutes(max(1, $duration));

        // Receivers must be on the same date and not the original team.
        $teams = OptimizationTeam::with('tasks')
            ->where('service_date', $rejectedTask->scheduled_date)
            ->where('id', '!=', $rejectedTask->assigned_team_id)
            ->get();

        $candidates = [];

        foreach ($teams as $team) {
            $teamTasks = $team->tasks
                ->filter(fn (Task $t) => in_array($t->status, ['Pending', 'Scheduled', 'In Progress'], true))
                ->values();

            // Need at least one task before AND one task after the rejected
            // task's window for it to count as a *mid-day* gap.
            $hasBefore = false;
            $hasAfter = false;
            $conflict = false;

            foreach ($teamTasks as $t) {
                $tStart = $this->taskStart($t);
                if (!$tStart) {
                    continue;
                }
                $tDuration = (int) ($t->estimated_duration_minutes ?? 60);
                $tEnd = $tStart->copy()->addMinutes(max(1, $tDuration));

                // Conflict: existing task overlaps the rejected task's slot.
                if ($tStart->lt($end) && $tEnd->gt($start)) {
                    $conflict = true;
                    break;
                }
                if ($tEnd->lte($start)) {
                    $hasBefore = true;
                }
                if ($tStart->gte($end)) {
                    $hasAfter = true;
                }
            }

            if ($conflict || !$hasBefore || !$hasAfter) {
                continue; // not a mid-day gap fit (either conflicts, or it's an end-of-day extension)
            }

            $score = $this->scoreReceiver($team, $duration);
            $candidates[] = [
                'receiver_team_id' => $team->id,
                'receiver_team_name' => $team->team_name,
                'score' => $score,
                'rationale' => "Mid-day gap on {$team->team_name}",
            ];
        }

        usort($candidates, fn ($a, $b) => $b['score'] <=> $a['score']);
        return $candidates;
    }

    /**
     * Score a free-slot receiver. Higher = better. Prefer teams that:
     *   - have more active members (better resilience)
     *   - have a lower current task count (more breathing room)
     */
    protected function scoreReceiver(OptimizationTeam $team, int $taskDurationMinutes): float
    {
        $activeMembers = max(1, $team->activeMemberCount());
        $taskCount = max(1, $team->tasks->count());

        return ($activeMembers / 2.0) * (1.0 / $taskCount);
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
