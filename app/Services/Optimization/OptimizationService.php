<?php

namespace App\Services\Optimization;

use App\Models\Task;
use App\Models\Employee;
use App\Models\OptimizationResult;
use App\Services\Optimization\PreProcessing\RuleBasedPreProcessor;
use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Main optimization entry point
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
            
            // PHASE 1: Rule-Based Pre-Processing
            $allTasks = Task::whereDate('scheduled_date', $serviceDate)
                ->when(!empty($locationIds), fn($q) => $q->whereIn('location_id', $locationIds))
                ->get();
            
            // // ✅ SIMPLIFIED: Get all active employees (ignore day-offs for now)
            // $allEmployees = Employee::where('is_active', true)
            //     ->where('is_day_off', false)
            //     ->where('is_busy', false)
            //     ->get();

            // \Log::info("Employees available", [
            //     'count' => $allEmployees->count()
            // ]);

            $allEmployees = Employee::where('is_active', true)
                ->whereDoesntHave('dayOffs', fn($q) => $q->whereDate('date', $serviceDate))
                ->get();

            // ✅ ADD THIS LOGGING
            \Log::info("Employees fetched", [
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

            // ✅ ADD THIS LOGGING
            \Log::info("Pre-processing complete", [
                'total_tasks_fetched' => $allTasks->count(),
                'valid_tasks' => $preprocessResult['valid_tasks']->count(),
                'invalid_tasks' => $preprocessResult['invalid_tasks']->count(),
                'selected_employees' => count($preprocessResult['selected_employees'])
            ]);
                        
            if ($preprocessResult['valid_tasks']->isEmpty()) {
                throw new \Exception('No valid tasks after pre-processing');
            }
            
            \Log::info("About to optimize", [
                'valid_tasks_count' => $preprocessResult['valid_tasks']->count(),
                'employee_allocations_structure' => json_encode($preprocessResult['employee_allocations']),
                'employee_allocations_type' => gettype($preprocessResult['employee_allocations'])
            ]);

            // PHASE 2: Genetic Algorithm Optimization
            $optimalSchedules = $this->optimizer->optimize(
                $preprocessResult['valid_tasks'],
                $preprocessResult['employee_allocations'],
                config('optimization.genetic_algorithm.max_generations', 100)
            );
            
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

    protected function getConstraints(): array
    {
        return [
            'work_start_time' => config('optimization.constraints.work_start_time', '08:00:00'),
            'work_end_time' => config('optimization.constraints.work_end_time', '18:00:00'),
            'budget_limit' => config('optimization.workforce.budget_limit', 10000),
            'daily_cost_per_employee' => config('optimization.workforce.daily_cost_per_employee', 100),
        ];
    }

    protected function saveResults(string $serviceDate, array $schedules, $invalidTasks): void
    {
        foreach ($schedules as $clientIdentifier => $schedule) {
            // Handle client ID logic
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
            
            // ✅ Calculate total teams from the schedule
            $scheduleData = $schedule->getSchedule();
            $totalTeams = count($scheduleData); // Number of teams formed
            $totalTasks = collect($scheduleData)->sum(fn($team) => $team['tasks']->count());
            $totalEmployees = collect($scheduleData)->sum(fn($team) => count($team['team']));
            $generationsRun = $schedule->getMetadata('generations_run') ?? 100; // ✅ Get actual value

            \Log::info("Saving optimization run", [
                'client_identifier' => $clientIdentifier,
                'total_teams' => $totalTeams,
                'total_tasks' => $totalTasks,
                'total_employees' => $totalEmployees,
                'generations_run' => $generationsRun // ✅ Log it
            ]);
            
            // ✅ Create with actual generation count
            $optimizationRun = \App\Models\OptimizationRun::create([
                'service_date' => $serviceDate,
                'total_tasks' => $totalTasks,
                'total_employees' => $totalEmployees,
                'total_teams' => $totalTeams,
                'final_fitness_score' => $schedule->getFitness() ?? 0.0,
                'generations_run' => $generationsRun, // ✅ Use actual value
                'status' => 'completed',
                'employee_allocation_data' => json_encode([]),
                'greedy_result_data' => json_encode([]),
            ]);
            
            \Log::info("Optimization run created", [
                'optimization_run_id' => $optimizationRun->id
            ]);
            
            // Update tasks with the optimization run ID
            $this->updateTasksWithOptimization($schedule, $optimizationRun->id);
        }
    }

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