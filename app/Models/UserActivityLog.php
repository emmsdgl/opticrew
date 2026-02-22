<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_PROFILE_UPDATED = 'profile_updated';

    protected $fillable = [
        'user_id',
        'type',
        'description',
        'data',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($userId, $type, $description, $data = null, $ipAddress = null)
    {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'description' => $description,
            'data' => $data,
            'ip_address' => $ipAddress,
        ]);
    }
}
