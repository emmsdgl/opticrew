<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Your model might have a $guarded or $fillable property here, which is fine.

    // ADD THIS FUNCTION
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class);
    }
    
    // You might also have a user() relationship here, which is also fine.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}