<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    // Notification types
    const TYPE_LEAVE_APPROVED = 'leave_approved';
    const TYPE_LEAVE_REJECTED = 'leave_rejected';
    const TYPE_LEAVE_SUBMITTED = 'leave_submitted';
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_TASK_UPDATED = 'task_updated';
    const TYPE_GENERAL = 'general';

    // Client notification types
    const TYPE_APPOINTMENT_APPROVED = 'appointment_approved';
    const TYPE_APPOINTMENT_CONFIRMED = 'appointment_confirmed';
    const TYPE_APPOINTMENT_REJECTED = 'appointment_rejected';
    const TYPE_TASK_STARTED = 'task_started';
    const TYPE_TASK_COMPLETED = 'task_completed';
    const TYPE_CHECKLIST_PROGRESS = 'checklist_progress';
    const TYPE_SERVICE_UPDATE = 'service_update';
    const TYPE_FEEDBACK_SUBMITTED = 'feedback_submitted';

    // Employee notification types
    const TYPE_EMPLOYEE_LEAVE_APPROVED = 'employee_leave_approved';
    const TYPE_EMPLOYEE_LEAVE_REJECTED = 'employee_leave_rejected';
    const TYPE_EMPLOYEE_REQUEST_APPROVED = 'employee_request_approved';
    const TYPE_EMPLOYEE_REQUEST_REJECTED = 'employee_request_rejected';
    const TYPE_EMPLOYEE_CLOCK_IN_REMINDER = 'employee_clock_in_reminder';
    const TYPE_EMPLOYEE_FEEDBACK_SUBMITTED = 'employee_feedback_submitted';

    // Admin notification types
    const TYPE_NEW_APPOINTMENT = 'new_appointment';
    const TYPE_APPOINTMENT_CANCELLED = 'appointment_cancelled';
    const TYPE_LEAVE_REQUEST = 'leave_request';
    const TYPE_TASK_COMPLETED_ADMIN = 'task_completed_admin';
    const TYPE_TASK_APPROVED = 'task_approved';
    const TYPE_TASK_DECLINED = 'task_declined';
    const TYPE_TASK_STARTED_ADMIN = 'task_started_admin';
    const TYPE_TASK_PROGRESS_ADMIN = 'task_progress_admin';
    const TYPE_JOB_APPLICATION_SUBMITTED = 'job_application_submitted';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to filter by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Determine if a notification has not been read.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }
}
