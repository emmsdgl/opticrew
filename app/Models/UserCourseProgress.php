<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
    use HasFactory;

    protected $table = 'user_course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
