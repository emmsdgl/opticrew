<?php

namespace App\Services\Optimization\PreProcessing;

use Illuminate\Support\Collection;

class WorkforceCalculator
{
    protected const MAX_HOURS_PER_DAY = 12;
    protected const TARGET_UTILIZATION = 0.85; // 85%

    public function selectOptimalWorkforce(
        Collection $tasks,
        Collection $employees,
        array $constraints
    ): Collection {
        if ($employees->isEmpty()) {
            \Log::error("WorkforceCalculator received 0 employees!");
            throw new \Exception("No employees available for workforce calculation");
        }
    
        // Calculate total required hours
        $totalRequiredHours = $this->calculateTotalHours($tasks);
        
        \Log::info("Workforce calculation", [
            'total_tasks' => $tasks->count(),
            'total_employees_available' => $employees->count(),
            'total_required_hours' => $totalRequiredHours
        ]);
    
        // Calculate minimum workforce needed
        $minWorkforce = ceil(
            $totalRequiredHours / (self::MAX_HOURS_PER_DAY * self::TARGET_UTILIZATION)
        );
    
        // Calculate maximum affordable workforce
        $budgetLimit = $constraints['budget_limit'] ?? PHP_INT_MAX;
        $dailyCost = $constraints['daily_cost_per_employee'] ?? 100;
        $maxAffordable = floor($budgetLimit / $dailyCost);
    
        // Determine final workforce size
        $finalSize = min($minWorkforce, $maxAffordable, $employees->count());
        
        // ✅ CRITICAL: Enforce MINIMUM 2 employees (for pairing constraint)
        $finalSize = max(2, $finalSize);
        
        // ✅ But don't exceed available employees
        $finalSize = min($finalSize, $employees->count());
    
        \Log::info("Workforce selected", [
            'min_needed' => $minWorkforce,
            'max_affordable' => $maxAffordable,
            'employees_available' => $employees->count(),
            'final_size' => $finalSize
        ]);
    
        // Return top N employees by efficiency
        return $employees->take($finalSize);
    }

    protected function calculateTotalHours(Collection $tasks): float
    {
        return $tasks->sum(function ($task) {
            return ($task->duration + $task->travel_time) / 60;
        });
    }
}