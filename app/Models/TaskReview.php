<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'contracted_client_id',
        'reviewer_user_id',
        'rating',
        'feedback_tags',
        'review_text',
        'metadata',
    ];

    protected $casts = [
        'rating' => 'integer',
        'feedback_tags' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the task being reviewed
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the contracted client who submitted the review
     */
    public function contractedClient()
    {
        return $this->belongsTo(ContractedClient::class);
    }

    /**
     * Get the user who submitted the review
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    /**
     * Scope to get reviews by rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get positive reviews (rating >= 4)
     */
    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope to get negative reviews (rating <= 2)
     */
    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    /**
     * Get average rating for a contracted client
     */
    public static function getAverageRatingForClient($contractedClientId)
    {
        return self::where('contracted_client_id', $contractedClientId)
            ->avg('rating');
    }

    /**
     * Get review statistics for a contracted client
     */
    public static function getStatisticsForClient($contractedClientId)
    {
        $reviews = self::where('contracted_client_id', $contractedClientId)->get();

        return [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('rating'), 1),
            'rating_distribution' => [
                5 => $reviews->where('rating', 5)->count(),
                4 => $reviews->where('rating', 4)->count(),
                3 => $reviews->where('rating', 3)->count(),
                2 => $reviews->where('rating', 2)->count(),
                1 => $reviews->where('rating', 1)->count(),
            ],
        ];
    }
}
