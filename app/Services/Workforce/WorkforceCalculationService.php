<?php

namespace App\Services\Workforce;

use Illuminate\Support\Facades\Log;

/**
 * Workforce Calculation Service
 *
 * Implements the 5-step methodology for determining optimal workforce size:
 * (a) Calculate cleaning duration per task: Di = Ai / Si
 * (b) Calculate total required work hours: T_req = Σ(Di + Li)
 * (c) Calculate minimum workforce: N_base = Ceiling(T_req / (H_avail * R))
 * (d) Calculate maximum affordable workforce: N_cost-max = Floor(C_limit / (W * H_avail + B))
 * (e) Determine final workforce: N_final = Maximum(N_base, Minimum(N_set, N_cost-max))
 */
class WorkforceCalculationService
{
    // Configuration constants (can be moved to config file later)
    protected const DEFAULT_AVAILABLE_HOURS = 8.0;      // H_avail: Work hours per employee per day
    protected const DEFAULT_UTILIZATION_RATE = 0.85;    // R: Target utilization (85% productive time)
    protected const DEFAULT_HOURLY_WAGE = 15.0;         // W: Average hourly wage (EUR/hour)
    protected const DEFAULT_BENEFITS_COST = 5.0;        // B: Additional costs per employee per day
    protected const DEFAULT_CLEANING_SPEED = 10.0;      // Si: Default cleaning speed (m²/hour)
    protected const DEFAULT_TRAVEL_TIME = 0.5;          // Li: Default travel time per task (hours)

    /**
     * Step (a): Calculate estimated cleaning duration for a task
     * Formula: Di = Ai / Si
     *
     * @param float $area Area to be cleaned (in square meters)
     * @param float|null $cleaningSpeed Cleaning speed (m²/hour), uses default if null
     * @return float Duration in hours
     */
    public function calculateCleaningDuration(float $area, ?float $cleaningSpeed = null): float
    {
        $speed = $cleaningSpeed ?? self::DEFAULT_CLEANING_SPEED;

        if ($speed <= 0) {
            throw new \InvalidArgumentException("Cleaning speed must be greater than 0");
        }

        $duration = $area / $speed;

        Log::debug("Workforce Calculation - Step (a): Cleaning Duration", [
            'area_m2' => $area,
            'speed_m2_per_hour' => $speed,
            'duration_hours' => $duration
        ]);

        return $duration;
    }

    /**
     * Step (a) Alternative: Calculate duration from stored task duration (minutes)
     * Converts task duration from minutes to hours
     *
     * @param int $durationMinutes Task duration in minutes
     * @return float Duration in hours
     */
    public function calculateDurationFromMinutes(int $durationMinutes): float
    {
        return $durationMinutes / 60.0;
    }

    /**
     * Step (b): Calculate total required work hours for all tasks
     * Formula: T_req = Σ(Di + Li) for all tasks from i=1 to N
     *
     * @param array $tasks Array of tasks with 'duration_hours' and optional 'travel_time_hours'
     * @return float Total required work hours
     */
    public function calculateTotalRequiredHours(array $tasks): float
    {
        $totalHours = 0.0;
        $taskCount = count($tasks);

        foreach ($tasks as $task) {
            $Di = $task['duration_hours'] ?? 0;
            $Li = $task['travel_time_hours'] ?? self::DEFAULT_TRAVEL_TIME;
            $totalHours += ($Di + $Li);
        }

        Log::info("Workforce Calculation - Step (b): Total Required Hours", [
            'task_count' => $taskCount,
            'total_hours' => $totalHours,
            'avg_hours_per_task' => $taskCount > 0 ? $totalHours / $taskCount : 0
        ]);

        return $totalHours;
    }

    /**
     * Step (b) Alternative: Calculate from task models with estimated_duration_minutes
     *
     * @param \Illuminate\Support\Collection $tasks Collection of Task models
     * @return float Total required work hours
     */
    public function calculateTotalRequiredHoursFromTasks($tasks): float
    {
        $totalHours = 0.0;

        foreach ($tasks as $task) {
            // Duration in minutes, convert to hours
            $Di = ($task->estimated_duration_minutes ?? 60) / 60.0;
            // Travel time in minutes, convert to hours
            $Li = ($task->travel_time ?? 30) / 60.0;
            $totalHours += ($Di + $Li);
        }

        Log::info("Workforce Calculation - Step (b): Total Required Hours from Tasks", [
            'task_count' => $tasks->count(),
            'total_hours' => round($totalHours, 2),
            'avg_hours_per_task' => $tasks->count() > 0 ? round($totalHours / $tasks->count(), 2) : 0
        ]);

        return $totalHours;
    }

    /**
     * Step (c): Calculate minimum/baseline workforce size
     * Formula: N_base = Ceiling(T_req / (H_avail * R))
     *
     * @param float $totalRequiredHours Total work hours needed (from step b)
     * @param float|null $availableHours Available work hours per employee per day
     * @param float|null $utilizationRate Target utilization rate (0-1)
     * @return int Minimum number of employees needed
     */
    public function calculateMinimumWorkforce(
        float $totalRequiredHours,
        ?float $availableHours = null,
        ?float $utilizationRate = null
    ): int {
        $hAvail = $availableHours ?? self::DEFAULT_AVAILABLE_HOURS;
        $r = $utilizationRate ?? self::DEFAULT_UTILIZATION_RATE;

        if ($hAvail <= 0 || $r <= 0 || $r > 1) {
            throw new \InvalidArgumentException("Invalid parameters: H_avail and R must be positive, R must be <= 1");
        }

        $productiveHoursPerEmployee = $hAvail * $r;
        $nBase = (int) ceil($totalRequiredHours / $productiveHoursPerEmployee);

        Log::info("Workforce Calculation - Step (c): Minimum Workforce", [
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
     * @param float $budgetLimit Maximum budget for workforce (per day)
     * @param float|null $hourlyWage Wage per hour per employee
     * @param float|null $availableHours Available work hours per employee per day
     * @param float|null $benefitsCost Additional costs per employee (benefits, insurance, etc.)
     * @return int Maximum affordable number of employees
     */
    public function calculateMaxAffordableWorkforce(
        float $budgetLimit,
        ?float $hourlyWage = null,
        ?float $availableHours = null,
        ?float $benefitsCost = null
    ): int {
        $w = $hourlyWage ?? self::DEFAULT_HOURLY_WAGE;
        $hAvail = $availableHours ?? self::DEFAULT_AVAILABLE_HOURS;
        $b = $benefitsCost ?? self::DEFAULT_BENEFITS_COST;

        if ($budgetLimit <= 0) {
            throw new \InvalidArgumentException("Budget limit must be positive");
        }

        $costPerEmployee = ($w * $hAvail) + $b;
        $nCostMax = (int) floor($budgetLimit / $costPerEmployee);

        Log::info("Workforce Calculation - Step (d): Maximum Affordable Workforce", [
            'budget_limit' => $budgetLimit,
            'hourly_wage' => $w,
            'available_hours' => $hAvail,
            'benefits_cost' => $b,
            'cost_per_employee' => round($costPerEmployee, 2),
            'max_affordable_workforce' => $nCostMax
        ]);

        return max(0, $nCostMax); // Ensure non-negative
    }

    /**
     * Step (e): Determine final workforce size
     * Formula: N_final = Maximum(N_base, Minimum(N_set, N_cost-max))
     *
     * Ensures:
     * 1. N_final ≥ N_base (enough to complete work)
     * 2. N_final ≤ N_set (doesn't exceed available employees)
     * 3. N_final ≤ N_cost-max (doesn't exceed budget)
     *
     * @param int $baseWorkforce Minimum workforce needed (from step c)
     * @param int $availableEmployees Set number of available employees (N_set)
     * @param int $maxAffordable Maximum affordable employees (from step d)
     * @return int Final determined workforce size
     */
    public function calculateFinalWorkforce(
        int $baseWorkforce,
        int $availableEmployees,
        int $maxAffordable
    ): int {
        // N_final = max(N_base, min(N_set, N_cost-max))
        $nFinal = max($baseWorkforce, min($availableEmployees, $maxAffordable));

        Log::info("Workforce Calculation - Step (e): Final Workforce Size", [
            'base_workforce_required' => $baseWorkforce,
            'available_employees' => $availableEmployees,
            'max_affordable' => $maxAffordable,
            'final_workforce' => $nFinal,
            'constraints' => [
                'meets_minimum_requirement' => $nFinal >= $baseWorkforce,
                'within_available_pool' => $nFinal <= $availableEmployees,
                'within_budget' => $nFinal <= $maxAffordable
            ]
        ]);

        // Validation warnings
        if ($nFinal < $baseWorkforce) {
            Log::warning("Final workforce is below minimum requirement!", [
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
     * Complete Workforce Calculation - All 5 Steps
     *
     * Performs the entire calculation workflow and returns comprehensive results
     *
     * @param array $options Configuration options
     * @return array Calculation results with all intermediate values
     */
    public function calculateWorkforce(array $options): array
    {
        // Extract options with defaults
        $tasks = $options['tasks'] ?? [];
        $availableEmployees = $options['available_employees'] ?? 0;
        $budgetLimit = $options['budget_limit'] ?? null;
        $availableHours = $options['available_hours'] ?? self::DEFAULT_AVAILABLE_HOURS;
        $utilizationRate = $options['utilization_rate'] ?? self::DEFAULT_UTILIZATION_RATE;
        $hourlyWage = $options['hourly_wage'] ?? self::DEFAULT_HOURLY_WAGE;
        $benefitsCost = $options['benefits_cost'] ?? self::DEFAULT_BENEFITS_COST;

        Log::info("=== Starting Complete Workforce Calculation ===", [
            'task_count' => count($tasks),
            'available_employees' => $availableEmployees,
            'budget_enabled' => !is_null($budgetLimit)
        ]);

        // Step (b): Calculate total required hours
        $totalRequiredHours = is_array($tasks) && isset($tasks[0]['duration_hours'])
            ? $this->calculateTotalRequiredHours($tasks)
            : $this->calculateTotalRequiredHoursFromTasks(collect($tasks));

        // Step (c): Calculate minimum workforce
        $minimumWorkforce = $this->calculateMinimumWorkforce(
            $totalRequiredHours,
            $availableHours,
            $utilizationRate
        );

        // Step (d): Calculate maximum affordable (if budget is set)
        $maxAffordable = $budgetLimit
            ? $this->calculateMaxAffordableWorkforce($budgetLimit, $hourlyWage, $availableHours, $benefitsCost)
            : PHP_INT_MAX; // No budget constraint

        // Step (e): Determine final workforce
        $finalWorkforce = $this->calculateFinalWorkforce(
            $minimumWorkforce,
            $availableEmployees,
            $maxAffordable
        );

        $result = [
            'total_required_hours' => round($totalRequiredHours, 2),
            'minimum_workforce' => $minimumWorkforce,
            'maximum_affordable' => $maxAffordable,
            'available_employees' => $availableEmployees,
            'final_workforce' => $finalWorkforce,
            'utilization_rate' => $utilizationRate,
            'available_hours_per_employee' => $availableHours,
            'constraints_met' => [
                'sufficient_for_workload' => $finalWorkforce >= $minimumWorkforce,
                'within_employee_pool' => $finalWorkforce <= $availableEmployees,
                'within_budget' => $finalWorkforce <= $maxAffordable
            ]
        ];

        Log::info("=== Workforce Calculation Complete ===", $result);

        return $result;
    }
}
