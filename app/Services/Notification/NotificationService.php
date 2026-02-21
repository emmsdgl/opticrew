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

    // ============================================
    // Employee Notification Methods
    // ============================================

    /**
     * Notify employee when they are assigned to a task.
     */
    public function notifyEmployeeTaskAssigned(User $employeeUser, $task, $appointment = null): ?Notification
    {
        if (!$employeeUser) return null;

        $serviceDate = $task->scheduled_date ? $task->scheduled_date->format('M d, Y') : 'TBD';
        $serviceTime = $task->scheduled_time ? \Carbon\Carbon::parse($task->scheduled_time)->format('g:i A') : '';
        $timeInfo = $serviceTime ? " at {$serviceTime}" : '';

        $clientName = 'a client';
        if ($appointment && $appointment->client) {
            $clientName = trim(($appointment->client->first_name ?? '') . ' ' . ($appointment->client->last_name ?? ''));
        } elseif ($task->client) {
            $clientName = trim(($task->client->first_name ?? '') . ' ' . ($task->client->last_name ?? ''));
        }

        return $this->create(
            $employeeUser,
            Notification::TYPE_TASK_ASSIGNED,
            'New Task Assignment',
            "You have been assigned to {$task->task_description} for {$clientName} on {$serviceDate}{$timeInfo}.",
            [
                'task_id' => $task->id,
                'appointment_id' => $appointment ? $appointment->id : null,
                'task_description' => $task->task_description,
                'service_date' => $serviceDate,
                'service_time' => $serviceTime,
                'client_name' => $clientName,
                'icon' => 'clipboard-list',
                'color' => 'blue',
                'action_url' => '/employee/tasks',
                'action_text' => 'View Task'
            ]
        );
    }

    /**
     * Notify employee when their leave request is approved.
     */
    public function notifyEmployeeLeaveApproved(User $employeeUser, $leaveRequest): ?Notification
    {
        if (!$employeeUser) return null;

        $startDate = $leaveRequest->date->format('M d, Y');
        $endDate = $leaveRequest->end_date ? $leaveRequest->end_date->format('M d, Y') : $startDate;
        $dateRange = $startDate === $endDate ? $startDate : "{$startDate} - {$endDate}";

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_LEAVE_APPROVED,
            'Leave Request Approved',
            "Your {$leaveRequest->type} leave request for {$dateRange} has been approved.",
            [
                'leave_request_id' => $leaveRequest->id,
                'leave_type' => $leaveRequest->type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'icon' => 'check-circle',
                'color' => 'green',
                'action_url' => '/employee/requests',
                'action_text' => 'View Requests'
            ]
        );
    }

    /**
     * Notify employee when their leave request is rejected.
     */
    public function notifyEmployeeLeaveRejected(User $employeeUser, $leaveRequest, string $reason = ''): ?Notification
    {
        if (!$employeeUser) return null;

        $startDate = $leaveRequest->date->format('M d, Y');
        $endDate = $leaveRequest->end_date ? $leaveRequest->end_date->format('M d, Y') : $startDate;
        $dateRange = $startDate === $endDate ? $startDate : "{$startDate} - {$endDate}";

        $message = "Your {$leaveRequest->type} leave request for {$dateRange} was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_LEAVE_REJECTED,
            'Leave Request Not Approved',
            $message,
            [
                'leave_request_id' => $leaveRequest->id,
                'leave_type' => $leaveRequest->type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
                'icon' => 'times-circle',
                'color' => 'red',
                'action_url' => '/employee/requests',
                'action_text' => 'View Requests'
            ]
        );
    }

    /**
     * Notify employee when their general request is approved (e.g., equipment, supplies, etc.).
     */
    public function notifyEmployeeRequestApproved(User $employeeUser, $request, string $requestType = 'request'): ?Notification
    {
        if (!$employeeUser) return null;

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_REQUEST_APPROVED,
            'Request Approved',
            "Your {$requestType} has been approved.",
            [
                'request_id' => $request->id ?? null,
                'request_type' => $requestType,
                'icon' => 'check-circle',
                'color' => 'green',
                'action_url' => '/employee/requests',
                'action_text' => 'View Requests'
            ]
        );
    }

    /**
     * Notify employee when their general request is rejected.
     */
    public function notifyEmployeeRequestRejected(User $employeeUser, $request, string $requestType = 'request', string $reason = ''): ?Notification
    {
        if (!$employeeUser) return null;

        $message = "Your {$requestType} was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_REQUEST_REJECTED,
            'Request Not Approved',
            $message,
            [
                'request_id' => $request->id ?? null,
                'request_type' => $requestType,
                'reason' => $reason,
                'icon' => 'times-circle',
                'color' => 'red',
                'action_url' => '/employee/requests',
                'action_text' => 'View Requests'
            ]
        );
    }

    /**
     * Notify employee with a daily clock in reminder.
     */
    public function notifyEmployeeClockInReminder(User $employeeUser, string $date = null): ?Notification
    {
        if (!$employeeUser) return null;

        $date = $date ?? now()->format('M d, Y');

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_CLOCK_IN_REMINDER,
            'Clock In Reminder',
            "Don't forget to clock in for your shift today ({$date}).",
            [
                'date' => $date,
                'icon' => 'clock',
                'color' => 'yellow',
                'action_url' => '/employee/attendance',
                'action_text' => 'Clock In'
            ]
        );
    }

    /**
     * Send clock in reminders to all employees who haven't clocked in today.
     */
    public function sendDailyClockInReminders(): Collection
    {
        $today = now()->format('Y-m-d');
        $notifications = collect();

        // Get all employees who are active and haven't clocked in today
        $employees = \App\Models\Employee::where('status', 'active')
            ->whereHas('user')
            ->with('user')
            ->get();

        foreach ($employees as $employee) {
            // Check if employee has already clocked in today
            $hasClockedIn = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereDate('clock_in', $today)
                ->exists();

            // Check if already sent a reminder today
            $alreadyReminded = Notification::where('user_id', $employee->user_id)
                ->where('type', Notification::TYPE_EMPLOYEE_CLOCK_IN_REMINDER)
                ->whereDate('created_at', $today)
                ->exists();

            if (!$hasClockedIn && !$alreadyReminded && $employee->user) {
                $notification = $this->notifyEmployeeClockInReminder($employee->user);
                if ($notification) {
                    $notifications->push($notification);
                }
            }
        }

        return $notifications;
    }

    /**
     * Notify employee when they successfully submit feedback for a task.
     */
    public function notifyEmployeeFeedbackSubmitted(User $employeeUser, $feedback, $task): ?Notification
    {
        if (!$employeeUser) return null;

        $taskDescription = $task->task_description ?? 'task';
        $location = $task->location->location_name ?? '';
        $taskInfo = $location ? "{$taskDescription} - {$location}" : $taskDescription;

        return $this->create(
            $employeeUser,
            Notification::TYPE_EMPLOYEE_FEEDBACK_SUBMITTED,
            'Feedback Submitted',
            "Thank you! Your {$feedback->rating}-star feedback for {$taskInfo} has been recorded.",
            [
                'feedback_id' => $feedback->id,
                'task_id' => $task->id,
                'task_description' => $taskDescription,
                'location' => $location,
                'rating' => $feedback->rating,
                'icon' => 'star',
                'color' => 'yellow',
                'action_url' => '/employee/history?tab=ratings',
                'action_text' => 'View Feedback'
            ]
        );
    }

    // ============================================
    // Client Notification Methods
    // ============================================

    /**
     * Notify client when their appointment is approved by admin.
     */
    public function notifyClientAppointmentApproved(User $clientUser, $appointment): ?Notification
    {
        if (!$clientUser) return null;

        $serviceDate = $appointment->service_date->format('M d, Y');

        return $this->create(
            $clientUser,
            Notification::TYPE_APPOINTMENT_APPROVED,
            'Appointment Approved',
            "Your {$appointment->service_type} appointment for {$serviceDate} has been approved. A team will be assigned shortly.",
            [
                'appointment_id' => $appointment->id,
                'service_type' => $appointment->service_type,
                'service_date' => $serviceDate,
                'icon' => 'check-circle',
                'color' => 'green'
            ]
        );
    }

    /**
     * Notify client when team is assigned and appointment is confirmed.
     */
    public function notifyClientAppointmentConfirmed(User $clientUser, $appointment, $teamMembers = []): ?Notification
    {
        if (!$clientUser) return null;

        $serviceDate = $appointment->service_date->format('M d, Y');
        $memberNames = collect($teamMembers)->pluck('name')->implode(', ') ?: 'Our professional team';

        return $this->create(
            $clientUser,
            Notification::TYPE_APPOINTMENT_CONFIRMED,
            'Team Assigned - Appointment Confirmed',
            "Your {$appointment->service_type} on {$serviceDate} is confirmed. {$memberNames} will be handling your service.",
            [
                'appointment_id' => $appointment->id,
                'service_type' => $appointment->service_type,
                'service_date' => $serviceDate,
                'team_members' => $teamMembers,
                'icon' => 'users',
                'color' => 'blue'
            ]
        );
    }

    /**
     * Notify client when an employee starts working on their task.
     */
    public function notifyClientTaskStarted(User $clientUser, $task, string $employeeName): ?Notification
    {
        if (!$clientUser) return null;

        return $this->create(
            $clientUser,
            Notification::TYPE_TASK_STARTED,
            'Service In Progress',
            "{$employeeName} has started working on your {$task->task_description}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'started_at' => now()->format('g:i A'),
                'icon' => 'play-circle',
                'color' => 'blue'
            ]
        );
    }

    /**
     * Notify client when their task/service is completed.
     */
    public function notifyClientTaskCompleted(User $clientUser, $task, string $employeeName): ?Notification
    {
        if (!$clientUser) return null;

        return $this->create(
            $clientUser,
            Notification::TYPE_TASK_COMPLETED,
            'Service Completed',
            "Great news! Your {$task->task_description} has been completed by {$employeeName}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'completed_at' => now()->format('g:i A'),
                'icon' => 'check-circle',
                'color' => 'green'
            ]
        );
    }

    /**
     * Notify client about checklist progress (batch updates).
     */
    public function notifyClientChecklistProgress(User $clientUser, $task, int $completedItems, int $totalItems, string $latestItem): ?Notification
    {
        if (!$clientUser) return null;

        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        return $this->create(
            $clientUser,
            Notification::TYPE_CHECKLIST_PROGRESS,
            'Service Progress Update',
            "Your service is {$percentage}% complete. Latest: {$latestItem}",
            [
                'task_id' => $task->id,
                'completed_items' => $completedItems,
                'total_items' => $totalItems,
                'percentage' => $percentage,
                'latest_item' => $latestItem,
                'icon' => 'clipboard-check',
                'color' => 'yellow'
            ]
        );
    }

    /**
     * Notify client when they submit feedback for a completed service.
     */
    public function notifyClientFeedbackSubmitted(User $clientUser, $feedback, $appointment): ?Notification
    {
        if (!$clientUser) return null;

        $serviceType = $appointment->service_type ?? 'service';
        $location = $appointment->cabin_name ?? '';
        $serviceInfo = $location ? "{$serviceType} - {$location}" : $serviceType;

        return $this->create(
            $clientUser,
            Notification::TYPE_FEEDBACK_SUBMITTED,
            'Feedback Submitted',
            "Thank you! Your {$feedback->rating}-star feedback for {$serviceInfo} has been recorded.",
            [
                'feedback_id' => $feedback->id,
                'appointment_id' => $appointment->id,
                'service_type' => $serviceType,
                'location' => $location,
                'rating' => $feedback->rating,
                'icon' => 'star',
                'color' => 'yellow',
                'action_url' => '/client/history?tab=ratings',
                'action_text' => 'View Feedback'
            ]
        );
    }

    /**
     * Notify client about appointment rejection.
     */
    public function notifyClientAppointmentRejected(User $clientUser, $appointment, string $reason = ''): ?Notification
    {
        if (!$clientUser) return null;

        $serviceDate = $appointment->service_date->format('M d, Y');
        $message = "Your {$appointment->service_type} appointment for {$serviceDate} was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return $this->create(
            $clientUser,
            Notification::TYPE_APPOINTMENT_REJECTED,
            'Appointment Not Approved',
            $message,
            [
                'appointment_id' => $appointment->id,
                'service_type' => $appointment->service_type,
                'service_date' => $serviceDate,
                'reason' => $reason,
                'icon' => 'times-circle',
                'color' => 'red'
            ]
        );
    }

    /**
     * Get formatted notifications for header display.
     */
    public function getHeaderNotifications($user, int $limit = 10): array
    {
        if (!$user) return [];

        $notifications = $this->getAll($user, $limit);

        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'time' => $notification->created_at->diffForHumans(),
                'read' => $notification->isRead(),
                'type' => $notification->type,
                'data' => $notification->data,
            ];
        })->toArray();
    }

    // ============================================
    // Admin Notification Methods
    // ============================================

    /**
     * Notify all admins when a new appointment is created by a client.
     */
    public function notifyAdminsNewAppointment($appointment, $clientName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $serviceDate = $appointment->service_date->format('M d, Y');

        return $this->createMany(
            $admins,
            Notification::TYPE_NEW_APPOINTMENT,
            'New Appointment Request',
            "{$clientName} has requested a {$appointment->service_type} appointment for {$serviceDate}.",
            [
                'appointment_id' => $appointment->id,
                'client_name' => $clientName,
                'service_type' => $appointment->service_type,
                'service_date' => $serviceDate,
                'icon' => 'calendar-plus',
                'color' => 'blue'
            ]
        );
    }

    /**
     * Notify all admins when an appointment is cancelled by a client.
     */
    public function notifyAdminsAppointmentCancelled($appointment, $clientName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $serviceDate = $appointment->service_date->format('M d, Y');

        return $this->createMany(
            $admins,
            Notification::TYPE_APPOINTMENT_CANCELLED,
            'Appointment Cancelled',
            "{$clientName} has cancelled their {$appointment->service_type} appointment for {$serviceDate}.",
            [
                'appointment_id' => $appointment->id,
                'client_name' => $clientName,
                'service_type' => $appointment->service_type,
                'service_date' => $serviceDate,
                'icon' => 'calendar-times',
                'color' => 'red'
            ]
        );
    }

    /**
     * Notify all admins when an employee submits a leave request.
     */
    public function notifyAdminsLeaveRequest($leaveRequest, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $startDate = $leaveRequest->date->format('M d, Y');
        $endDate = $leaveRequest->end_date ? $leaveRequest->end_date->format('M d, Y') : $startDate;
        $dateRange = $startDate === $endDate ? $startDate : "{$startDate} - {$endDate}";

        return $this->createMany(
            $admins,
            Notification::TYPE_LEAVE_REQUEST,
            'New Leave Request',
            "{$employeeName} has submitted a {$leaveRequest->type} leave request for {$dateRange}.",
            [
                'leave_request_id' => $leaveRequest->id,
                'employee_name' => $employeeName,
                'leave_type' => $leaveRequest->type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'icon' => 'user-clock',
                'color' => 'yellow'
            ]
        );
    }

    /**
     * Notify all admins when a task is completed by an employee.
     */
    public function notifyAdminsTaskCompleted($task, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();

        return $this->createMany(
            $admins,
            Notification::TYPE_TASK_COMPLETED_ADMIN,
            'Task Completed',
            "{$employeeName} has completed the task: {$task->task_description}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'task_description' => $task->task_description,
                'completed_at' => now()->format('g:i A'),
                'icon' => 'check-circle',
                'color' => 'green'
            ]
        );
    }

    /**
     * Notify all admins when an employee approves a task assignment.
     */
    public function notifyAdminsTaskApproved($task, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $serviceDate = $task->scheduled_date ? $task->scheduled_date->format('M d, Y') : 'N/A';

        return $this->createMany(
            $admins,
            Notification::TYPE_TASK_APPROVED,
            'Task Approved by Employee',
            "{$employeeName} has approved the task assignment: {$task->task_description} for {$serviceDate}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'task_description' => $task->task_description,
                'service_date' => $serviceDate,
                'approved_at' => now()->format('g:i A'),
                'icon' => 'check-circle',
                'color' => 'green'
            ]
        );
    }

    /**
     * Notify all admins when an employee declines a task assignment.
     */
    public function notifyAdminsTaskDeclined($task, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $serviceDate = $task->scheduled_date ? $task->scheduled_date->format('M d, Y') : 'N/A';

        return $this->createMany(
            $admins,
            Notification::TYPE_TASK_DECLINED,
            'Task Declined by Employee',
            "{$employeeName} has declined the task assignment: {$task->task_description} for {$serviceDate}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'task_description' => $task->task_description,
                'service_date' => $serviceDate,
                'declined_at' => now()->format('g:i A'),
                'icon' => 'times-circle',
                'color' => 'red'
            ]
        );
    }

    /**
     * Notify all admins when an employee starts a task.
     */
    public function notifyAdminsTaskStarted($task, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();

        return $this->createMany(
            $admins,
            Notification::TYPE_TASK_STARTED_ADMIN,
            'Task Started',
            "{$employeeName} has started working on: {$task->task_description}.",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'task_description' => $task->task_description,
                'started_at' => now()->format('g:i A'),
                'icon' => 'play-circle',
                'color' => 'blue'
            ]
        );
    }

    /**
     * Notify all admins about task progress update.
     */
    public function notifyAdminsTaskProgress($task, $employeeName, int $completedItems, int $totalItems): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $percentage = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;

        return $this->createMany(
            $admins,
            Notification::TYPE_TASK_PROGRESS_ADMIN,
            'Task Progress Update',
            "{$employeeName} - {$task->task_description} is {$percentage}% complete ({$completedItems}/{$totalItems} items).",
            [
                'task_id' => $task->id,
                'employee_name' => $employeeName,
                'task_description' => $task->task_description,
                'completed_items' => $completedItems,
                'total_items' => $totalItems,
                'percentage' => $percentage,
                'icon' => 'tasks',
                'color' => 'yellow'
            ]
        );
    }

    /**
     * Notify all admins when a new job application is submitted.
     */
    public function notifyAdminsNewJobApplication($application): Collection
    {
        $admins = User::where('role', 'admin')->get();

        return $this->createMany(
            $admins,
            Notification::TYPE_JOB_APPLICATION_SUBMITTED,
            'New Job Application',
            "{$application->email} has applied for the {$application->job_title} position.",
            [
                'application_id' => $application->id,
                'job_title' => $application->job_title,
                'email' => $application->email,
                'icon' => 'user-plus',
                'color' => 'blue',
                'action_url' => route('admin.recruitment.index'),
                'action_text' => 'View Applications'
            ]
        );
    }

    /**
     * Notify all admins when an employee submits an absence/leave request (web form).
     */
    public function notifyAdminsEmployeeRequest($employeeRequest, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $absenceDate = $employeeRequest->absence_date->format('M d, Y');

        return $this->createMany(
            $admins,
            Notification::TYPE_LEAVE_REQUEST,
            'New Leave Request',
            "{$employeeName} has submitted a {$employeeRequest->absence_type} leave request for {$absenceDate}.",
            [
                'leave_request_id' => $employeeRequest->id,
                'employee_name' => $employeeName,
                'leave_type' => $employeeRequest->absence_type,
                'start_date' => $absenceDate,
                'reason' => $employeeRequest->reason,
                'icon' => 'user-clock',
                'color' => 'yellow',
                'action_url' => route('admin.attendance'),
            ]
        );
    }

    /**
     * Notify all admins when an employee cancels a leave request.
     */
    public function notifyAdminsEmployeeRequestCancelled($employeeRequest, $employeeName): Collection
    {
        $admins = User::where('role', 'admin')->get();
        $absenceDate = $employeeRequest->absence_date->format('M d, Y');

        return $this->createMany(
            $admins,
            Notification::TYPE_LEAVE_REQUEST_CANCELLED,
            'Leave Request Cancelled',
            "{$employeeName} has cancelled their {$employeeRequest->absence_type} leave request for {$absenceDate}.",
            [
                'leave_request_id' => $employeeRequest->id,
                'employee_name' => $employeeName,
                'leave_type' => $employeeRequest->absence_type,
                'start_date' => $absenceDate,
                'icon' => 'calendar-times',
                'color' => 'red',
                'action_url' => route('admin.attendance'),
            ]
        );
    }
}
