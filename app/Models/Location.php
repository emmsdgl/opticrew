<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_name',
        'contracted_client_id',
        'location_type',
        'base_cleaning_duration_minutes',
        'normal_rate_per_hour',
        'sunday_holiday_rate',
        'deep_cleaning_rate',
        'light_deep_cleaning_rate',
        'student_rate',
        'student_sunday_holiday_rate'
    ];

    /**
     * Get the contracted client that owns this location
     */
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class, 'contracted_client_id');
    }

    /**
     * Get all tasks for this location
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'location_id');
    }
}