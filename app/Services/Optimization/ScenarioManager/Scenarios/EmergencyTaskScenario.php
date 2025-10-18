<?php

namespace App\Services\Optimization\ScenarioManager\Scenarios;

use App\Services\Optimization\PreProcessing\RuleBasedPreProcessor;
use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer;
use Illuminate\Support\Collection;

class EmergencyTaskScenario
{
    protected RuleBasedPreProcessor $preProcessor;
    protected GeneticAlgorithmOptimizer $optimizer;

    public function __construct()
    {
        $this->preProcessor = app(RuleBasedPreProcessor::class);
        $this->optimizer = app(GeneticAlgorithmOptimizer::class);
    }

    /**
     * Handle emergency tasks added to schedule
     */
    public function handle(array $originalSchedule, array $parameters): array
    {
        $emergencyTasks = collect($parameters['emergency_tasks'] ?? []);
        
        if ($emergencyTasks->isEmpty()) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => $this->calculateFitness($originalSchedule),
                'is_feasible' => true,
                'affected_teams' => 0,
                'reassignments' => 0,
            ];
        }
        
        // 1. Combine existing and emergency tasks
        $existingTasks = $this->extractAllTasks($originalSchedule);
        $allTasks = $existingTasks->merge($emergencyTasks);
        
        // 2. Get current employee pool
        $currentEmployees = $this->extractAllEmployees($originalSchedule);
        
        // 3. Re-optimize entire schedule with new tasks
        $constraints = $parameters['constraints'] ?? [];
        
        $preprocessResult = $this->preProcessor->process(
            $allTasks,
            collect($currentEmployees),
            $constraints
        );
        
        if ($preprocessResult['valid_tasks']->isEmpty()) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => 0,
                'is_feasible' => false,
                'affected_teams' => 'all',
                'reassignments' => $allTasks->count(),
                'error' => 'Cannot accommodate emergency tasks',
            ];
        }
        
        // 4. Run genetic algorithm with priority for emergency tasks
        $newSchedule = $this->optimizer->optimize(
            $preprocessResult['valid_tasks'],
            $preprocessResult['employee_allocations'],
            50
        );
        
        return [
            'schedule' => $newSchedule,
            'fitness' => $this->calculateFitness($newSchedule),
            'is_feasible' => $preprocessResult['invalid_tasks']->isEmpty(),
            'affected_teams' => 'all',
            'reassignments' => $allTasks->count(),
            'emergency_tasks_count' => $emergencyTasks->count(),
            'invalid_tasks' => $preprocessResult['invalid_tasks'],
        ];
    }

    /**
     * Handle emergency with priority insertion (faster alternative)
     */
    public function handleWithPriorityInsertion(array $originalSchedule, array $parameters): array
    {
        $emergencyTasks = collect($parameters['emergency_tasks'] ?? []);
        $modifiedSchedule = $originalSchedule;
        
        foreach ($emergencyTasks as $task) {
            // Find team with lowest current workload
            $targetTeam = $this->findLeastLoadedTeam($modifiedSchedule);
            
            if ($targetTeam) {
                // Insert at beginning (highest priority)
                array_unshift($modifiedSchedule[$targetTeam['client_id']]['schedule'][$targetTeam['team_index']]['tasks'], $task);
            }
        }
        
        return [
            'schedule' => $modifiedSchedule,
            'fitness' => $this->calculateFitness($modifiedSchedule),
            'is_feasible' => true,
            'affected_teams' => $emergencyTasks->count(),
            'reassignments' => $emergencyTasks->count(),
            'method' => 'priority_insertion',
        ];
    }

    protected function extractAllTasks(array $schedule): Collection
    {
        $allTasks = collect();
        
        foreach ($schedule as $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamSchedule) {
                    if (isset($teamSchedule['tasks'])) {
                        $allTasks = $allTasks->merge($teamSchedule['tasks']);
                    }
                }
            }
        }
        
        return $allTasks;
    }

    protected function extractAllEmployees(array $schedule): array
    {
        $employees = [];
        
        foreach ($schedule as $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamSchedule) {
                    if (isset($teamSchedule['team'])) {
                        foreach ($teamSchedule['team'] as $employee) {
                            $employees[$employee->id] = $employee;
                        }
                    }
                }
            }
        }
        
        return array_values($employees);
    }

    protected function findLeastLoadedTeam(array $schedule): ?array
    {
        $minWorkload = PHP_INT_MAX;
        $targetTeam = null;
        
        foreach ($schedule as $clientId => $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamIndex => $teamSchedule) {
                    $workload = $this->calculateWorkload($teamSchedule['tasks'] ?? collect());
                    
                    if ($workload < $minWorkload) {
                        $minWorkload = $workload;
                        $targetTeam = [
                            'client_id' => $clientId,
                            'team_index' => $teamIndex,
                        ];
                    }
                }
            }
        }
        
        return $targetTeam;
    }

    protected function calculateWorkload($tasks): float
    {
        $total = 0;
        foreach ($tasks as $task) {
            $total += ($task->duration ?? 0) + ($task->travel_time ?? 0);
        }
        return $total / 60;
    }

    protected function calculateFitness(array $schedule): float
    {
        $totalFitness = 0;
        $count = 0;
        
        foreach ($schedule as $clientSchedule) {
            if (is_object($clientSchedule) && method_exists($clientSchedule, 'getFitness')) {
                $totalFitness += $clientSchedule->getFitness() ?? 0;
                $count++;
            }
        }
        
        return $count > 0 ? $totalFitness / $count : 0;
    }
}