<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Feedback;

class ClientAppointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'is_company_inquiry',
        'booking_type',
        'service_type',
        'company_service_types',
        'service_date',
        'service_time',
        'is_sunday',
        'is_holiday',
        'number_of_units',
        'unit_size',
        'cabin_name',
        'unit_details',
        'special_requests',
        'other_concerns',
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
        'is_company_inquiry' => 'boolean',
        'company_service_types' => 'array',
        'unit_details' => 'array',
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

    public function scopeCompanyInquiry($query)
    {
        return $query->where('is_company_inquiry', true);
    }

    public function scopePersonalBooking($query)
    {
        return $query->where('is_company_inquiry', false);
    }

    /**
     * Get the related task for this appointment
     * Matches based on client_id, service_date, and service_type
     */
    public function task()
    {
        return $this->hasOne(Task::class, 'client_id', 'client_id')
            ->where('scheduled_date', $this->service_date)
            ->where('task_description', 'like', '%' . $this->service_type . '%');
    }

    /**
     * Get the related task with checklist completions
     */
    public function getRelatedTaskAttribute()
    {
        return Task::with('checklistCompletions')
            ->where('client_id', $this->client_id)
            ->whereDate('scheduled_date', $this->service_date)
            ->where('task_description', 'like', '%' . $this->service_type . '%')
            ->first();
    }

    /**
     * Get the client feedback for this appointment
     */
    public function clientFeedback()
    {
        return $this->hasOne(Feedback::class, 'appointment_id');
    }
}
