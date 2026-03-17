<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_title',
        'job_type',
        'email',
        'alternative_email',
        'resume_path',
        'resume_original_name',
        'applicant_profile',
        'status',
        'admin_notes',
        'reviewed_at',
        'interview_date',
        'interview_duration',
        'status_history',
        'withdraw_reason',
        'withdraw_details',
        'documents',
    ];

    protected $casts = [
        'reviewed_at'    => 'datetime',
        'interview_date' => 'datetime',
        'status_history' => 'array',
        'documents'      => 'array',
    ];

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'reviewed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'interview_scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'hired' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'withdrawn' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'reviewed' => 'Reviewed',
            'interview_scheduled' => 'Interview Scheduled',
            'hired' => 'Hired',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => ucfirst($this->status),
        };
    }
}
