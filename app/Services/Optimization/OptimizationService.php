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
            $savedRuns = OptimizationRun::where('service_date', $serviceDate)
                ->where('is_saved', true) // Only consider saved schedules
                ->get();

            if ($isRealTimeAddition && $savedRuns->isNotEmpty()) {
                // ✅ MULTI-TASK FIX: Find ALL pending tasks for this date, not just the triggered one
                $allPendingTasks = Task::with(['location.contractedClient', 'client'])
                    ->whereDate('scheduled_date', $serviceDate)
                    ->where('status', 'Pending')
                    ->get();

                if ($allPendingTasks->isEmpty()) {
                    Log::warning("No pending tasks found for real-time addition", [
                        'service_date' => $serviceDate
                    ]);
                    DB::commit(); // Commit empty transaction before returning
                    return [
                        'status' => 'success',
                        'message' => 'No pending tasks to assign'
                    ];
                }

                Log::info("Real-time addition detected - processing ALL pending tasks", [
                    'service_date' => $serviceDate,
                    'total_pending_tasks' => $allPendingTasks->count(),
                    'pending_task_ids' => $allPendingTasks->pluck('id')->toArray(),
                    'total_saved_runs_for_date' => $savedRuns->count()
                ]);

                // Group pending tasks by client
                $tasksByClient = $allPendingTasks->groupBy(function($task) {
                    if ($task->location && $task->location->contracted_client_id) {
                        return 'contracted_' . $task->location->contracted_client_id;
                    } elseif ($task->client_id) {
                        return 'client_' . $task->client_id;
                    }
                    return 'unassigned';
                });

                $assignmentResults = [];
                $unassignedTaskIds = [];

                // Process each client's pending tasks
                foreach ($tasksByClient as $clientIdentifier => $clientTasks) {
                    // Find the saved run for this client
                    $correctRun = null;
                    foreach ($savedRuns as $run) {
                        $runTeams = \App\Models\OptimizationTeam::where('optimization_run_id', $run->id)->first();
                        if ($runTeams) {
                            $runTask = Task::with(['location.contractedClient'])
                                ->where('assigned_team_id', $runTeams->id)
                                ->first();

                            if ($runTask) {
                                $runClientIdentifier = null;
                                if ($runTask->location && $runTask->location->contracted_client_id) {
                                    $runClientIdentifier = 'contracted_' . $runTask->location->contracted_client_id;
                                } elseif ($runTask->client_id) {
                                    $runClientIdentifier = 'client_' . $runTask->client_id;
                                }

                                if ($runClientIdentifier === $clientIdentifier) {
                                    $correctRun = $run;
                                    break;
                                }
                            }
                        }
                    }

                    if (!$correctRun) {
                        // ✅ FIX: Track unassigned tasks instead of skipping
                        Log::warning("No saved run found for client - will trigger full optimization", [
                            'client_identifier' => $clientIdentifier,
                            'task_count' => $clientTasks->count(),
                            'task_ids' => $clientTasks->pluck('id')->toArray()
                        ]);
                        $unassignedTaskIds = array_merge($unassignedTaskIds, $clientTasks->pluck('id')->toArray());
                        continue;
                    }

                    // Assign ALL tasks for this client to teams in the correct run
                    foreach ($clientTasks as $task) {
                        $result = $this->addSingleTaskToExistingTeams(
                            $serviceDate,
                            $task->id,
                            $correctRun
                        );
                        $assignmentResults[] = $result;
                    }
                }

                // ✅ FIX: If there are unassigned tasks, fall through to full optimization
                if (!empty($unassignedTaskIds)) {
                    Log::info("⚠️ Real-time addition incomplete - falling through to full optimization", [
                        'assigned_tasks' => count($assignmentResults),
                        'unassigned_tasks' => count($unassignedTaskIds),
                        'unassigned_task_ids' => $unassignedTaskIds
                    ]);
                    // Don't return - continue to standard optimization flow below
                } else {
                    // All tasks successfully assigned
                    DB::commit(); // Commit transaction before returning
                    return [
                        'status' => 'success',
                        'message' => 'All pending tasks assigned to existing teams',
                        'total_tasks_assigned' => count($assignmentResults),
                        'assignments' => $assignmentResults
                    ];
                }
            }

            // ✅ RE-OPTIMIZATION LOGIC: Check for existing unsaved runs
            // MULTI-COMPANY FIX: Only delete unsaved runs for the SAME client as the new task
            // This preserves team assignments for OTHER companies
            $unsavedRuns = OptimizationRun::where('service_date', $serviceDate)
                ->where('is_saved', false)
                ->get();

            if ($unsavedRuns->isNotEmpty() && $triggeredByTaskId) {
                // Get the client identifier for the triggered task
                $triggeredTask = Task::with(['location.contractedClient'])->find($triggeredByTaskId);
                $triggeredClientId = null;

                if ($triggeredTask && $triggeredTask->location && $triggeredTask->location->contracted_client_id) {
                    $triggeredClientId = 'contracted_' . $triggeredTask->location->contracted_client_id;
                } elseif ($triggeredTask && $triggeredTask->client_id) {
                    $triggeredClientId = 'client_' . $triggeredTask->client_id;
                }

                Log::info("Re-optimization triggered - checking unsaved runs", [
                    'service_date' => $serviceDate,
                    'triggered_task_id' => $triggeredByTaskId,
                    'triggered_client_id' => $triggeredClientId,
                    'unsaved_runs_found' => $unsavedRuns->count(),
                ]);

                // Check if there's an existing unsaved run for the SAME client
                $sameClientRun = null;
                foreach ($unsavedRuns as $run) {
                    $runTeams = \App\Models\OptimizationTeam::where('optimization_run_id', $run->id)->first();
                    if ($runTeams) {
                        $runTask = Task::with(['location.contractedClient'])
                            ->where('assigned_team_id', $runTeams->id)
                            ->first();

                        if ($runTask) {
                            $runClientId = null;
                            if ($runTask->location && $runTask->location->contracted_client_id) {
                                $runClientId = 'contracted_' . $runTask->location->contracted_client_id;
                            } elseif ($runTask->client_id) {
                                $runClientId = 'client_' . $runTask->client_id;
                            }

                            if ($runClientId === $triggeredClientId) {
                                $sameClientRun = $run;
                                break;
                            }
                        }
                    }
                }

                if ($sameClientRun) {
                    // Only delete the run for the SAME client and re-optimize that client's tasks
                    Log::info("🔄 RE-OPTIMIZATION for same client - preserving other clients' runs", [
                        'deleting_run_id' => $sameClientRun->id,
                        'triggered_client_id' => $triggeredClientId,
                    ]);
                    $sameClientRun->delete();
                } else {
                    // New client - don't delete existing runs, just add this task
                    // The new task will get its own optimization run
                    Log::info("✅ NEW CLIENT detected - preserving existing optimization runs", [
                        'triggered_client_id' => $triggeredClientId,
                        'existing_runs_preserved' => $unsavedRuns->count(),
                    ]);
                }
            } elseif ($unsavedRuns->isNotEmpty()) {
                // Fallback: no triggered task ID, delete all unsaved runs
                Log::warning("🔄 RE-OPTIMIZATION TRIGGERED - No task ID provided, full re-optimization", [
                    'service_date' => $serviceDate,
                    'unsaved_runs_found' => $unsavedRuns->count(),
                    'run_ids' => $unsavedRuns->pluck('id')->toArray(),
                ]);

                foreach ($unsavedRuns as $run) {
                    Log::info("Deleting unsaved optimization run for re-optimization", [
                        'id' => $run->id,
                        'total_tasks' => $run->total_tasks,
                        'total_teams' => $run->total_teams,
                        'created_at' => $run->created_at
                    ]);
                    $run->delete();
                }
            }

            // Standard optimization flow
            // MULTI-COMPANY FIX: Only fetch tasks that need optimization
            // When preserving existing runs, only optimize tasks without valid team assignments
            $tasksQuery = Task::with(['location.contractedClient', 'client'])
                ->whereDate('scheduled_date', $serviceDate);

            // Check if we're preserving existing runs (new client scenario)
            $existingUnsavedRunIds = $unsavedRuns->pluck('id')->toArray();
            if (!empty($existingUnsavedRunIds) && isset($triggeredClientId)) {
                // Only get tasks that are:
                // 1. Pending (not yet assigned) OR
                // 2. Scheduled but NOT assigned to a valid team from preserved runs
                $tasksQuery->where(function($query) use ($existingUnsavedRunIds) {
                    $query->where('status', 'Pending')
                          ->orWhere(function($q) use ($existingUnsavedRunIds) {
                              $q->where('status', 'Scheduled')
                                ->where(function($inner) use ($existingUnsavedRunIds) {
                                    $inner->whereNull('assigned_team_id')
                                          ->orWhereNotIn('optimization_run_id', $existingUnsavedRunIds);
                                });
                          });
                });

                Log::info("MULTI-COMPANY: Fetching only tasks needing optimization", [
                    'preserved_run_ids' => $existingUnsavedRunIds,
                    'triggered_client_id' => $triggeredClientId,
                ]);
            } else {
                // Normal flow: get all pending and scheduled tasks
                $tasksQuery->whereIn('status', ['Pending', 'Scheduled']);
            }

            $allTasks = $tasksQuery->get();

            Log::info('Fetched tasks for optimization', [
                'total_tasks' => $allTasks->count(),
                'task_ids' => $allTasks->pluck('id')->toArray()
            ]);

            // ✅ Get active employees whose users are NOT soft-deleted
            // MULTI-COMPANY FIX: Exclude employees already assigned to preserved runs
            $employeesQuery = Employee::where('is_active', true)
                ->whereDoesntHave('dayOffs', fn($q) => $q->whereDate('date', $serviceDate))
                ->whereHas('user', function($q) {
                    $q->whereNull('deleted_at'); // Exclude soft-deleted users
                })
                ->with('user');

            // MULTI-COMPANY: Only exclude employees who have INCOMPLETE tasks with other companies
            // Employees who are done with their tasks OR have no tasks yet can be assigned to any company
            if (!empty($existingUnsavedRunIds) && isset($triggeredClientId)) {
                // Find employees who have INCOMPLETE tasks (not Completed, not Cancelled) in preserved runs
                $employeesWithIncompleteTasks = Task::whereDate('scheduled_date', $serviceDate)
                    ->whereIn('optimization_run_id', $existingUnsavedRunIds)
                    ->whereNotIn('status', ['Completed', 'Cancelled'])
                    ->whereNotNull('assigned_team_id')
                    ->pluck('assigned_team_id')
                    ->unique();

                $assignedEmployeeIds = \App\Models\OptimizationTeamMember::whereIn('optimization_team_id', $employeesWithIncompleteTasks)
                    ->pluck('employee_id')
                    ->unique()
                    ->toArray();

                if (!empty($assignedEmployeeIds)) {
                    $employeesQuery->whereNotIn('id', $assignedEmployeeIds);
                    Log::info("MULTI-COMPANY: Excluding employees with incomplete tasks", [
                        'excluded_employee_ids' => $assignedEmployeeIds,
                        'reason' => 'Employees with incomplete tasks in other companies are excluded',
                        'note' => 'Employees who completed their tasks are available for new assignments'
                    ]);
                }
            }

            $allEmployees = $employeesQuery->get();

            // MULTI-COMPANY FIX: Handle case when no employees are available for new client
            if ($allEmployees->isEmpty() && !empty($existingUnsavedRunIds)) {
                Log::warning("No available employees for new client - all employees assigned to existing runs", [
                    'existing_run_ids' => $existingUnsavedRunIds,
                    'tasks_to_optimize' => $allTasks->count(),
                ]);

                // Tasks remain unassigned until more employees are available or schedule is saved
                DB::commit();
                return [
                    'status' => 'warning',
                    'message' => 'No available employees - all are assigned to other companies. Tasks saved as pending.',
                    'tasks_pending' => $allTasks->pluck('id')->toArray(),
                ];
            }

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
            // Note: During re-optimization, we don't use locked teams (Phase 2 feature)
            // Locked teams are only used for what-if scenarios
            $lockedTeams = null;

            // PHASE 2: Genetic Algorithm Optimization
            $optimalSchedules = $this->optimizer->optimize(
                $preprocessResult['valid_tasks'],
                $preprocessResult['employee_allocations'],
                config('optimization.genetic_algorithm.max_generations', 100),
                $lockedTeams // ✅ Pass locked teams
            );
            
            // ✅ Clear old team assignments ONLY for tasks being optimized
            // MULTI-COMPANY FIX: Don't clear assignments for tasks in preserved runs
            foreach ($allTasks as $task) {
                // Only clear if task is not in a preserved run
                if (empty($existingUnsavedRunIds) || !in_array($task->optimization_run_id, $existingUnsavedRunIds)) {
                    $task->assigned_team_id = null;
                    $task->optimization_run_id = null;
                    $task->save();
                }
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
     * ✅ RULE 8: Add a single task to existing saved teams (real-time addition)
     */
    protected function addSingleTaskToExistingTeams(
        string $serviceDate,
        ?int $newTaskId,
        OptimizationRun $existingRun
    ): array {
        if (!$newTaskId) {
            throw new \Exception('Task ID is required for real-time addition');
        }

        $newTask = Task::with(['location.contractedClient', 'client'])
            ->findOrFail($newTaskId);

        // Determine which client this task belongs to
        $taskClientName = 'Unknown';
        if ($newTask->location && $newTask->location->contractedClient) {
            $taskClientName = $newTask->location->contractedClient->name;
        } elseif ($newTask->client) {
            $taskClientName = $newTask->client->first_name . ' ' . $newTask->client->last_name;
        }

        Log::info("Adding new task to existing teams (SAVED SCHEDULE)", [
            'task_id' => $newTaskId,
            'task_description' => $newTask->task_description,
            'client_name' => $taskClientName,
            'optimization_run_id' => $existingRun->id,
            'service_date' => $serviceDate
        ]);

        // Get existing teams from this optimization run
        $existingTeams = \App\Models\OptimizationTeam::where('optimization_run_id', $existingRun->id)
            ->with(['members.employee'])
            ->get();

        if ($existingTeams->isEmpty()) {
            throw new \Exception('No existing teams found for this optimization run');
        }

        // ✅ RULE 6 & 7: Find team with LEAST TASKS (primary) and least workload (secondary)
        // This matches the fair distribution logic in generateFairGreedySchedule
        $selectedTeam = null;
        $minTaskCount = PHP_INT_MAX;
        $minWorkload = PHP_INT_MAX;

        foreach ($existingTeams as $team) {
            $teamTasks = Task::where('assigned_team_id', $team->id)->get();
            $taskCount = $teamTasks->count();
            $totalHours = $teamTasks->sum(fn($t) => ($t->duration + $t->travel_time) / 60);

            $newTaskHours = ($newTask->duration + $newTask->travel_time) / 60;
            $projectedHours = $totalHours + $newTaskHours;

            // ✅ RULE 7: Skip if exceeds 12 hours
            if ($projectedHours > 12) {
                Log::info("Skipping team - would exceed 12 hours", [
                    'team_id' => $team->id,
                    'task_count' => $taskCount,
                    'current_hours' => $totalHours,
                    'projected_hours' => $projectedHours
                ]);
                continue;
            }

            // ✅ RULE 6: Primary criterion is task count (fair distribution)
            // Secondary criterion is workload (tiebreaker)
            if ($taskCount < $minTaskCount ||
                ($taskCount === $minTaskCount && $totalHours < $minWorkload)) {
                $minTaskCount = $taskCount;
                $minWorkload = $totalHours;
                $selectedTeam = $team;
            }
        }

        // If no team found (all at 12 hours), assign to team with fewest tasks
        if (!$selectedTeam) {
            Log::warning("All teams at capacity, assigning to team with fewest tasks anyway");
            $selectedTeam = $existingTeams->sortBy(function($team) {
                return Task::where('assigned_team_id', $team->id)->count();
            })->first();
        }

        // Assign the new task
        $newTask->update([
            'assigned_team_id' => $selectedTeam->id,
            'optimization_run_id' => $existingRun->id,
            'status' => 'Scheduled'
        ]);

        // Calculate final workload for logging
        $teamTasks = Task::where('assigned_team_id', $selectedTeam->id)->get();
        $finalHours = $teamTasks->sum(fn($t) => ($t->duration + $t->travel_time) / 60);

        // Get all teams' task counts for logging
        $allTeamTaskCounts = [];
        foreach ($existingTeams as $team) {
            $count = Task::where('assigned_team_id', $team->id)->count();
            $allTeamTaskCounts['Team ' . $team->team_index] = $count;
        }

        Log::info("✅ Task assigned to existing team (REAL-TIME)", [
            'task_id' => $newTask->id,
            'task_description' => $newTask->task_description,
            'client_name' => $taskClientName,
            'team_id' => $selectedTeam->id,
            'team_index' => $selectedTeam->team_index,
            'team_members' => $selectedTeam->members->pluck('employee.user.name')->toArray(),
            'team_task_count_before' => $teamTasks->count() - 1,
            'team_task_count_after' => $teamTasks->count(),
            'team_hours_after' => round($finalHours, 2),
            'all_teams_task_counts' => $allTeamTaskCounts
        ]);

        return [
            'status' => 'success',
            'message' => 'Task added to existing ' . $taskClientName . ' team',
            'assigned_team_id' => $selectedTeam->id,
            'team_members' => $selectedTeam->members->pluck('employee.user.name')->toArray(),
            'optimization_run_id' => $existingRun->id
        ];
    }

    /**
     * ✅ RULE 9: Get locked teams (teams without what-if scenarios)
     * Note: what_if_scenario_id is a Phase 2 feature, for now get all teams
     */
    protected function getLockedTeams(OptimizationRun $optimizationRun): \Illuminate\Support\Collection
    {
        return \App\Models\OptimizationTeam::where('optimization_run_id', $optimizationRun->id)
            // Phase 2: ->whereNull('what_if_scenario_id') // Teams without what-if are locked
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

            // ✅ Count UNIQUE tasks, not total assignments (prevents double-counting)
            $uniqueTaskIds = collect($scheduleData)
                ->flatMap(fn($team) => $team['tasks'])
                ->pluck('id')
                ->unique();
            $totalTasks = $uniqueTaskIds->count();

            $totalEmployees = collect($scheduleData)->sum(fn($team) => count($team['team']));
            $generationsRun = $schedule->getMetadata('generations_run') ?? 100;

            Log::info("Saving optimization run", [
                'client_identifier' => $clientIdentifier,
                'total_teams' => $totalTeams,
                'total_tasks' => $totalTasks,
                'total_task_assignments' => collect($scheduleData)->sum(fn($team) => $team['tasks']->count()),
                'unique_task_ids' => $uniqueTaskIds->toArray(),
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
            
            Log::info("Optimization run created", [
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

        // ✅ Track assigned tasks to detect duplicates
        $assignedTaskIds = [];

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

            Log::info("Optimization team created", [
                'optimization_team_id' => $optimizationTeam->id,
                'team_index' => $teamIndex + 1,
                'employee_ids' => collect($team)->pluck('id')->toArray(),
                'optimization_run_id' => $optimizationRunId
            ]);

            // ✅ Assign tasks to this team (skip duplicates)
            foreach ($tasks as $task) {
                // Check if task was already assigned to another team
                if (in_array($task->id, $assignedTaskIds)) {
                    Log::warning("⚠️ Duplicate task assignment detected - skipping", [
                        'task_id' => $task->id,
                        'current_team' => $optimizationTeam->id,
                        'team_index' => $teamIndex + 1,
                        'optimization_run_id' => $optimizationRunId
                    ]);
                    continue; // Skip this duplicate assignment
                }

                // Mark task as assigned
                $assignedTaskIds[] = $task->id;

                Task::where('id', $task->id)->update([
                    'status' => 'Scheduled',
                    'optimization_run_id' => $optimizationRunId,
                    'assigned_by_generation' => null,
                    'assigned_team_id' => $optimizationTeam->id, // ✅ Unique team ID
                ]);
                
                Log::info("Task updated", [
                    'task_id' => $task->id,
                    'status' => 'Scheduled',
                    'optimization_team_id' => $optimizationTeam->id,
                    'optimization_run_id' => $optimizationRunId
                ]);
            }
        }

        // ✅ Log summary of task assignments
        $totalTasksInSchedule = collect($scheduleData)->sum(fn($team) => $team['tasks']->count());
        $duplicateCount = $totalTasksInSchedule - count($assignedTaskIds);

        Log::info("Task assignment summary", [
            'optimization_run_id' => $optimizationRunId,
            'total_task_assignments_in_schedule' => $totalTasksInSchedule,
            'unique_tasks_assigned' => count($assignedTaskIds),
            'duplicates_detected_and_skipped' => $duplicateCount,
            'assigned_task_ids' => $assignedTaskIds
        ]);
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