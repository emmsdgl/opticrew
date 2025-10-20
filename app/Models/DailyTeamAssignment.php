<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated This model is deprecated and will be removed in a future version.
 * Use App\Models\OptimizationTeam and App\Models\OptimizationTeamMember instead.
 *
 * The new optimization system (based on the pseudocode) uses:
 * - OptimizationTeam: Represents a team created by the optimization algorithm
 * - OptimizationTeamMember: Represents employees in that team
 *
 * This model is kept temporarily for backward compatibility with old task assignments.
 * All new task assignments should use the OptimizationTeam structure.
 */
class DailyTeamAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_date',
        'contracted_client_id',
        'car_id',
    ];
    
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'daily_team_id'); // ✅ Add this parameter
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_team_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}