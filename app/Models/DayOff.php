<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'end_date',
        'reason',
        'type',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Type constants
    const TYPE_VACATION = 'vacation';
    const TYPE_SICK = 'sick';
    const TYPE_PERSONAL = 'personal';
    const TYPE_OTHER = 'other';

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes for filtering
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    // Calculate duration in days
    public function getDurationDaysAttribute()
    {
        if ($this->end_date) {
            return $this->date->diffInDays($this->end_date) + 1;
        }
        return 1;
    }
}