<?php

/*
|--------------------------------------------------------------------------
| Task Rejection Policy
|--------------------------------------------------------------------------
|
| Tunable parameters for the preference-based task-rejection cascade.
| See docs/task-rejection-reassignment-policy.md for the full policy.
|
| Override via .env if you want operations-driven values without code changes.
|
*/

return [

    // How many times an individual employee may reject in a calendar month.
    // Reset is implicit (rolling month boundary based on rejected_at).
    'monthly_budget' => (int) env('REJECTION_MONTHLY_BUDGET', 3),

    // After how many rejections of THE SAME task the cascade stops and the
    // task is escalated to admin for manual handling.
    'per_task_ceiling' => (int) env('REJECTION_PER_TASK_CEILING', 3),

    // Rejections must arrive at least this many hours before the task's scheduled
    // start. Past this, only the existing emergency-leave flow applies.
    'window_hours_before_start' => (int) env('REJECTION_WINDOW_HOURS', 24),

    // Mass-rejection meta-trigger. The cascade pauses and the GA re-runs
    // (with admin approval) when EITHER threshold is met within the window.
    'mass_rejection' => [
        'min_count'        => (int) env('MASS_REJECTION_MIN_COUNT', 3),
        'min_percent'      => (int) env('MASS_REJECTION_MIN_PERCENT', 25),
        'window_hours'     => (int) env('MASS_REJECTION_WINDOW_HOURS', 4),
    ],

    // Allowed reasons for rejection. Employees must pick one of these
    // (or 'other' with free-text note). Translatable later if needed.
    'allowed_reasons' => [
        'location_too_far'       => 'Location too far from where I live',
        'service_type_preference'=> 'Service type preference',
        'schedule_conflict'      => 'Personal schedule conflict',
        'workload_concern'       => 'Workload concern',
        'other'                  => 'Other (please specify)',
    ],

];
