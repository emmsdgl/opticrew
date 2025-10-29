<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display all notifications for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getAll($user, 50);
        $unreadCount = $this->notificationService->getUnreadCount($user);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications count (for header badge).
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($id)
    {
        $this->notificationService->markAsRead($id);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Delete a notification.
     */
    public function delete($id)
    {
        $this->notificationService->delete($id);

        return response()->json(['success' => true]);
    }

    /**
     * TEST ONLY - Create sample notifications for testing.
     */
    public function createTestNotifications()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first to test notifications.');
        }

        // Create different types of test notifications
        $this->notificationService->create(
            $user,
            'appointment_approved',
            'Appointment Approved',
            'Your cleaning appointment for tomorrow has been approved and confirmed.',
            ['appointment_id' => 123, 'date' => now()->addDay()->format('Y-m-d')]
        );

        $this->notificationService->create(
            $user,
            'task_assigned',
            'New Task Assigned',
            'You have been assigned to clean the main office building on Floor 3.',
            ['task_id' => 456, 'location' => 'Main Office - Floor 3']
        );

        $this->notificationService->create(
            $user,
            'schedule_updated',
            'Schedule Updated',
            'Your work schedule for next week has been updated. Please review the changes.',
            ['date' => now()->addWeek()->format('Y-m-d')]
        );

        $this->notificationService->create(
            $user,
            'system_message',
            'Welcome to OptiCrew',
            'Thank you for using our notification system. You will receive important updates here.',
            ['priority' => 'low']
        );

        $this->notificationService->create(
            $user,
            'payment_received',
            'Payment Received',
            'Your payment of €150.00 has been successfully processed.',
            ['amount' => 150.00, 'invoice_id' => 789]
        );

        return redirect()->back()->with('success', '5 test notifications created successfully! Check your notifications.');
    }
}
