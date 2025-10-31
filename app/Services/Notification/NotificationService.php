<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a new notification for a user.
     *
     * @param int|User $user User ID or User model instance
     * @param string $type Type of notification (e.g., 'appointment_approved', 'task_assigned')
     * @param string $title Short heading for the notification
     * @param string $message Full notification message
     * @param array|null $data Additional data (appointment_id, task_id, links, etc.)
     * @return Notification
     */
    public function create($user, string $type, string $title, string $message, ?array $data = null): Notification
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple users.
     *
     * @param array|Collection $users Array of user IDs or User model instances
     * @param string $type Type of notification
     * @param string $title Short heading for the notification
     * @param string $message Full notification message
     * @param array|null $data Additional data
     * @return Collection
     */
    public function createMany($users, string $type, string $title, string $message, ?array $data = null): Collection
    {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push($this->create($user, $type, $title, $message, $data));
        }

        return $notifications;
    }

    /**
     * Get all unread notifications for a user.
     *
     * @param int|User $user User ID or User model instance
     * @return Collection
     */
    public function getUnread($user): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread notification count for a user.
     *
     * @param int|User $user User ID or User model instance
     * @return int
     */
    public function getUnreadCount($user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Mark a notification as read.
     *
     * @param int|Notification $notification Notification ID or Notification model instance
     * @return bool
     */
    public function markAsRead($notification): bool
    {
        if (is_int($notification)) {
            $notification = Notification::find($notification);
        }

        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Mark multiple notifications as read.
     *
     * @param array|Collection $notificationIds Array of notification IDs
     * @return int Number of notifications marked as read
     */
    public function markManyAsRead($notificationIds): int
    {
        return Notification::whereIn('id', $notificationIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int|User $user User ID or User model instance
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead($user): int
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Delete a notification.
     *
     * @param int|Notification $notification Notification ID or Notification model instance
     * @return bool
     */
    public function delete($notification): bool
    {
        if (is_int($notification)) {
            $notification = Notification::find($notification);
        }

        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    /**
     * Delete multiple notifications.
     *
     * @param array|Collection $notificationIds Array of notification IDs
     * @return int Number of notifications deleted
     */
    public function deleteMany($notificationIds): int
    {
        return Notification::whereIn('id', $notificationIds)->delete();
    }

    /**
     * Get all notifications for a user (read and unread).
     *
     * @param int|User $user User ID or User model instance
     * @param int $limit Limit the number of results
     * @return Collection
     */
    public function getAll($user, int $limit = 50): Collection
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Create notification for appointment approval.
     *
     * @param User $user
     * @param int $appointmentId
     * @return Notification
     */
    public function notifyAppointmentApproved(User $user, int $appointmentId): Notification
    {
        return $this->create(
            $user,
            'appointment_approved',
            'Appointment Approved',
            'Your appointment has been approved and scheduled.',
            ['appointment_id' => $appointmentId]
        );
    }

    /**
     * Create notification for task assignment.
     *
     * @param User $user
     * @param int $taskId
     * @param string $taskDescription
     * @return Notification
     */
    public function notifyTaskAssigned(User $user, int $taskId, string $taskDescription): Notification
    {
        return $this->create(
            $user,
            'task_assigned',
            'New Task Assigned',
            "You have been assigned a new task: {$taskDescription}",
            ['task_id' => $taskId]
        );
    }

    /**
     * Create notification for schedule update.
     *
     * @param User $user
     * @param string $date
     * @return Notification
     */
    public function notifyScheduleUpdated(User $user, string $date): Notification
    {
        return $this->create(
            $user,
            'schedule_updated',
            'Schedule Updated',
            "Your schedule for {$date} has been updated.",
            ['date' => $date]
        );
    }
}
