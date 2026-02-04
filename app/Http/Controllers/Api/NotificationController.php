<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\PushToken;
use App\Services\PushNotificationService;

class NotificationController extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Get user's notifications
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $perPage = $request->query('per_page', 20);
            $unreadOnly = $request->query('unread_only', false);

            $query = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            if ($unreadOnly) {
                $query->unread();
            }

            $notifications = $query->paginate($perPage);

            // Get unread count
            $unreadCount = Notification::where('user_id', $user->id)
                ->unread()
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function unreadCount(Request $request)
    {
        try {
            $user = $request->user();
            $count = Notification::where('user_id', $user->id)
                ->unread()
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        try {
            $user = $request->user();
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();

            Notification::where('user_id', $user->id)
                ->unread()
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, $notificationId)
    {
        try {
            $user = $request->user();
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $user->id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register push notification token
     */
    public function registerPushToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'device_type' => 'nullable|string|in:ios,android',
            ]);

            $user = $request->user();
            $token = $this->pushService->registerToken(
                $user,
                $request->token,
                $request->device_type
            );

            return response()->json([
                'success' => true,
                'message' => 'Push token registered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register push token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unregister push notification token
     */
    public function unregisterPushToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);

            $this->pushService->unregisterToken($request->token);

            return response()->json([
                'success' => true,
                'message' => 'Push token unregistered successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister push token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
