<?php

namespace App\Services\Optimization\ScenarioManager;

class ImpactAnalyzer
{
    /**
     * Analyze the impact of a scenario on the schedule
     */
    public function analyze(
        float $baselineFitness,
        array $modifiedResult,
        array $originalSchedule
    ): array {
        $scenarioFitness = $this->calculateFitness($modifiedResult['schedule']);
        $fitnessDifference = $scenarioFitness - $baselineFitness;
        
        return [
            'baseline_fitness' => $baselineFitness,
            'scenario_fitness' => $scenarioFitness,
            'fitness_difference' => $fitnessDifference,
            'fitness_change_percentage' => $this->calculatePercentageChange($baselineFitness, $scenarioFitness),
            'affected_teams' => $modifiedResult['affected_teams'] ?? 0,
            'reassignments_needed' => $modifiedResult['reassignments'] ?? 0,
            'feasible' => $modifiedResult['is_feasible'] ?? true,
            'workload_distribution' => $this->analyzeWorkloadDistribution($modifiedResult['schedule']),
            'task_completion_risk' => $this->assessCompletionRisk($modifiedResult),
            'resource_utilization' => $this->calculateResourceUtilization($modifiedResult['schedule']),
            'critical_issues' => $this->identifyCriticalIssues($modifiedResult),
        ];
    }

    /**
     * Calculate overall fitness of a schedule
     */
    protected function calculateFitness(array $schedule): float
    {
        if (empty($schedule)) {
            return 0;
        }
        
        $totalFitness = 0;
        $count = 0;
        
        foreach ($schedule as $clientSchedule) {
            if (isset($clientSchedule['fitness'])) {
                $totalFitness += $clientSchedule['fitness'];
                $count++;
            } elseif (is_object($clientSchedule) && method_exists($clientSchedule, 'getFitness')) {
                $totalFitness += $clientSchedule->getFitness() ?? 0;
                $count++;
            }
        }
        
        return $count > 0 ? $totalFitness / $count : 0;
    }

    /**
     * Calculate percentage change in fitness
     */
    protected function calculatePercentageChange(float $baseline, float $current): float
    {
        if ($baseline == 0) {
            return 0;
        }
        
        return (($current - $baseline) / $baseline) * 100;
    }

    /**
     * Analyze workload distribution across teams
     */
    protected function analyzeWorkloadDistribution(array $schedule): array
    {
        $workloads = [];
        
        foreach ($schedule as $clientId => $clientSchedule) {
            if (is_array($clientSchedule)) {
                foreach ($clientSchedule as $teamSchedule) {
                    $workload = $this->calculateTeamWorkload($teamSchedule);
                    $workloads[] = $workload;
                }
            }
        }
        
        if (empty($workloads)) {
            return [
                'mean' => 0,
                'std_dev' => 0,
                'min' => 0,
                'max' => 0,
                'balance_score' => 1.0,
            ];
        }
        
        $mean = array_sum($workloads) / count($workloads);
        $variance = 0;
        
        foreach ($workloads as $workload) {
            $variance += pow($workload - $mean, 2);
        }
        
        $stdDev = sqrt($variance / count($workloads));
        
        return [
            'mean' => round($mean, 2),
            'std_dev' => round($stdDev, 2),
            'min' => round(min($workloads), 2),
            'max' => round(max($workloads), 2),
            'balance_score' => round(1 / (1 + $stdDev), 3),
        ];
    }

    /**
     * Calculate workload for a team
     */
    protected function calculateTeamWorkload($teamSchedule): float
    {
        if (!isset($teamSchedule['tasks'])) {
            return 0;
        }
        
        $tasks = $teamSchedule['tasks'];
        $totalMinutes = 0;
        
        foreach ($tasks as $task) {
            $totalMinutes += ($task->duration ?? 0) + ($task->travel_time ?? 0);
        }
        
        return $totalMinutes / 60; // Convert to hours
    }

    /**
     * Assess risk of not completing tasks on time
     */
    protected function assessCompletionRisk(array $result): string
    {
        if (!($result['is_feasible'] ?? true)) {
            return 'HIGH';
        }
        
        $reassignments = $result['reassignments'] ?? 0;
        
        if ($reassignments > 20) {
            return 'HIGH';
        } elseif ($reassignments > 10) {
            return 'MEDIUM';
        } else {
            return 'LOW';
        }
    }

    /**
     * Calculate resource utilization percentage
     */
    protected function calculateResourceUtilization(array $schedule): float
    {
        $totalAvailableHours = 0;
        $totalUsedHours = 0;
        $maxHoursPerDay = 8;
        
        foreach ($schedule as $clientSchedule) {
            if (is_array($clientSchedule)) {
                foreach ($clientSchedule as $teamSchedule) {
                    $teamSize = isset($teamSchedule['team']) ? count($teamSchedule['team']) : 1;
                    $totalAvailableHours += $teamSize * $maxHoursPerDay;
                    $totalUsedHours += $this->calculateTeamWorkload($teamSchedule);
                }
            }
        }
        
        if ($totalAvailableHours == 0) {
            return 0;
        }
        
        return round(($totalUsedHours / $totalAvailableHours) * 100, 2);
    }

    /**
     * Identify critical issues in the scenario result
     */
    protected function identifyCriticalIssues(array $result): array
    {
        $issues = [];
        
        if (!($result['is_feasible'] ?? true)) {
            $issues[] = [
                'severity' => 'CRITICAL',
                'message' => 'Schedule is not feasible - cannot complete all tasks',
            ];
        }
        
        if (($result['affected_teams'] ?? 0) > 5) {
            $issues[] = [
                'severity' => 'HIGH',
                'message' => 'Large number of teams affected - significant disruption expected',
            ];
        }
        
        if (($result['reassignments'] ?? 0) > 15) {
            $issues[] = [
                'severity' => 'MEDIUM',
                'message' => 'High number of task reassignments required',
            ];
        }
        
        return $issues;
    }

    /**
     * Generate comparison report
     */
    public function generateComparisonReport(array $scenarios): array
    {
        return collect($scenarios)->map(function ($scenario, $name) {
            return [
                'scenario_name' => $name,
                'fitness_score' => $scenario['impact_analysis']['scenario_fitness'] ?? 0,
                'feasible' => $scenario['impact_analysis']['feasible'] ?? false,
                'risk_level' => $scenario['impact_analysis']['task_completion_risk'] ?? 'UNKNOWN',
                'affected_teams' => $scenario['impact_analysis']['affected_teams'] ?? 0,
            ];
        })->sortByDesc('fitness_score')->values()->all();
    }
}