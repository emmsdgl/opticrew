<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active tokens
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for user tokens
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
