<?php

namespace App\Http\Controllers;

use App\Models\DayOff;
use App\Services\Notification\NotificationService;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * SCENARIO #12: One-Click Approve/Deny for emergency leave escalation notifications.
 *
 * Routes are protected by Laravel's `signed` middleware — the URLs included in
 * notifications carry a HMAC signature, so no login is required. The signature
 * proves the URL was minted by the system and hasn't been tampered with, which
 * is the same trust model Laravel uses for password reset links.
 */
class LeaveQuickActionController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private PushNotificationService $pushService,
    ) {}

    public function approve(Request $request, int $leaveId)
    {
        $leave = DayOff::find($leaveId);
        if (!$leave) {
            return response()->view('leave-quick-action', [
                'success' => false,
                'title' => 'Leave Request Not Found',
                'message' => 'This leave request could not be found. It may have been deleted.',
            ], 404);
        }

        if ($leave->status !== 'pending') {
            return response()->view('leave-quick-action', [
                'success' => false,
                'title' => 'Already Processed',
                'message' => "This request has already been {$leave->status}.",
            ]);
        }

        $leave->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => 'Approved via one-click escalation link',
        ]);

        $employee = $leave->employee;
        if ($employee && Carbon::parse($leave->date)->isToday()) {
            $employee->update(['is_day_off' => true]);
        }

        if ($employee && $employee->user) {
            $this->notificationService->notifyEmployeeLeaveApproved($employee->user, $leave);
        }

        return response()->view('leave-quick-action', [
            'success' => true,
            'title' => 'Leave Approved',
            'message' => 'The emergency leave request has been approved. The employee has been notified.',
        ]);
    }

    public function deny(Request $request, int $leaveId)
    {
        $leave = DayOff::find($leaveId);
        if (!$leave) {
            return response()->view('leave-quick-action', [
                'success' => false,
                'title' => 'Leave Request Not Found',
                'message' => 'This leave request could not be found.',
            ], 404);
        }

        if ($leave->status !== 'pending') {
            return response()->view('leave-quick-action', [
                'success' => false,
                'title' => 'Already Processed',
                'message' => "This request has already been {$leave->status}.",
            ]);
        }

        $leave->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => 'Denied via one-click escalation link',
        ]);

        $employee = $leave->employee;
        if ($employee && $employee->user) {
            $this->notificationService->notifyEmployeeLeaveRejected($employee->user, $leave);
        }

        return response()->view('leave-quick-action', [
            'success' => true,
            'title' => 'Leave Denied',
            'message' => 'The emergency leave request has been denied. The employee has been notified.',
        ]);
    }
}
