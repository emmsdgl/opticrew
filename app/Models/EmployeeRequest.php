<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'absence_type',
        'absence_date',
        'time_range',
        'from_time',
        'to_time',
        'reason',
        'description',
        'proof_document',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'absence_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
