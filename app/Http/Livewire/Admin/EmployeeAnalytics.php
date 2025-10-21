<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\OptimizationRun;
use App\Models\OptimizationTeam;
use App\Models\Task;
use Carbon\Carbon;

class EmployeeAnalytics extends Component
{
    public function render()
    {
        // Get all optimization runs ordered by most recent first
        $optimizationRuns = OptimizationRun::with(['teams.members.employee', 'teams.tasks'])
            ->orderBy('created_at', 'desc')
            ->limit(10) // Show last 10 runs
            ->get()
            ->map(function($run) {
                // Calculate runtime in seconds
                $runtime = $run->created_at->diffInSeconds($run->updated_at);

                // Calculate convergence rate (generations needed to reach best fitness)
                $convergenceRate = $run->generations_run;

                // Fitness rate (already stored)
                $fitnessRate = round($run->final_fitness_score, 4);

                // Check if fitness reached 1.0 (optimal)
                $isOptimal = $fitnessRate >= 0.999; // Allow small floating point errors

                // Count teams and tasks
                $totalTeams = $run->teams->count();
                $totalTasks = $run->total_tasks;

                return [
                    'id' => $run->id,
                    'service_date' => Carbon::parse($run->service_date)->format('M d, Y'),
                    'is_saved' => $run->is_saved,
                    'fitness_rate' => $fitnessRate,
                    'convergence_rate' => $convergenceRate,
                    'runtime' => $runtime,
                    'runtime_formatted' => $this->formatRuntime($runtime),
                    'is_optimal' => $isOptimal,
                    'total_teams' => $totalTeams,
                    'total_tasks' => $totalTasks,
                    'created_at' => $run->created_at->format('M d, Y H:i:s'),
                ];
            });

        // Get latest optimization run for detailed view
        $latestRun = $optimizationRuns->first();

        return view('livewire.admin.employee-analytics', [
            'optimizationRuns' => $optimizationRuns,
            'latestRun' => $latestRun,
        ])->layout('components.layouts.general-employer', [
            'title' => 'Employee Analytics Dashboard',
        ]);
    }

    private function formatRuntime($seconds)
    {
        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;
            return "{$minutes}m " . round($secs) . "s";
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return "{$hours}h {$minutes}m";
        }
    }
}
