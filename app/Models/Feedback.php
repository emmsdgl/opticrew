<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'client_id',
        'service_type',
        'overall_rating',
        'quality_rating',
        'cleanliness_rating',
        'punctuality_rating',
        'professionalism_rating',
        'value_rating',
        'comments',
        'would_recommend',
        // Modal feedback fields
        'task_id',
        'appointment_id',
        'employee_id',
        'user_type',
        'rating',
        'keywords',
        'feedback_text',
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
        'keywords' => 'array',
        'rating' => 'integer',
    ];

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with Task (for employee feedback)
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Relationship with ClientAppointment (for client feedback)
    public function appointment()
    {
        return $this->belongsTo(ClientAppointment::class, 'appointment_id');
    }

    // Relationship with Employee (for employee-submitted feedback)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper method to get average rating for a service type
    public static function averageRatingForService($serviceType)
    {
        return self::where('service_type', $serviceType)
            ->avg('overall_rating') ?? 0;
    }
}
