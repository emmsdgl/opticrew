<?php

namespace App\Services\Optimization\ScenarioManager\Scenarios;

use App\Services\Optimization\PreProcessing\RuleBasedPreProcessor;
use App\Services\Optimization\GeneticAlgorithm\GeneticAlgorithmOptimizer;
use Illuminate\Support\Collection;

class TimeConstraintScenario
{
    protected RuleBasedPreProcessor $preProcessor;
    protected GeneticAlgorithmOptimizer $optimizer;

    public function __construct()
    {
        $this->preProcessor = app(RuleBasedPreProcessor::class);
        $this->optimizer = app(GeneticAlgorithmOptimizer::class);
    }

    /**
     * Handle time constraint scenario (client needs early completion)
     */
    public function handle(array $originalSchedule, array $parameters): array
    {
        $clientId = $parameters['client_id'];
        $deadline = $parameters['deadline']; // Earlier deadline
        
        // 1. Get tasks for the constrained client
        $clientTasks = $this->extractClientTasks($originalSchedule, $clientId);
        
        if ($clientTasks->isEmpty()) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => $this->calculateFitness($originalSchedule),
                'is_feasible' => true,
                'affected_teams' => 0,
                'reassignments' => 0,
            ];
        }
        
        // 2. Check if current schedule meets deadline
        $currentCompletionTime = $this->estimateCompletionTime($originalSchedule, $clientId);
        
        if ($currentCompletionTime <= strtotime($deadline)) {
            return [
                'schedule' => $originalSchedule,
                'fitness' => $this->calculateFitness($originalSchedule),
                'is_feasible' => true,
                'affected_teams' => 0,
                'reassignments' => 0,
                'message' => 'Current schedule already meets deadline',
            ];
        }
        
        // 3. Try to optimize with more resources
        $modifiedSchedule = $this->optimizeForDeadline(
            $originalSchedule,
            $clientId,
            $deadline,
            $parameters
        );
        
        $newCompletionTime = $this->estimateCompletionTime($modifiedSchedule, $clientId);
        $meetsDeadline = $newCompletionTime <= strtotime($deadline);
        
        return [
            'schedule' => $modifiedSchedule,
            'fitness' => $this->calculateFitness($modifiedSchedule),
            'is_feasible' => $meetsDeadline,
            'affected_teams' => $this->countAffectedTeams($originalSchedule, $modifiedSchedule, $clientId),
            'reassignments' => $clientTasks->count(),
            'estimated_completion' => date('Y-m-d H:i:s', $newCompletionTime),
            'deadline' => $deadline,
            'meets_deadline' => $meetsDeadline,
        ];
    }

    protected function extractClientTasks(array $schedule, int $clientId): Collection
    {
        $tasks = collect();
        
        if (isset($schedule[$clientId]['schedule'])) {
            foreach ($schedule[$clientId]['schedule'] as $teamSchedule) {
                if (isset($teamSchedule['tasks'])) {
                    $tasks = $tasks->merge($teamSchedule['tasks']);
                }
            }
        }
        
        return $tasks;
    }

    protected function estimateCompletionTime(array $schedule, int $clientId): int
    {
        if (!isset($schedule[$clientId]['schedule'])) {
            return time();
        }
        
        $maxCompletionTime = 0;
        
        foreach ($schedule[$clientId]['schedule'] as $teamSchedule) {
            $teamWorkload = 0;
            $tasks = $teamSchedule['tasks'] ?? collect();
            
            foreach ($tasks as $task) {
                $teamWorkload += ($task->duration ?? 0) + ($task->travel_time ?? 0);
            }
            
            // Assume work starts at 8 AM
            $startTime = strtotime('today 08:00:00');
            $completionTime = $startTime + ($teamWorkload * 60); // Convert minutes to seconds
            
            $maxCompletionTime = max($maxCompletionTime, $completionTime);
        }
        
        return $maxCompletionTime ?: time();
    }

    protected function optimizeForDeadline(array $schedule, int $clientId, string $deadline, array $parameters): array
    {
        // Strategy: Allocate more employees to this client
        $clientTasks = $this->extractClientTasks($schedule, $clientId);
        $allEmployees = $this->extractAllEmployees($schedule);
        
        // Increase employee allocation for this client
        $constraints = $parameters['constraints'] ?? [];
        $constraints['priority_client'] = $clientId;
        
        $preprocessResult = $this->preProcessor->process(
            $clientTasks,
            collect($allEmployees),
            $constraints
        );
        
        // Optimize with more aggressive parameters
        $newClientSchedule = $this->optimizer->optimize(
            $preprocessResult['valid_tasks'],
            $preprocessResult['employee_allocations'],
            75 // More generations for better optimization
        );
        
        // Merge back into full schedule
        $modifiedSchedule = $schedule;
        $modifiedSchedule[$clientId] = $newClientSchedule[$clientId] ?? $schedule[$clientId];
        
        return $modifiedSchedule;
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

    protected function countAffectedTeams(array $original, array $modified, int $clientId): int
    {
        if (!isset($original[$clientId]['schedule']) || !isset($modified[$clientId]['schedule'])) {
            return 0;
        }
        
        return count($modified[$clientId]['schedule']);
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