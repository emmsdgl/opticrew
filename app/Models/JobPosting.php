<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'salary',
        'type',
        'type_badge',
        'icon',
        'icon_color',
        'is_active',
        'status',
        'required_skills',
        'required_docs',
        'benefits',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'required_skills' => 'array',
        'required_docs' => 'array',
        'benefits' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'published');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function getTypeBadgeAttribute($value)
    {
        if ($value) return $value;

        return match($this->type) {
            'full-time' => 'Full-time Employee',
            'part-time' => 'Part-time Employee',
            'remote' => 'Remote',
            default => 'Full-time Employee',
        };
    }
}
