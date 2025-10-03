<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTeamAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assignment_date',
        'contracted_client_id',
        'car_id',
    ];

    // DEFINE YOUR RELATIONSHIPS
    public function members()
    {
        return $this->hasMany(TeamMember::class, 'daily_team_id');
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