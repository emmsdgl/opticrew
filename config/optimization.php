<?php

return [
    'genetic_algorithm' => [
        'population_size' => env('GA_POPULATION_SIZE', 20),
        'max_generations' => env('GA_MAX_GENERATIONS', 100),
        'mutation_rate' => env('GA_MUTATION_RATE', 0.1),
        'tournament_size' => env('GA_TOURNAMENT_SIZE', 5),
        'patience' => env('GA_PATIENCE', 15),
    ],

    'workforce' => [
        // RULE 7: Maximum work hours per team per day (12-hour limit)
        'max_hours_per_day' => 12,

        // 5-Step Methodology Parameters
        'available_hours' => env('WORKFORCE_AVAILABLE_HOURS', 8.0), // H_avail: Standard work hours per employee
        'target_utilization' => env('WORKFORCE_UTILIZATION_RATE', 0.85), // R: Target utilization (85%)
        'hourly_wage' => env('WORKFORCE_HOURLY_WAGE', 15.0), // W: Average hourly wage (EUR/hour)
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
];