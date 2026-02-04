<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PushToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    /**
     * Create an in-app notification and optionally send push notification
     */
    public function notify(User $user, string $type, string $title, string $message, array $data = [], bool $sendPush = true): Notification
    {
        // Create in-app notification
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        // Send push notification if enabled
        if ($sendPush) {
            $this->sendPushNotification($user, $title, $message, $data);
        }

        return $notification;
    }

    /**
     * Send push notification to user's devices via Expo
     */
    public function sendPushNotification(User $user, string $title, string $body, array $data = []): bool
    {
        $tokens = PushToken::forUser($user->id)->active()->pluck('token')->toArray();

        if (empty($tokens)) {
            Log::info("No push tokens found for user {$user->id}");
            return false;
        }

        $messages = [];
        foreach ($tokens as $token) {
            $messages[] = [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ];
        }

        return $this->sendToExpo($messages);
    }

    /**
     * Send messages to Expo Push API
     */
    private function sendToExpo(array $messages): bool
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(self::EXPO_PUSH_URL, $messages);

            if ($response->successful()) {
                $data = $response->json();

                // Check for any errors in the response
                if (isset($data['data'])) {
                    foreach ($data['data'] as $index => $result) {
                        if (isset($result['status']) && $result['status'] === 'error') {
                            Log::warning("Push notification error: " . ($result['message'] ?? 'Unknown error'));

                            // If token is invalid, deactivate it
                            if (isset($result['details']['error']) &&
                                in_array($result['details']['error'], ['DeviceNotRegistered', 'InvalidCredentials'])) {
                                $this->deactivateToken($messages[$index]['to']);
                            }
                        }
                    }
                }

                return true;
            }

            Log::error("Expo push API error: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Push notification exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deactivate an invalid push token
     */
    private function deactivateToken(string $token): void
    {
        PushToken::where('token', $token)->update(['is_active' => false]);
    }

    /**
     * Register or update a push token for a user
     */
    public function registerToken(User $user, string $token, ?string $deviceType = null): PushToken
    {
        return PushToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id' => $user->id,
                'device_type' => $deviceType,
                'is_active' => true,
            ]
        );
    }

    /**
     * Unregister a push token
     */
    public function unregisterToken(string $token): bool
    {
        return PushToken::where('token', $token)->delete() > 0;
    }

    /**
     * Send notification for leave request approved
     */
    public function notifyLeaveApproved(User $employee, array $leaveData): Notification
    {
        $startDate = $leaveData['date'] ?? 'N/A';
        $message = "Your leave request for {$startDate} has been approved.";

        if (!empty($leaveData['admin_notes'])) {
            $message .= " Note: {$leaveData['admin_notes']}";
        }

        return $this->notify(
            $employee,
            Notification::TYPE_LEAVE_APPROVED,
            'Leave Request Approved',
            $message,
            $leaveData
        );
    }

    /**
     * Send notification for leave request rejected
     */
    public function notifyLeaveRejected(User $employee, array $leaveData): Notification
    {
        $startDate = $leaveData['date'] ?? 'N/A';
        $reason = $leaveData['admin_notes'] ?? 'No reason provided';
        $message = "Your leave request for {$startDate} has been rejected. Reason: {$reason}";

        return $this->notify(
            $employee,
            Notification::TYPE_LEAVE_REJECTED,
            'Leave Request Rejected',
            $message,
            $leaveData
        );
    }
}
