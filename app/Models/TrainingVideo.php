<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'title',
        'title_fi',
        'description',
        'description_fi',
        'video_id',
        'platform',
        'duration',
        'required',
        'thumbnail_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Video categories
     */
    const CATEGORY_CLEANING_TECHNIQUES = 'cleaning_techniques';
    const CATEGORY_BODY_SAFETY = 'body_safety';
    const CATEGORY_HAZARD_PREVENTION = 'hazard_prevention';
    const CATEGORY_CHEMICAL_SAFETY = 'chemical_safety';

    /**
     * Get all available categories with their info
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_CLEANING_TECHNIQUES => [
                'title' => 'Cleaning Techniques',
                'titleFi' => 'Puhdistustekniikat',
                'color' => '#2563eb',
                'icon' => 'cleaning',
            ],
            self::CATEGORY_BODY_SAFETY => [
                'title' => 'Body Safety',
                'titleFi' => 'Kehon turvallisuus',
                'color' => '#22c55e',
                'icon' => 'safety',
            ],
            self::CATEGORY_HAZARD_PREVENTION => [
                'title' => 'Hazard Prevention',
                'titleFi' => 'Vaarojen ehkäisy',
                'color' => '#f59e0b',
                'icon' => 'hazard',
            ],
            self::CATEGORY_CHEMICAL_SAFETY => [
                'title' => 'Chemical Safety',
                'titleFi' => 'Kemikaaliturvallisuus',
                'color' => '#ef4444',
                'icon' => 'chemical',
            ],
        ];
    }

    /**
     * Scope to get only active videos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get employees who watched this video
     */
    public function watchedByEmployees()
    {
        return $this->belongsToMany(User::class, 'employee_watched_videos')
            ->withPivot('watched_at')
            ->withTimestamps();
    }

    /**
     * Check if video was watched by a specific user
     */
    public function isWatchedBy($userId): bool
    {
        return $this->watchedByEmployees()->where('user_id', $userId)->exists();
    }

    /**
     * Generate thumbnail URL from video ID
     */
    public function getThumbnailAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if ($this->platform === 'youtube' && $this->video_id) {
            return "https://img.youtube.com/vi/{$this->video_id}/hqdefault.jpg";
        }

        return null;
    }
}
