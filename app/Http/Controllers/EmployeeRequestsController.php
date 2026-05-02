<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Notification\NotificationService;
use App\Services\Leave\EmergencyLeaveService;
use App\Models\DayOff;
use App\Models\Task;
use App\Models\Employee;
use Carbon\Carbon;

class EmployeeRequestsController extends Controller
{
    public function create()
    {
        $employee = Auth::user()->employee;
        $currentStep = 1;
        
        return view('employee.requests.create', compact('employee', 'currentStep'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'absence_type' => 'required|string|max:255',
            'absence_date' => 'required|date|after_or_equal:today',
            'time_range' => 'required|string|max:255',
            'from_time' => 'nullable|required_if:time_range,Custom Hours',
            'to_time' => 'nullable|required_if:time_range,Custom Hours',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:350',
            'proof_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ]);

        $employee = Auth::user()->employee;

        // Auto-calculate from_time and to_time based on time_range and employee shift
        $shiftStart = $employee->shift_start ?? '11:00';
        $shiftEnd = $employee->shift_end ?? '20:00';
        $fromTime = $validated['from_time'] ?? null;
        $toTime = $validated['to_time'] ?? null;

        if ($validated['time_range'] !== 'Custom Hours') {
            $startMinutes = intval(substr($shiftStart, 0, 2)) * 60 + intval(substr($shiftStart, 3, 2));
            $endMinutes = intval(substr($shiftEnd, 0, 2)) * 60 + intval(substr($shiftEnd, 3, 2));
            $midMinutes = $startMinutes + intdiv($endMinutes - $startMinutes, 2);
            $midTime = sprintf('%02d:%02d', intdiv($midMinutes, 60), $midMinutes % 60);

            if ($validated['time_range'] === 'Full Shift') {
                $fromTime = $shiftStart;
                $toTime = $shiftEnd;
            } elseif ($validated['time_range'] === 'Morning (First Half)') {
                $fromTime = $shiftStart;
                $toTime = $midTime;
            } elseif ($validated['time_range'] === 'Afternoon (Second Half)') {
                $fromTime = $midTime;
                $toTime = $shiftEnd;
            }
        }

        // Handle file upload if present
        $proofPath = null;
        if ($request->hasFile('proof_document')) {
            $proofPath = $request->file('proof_document')->store('employee-requests', 'public');
        }

        // Mirror to day_offs so the emergency-leave automation pipeline (escalation,
        // One-Click Approve/Deny, Active Recovery) sees web-submitted leaves too.
        // See memory: Dual Leave Storage Tables — needs unification post-thesis.
        $isEmergency = $validated['absence_type'] === 'Emergency Leave';
        $dayOffTypeMap = [
            'Sick Leave' => 'sick',
            'Vacation Leave' => 'vacation',
            'Emergency Leave' => 'emergency',
            'Maternity/Paternity Leave' => 'personal',
            'Unpaid Leave' => 'other',
            'Other' => 'other',
        ];
        $dayOffType = $dayOffTypeMap[$validated['absence_type']] ?? 'other';

        $dayOff = DayOff::create([
            'employee_id' => $employee->id,
            'date' => $validated['absence_date'],
            'end_date' => $validated['absence_date'],
            'reason' => $validated['reason'] . ($validated['description'] ? ' — ' . $validated['description'] : ''),
            'type' => $dayOffType,
            'status' => 'pending',
            'is_emergency' => $isEmergency,
            'escalation_level' => 0,
        ]);

        $employeeRequest = \App\Models\EmployeeRequest::create([
            'day_off_id' => $dayOff->id,
            'employee_id' => $employee->id,
            'absence_type' => $validated['absence_type'],
            'absence_date' => $validated['absence_date'],
            'time_range' => $validated['time_range'],
            'from_time' => $fromTime,
            'to_time' => $toTime,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'proof_document' => $proofPath,
            'status' => 'Pending', // Default status
        ]);

        // Notify all admins about the new leave request
        $notificationService = app(NotificationService::class);
        $employeeName = Auth::user()->name;
        $notificationService->notifyAdminsEmployeeRequest($employeeRequest, $employeeName);

        // SCENARIO #11/#12/#13: Emergency leave path — fire escalation level 1 + Active Recovery
        $activeRecoveryTriggered = false;
        $tasksAffected = 0;
        if ($isEmergency) {
            $notificationService->notifyManagerEmergencyLeave($dayOff, $employeeName, 1);
            $dayOff->update([
                'escalation_level' => 1,
                'escalation_notified_at' => now(),
            ]);
            $tasksAffected = app(EmergencyLeaveService::class)->triggerActiveRecovery($dayOff, $employee);
            $activeRecoveryTriggered = $tasksAffected > 0;
        }

        $message = $isEmergency
            ? 'Your emergency absence request has been submitted. Manager has been notified immediately.'
            : 'Your absence request has been submitted successfully!';
        if ($activeRecoveryTriggered) {
            $message .= " Active Recovery initiated for {$tasksAffected} affected task(s).";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect_url' => route('employee.dashboard'),
            'is_emergency' => $isEmergency,
            'active_recovery_triggered' => $activeRecoveryTriggered,
            'tasks_affected' => $tasksAffected,
        ]);
    }

    /**
     * Cancel an employee request (only pending requests)
     */
    public function cancel($id)
    {
        $employee = Auth::user()->employee;

        $employeeRequest = \App\Models\EmployeeRequest::where('id', $id)
            ->where('employee_id', $employee->id)
            ->first();

        if (!$employeeRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }

        if ($employeeRequest->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be cancelled'
            ], 400);
        }

        $employeeRequest->update([
            'status' => 'Cancelled'
        ]);

        // Mirror cancel into the linked day_offs row so automation stays in sync.
        if ($employeeRequest->day_off_id) {
            DayOff::where('id', $employeeRequest->day_off_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        // Notify all admins about the cancelled leave request
        $notificationService = app(NotificationService::class);
        $employeeName = Auth::user()->name;
        $notificationService->notifyAdminsEmployeeRequestCancelled($employeeRequest, $employeeName);

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully'
        ]);
    }

    /**
     * Check for conflicting tasks on a given date/time range
     */
    public function checkConflicts(Request $request)
    {
        $validated = $request->validate([
            'absence_date' => 'required|date',
            'from_time' => 'nullable|string',
            'to_time' => 'nullable|string',
        ]);

        $employee = Auth::user()->employee;
        $date = $validated['absence_date'];

        $tasks = Task::with('location')
            ->whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->where('scheduled_date', $date)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->orderBy('optimized_start_minutes')
            ->get();

        // If from_time/to_time provided, filter tasks that overlap with the leave time range
        $fromTime = $validated['from_time'] ?? null;
        $toTime = $validated['to_time'] ?? null;

        if ($fromTime && $toTime) {
            $leaveStartMin = intval(substr($fromTime, 0, 2)) * 60 + intval(substr($fromTime, 3, 2));
            $leaveEndMin = intval(substr($toTime, 0, 2)) * 60 + intval(substr($toTime, 3, 2));

            $tasks = $tasks->filter(function ($task) use ($leaveStartMin, $leaveEndMin) {
                $taskStart = $task->optimized_start_minutes ?? 0;
                $taskEnd = $task->optimized_end_minutes ?? ($taskStart + ($task->duration ?? 60));
                // Check overlap
                return $taskStart < $leaveEndMin && $taskEnd > $leaveStartMin;
            });
        }

        $conflicting = $tasks->map(function ($task) {
            $startH = intdiv($task->optimized_start_minutes ?? 0, 60);
            $startM = ($task->optimized_start_minutes ?? 0) % 60;
            $endMin = $task->optimized_end_minutes ?? (($task->optimized_start_minutes ?? 0) + ($task->duration ?? 60));
            $endH = intdiv($endMin, 60);
            $endM = $endMin % 60;

            $title = $task->task_description ?? 'Task #' . $task->id;
            if ($task->location) {
                $title .= ' @ ' . $task->location->name;
            }

            return [
                'id' => $task->id,
                'title' => $title,
                'time' => sprintf('%d:%02d %s - %d:%02d %s',
                    $startH % 12 ?: 12, $startM, $startH >= 12 ? 'PM' : 'AM',
                    $endH % 12 ?: 12, $endM, $endH >= 12 ? 'PM' : 'AM'),
                'status' => $task->status,
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'conflicts' => $conflicting,
        ]);
    }
}