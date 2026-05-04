<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\UrgentLeave;
use App\Services\CompanySettingService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * SCENARIO #18: Urgent Leave — same-day mid-shift exit by an employee.
 *
 * Flow:
 *  1. Employee taps "Urgent Leave" on dashboard (button is locked unless they
 *     have an active clock-in for today).
 *  2. System auto-clocks them out, creates an UrgentLeave row, notifies admins.
 *  3. Admin has `reassignment_grace_period_minutes` (default 30) to manually
 *     assign a replacement and set compensation.
 *  4. If the grace period expires, ProcessUrgentLeaveGrace command auto-assigns
 *     the employee with the fewest pending tasks today (Escalation 1) and
 *     notifies admins to set the compensation amount.
 */
class UrgentLeaveController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    /**
     * Employee endpoint: submit an Urgent Leave for the current shift.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee record not found.'], 404);
        }

        // Active clock-in = today's attendance with clock_in set and clock_out null
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->latest('clock_in')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'You must be clocked in to submit an Urgent Leave.',
                'error_code' => 'NOT_CLOCKED_IN',
            ], 422);
        }

        // Avoid duplicate pending urgent leave for the same shift
        $existing = UrgentLeave::where('employee_id', $employee->id)
            ->where('attendance_id', $attendance->id)
            ->whereIn('status', [UrgentLeave::STATUS_AWAITING_ADMIN, UrgentLeave::STATUS_AUTO_ASSIGNED])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an Urgent Leave in progress for this shift.',
                'error_code' => 'ALREADY_SUBMITTED',
            ], 422);
        }

        $now = now();
        $urgentLeave = DB::transaction(function () use ($employee, $attendance, $request, $now) {
            // Auto clock-out — closes the active attendance immediately
            $minutesWorked = $attendance->clock_in
                ? Carbon::parse($attendance->clock_in)->diffInMinutes($now)
                : 0;
            $attendance->update([
                'clock_out' => $now,
                'total_minutes_worked' => $minutesWorked,
                'hours_worked' => round($minutesWorked / 60, 2),
            ]);

            return UrgentLeave::create([
                'employee_id' => $employee->id,
                'attendance_id' => $attendance->id,
                'triggered_at' => $now,
                'clock_out_at' => $now,
                'reason' => $request->input('reason'),
                'status' => UrgentLeave::STATUS_AWAITING_ADMIN,
            ]);
        });

        $employeeName = $employee->fullName ?? ($user->name ?? 'Employee');
        $this->notificationService->notifyAdminsUrgentLeave($urgentLeave, $employeeName);

        Log::warning('Urgent Leave submitted', [
            'urgent_leave_id' => $urgentLeave->id,
            'employee_id' => $employee->id,
            'attendance_id' => $attendance->id,
        ]);

        $graceMinutes = (int) CompanySettingService::get('reassignment_grace_period_minutes', 30);

        return response()->json([
            'success' => true,
            'message' => "Urgent Leave submitted. You have been clocked out. Admin has {$graceMinutes} minutes to assign a replacement.",
            'urgent_leave' => [
                'id' => $urgentLeave->id,
                'triggered_at' => $urgentLeave->triggered_at->toIso8601String(),
                'compensation_visibility' => 'Compensation will vary',
            ],
        ]);
    }

    /**
     * Admin endpoint: list pending urgent leaves.
     */
    public function index()
    {
        $leaves = UrgentLeave::with(['employee.user', 'replacement.user', 'processedBy'])
            ->orderByRaw("CASE WHEN status='awaiting_admin' THEN 0 WHEN status='auto_assigned' THEN 1 ELSE 2 END")
            ->orderByDesc('triggered_at')
            ->paginate(25);

        return view('admin.urgent-leaves', [
            'leaves' => $leaves,
        ]);
    }

    /**
     * Admin endpoint: assign a replacement and set compensation.
     */
    public function assign(Request $request, int $id)
    {
        $request->validate([
            'replacement_employee_id' => 'required|integer|exists:employees,id',
            'compensation_amount' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leave = UrgentLeave::find($id);
        if (!$leave) {
            return response()->json(['success' => false, 'message' => 'Urgent Leave not found.'], 404);
        }

        if (in_array($leave->status, [UrgentLeave::STATUS_MANUALLY_ASSIGNED, UrgentLeave::STATUS_CANCELLED], true)) {
            return response()->json([
                'success' => false,
                'message' => "This Urgent Leave is already {$leave->status} and cannot be re-assigned.",
            ], 422);
        }

        $leave->update([
            'replacement_employee_id' => $request->input('replacement_employee_id'),
            'compensation_amount' => $request->input('compensation_amount'),
            'admin_notes' => $request->input('admin_notes'),
            'status' => UrgentLeave::STATUS_MANUALLY_ASSIGNED,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // SCENARIO #18: Actually swap the team membership for today's tasks
        $teamsAffected = UrgentLeave::reassignTodaysTasks($leave->fresh());

        Log::info('Urgent Leave manually assigned', [
            'urgent_leave_id' => $leave->id,
            'replacement_employee_id' => $leave->replacement_employee_id,
            'compensation_amount' => $leave->compensation_amount,
            'teams_reassigned' => $teamsAffected,
            'processed_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Replacement assigned and compensation recorded. {$teamsAffected} team(s) updated for today.",
            'teams_reassigned' => $teamsAffected,
            'urgent_leave' => $leave->fresh()->load('replacement.user'),
        ]);
    }

    /**
     * Admin endpoint: cancel an urgent leave (e.g., false alarm).
     */
    public function cancel(Request $request, int $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leave = UrgentLeave::find($id);
        if (!$leave) {
            return response()->json(['success' => false, 'message' => 'Urgent Leave not found.'], 404);
        }

        $leave->update([
            'status' => UrgentLeave::STATUS_CANCELLED,
            'admin_notes' => $request->input('admin_notes'),
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Urgent Leave cancelled.']);
    }
}
