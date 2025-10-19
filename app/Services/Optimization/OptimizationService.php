<?php

namespace App\Services\Optimization;

use App\Models\Task;
use App\Models\Employee;
use App\Models\OptimizationRun;
use App\Services\Optimization\PreProcessing\RuleBasedPreProcessor;
use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OptimizationService
{
    protected RuleBasedPreProcessor $preProcessor;
    protected GeneticAlgorithmOptimizer $optimizer;

    public function __construct(
        RuleBasedPreProcessor $preProcessor,
        GeneticAlgorithmOptimizer $optimizer
    ) {
        $this->preProcessor = $preProcessor;
        $this->optimizer = $optimizer;
    }

    /**
     * ✅ RULE 4 & 8: Main optimization with saved schedule detection
     */
    public function optimizeSchedule(
        string $serviceDate,
        array $locationIds = [],
        ?int $triggeredByTaskId = null
    ): array {
        DB::beginTransaction();
        
        try {
            Log::info('Starting schedule optimization', [
                'service_date' => $serviceDate,
                'location_ids' => $locationIds,
                'triggered_by' => $triggeredByTaskId,
            ]);
            
            // ✅ RULE 8: Check if this is real-time addition for TODAY
            $isRealTimeAddition = $this->isRealTimeAddition($serviceDate);
            
            // ✅ RULE 4 & 9: Check if schedule exists and is saved
            $existingRun = OptimizationRun::where('service_date', $serviceDate)
                ->where('is_saved', true) // Only consider saved schedules
                ->first();

            if ($isRealTimeAddition && $existingRun) {
                Log::info("Real-time addition detected - adding to existing teams", [
                    'optimization_run_id' => $existingRun->id
                ]);
                
                return $this->addTaskToExistingTeams(
                    $serviceDate,
                    $triggeredByTaskId,
                    $existingRun
                );
            }

            // ✅ RULE 4: Delete unsaved optimization runs before re-optimizing
            if (!$existingRun) {
                $deletedCount = OptimizationRun::where('service_date', $serviceDate)
                    ->where('is_saved', false)
                    ->delete();
                
                Log::info("Deleted unsaved optimization runs", [
                    'count' => $deletedCount,
                    'service_date' => $serviceDate
                ]);
            }

            // Standard optimization flow
            $allTasks = Task::with(['location.contractedClient', 'client'])
                ->whereDate('scheduled_date', $serviceDate)
                ->whereIn('status', ['Pending', 'Scheduled'])
                ->get();
                    
            Log::info('Fetched all tasks for date', [
                'total_tasks' => $allTasks->count(),
                'task_ids' => $allTasks->pluck('id')->toArray()
            ]);

            $allEmployees = Employee::where('is_active', true)
                ->whereDoesntHave('dayOffs', fn($q) => $q->whereDate('date', $serviceDate))
                ->get();

            Log::info("Employees fetched", [
                'total' => $allEmployees->count(),
                'employee_ids' => $allEmployees->pluck('id')->toArray(),
                'service_date' => $serviceDate
            ]);

            $constraints = $this->getConstraints();
            
            $preprocessResult = $this->preProcessor->process(
                $allTasks,
                $allEmployees,
                $constraints
            );

            Log::info("Pre-processing complete", [
                'total_tasks_fetched' => $allTasks->count(),
                'valid_tasks' => $preprocessResult['valid_tasks']->count(),
                'invalid_tasks' => $preprocessResult['invalid_tasks']->count(),
                'selected_employees' => count($preprocessResult['selected_employees'])
            ]);
                        
            if ($preprocessResult['valid_tasks']->isEmpty()) {
                throw new \Exception('No valid tasks after pre-processing');
            }

            // ✅ RULE 9: Get locked teams if schedule is saved
            $lockedTeams = null;
            if ($existingRun) {
                $lockedTeams = $this->getLockedTeams($existingRun);
            }

            // PHASE 2: Genetic Algorithm Optimization
            $optimalSchedules = $this->optimizer->optimize(
                $preprocessResult['valid_tasks'],
                $preprocessResult['employee_allocations'],
                config('optimization.genetic_algorithm.max_generations', 100),
                $lockedTeams // ✅ Pass locked teams
            );
            
            // ✅ Clear old unsaved team assignments
            foreach ($allTasks as $task) {
                $task->assigned_team_id = null;
                $task->optimization_run_id = null;
                $task->save();
            }
            
            // Save results to database
            $this->saveResults($serviceDate, $optimalSchedules, $preprocessResult['invalid_tasks']);
            
            DB::commit();
            
            Log::info('Schedule optimization completed successfully');
            
            return [
                'status' => 'success',
                'schedules' => $optimalSchedules,
                'invalid_tasks' => $preprocessResult['invalid_tasks'],
                'statistics' => $this->generateStatistics($optimalSchedules, $preprocessResult),
                'what_if_ready' => true,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Schedule optimization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

//             // PHASE 1: Rule-Based Pre-Processing
//             $allTasks = Task::with(['location.contractedClient', 'client'])
//                 ->whereDate('scheduled_date', $serviceDate)
//                 ->whereIn('status', ['Pending', 'Scheduled'])
//                 ->get();
                       
//             Log::info('Fetched all tasks for date', [
//                 'total_tasks' => $allTasks->count(),
//                 'task_ids' => $allTasks->pluck('id')->toArray()
//             ]);

//             $allEmployees = Employee::where('is_active', true)
//                 ->whereDoesntHave('dayOffs', fn($q) => $q->whereDate('date', $serviceDate))
//                 ->get();

//             Log::info("Employees fetched", [
//                 'total' => $allEmployees->count(),
//                 'employee_ids' => $allEmployees->pluck('id')->toArray(),
//                 'service_date' => $serviceDate
//             ]);

//             $constraints = $this->getConstraints();
            
//             $preprocessResult = $this->preProcessor->process(
//                 $allTasks,
//                 $allEmployees,
//                 $constraints
//             );

//             Log::info("Pre-processing complete", [
//                 'total_tasks_fetched' => $allTasks->count(),
//                 'valid_tasks' => $preprocessResult['valid_tasks']->count(),
//                 'invalid_tasks' => $preprocessResult['invalid_tasks']->count(),
//                 'selected_employees' => count($preprocessResult['selected_employees'])
//             ]);
                        
//             if ($preprocessResult['valid_tasks']->isEmpty()) {
//                 throw new \Exception('No valid tasks after pre-processing');
//             }
            
//             \Log::info("About to optimize", [
//                 'valid_tasks_count' => $preprocessResult['valid_tasks']->count(),
//                 'employee_allocations_structure' => json_encode($preprocessResult['employee_allocations']),
//                 'employee_allocations_type' => gettype($preprocessResult['employee_allocations'])
//             ]);

//             // PHASE 2: Genetic Algorithm Optimization
//             $optimalSchedules = $this->optimizer->optimize(
//                 $preprocessResult['valid_tasks'],
//                 $preprocessResult['employee_allocations'],
//                 config('optimization.genetic_algorithm.max_generations', 100)
//             );
            
//             // ✅ IMPORTANT: Clear old team assignments first
//             foreach ($allTasks as $task) {
//                 $task->assigned_team_id = null;
//                 $task->optimization_run_id = null;
//                 $task->save();
//             }
            
//             // Save results to database
//             $this->saveResults($serviceDate, $optimalSchedules, $preprocessResult['invalid_tasks']);
            
//             DB::commit();
            
//             Log::info('Schedule optimization completed successfully');
            
//             return [
//                 'status' => 'success',
//                 'schedules' => $optimalSchedules,
//                 'invalid_tasks' => $preprocessResult['invalid_tasks'],
//                 'statistics' => $this->generateStatistics($optimalSchedules, $preprocessResult),
//                 'what_if_ready' => true,
//             ];
            
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Schedule optimization failed', [
//                 'error' => $e->getMessage(),
//                 'trace' => $e->getTraceAsString(),
//             ]);
            
//             throw $e;
//         }
//     }

    /**
     * ✅ RULE 8: Check if this is a real-time addition (for today)
     */
    protected function isRealTimeAddition(string $serviceDate): bool
    {
        $today = Carbon::now()->format('Y-m-d');
        return $serviceDate === $today;
    }

    /**
     * ✅ RULE 8: Add task to existing saved teams (real-time addition)
     */
    protected function addTaskToExistingTeams(
        string $serviceDate,
        ?int $newTaskId,
        OptimizationRun $existingRun
    ): array {
        Log::info("Adding new task to existing teams", [
            'task_id' => $newTaskId,
            'optimization_run_id' => $existingRun->id
        ]);

        if (!$newTaskId) {
            throw new \Exception('Task ID is required for real-time addition');
        }

        $newTask = Task::with(['location.contractedClient', 'client'])
            ->findOrFail($newTaskId);

        // Get existing teams from this optimization run
        $existingTeams = \App\Models\OptimizationTeam::where('optimization_run_id', $existingRun->id)
            ->with(['members.employee'])
            ->get();

        if ($existingTeams->isEmpty()) {
            throw new \Exception('No existing teams found for this optimization run');
        }

        // ✅ RULE 7: Find team with least workload that won't exceed 12 hours
        $selectedTeam = null;
        $minWorkload = PHP_INT_MAX;

        foreach ($existingTeams as $team) {
            $teamTasks = Task::where('assigned_team_id', $team->id)->get();
            $totalHours = $teamTasks->sum(fn($t) => ($t->duration + $t->travel_time) / 60);
            
            $newTaskHours = ($newTask->duration + $newTask->travel_time) / 60;
            $projectedHours = $totalHours + $newTaskHours;

            // ✅ RULE 7: Skip if exceeds 12 hours
            if ($projectedHours > 12) {
                Log::info("Skipping team - would exceed 12 hours", [
                    'team_id' => $team->id,
                    'current_hours' => $totalHours,
                    'projected_hours' => $projectedHours
                ]);
                continue;
            }

            if ($totalHours < $minWorkload) {
                $minWorkload = $totalHours;
                $selectedTeam = $team;
            }
        }

        // If no team found (all at 12 hours), assign to least loaded anyway
        if (!$selectedTeam) {
            Log::warning("All teams at capacity, assigning to least loaded team anyway");
            $selectedTeam = $existingTeams->sortBy(function($team) {
                return Task::where('assigned_team_id', $team->id)
                    ->get()
                    ->sum(fn($t) => ($t->duration + $t->travel_time) / 60);
            })->first();
        }

        // Assign the new task
        $newTask->update([
            'assigned_team_id' => $selectedTeam->id,
            'optimization_run_id' => $existingRun->id,
            'status' => 'Scheduled'
        ]);

        Log::info("Task assigned to existing team", [
            'task_id' => $newTask->id,
            'team_id' => $selectedTeam->id,
            'team_members' => $selectedTeam->members->pluck('employee.full_name')->toArray()
        ]);

        return [
            'status' => 'success',
            'message' => 'Task added to existing team',
            'assigned_team_id' => $selectedTeam->id,
            'team_members' => $selectedTeam->members->pluck('employee.full_name')->toArray()
        ];
    }

    /**
     * ✅ RULE 9: Get locked teams (teams without what-if scenarios)
     */
    protected function getLockedTeams(OptimizationRun $optimizationRun): \Illuminate\Support\Collection
    {
        return \App\Models\OptimizationTeam::where('optimization_run_id', $optimizationRun->id)
            ->whereNull('what_if_scenario_id') // Teams without what-if are locked
            ->with('members.employee')
            ->get()
            ->map(function($team) {
                return $team->members->map->employee;
            });
    }

    protected function getConstraints(): array
    {
        return [
            'work_start_time' => config('optimization.constraints.work_start_time', '08:00:00'),
            'work_end_time' => config('optimization.constraints.work_end_time', '20:00:00'), // ✅ Extended to 20:00 (12 hours from 8am)
            'budget_limit' => config('optimization.workforce.budget_limit', 10000),
            'daily_cost_per_employee' => config('optimization.workforce.daily_cost_per_employee', 100),
        ];
    }

    protected function saveResults(string $serviceDate, array $schedules, $invalidTasks): void
    {
        foreach ($schedules as $clientIdentifier => $schedule) {
            $clientId = null;
            $contractedClientId = null;
            
            if ($clientIdentifier && $clientIdentifier !== 'unassigned' && $clientIdentifier !== '') {
                if (is_numeric($clientIdentifier)) {
                    $contractedClient = \App\Models\ContractedClient::find($clientIdentifier);
                    
                    if ($contractedClient) {
                        $contractedClientId = $clientIdentifier;
                    } else {
                        $externalClient = \App\Models\Client::find($clientIdentifier);
                        if ($externalClient) {
                            $clientId = $clientIdentifier;
                        }
                    }
                }
            }
            
            $scheduleData = $schedule->getSchedule();
            $totalTeams = count($scheduleData);
            $totalTasks = collect($scheduleData)->sum(fn($team) => $team['tasks']->count());
            $totalEmployees = collect($scheduleData)->sum(fn($team) => count($team['team']));
            $generationsRun = $schedule->getMetadata('generations_run') ?? 100;
    
            \Log::info("Saving optimization run", [
                'client_identifier' => $clientIdentifier,
                'total_teams' => $totalTeams,
                'total_tasks' => $totalTasks,
                'total_employees' => $totalEmployees,
                'generations_run' => $generationsRun
            ]);
            
            // ✅ RULE 4: Create as UNSAVED by default
            $optimizationRun = \App\Models\OptimizationRun::create([
                'service_date' => $serviceDate,
                'total_tasks' => $totalTasks,
                'total_employees' => $totalEmployees,
                'total_teams' => $totalTeams,
                'final_fitness_score' => $schedule->getFitness() ?? 0.0,
                'generations_run' => $generationsRun,
                'status' => 'completed',
                'is_saved' => false, // ✅ UNSAVED by default
                'employee_allocation_data' => json_encode([]),
                'greedy_result_data' => json_encode([]),
            ]);
            
            \Log::info("Optimization run created", [
                'optimization_run_id' => $optimizationRun->id
            ]);
            
            $this->updateTasksWithOptimization($schedule, $optimizationRun->id);
        }
    }
    
    // protected function saveResults(string $serviceDate, array $schedules, $invalidTasks): void
    // {
    //     foreach ($schedules as $clientIdentifier => $schedule) {
    //         // Handle client ID logic
    //         $clientId = null;
    //         $contractedClientId = null;
            
    //         if ($clientIdentifier && $clientIdentifier !== 'unassigned' && $clientIdentifier !== '') {
    //             if (is_numeric($clientIdentifier)) {
    //                 $contractedClient = \App\Models\ContractedClient::find($clientIdentifier);
                    
    //                 if ($contractedClient) {
    //                     $contractedClientId = $clientIdentifier;
    //                 } else {
    //                     $externalClient = \App\Models\Client::find($clientIdentifier);
    //                     if ($externalClient) {
    //                         $clientId = $clientIdentifier;
    //                     }
    //                 }
    //             }
    //         }
            
    //         // ✅ Calculate total teams from the schedule
    //         $scheduleData = $schedule->getSchedule();
    //         $totalTeams = count($scheduleData); // Number of teams formed
    //         $totalTasks = collect($scheduleData)->sum(fn($team) => $team['tasks']->count());
    //         $totalEmployees = collect($scheduleData)->sum(fn($team) => count($team['team']));
    //         $generationsRun = $schedule->getMetadata('generations_run') ?? 100; // ✅ Get actual value

    //         \Log::info("Saving optimization run", [
    //             'client_identifier' => $clientIdentifier,
    //             'total_teams' => $totalTeams,
    //             'total_tasks' => $totalTasks,
    //             'total_employees' => $totalEmployees,
    //             'generations_run' => $generationsRun // ✅ Log it
    //         ]);
            
    //         // ✅ Create with actual generation count
    //         $optimizationRun = \App\Models\OptimizationRun::create([
    //             'service_date' => $serviceDate,
    //             'total_tasks' => $totalTasks,
    //             'total_employees' => $totalEmployees,
    //             'total_teams' => $totalTeams,
    //             'final_fitness_score' => $schedule->getFitness() ?? 0.0,
    //             'generations_run' => $generationsRun, // ✅ Use actual value
    //             'status' => 'completed',
    //             'employee_allocation_data' => json_encode([]),
    //             'greedy_result_data' => json_encode([]),
    //         ]);
            
    //         \Log::info("Optimization run created", [
    //             'optimization_run_id' => $optimizationRun->id
    //         ]);
            
    //         // Update tasks with the optimization run ID
    //         $this->updateTasksWithOptimization($schedule, $optimizationRun->id);
    //     }
    // }

    /**
     * Update tasks with optimization results
     */
    protected function updateTasksWithOptimization($schedule, int $optimizationRunId): void
    {
        $scheduleData = $schedule->getSchedule();
        
        // Get service date from optimization run
        $optimizationRun = \App\Models\OptimizationRun::find($optimizationRunId);
        
        foreach ($scheduleData as $teamIndex => $teamSchedule) {
            $team = $teamSchedule['team'];
            $tasks = $teamSchedule['tasks'];
            
            // ✅ Create optimization team
            $optimizationTeam = \App\Models\OptimizationTeam::create([
                'optimization_run_id' => $optimizationRunId,
                'team_index' => $teamIndex + 1,
                'service_date' => $optimizationRun->service_date,
                'car_id' => null, // Can be assigned later
            ]);
            
            // ✅ Create team members
            foreach ($team as $employee) {
                \App\Models\OptimizationTeamMember::create([
                    'optimization_team_id' => $optimizationTeam->id,
                    'employee_id' => $employee->id,
                ]);
            }
            
            \Log::info("Optimization team created", [
                'optimization_team_id' => $optimizationTeam->id,
                'team_index' => $teamIndex + 1,
                'employee_ids' => collect($team)->pluck('id')->toArray(),
                'optimization_run_id' => $optimizationRunId
            ]);
            
            // ✅ Assign tasks to this team
            foreach ($tasks as $task) {
                Task::where('id', $task->id)->update([
                    'status' => 'Scheduled',
                    'optimization_run_id' => $optimizationRunId,
                    'assigned_by_generation' => null,
                    'assigned_team_id' => $optimizationTeam->id, // ✅ Unique team ID
                ]);
                
                \Log::info("Task updated", [
                    'task_id' => $task->id,
                    'status' => 'Scheduled',
                    'optimization_team_id' => $optimizationTeam->id,
                    'optimization_run_id' => $optimizationRunId
                ]);
            }
        }
    }

    protected function generateStatistics(array $schedules, array $preprocessResult): array
    {
        $totalTasks = $preprocessResult['valid_tasks']->count();
        $totalEmployees = collect($preprocessResult['employee_allocations'])->flatten(1)->count();
        
        return [
            'total_tasks' => $totalTasks,
            'total_employees' => $totalEmployees,
            'total_clients' => count($schedules),
            'invalid_tasks_count' => $preprocessResult['invalid_tasks']->count(),
            'average_fitness' => collect($schedules)->avg(fn($s) => $s->getFitness()),
            'optimization_date' => now()->toDateTimeString(),
        ];
    }
}