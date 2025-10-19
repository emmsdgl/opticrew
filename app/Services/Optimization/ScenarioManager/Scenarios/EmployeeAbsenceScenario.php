<?php

namespace App\Services\Optimization\ScenarioManager\Scenarios;

use App\Services\Optimization\PreProcessing\RuleBasedPreProcessor;
use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer;
use Illuminate\Support\Collection;

class EmployeeAbsenceScenario
{
    protected RuleBasedPreProcessor $preProcessor;
    protected GeneticAlgorithmOptimizer $optimizer;

    public function __construct()
    {
        $this->preProcessor = app(RuleBasedPreProcessor::class);
        $this->optimizer = app(GeneticAlgorithmOptimizer::class);
    }

    /**
     * Handle employee absence scenario
     */
    public function handle(array $originalSchedule, array $parameters): array
    {
        $absentEmployeeId = $parameters['employee_id'];
        
        // 1. Identify affected teams
        $affectedTeams = $this->identifyAffectedTeams($originalSchedule, $absentEmployeeId);
        
        if (empty($affectedTeams)) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => $this->calculateFitness($originalSchedule),
                'is_feasible' => true,
                'affected_teams' => 0,
                'reassignments' => 0,
            ];
        }
        
        // 2. Extract orphaned tasks
        $orphanedTasks = $this->extractOrphanedTasks($originalSchedule, $affectedTeams);
        
        // 3. Get remaining employees
        $remainingEmployees = $this->getRemainingEmployees($originalSchedule, $absentEmployeeId);
        
        // 4. Re-optimize with constraints
        $constraints = $parameters['constraints'] ?? [];
        
        $preprocessResult = $this->preProcessor->process(
            collect($orphanedTasks),
            collect($remainingEmployees),
            $constraints
        );
        
        if ($preprocessResult['valid_tasks']->isEmpty()) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => 0,
                'is_feasible' => false,
                'affected_teams' => count($affectedTeams),
                'reassignments' => count($orphanedTasks),
                'error' => 'No valid tasks after re-processing',
            ];
        }
        
        // 5. Re-run genetic algorithm with fewer generations for quick response
        $newSchedule = $this->optimizer->optimize(
            $preprocessResult['valid_tasks'],
            $preprocessResult['employee_allocations'],
            50 // Reduced generations for faster response
        );
        
        // 6. Merge with unaffected parts
        $finalSchedule = $this->mergeSchedules($originalSchedule, $newSchedule, $affectedTeams);
        
        return [
            'schedule' => $finalSchedule,
            'fitness' => $this->calculateFitness($finalSchedule),
            'is_feasible' => $preprocessResult['invalid_tasks']->isEmpty(),
            'affected_teams' => count($affectedTeams),
            'reassignments' => count($orphanedTasks),
            'absent_employee_id' => $absentEmployeeId,
        ];
    }

    protected function identifyAffectedTeams(array $schedule, int $employeeId): array
    {
        $affected = [];
        
        foreach ($schedule as $clientId => $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamIndex => $teamSchedule) {
                    if (isset($teamSchedule['team'])) {
                        $teamMemberIds = collect($teamSchedule['team'])->pluck('id');
                        if ($teamMemberIds->contains($employeeId)) {
                            $affected[] = [
                                'client_id' => $clientId,
                                'team_index' => $teamIndex,
                            ];
                        }
                    }
                }
            }
        }
        
        return $affected;
    }

    protected function extractOrphanedTasks(array $schedule, array $affectedTeams): array
    {
        $orphanedTasks = [];
        
        foreach ($affectedTeams as $affected) {
            $clientId = $affected['client_id'];
            $teamIndex = $affected['team_index'];
            
            if (isset($schedule[$clientId]['schedule'][$teamIndex]['tasks'])) {
                $tasks = $schedule[$clientId]['schedule'][$teamIndex]['tasks'];
                $orphanedTasks = array_merge($orphanedTasks, $tasks->all());
            }
        }
        
        return $orphanedTasks;
    }

    protected function getRemainingEmployees(array $schedule, int $absentEmployeeId): array
    {
        $allEmployees = [];
        
        foreach ($schedule as $clientSchedule) {
            if (is_array($clientSchedule) && isset($clientSchedule['schedule'])) {
                foreach ($clientSchedule['schedule'] as $teamSchedule) {
                    if (isset($teamSchedule['team'])) {
                        foreach ($teamSchedule['team'] as $employee) {
                            if ($employee->id !== $absentEmployeeId) {
                                $allEmployees[$employee->id] = $employee;
                            }
                        }
                    }
                }
            }
        }
        
        return array_values($allEmployees);
    }

    protected function mergeSchedules(array $original, array $new, array $affectedTeams): array
    {
        $merged = $original;
        
        foreach ($new as $clientId => $newClientSchedule) {
            $merged[$clientId] = $newClientSchedule;
        }
        
        return $merged;
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