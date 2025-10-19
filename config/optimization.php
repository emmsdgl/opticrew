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
        'max_hours_per_day' => 8,
        'target_utilization' => 0.85,
        'daily_cost_per_employee' => 100,
        'budget_limit' => env('WORKFORCE_BUDGET_LIMIT', 10000),
    ],

    'constraints' => [
        'work_start_time' => env('WORK_START_TIME', '08:00:00'),
        'work_end_time' => env('WORK_END_TIME', '18:00:00'),
    ],
];