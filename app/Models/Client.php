<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    // Define which fields can be mass-assigned
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'middle_initial',
        'birthdate',
        'phone_number',
        'security_question_1',
        'security_answer_1',
        'security_question_2',
        'security_answer_2',
    ];

    // Define the inverse relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}