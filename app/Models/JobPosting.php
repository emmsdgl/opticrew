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
        'required_skills',
        'required_docs',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'required_skills' => 'array',
        'required_docs' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
