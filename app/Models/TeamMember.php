<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daily_team_id',
        'employee_id',
    ];

    // DEFINE YOUR RELATIONSHIPS
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}