<?php

namespace App\Services\Optimization\PreProcessing;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Workforce Calculator - Implements 5-Step Workforce Calculation Methodology
 *
 * This service calculates optimal workforce size based on:
 * (a) Individual task durations: Di = Ai / Si
 * (b) Total required work hours: T_req = Σ(Di + Li)
 * (c) Minimum workforce (baseline): N_base = Ceiling(T_req / (H_avail * R))
 * (d) Maximum affordable workforce: N_cost-max = Floor(C_limit / (W * H_avail + B))
 * (e) Final workforce: N_final = Maximum(N_base, Minimum(N_set, N_cost-max))
 */
class WorkforceCalculator
{
    /**
     * Step (a): Calculate estimated cleaning duration for a task
     * Formula: Di = Ai / Si
     *
     * @param float $area Area to be cleaned (square meters)
     * @param float|null $cleaningSpeed Cleaning speed (m²/hour), uses config default if null
     * @return float Duration in hours
     */
    public function calculateCleaningDuration(float $area, ?float $cleaningSpeed = null): float
    {
        $speed = $cleaningSpeed ?? config('optimization.workforce.cleaning_speed', 10.0);

        if ($speed <= 0) {
            throw new \InvalidArgumentException("Cleaning speed must be greater than 0");
        }

        $duration = $area / $speed;

        Log::debug("Workforce - Step (a): Cleaning Duration", [
            'area_m2' => $area,
            'speed_m2_per_hour' => $speed,
            'duration_hours' => round($duration, 2)
        ]);

        return $duration;
    }

    /**
     * Step (b): Calculate total required work hours from Task models
     * Formula: T_req = Σ(Di) + one-time travel per client group
     *
     * @param Collection $tasks Collection of Task models
     * @return float Total required work hours
     */
    protected function calculateTotalHours(Collection $tasks): float
    {
        $totalHours = 0.0;

        // Calculate task duration hours
        foreach ($tasks as $task) {
            // Duration in minutes, convert to hours
            $Di = ($task->estimated_duration_minutes ?? $task->duration ?? 60) / 60.0;
            $totalHours += $Di;
        }

        // ✅ Add one-time travel time per client group
        // Group tasks by contracted client
        $clientGroups = $tasks->groupBy(function($task) {
            if ($task->location && $task->location->contracted_client_id) {
                return $task->location->contracted_client_id;
            }
            return 'unknown';
        });

        // Add travel time for each client group
        foreach ($clientGroups as $clientId => $clientTasks) {
            if ($clientId === 'unknown') continue;

            // Kakslauttanen (ID=1): 30 min, Aikamatkat (ID=2): 15 min
            $travelMinutes = ($clientId == 1) ? 30 : 15;
            $totalHours += $travelMinutes / 60.0;
        }

        Log::info("Workforce - Step (b): Total Required Hours", [
            'task_count' => $tasks->count(),
            'client_groups' => $clientGroups->keys()->toArray(),
            'total_hours' => round($totalHours, 2),
            'avg_hours_per_task' => $tasks->count() > 0 ? round($totalHours / $tasks->count(), 2) : 0
        ]);

        return $totalHours;
    }

    /**
     * Step (c): Calculate minimum/baseline workforce size
     * Formula: N_base = Ceiling(T_req / (H_avail * R))
     *
     * @param float $totalRequiredHours Total work hours needed
     * @param float|null $availableHours Available work hours per employee per day
     * @param float|null $utilizationRate Target utilization rate (0-1)
     * @return int Minimum number of employees needed
     */
    protected function calculateMinimumWorkforce(
        float $totalRequiredHours,
        ?float $availableHours = null,
        ?float $utilizationRate = null
    ): int {
        $hAvail = $availableHours ?? config('optimization.workforce.available_hours', 8.0);
        $r = $utilizationRate ?? config('optimization.workforce.target_utilization', 0.85);

        if ($hAvail <= 0 || $r <= 0 || $r > 1) {
            throw new \InvalidArgumentException("Invalid parameters: H_avail must be positive, R must be 0-1");
        }

        $productiveHoursPerEmployee = $hAvail * $r;
        $nBase = (int) ceil($totalRequiredHours / $productiveHoursPerEmployee);

        Log::info("Workforce - Step (c): Minimum Workforce", [
            'total_required_hours' => round($totalRequiredHours, 2),
            'available_hours_per_employee' => $hAvail,
            'utilization_rate' => $r,
            'productive_hours_per_employee' => round($productiveHoursPerEmployee, 2),
            'minimum_workforce' => $nBase
        ]);

        return $nBase;
    }

    /**
     * Step (d): Calculate maximum affordable workforce based on budget
     * Formula: N_cost-max = Floor(C_limit / (W * H_avail + B))
     *
     * @param float|null $budgetLimit Maximum budget for workforce (per day)
     * @param float|null $hourlyWage Wage per hour per employee
     * @param float|null $availableHours Available work hours per employee per day
     * @param float|null $benefitsCost Additional costs per employee
     * @return int Maximum affordable number of employees
     */
    protected function calculateMaxAffordableWorkforce(
        ?float $budgetLimit = null,
        ?float $hourlyWage = null,
        ?float $availableHours = null,
        ?float $benefitsCost = null
    ): int {
        // If no budget limit, return max int (no constraint)
        if ($budgetLimit === null) {
            $budgetLimit = config('optimization.workforce.budget_limit');
            if ($budgetLimit === null) {
                return PHP_INT_MAX;
            }
        }

        $w = $hourlyWage ?? config('optimization.workforce.hourly_wage', 15.0);
        $hAvail = $availableHours ?? config('optimization.workforce.available_hours', 8.0);
        $b = $benefitsCost ?? config('optimization.workforce.benefits_cost', 5.0);

        if ($budgetLimit <= 0) {
            throw new \InvalidArgumentException("Budget limit must be positive");
        }

        $costPerEmployee = ($w * $hAvail) + $b;
        $nCostMax = (int) floor($budgetLimit / $costPerEmployee);

        Log::info("Workforce - Step (d): Maximum Affordable Workforce", [
            'budget_limit' => $budgetLimit,
            'hourly_wage' => $w,
            'available_hours' => $hAvail,
            'benefits_cost' => $b,
            'cost_per_employee' => round($costPerEmployee, 2),
            'max_affordable_workforce' => $nCostMax
        ]);

        return max(0, $nCostMax);
    }

    /**
     * Step (e): Determine final workforce size
     * ✅ FIXED Formula: N_final = Minimum(N_base, N_set, N_cost-max)
     *
     * Ensures:
     * 1. N_final ≤ N_base (don't allocate more than needed)
     * 2. N_final ≤ N_set (doesn't exceed available employees)
     * 3. N_final ≤ N_cost-max (doesn't exceed budget)
     * 4. Warns if N_base > N_set (insufficient employees for workload)
     *
     * @param int $baseWorkforce Minimum workforce needed
     * @param int $availableEmployees Number of available employees (N_set)
     * @param int $maxAffordable Maximum affordable employees
     * @return int Final determined workforce size
     */
    protected function calculateFinalWorkforce(
        int $baseWorkforce,
        int $availableEmployees,
        int $maxAffordable
    ): int {
        // ✅ FIX: Use MINIMUM of needed/available/affordable
        // Don't allocate ALL employees when only a few are needed!
        $nFinal = min($baseWorkforce, $availableEmployees, $maxAffordable);

        // RULE 1: Ensure even number for PAIRS, or +1 for TRIO if odd
        if ($nFinal % 2 !== 0 && $nFinal < $availableEmployees) {
            $nFinal++; // Make pairs, or add one more for a trio
        }

        // Ensure minimum of 2 employees (1 pair)
        $nFinal = max(2, $nFinal);

        Log::info("Workforce - Step (e): Final Workforce Size", [
            'base_workforce_required' => $baseWorkforce,
            'available_employees' => $availableEmployees,
            'max_affordable' => $maxAffordable,
            'final_workforce' => $nFinal,
            'constraints_met' => [
                'meets_minimum_requirement' => $nFinal >= $baseWorkforce,
                'within_available_pool' => $nFinal <= $availableEmployees,
                'within_budget' => $nFinal <= $maxAffordable
            ]
        ]);

        // Warnings
        if ($nFinal < $baseWorkforce) {
            Log::warning("Final workforce below minimum requirement!", [
                'required' => $baseWorkforce,
                'assigned' => $nFinal
            ]);
        }

        if ($baseWorkforce > $maxAffordable) {
            Log::warning("Workload exceeds budget constraints!", [
                'required_employees' => $baseWorkforce,
                'affordable_employees' => $maxAffordable,
                'shortfall' => $baseWorkforce - $maxAffordable
            ]);
        }

        return $nFinal;
    }

    /**
     * Main Method: Select optimal workforce using 5-step methodology
     *
     * @param Collection $tasks Collection of Task models
     * @param Collection $employees Collection of Employee models
     * @param array $constraints Additional constraints (budget, etc.)
     * @return Collection Selected employees
     */
    public function selectOptimalWorkforce(
        Collection $tasks,
        Collection $employees,
        array $constraints
    ): Collection {
        if ($employees->isEmpty()) {
            Log::error("WorkforceCalculator received 0 employees!");
            throw new \Exception("No employees available for workforce calculation");
        }

        Log::info("=== Starting 5-Step Workforce Calculation ===", [
            'total_tasks' => $tasks->count(),
            'total_employees_available' => $employees->count()
        ]);

        // Step (b): Calculate total required hours
        $totalRequiredHours = $this->calculateTotalHours($tasks);

        // Step (c): Calculate minimum workforce
        $minimumWorkforce = $this->calculateMinimumWorkforce($totalRequiredHours);

        // Step (d): Calculate maximum affordable (if budget set)
        $budgetLimit = $constraints['budget_limit'] ?? config('optimization.workforce.budget_limit');
        $maxAffordable = $this->calculateMaxAffordableWorkforce($budgetLimit);

        // Step (e): Determine final workforce
        $finalSize = $this->calculateFinalWorkforce(
            $minimumWorkforce,
            $employees->count(),
            $maxAffordable
        );

        Log::info("=== Workforce Calculation Complete ===", [
            'total_required_hours' => round($totalRequiredHours, 2),
            'minimum_workforce' => $minimumWorkforce,
            'max_affordable' => $maxAffordable === PHP_INT_MAX ? 'unlimited' : $maxAffordable,
            'employees_available' => $employees->count(),
            'final_workforce_size' => $finalSize
        ]);

        // Return top N employees (can be enhanced to sort by efficiency/experience)
        return $employees->take($finalSize);
    }
}