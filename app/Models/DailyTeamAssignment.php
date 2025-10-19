<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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