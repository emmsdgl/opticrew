<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Genetic Algorithm Configuration
    |--------------------------------------------------------------------------
    |
    | Parameters aligned with simulation model for consistency while
    | maintaining production performance requirements.
    |
    | Simulation model uses: population=100, generations=150, mutation=20%
    | Production uses optimized values balancing quality vs speed:
    |   - Increased population from 20 to 50 for better exploration
    |   - Increased mutation from 10% to 20% (matches simulation)
    |   - Reduced tournament from 5 to 3 (matches simulation, less selection pressure)
    |
    */
    'genetic_algorithm' => [
        'population_size' => env('GA_POPULATION_SIZE', 50), // Increased from 20 (sim: 100)
        'max_generations' => env('GA_MAX_GENERATIONS', 100), // Kept for performance (sim: 150)
        'mutation_rate' => env('GA_MUTATION_RATE', 0.20), // Matches simulation (was 0.1)
        'tournament_size' => env('GA_TOURNAMENT_SIZE', 3), // Matches simulation (was 5)
        'patience' => env('GA_PATIENCE', 15), // Kept for performance (sim: 25)
    ],

    'workforce' => [
        // RULE 7: Maximum work hours per team per day (12-hour limit)
        'max_hours_per_day' => 12,

        // 5-Step Methodology Parameters
        'available_hours' => env('WORKFORCE_AVAILABLE_HOURS', 8.0), // H_avail: Standard work hours per employee
        'target_utilization' => env('WORKFORCE_UTILIZATION_RATE', 0.85), // R: Target utilization (85%)
        'hourly_wage' => env('WORKFORCE_HOURLY_WAGE', 13.0), // W: Average hourly wage (EUR/hour)
        'benefits_cost' => env('WORKFORCE_BENEFITS_COST', 5.0), // B: Additional costs per employee per day
        'cleaning_speed' => env('WORKFORCE_CLEANING_SPEED', 10.0), // Si: Default cleaning speed (m²/hour)
        'default_travel_time' => env('WORKFORCE_TRAVEL_TIME', 0.5), // Li: Default travel time per task (hours)

        // Budget Constraints
        'daily_cost_per_employee' => 100, // Deprecated: Use hourly_wage + benefits_cost instead
        'budget_limit' => env('WORKFORCE_BUDGET_LIMIT', null), // C_limit: Maximum budget (null = no limit)
    ],

    'constraints' => [
        'work_start_time' => env('WORK_START_TIME', '08:00:00'),
        'work_end_time' => env('WORK_END_TIME', '18:00:00'),
    ],

    'alerts' => [
        // Alert threshold for tasks on hold (minutes)
        'on_hold_threshold_minutes' => env('ALERT_ON_HOLD_THRESHOLD', 30),

        // Alert threshold for duration exceeded (percentage)
        'duration_exceeded_threshold_percent' => env('ALERT_DURATION_THRESHOLD', 20),
    ],

    'pricing' => [
        // Maximum price for extra tasks (EUR)
        'max_extra_task_price' => env('MAX_EXTRA_TASK_PRICE', 10000),
    ],

    'penalties' => [
        // Fitness penalties for constraint violations
        'deadline_violation' => 50000,  // 3PM deadline violation
        'hour_limit_violation' => 100000, // 12-hour daily limit violation
        'unassigned_task' => 10000,      // Unassigned task penalty
    ],
];