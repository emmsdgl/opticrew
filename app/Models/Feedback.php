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
    ];

    protected $casts = [
        'would_recommend' => 'boolean',
    ];

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Helper method to get average rating for a service type
    public static function averageRatingForService($serviceType)
    {
        return self::where('service_type', $serviceType)
            ->avg('overall_rating') ?? 0;
    }
}
