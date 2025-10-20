<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'alert_type',
        'delay_minutes',
        'reason',
        'triggered_at',
        'acknowledged_at',
        'acknowledged_by',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'delay_minutes' => 'integer',
    ];

    /**
     * Get the task that triggered this alert
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who acknowledged this alert
     */
    public function acknowledgedByUser()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Alias for acknowledgedByUser (for consistency)
     */
    public function acknowledgedBy()
    {
        return $this->acknowledgedByUser();
    }

    /**
     * Check if alert is acknowledged
     */
    public function isAcknowledged(): bool
    {
        return $this->acknowledged_at !== null;
    }

    /**
     * Acknowledge this alert
     */
    public function acknowledge(int $userId): void
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $userId,
        ]);
    }
}
