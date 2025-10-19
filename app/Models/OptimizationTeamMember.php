<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizationTeamMember extends Model
{
    protected $fillable = [
        'optimization_team_id',
        'employee_id',
    ];

    public function team()
    {
        return $this->belongsTo(OptimizationTeam::class, 'optimization_team_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}