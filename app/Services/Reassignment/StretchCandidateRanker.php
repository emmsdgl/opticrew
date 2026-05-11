<?php

namespace App\Services\Reassignment;

use App\Models\OptimizationTeam;
use App\Models\Task;

/**
 * Ranks teams as stretch candidates for a rejected task (Try 2b of the cascade).
 *
 * A "stretch candidate" is a team that doesn't have a genuine mid-day gap
 * (so Try 2a couldn't place the task) but is **estimated to finish its
 * scheduled work early enough** that it could absorb the rejected task as
 * an end-of-day extension. The candidate's day extends past its planned
 * finish, and that extension is the basis for compensation.
 *
 * Ranking criterion (per the policy doc): **earliest computed estimated
 * completion time first** — minimizing the makespan extension.
 *
 * This service does NOT execute the assignment. It surfaces candidates
 * to admin, who chooses one and sends an offer to that team. The employee
 * (or team representative) then opts in for compensation.
 *
 * See docs/task-rejection-reassignment-policy.md (§4 Try 2b).
 */
class StretchCandidateRanker
{
    /**
     * Rank stretch candidates for a rejected task. Returns an array of:
     *   ['team_id' => int, 'team_name' => string,
     *    'estimated_finish_minutes' => int,   // minutes since midnight
     *    'estimated_finish_human' => string,
     *    'capacity_remaining_minutes' => int, // assumed against an 8h workday
     *    'rationale' => string]
     */
    public function rank(Task $rejectedTask): array
    {
        if (!$rejectedTask->scheduled_date) {
            return [];
        }

        // Default workday end (minutes since midnight). Tunable.
        $workdayEndMinutes = (int) config('rejection.workday_end_minutes', 17 * 60); // 5 PM

        $teams = OptimizationTeam::with('tasks')
            ->where('service_date', $rejectedTask->scheduled_date)
            ->where('id', '!=', $rejectedTask->assigned_team_id)
            ->get();

        $candidates = [];

        foreach ($teams as $team) {
            $finishMinutes = $this->estimatedFinishMinutes($team);
            if ($finishMinutes === null) {
                continue; // No tasks or no times → can't compute, skip.
            }

            $capacityRemaining = max(0, $workdayEndMinutes - $finishMinutes);

            $candidates[] = [
                'team_id' => $team->id,
                'team_name' => $team->team_name,
                'estimated_finish_minutes' => $finishMinutes,
                'estimated_finish_human' => $this->minutesToHuman($finishMinutes),
                'capacity_remaining_minutes' => $capacityRemaining,
                'active_member_count' => $team->activeMemberCount(),
                'current_task_count' => $team->tasks->count(),
                'rationale' => "{$team->team_name} estimated finish at "
                    . $this->minutesToHuman($finishMinutes)
                    . ", {$capacityRemaining} min remaining capacity",
            ];
        }

        // Earliest finish first (smallest estimated_finish_minutes wins).
        usort($candidates, fn ($a, $b) => $a['estimated_finish_minutes'] <=> $b['estimated_finish_minutes']);

        return $candidates;
    }

    /**
     * Compute when a team is *estimated* to finish its assigned tasks for
     * the day (minutes since midnight). Sums durations of remaining tasks
     * starting from the latest task's start time. Returns null if the team
     * has no tasks with scheduled_time.
     */
    protected function estimatedFinishMinutes(OptimizationTeam $team): ?int
    {
        $latestEnd = null;

        foreach ($team->tasks as $task) {
            // Skip already-completed tasks — they don't extend the workday.
            if ($task->status === 'Completed' || $task->status === 'Cancelled' || $task->status === 'Rejected') {
                continue;
            }

            $rawTime = $task->getRawOriginal('scheduled_time');
            if (!$rawTime) {
                continue;
            }
            [$h, $m] = array_pad(explode(':', $rawTime), 2, '0');
            $startMinutes = ((int) $h) * 60 + (int) $m;
            $duration = (int) ($task->estimated_duration_minutes ?? 60);
            $endMinutes = $startMinutes + max(1, $duration);

            if ($latestEnd === null || $endMinutes > $latestEnd) {
                $latestEnd = $endMinutes;
            }
        }

        return $latestEnd;
    }

    protected function minutesToHuman(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        $ampm = $h >= 12 ? 'PM' : 'AM';
        $h12 = $h % 12 ?: 12;
        return sprintf('%d:%02d %s', $h12, $m, $ampm);
    }
}
