<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'booking_type',
        'service_type',
        'service_date',
        'service_time',
        'is_sunday',
        'is_holiday',
        'number_of_units',
        'unit_size',
        'cabin_name',
        'special_requests',
        'quotation',
        'vat_amount',
        'total_amount',
        'status',
        'assigned_team_id',
        'recommended_team_id',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'client_notified',
        'notified_at',
    ];

    protected $casts = [
        'service_date' => 'date',
        'service_time' => 'datetime',
        'is_sunday' => 'boolean',
        'is_holiday' => 'boolean',
        'quotation' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'client_notified' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'notified_at' => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTeam()
    {
        return $this->belongsTo(OptimizationTeam::class, 'assigned_team_id');
    }

    public function recommendedTeam()
    {
        return $this->belongsTo(OptimizationTeam::class, 'recommended_team_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
