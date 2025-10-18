<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScenarioAnalysis extends Model
{
    protected $fillable = [
        'service_date',
        'scenario_type',
        'parameters',
        'modified_schedule',
        'impact_analysis',
        'recommendations',
    ];

    protected $casts = [
        'service_date' => 'date',
        'parameters' => 'array',
        'modified_schedule' => 'array',
        'impact_analysis' => 'array',
        'recommendations' => 'array',
    ];
}