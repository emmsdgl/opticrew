<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    public function appointments(): HasMany
    {
        // This assumes a standard setup where the 'appointments' table has a 'client_id' foreign key.
        return $this->hasMany(Task::class);
    }

    /**
     * Create a full_name accessor for use in the dashboard header.
     * e.g., $client->full_name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => 
                // Construct the name: First Name [Middle Initial.] Last Name
                trim($attributes['first_name'] . ' ' . 
                     ($attributes['middle_initial'] ? $attributes['middle_initial'] . '.' : '') . ' ' . 
                     $attributes['last_name']),
        );
    }
}