<?php

namespace App\Services\Alert;

use App\Models\Alert;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * AlertService
 *
 * Handles admin notifications for:
 * - Task delays (> 30 minutes on hold)
 * - Duration exceeded
 * - Other critical events
 *
 * Based on pseudocode TRIGGER_ALERT_TO_ADMIN function
 */
class AlertService
{
    /**
     * Trigger alert to admin for a delayed task
     *
     * @param Task $task
     * @param array $alertData
     * @return Alert
     */
    public function triggerTaskDelayedAlert(Task $task, array $alertData): Alert
    {
        Log::info("Triggering task delayed alert", [
            'task_id' => $task->id,
            'delay_minutes' => $alertData['delay_minutes'] ?? null,
            'reason' => $alertData['reason'] ?? null
        ]);

        // Create alert record
        $alert = Alert::create([
            'task_id' => $task->id,
            'alert_type' => 'task_delayed',
            'delay_minutes' => $alertData['delay_minutes'] ?? null,
            'reason' => $alertData['reason'] ?? null,
            'triggered_at' => now(),
        ]);

        // Get all admin users
        $admins = User::where('role', 'admin')
            ->get();

        if ($admins->isEmpty()) {
            Log::warning("No active admins found to send alert");
            return $alert;
        }

        // Send notifications to all admins
        foreach ($admins as $admin) {
            $this->sendAdminNotification($admin, $alert, $task, $alertData);
        }

        Log::info("Alert sent to " . $admins->count() . " admin(s)", [
            'alert_id' => $alert->id
        ]);

        return $alert;
    }

    /**
     * Send notification to a specific admin
     *
     * @param User $admin
     * @param Alert $alert
     * @param Task $task
     * @param array $alertData
     * @return void
     */
    protected function sendAdminNotification(User $admin, Alert $alert, Task $task, array $alertData): void
    {
        $taskDescription = $task->task_description ?? 'Unknown Task';
        $location = $task->location->location_name ?? 'Unknown Location';
        $delayMinutes = $alertData['delay_minutes'] ?? 0;
        $reason = $alertData['reason'] ?? 'Unknown';

        // Email notification placeholder
        Log::info("Email notification would be sent to: " . $admin->email, [
            'subject' => "🚨 Task Delayed: " . $taskDescription,
            'task_id' => $task->id
        ]);

        // Push notification placeholder
        if (isset($admin->push_token) && $admin->push_token) {
            Log::info("Push notification would be sent to admin", [
                'admin_id' => $admin->id,
                'task_id' => $task->id
            ]);
        }
    }

    /**
     * Acknowledge an alert
     *
     * @param int $alertId
     * @param int $userId
     * @return bool
     */
    public function acknowledgeAlert(int $alertId, int $userId): bool
    {
        $alert = Alert::find($alertId);

        if (!$alert) {
            Log::warning("Alert not found", ['alert_id' => $alertId]);
            return false;
        }

        $alert->acknowledge($userId);

        Log::info("Alert acknowledged", [
            'alert_id' => $alertId,
            'acknowledged_by' => $userId
        ]);

        return true;
    }

    /**
     * Get unacknowledged alerts
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnacknowledgedAlerts()
    {
        return Alert::whereNull('acknowledged_at')
            ->with('task.location')
            ->orderBy('triggered_at', 'desc')
            ->get();
    }

    /**
     * Get alerts for a specific date
     *
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAlertsForDate(string $date)
    {
        return Alert::whereHas('task', function($query) use ($date) {
                $query->whereDate('scheduled_date', $date);
            })
            ->with('task.location', 'acknowledgedByUser')
            ->orderBy('triggered_at', 'desc')
            ->get();
    }
}
