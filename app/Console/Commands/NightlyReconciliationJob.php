<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\Location;
use App\Models\Employee;
use App\Models\EmployeePerformance;
use App\Models\OptimizationTeamMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Nightly Reconciliation Job
 *
 * Runs every night to:
 * 1. Analyze estimated vs actual durations
 * 2. Update location base durations if patterns detected
 * 3. Update employee performance metrics
 *
 * Based on pseudocode NIGHTLY_JOB_RECONCILE_AND_LEARN function
 */
class NightlyReconciliationJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:reconcile {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile and learn from actual vs estimated task durations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Nightly Reconciliation Job...');

        // Get yesterday's date (or specific date if provided)
        $yesterday = $this->argument('date')
            ? Carbon::parse($this->argument('date'))
            : Carbon::yesterday();

        $this->info("Processing tasks from: " . $yesterday->toDateString());

        // Get all completed tasks from yesterday
        $completedTasks = Task::whereDate('scheduled_date', $yesterday)
            ->where('status', 'Completed')
            ->whereNotNull('actual_duration')
            ->with(['location', 'optimizationTeam.members.employee'])
            ->get();

        $this->info("Found {$completedTasks->count()} completed tasks");

        if ($completedTasks->isEmpty()) {
            $this->warn('No completed tasks found for this date');
            return Command::SUCCESS;
        }

        Log::info("Nightly reconciliation started", [
            'date' => $yesterday->toDateString(),
            'completed_tasks' => $completedTasks->count()
        ]);

        // Track statistics
        $stats = [
            'tasks_processed' => 0,
            'significant_variances' => 0,
            'locations_updated' => [],
            'employees_updated' => []
        ];

        // ===================================================================
        // PHASE 1: Analyze Estimated vs. Actual Durations
        // ===================================================================

        $this->info("\nPhase 1: Analyzing task durations...");
        $bar = $this->output->createProgressBar($completedTasks->count());
        $bar->start();

        foreach ($completedTasks as $task) {
            $estimated = $task->estimated_duration_minutes;
            $actual = $task->actual_duration;

            if ($actual && $estimated > 0) {
                $variance = $actual - $estimated;
                $variancePercent = ($variance / $estimated) * 100;

                // Log significant variances (> 20%)
                if (abs($variancePercent) > 20) {
                    $stats['significant_variances']++;

                    Log::info("Significant variance detected", [
                        'task_id' => $task->id,
                        'location' => $task->location->location_name ?? 'Unknown',
                        'estimated' => $estimated,
                        'actual' => $actual,
                        'variance_percent' => round($variancePercent, 2)
                    ]);

                    // Update location base duration if pattern detected
                    if ($task->location_id) {
                        $this->updateLocationBaseDuration($task, $stats);
                    }
                }
            }

            $stats['tasks_processed']++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ===================================================================
        // PHASE 2: Update Employee Performance Metrics
        // ===================================================================

        $this->info("\nPhase 2: Updating employee performance...");

        foreach ($completedTasks as $task) {
            if (!$task->actual_duration || !$task->estimated_duration_minutes) {
                continue;
            }

            // Calculate performance score (estimated/actual)
            // Score > 1.0 = faster than expected (good)
            // Score < 1.0 = slower than expected
            $performanceScore = $task->estimated_duration_minutes / $task->actual_duration;

            // Get team members for this task
            if ($task->optimizationTeam) {
                $teamMembers = $task->optimizationTeam->members()
                    ->with('employee')
                    ->get();

                foreach ($teamMembers as $member) {
                    $employee = $member->employee;

                    if ($employee) {
                        $this->updateEmployeePerformance($employee, $yesterday, $performanceScore, $stats);
                    }
                }
            }
        }

        // ===================================================================
        // SUMMARY
        // ===================================================================

        $this->newLine();
        $this->info('✅ Nightly Reconciliation Complete!');
        $this->newLine();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Tasks Processed', $stats['tasks_processed']],
                ['Significant Variances', $stats['significant_variances']],
                ['Locations Updated', count($stats['locations_updated'])],
                ['Employees Updated', count($stats['employees_updated'])]
            ]
        );

        if (!empty($stats['locations_updated'])) {
            $this->info("\nLocations with updated base durations:");
            foreach ($stats['locations_updated'] as $location) {
                $this->line("  - {$location['name']}: {$location['old']} → {$location['new']} minutes");
            }
        }

        Log::info("Nightly reconciliation complete", $stats);

        return Command::SUCCESS;
    }

    /**
     * Update location base duration based on actual performance
     *
     * @param Task $task
     * @param array &$stats
     * @return void
     */
    protected function updateLocationBaseDuration(Task $task, array &$stats): void
    {
        // Get similar tasks for this location (last 10 completed)
        $similarTasks = Task::where('location_id', $task->location_id)
            ->where('task_description', $task->task_description)
            ->whereNotNull('actual_duration')
            ->where('status', 'Completed')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Need at least 5 samples to update base duration
        if ($similarTasks->count() >= 5) {
            $avgActual = $similarTasks->avg('actual_duration');
            $roundedAvg = round($avgActual);

            $location = Location::find($task->location_id);
            $oldDuration = $location->base_cleaning_duration_minutes;

            // Only update if difference is significant (> 10%)
            if ($oldDuration && abs($roundedAvg - $oldDuration) / $oldDuration > 0.1) {
                $location->update([
                    'base_cleaning_duration_minutes' => $roundedAvg
                ]);

                $stats['locations_updated'][] = [
                    'id' => $location->id,
                    'name' => $location->location_name,
                    'old' => $oldDuration,
                    'new' => $roundedAvg
                ];

                Log::info("Location base duration updated", [
                    'location_id' => $location->id,
                    'location_name' => $location->location_name,
                    'old_duration' => $oldDuration,
                    'new_duration' => $roundedAvg,
                    'sample_size' => $similarTasks->count()
                ]);
            }
        }
    }

    /**
     * Update employee performance metrics
     *
     * @param Employee $employee
     * @param Carbon $date
     * @param float $performanceScore
     * @param array &$stats
     * @return void
     */
    protected function updateEmployeePerformance(
        Employee $employee,
        Carbon $date,
        float $performanceScore,
        array &$stats
    ): void {
        $existingRecord = EmployeePerformance::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        if ($existingRecord) {
            // Update existing record
            $existingRecord->addTaskCompletion($performanceScore);

            Log::debug("Employee performance updated", [
                'employee_id' => $employee->id,
                'tasks_completed' => $existingRecord->tasks_completed,
                'average_performance' => $existingRecord->average_performance
            ]);
        } else {
            // Create new record
            EmployeePerformance::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'tasks_completed' => 1,
                'total_performance_score' => $performanceScore,
                'average_performance' => $performanceScore
            ]);

            Log::debug("Employee performance record created", [
                'employee_id' => $employee->id,
                'performance_score' => $performanceScore
            ]);
        }

        // Track for stats
        if (!in_array($employee->id, $stats['employees_updated'])) {
            $stats['employees_updated'][] = $employee->id;
        }
    }
}
