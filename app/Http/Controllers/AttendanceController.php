<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DayOff;
use App\Models\Employee;
use App\Models\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Admin Attendance View - Shows all employees' attendance and leave requests
     */
    public function adminIndex(Request $request)
    {
        // Get current month or selected month
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Get all attendance records for the month
        $attendances = Attendance::with(['employee.user'])
            ->whereBetween('clock_in', [$startDate, $endDate])
            ->orderBy('clock_in', 'desc')
            ->get();

        // Format attendance records for display
        $attendanceRecords = $attendances->map(function ($attendance) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;

            // Determine status
            $status = 'present';
            $scheduledTime = $clockIn->copy()->setTime(11, 0);

            if ($clockIn->gt($scheduledTime)) {
                $minutesLate = $clockIn->diffInMinutes($scheduledTime);
                $status = 'late';
                $timeInNote = $minutesLate . ' m late';
            } else {
                $minutesEarly = $scheduledTime->diffInMinutes($clockIn);
                $timeInNote = $minutesEarly . ' m early';
            }

            // Calculate hours worked
            $hoursWorkedText = null;
            if ($clockOut) {
                $minutesWorked = $attendance->total_minutes_worked ?? $clockOut->diffInMinutes($clockIn);
                $hours = floor($minutesWorked / 60);
                $minutes = $minutesWorked % 60;

                if ($hours > 0 && $minutes > 0) {
                    $hoursWorkedText = "{$hours} hr {$minutes} min";
                } elseif ($hours > 0) {
                    $hoursWorkedText = $hours == 1 ? "{$hours} hr" : "{$hours} hrs";
                } else {
                    $hoursWorkedText = "{$minutes} min";
                }
            }

            $employeeName = $attendance->employee?->fullName ?? 'Unknown';

            return [
                'status' => $status,
                'date' => $clockIn->format('F d') . ' - ' . $employeeName,
                'dayOfWeek' => $clockIn->format('l'),
                'timeIn' => $clockIn->format('g:i a'),
                'timeInNote' => $timeInNote ?? '',
                'timeOut' => $clockOut ? $clockOut->format('g:i a') : null,
                'timeOutNote' => '',
                'hoursWorked' => $hoursWorkedText,
                'timedIn' => true,
                'isTimedOut' => $clockOut !== null
            ];
        })->toArray();

        // Get all active employees count
        $totalEmployees = Employee::where('is_active', true)->count();

        // Get today's attendance for present/absent calculation
        $today = now()->toDateString();
        $todayAttendances = Attendance::whereDate('clock_in', $today)->get();
        $presentToday = $todayAttendances->unique('employee_id')->count();
        $absentToday = $totalEmployees - $presentToday;

        // Calculate total hours worked this week
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyAttendances = Attendance::whereBetween('clock_in', [$weekStart, $weekEnd])->get();
        $totalWeeklyMinutes = $weeklyAttendances->sum('total_minutes_worked');
        $totalWeeklyHours = floor($totalWeeklyMinutes / 60);
        $totalWeeklyMinutesRemainder = $totalWeeklyMinutes % 60;

        // Calculate employees on leave today
        $onLeaveToday = DayOff::where('status', 'approved')
            ->where('date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->where('end_date', '>=', $today)
                  ->orWhereNull('end_date');
            })
            ->count();

        // Prepare statistics
        $stats = [
            [
                'title' => 'Present Today',
                'value' => $presentToday,
                'subtitle' => 'Employees clocked in today',
                'icon' => 'fa-solid fa-user-check',
                'iconBg' => '',
                'iconColor' => 'text-green-600',
            ],
            [
                'title' => 'Absent Today',
                'value' => $absentToday,
                'subtitle' => $onLeaveToday . ' on approved leave',
                'icon' => 'fa-solid fa-user-xmark',
                'iconBg' => '',
                'iconColor' => 'text-red-600',
            ],
            [
                'title' => 'Hours Worked This Week',
                'value' => $totalWeeklyHours . ' h ' . $totalWeeklyMinutesRemainder . ' m',
                'subtitle' => 'Total hours across all employees',
                'icon' => 'fa-solid fa-clock',
                'iconBg' => '',
                'iconColor' => 'text-blue-600',
            ],
        ];

        // Get all employee requests (leave/absence requests)
        $employeeRequests = EmployeeRequest::with(['employee.user'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        // Format request records for display (table format + modal data)
        $requestRecords = $employeeRequests->map(function ($request, $index) {
            $employeeName = $request->employee?->fullName ?? 'Unknown';
            $date = Carbon::parse($request->absence_date);

            // Map status to display format for badge colors
            $statusMap = [
                'Pending' => 'late',       // Yellow/Orange color
                'Approved' => 'present',   // Green color
                'Rejected' => 'absent',    // Red color
                'Cancelled' => 'archived'  // Gray color
            ];

            return [
                // Table display fields
                'status' => $statusMap[$request->status] ?? 'late',
                'date' => $date->format('F d') . ' - ' . $employeeName,
                'dayOfWeek' => $request->absence_type . ' (' . $request->status . ')',
                'timeIn' => Str::limit($request->reason, 30),
                'timeInNote' => '',
                'timeOut' => $request->time_range,
                'timeOutNote' => '',
                'hoursWorked' => $request->time_range,
                'timedIn' => true,
                'isTimedOut' => false,
                'buttonLabel' => 'View',
                // Modal data fields
                'requestIndex' => $index,
                'requestId' => $request->id,
                'requestEmployeeName' => $employeeName,
                'requestType' => $request->absence_type,
                'requestStatus' => strtolower($request->status),
                'requestDate' => $date->format('F d, Y'),
                'requestEndDate' => null,
                'requestTimeRange' => $request->time_range,
                'requestFromTime' => $request->from_time ? Carbon::parse($request->from_time)->format('h:i A') : null,
                'requestToTime' => $request->to_time ? Carbon::parse($request->to_time)->format('h:i A') : null,
                'requestReason' => $request->reason,
                'requestDescription' => $request->description,
                'requestProofDocument' => $request->proof_document,
                'requestAdminNotes' => $request->admin_notes ?? '',
                'requestDurationDays' => 1,
                'requestCreatedAt' => $request->created_at->format('M d, Y h:i A'),
            ];
        })->toArray();

        return view('admin.attendance', compact('stats', 'attendanceRecords', 'requestRecords'));
    }

    public function index(Request $request)
    {
        // Get the authenticated employee
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee profile not found');
        }

        // Get current month or selected month
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();

        // Get attendance records for the employee
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$startDate, $endDate])
            ->orderBy('clock_in', 'desc')
            ->get();

        // Calculate days worked
        $daysWorked = $attendances->count();

        // Calculate total worked hours
        $totalMinutesWorked = $attendances->sum('total_minutes_worked');
        $totalHoursWorked = floor($totalMinutesWorked / 60);
        $totalMinutesRemainder = $totalMinutesWorked % 60;

        // Calculate average hours per day
        $avgMinutesPerDay = $daysWorked > 0 ? $totalMinutesWorked / $daysWorked : 0;
        $avgHours = floor($avgMinutesPerDay / 60);
        $avgMinutes = round($avgMinutesPerDay % 60);

        // Calculate previous month comparison (for trend)
        $previousMonth = $startDate->copy()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        
        $previousMonthMinutes = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$previousMonth, $previousMonthEnd])
            ->sum('total_minutes_worked');

        $trendPercentage = 0;
        if ($previousMonthMinutes > 0) {
            $trendPercentage = (($totalMinutesWorked - $previousMonthMinutes) / $previousMonthMinutes) * 100;
        }

        // Format attendance records for display
        $attendanceRecords = $attendances->map(function ($attendance) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = $attendance->clock_out ? Carbon::parse($attendance->clock_out) : null;
            $isToday = $clockIn->isToday();

            // Determine status
            $status = 'present';
            $scheduledTime = $clockIn->copy()->setTime(11, 0); // Example: 11:00 AM scheduled time

            if ($clockIn->gt($scheduledTime)) {
                $minutesLate = $clockIn->diffInMinutes($scheduledTime);
                $status = 'late';
                $timeInNote = $minutesLate . ' m late';
            } else {
                $minutesEarly = $scheduledTime->diffInMinutes($clockIn);
                $timeInNote = $minutesEarly . ' m early';
            }

            // Calculate hours worked
            $hoursWorkedText = null;
            if ($clockOut) {
                $minutesWorked = $attendance->total_minutes_worked ?? $clockOut->diffInMinutes($clockIn);
                $hours = floor($minutesWorked / 60);
                $minutes = $minutesWorked % 60;

                if ($hours > 0 && $minutes > 0) {
                    $hoursWorkedText = "{$hours} hr {$minutes} min";
                } elseif ($hours > 0) {
                    $hoursWorkedText = $hours == 1 ? "{$hours} hr" : "{$hours} hrs";
                } else {
                    $hoursWorkedText = "{$minutes} min";
                }
            }

            return [
                'status' => $status,
                'date' => $clockIn->format('F d'),
                'dayOfWeek' => $clockIn->format('l'),
                'timeIn' => $clockIn->format('g:i a'),
                'timeInNote' => $timeInNote ?? '',
                'timeOut' => $clockOut ? $clockOut->format('g:i a') : null,
                'timeOutNote' => '',
                'hoursWorked' => $hoursWorkedText,
                'timedIn' => true,
                'isTimedOut' => $clockOut !== null,
                'isToday' => $isToday
            ];
        })->toArray();

        // Prepare statistics
        $stats = [
            [
                'title' => 'Days Worked',
                'value' => $daysWorked,
                'subtitle' => 'Total attendance days this month',
                'icon' => 'fa-solid fa-calendar-check',
                'iconBg' => '',
                'iconColor' => 'text-blue-600',
            ],
            [
                'title' => 'Worked Hours',
                'value' => $totalHoursWorked . ' h ' . $totalMinutesRemainder . ' m',
                'trend' => $trendPercentage >= 0 ? 'up' : 'down',
                'trendValue' => number_format(abs($trendPercentage), 1) . '%',
                'trendLabel' => 'vs last month',
                'icon' => 'fa-solid fa-hourglass-start',
                'iconBg' => '',
                'iconColor' => 'text-blue-600',
            ],
            [
                'title' => 'Average Hours Per Day',
                'value' => $avgHours . ' h ' . $avgMinutes . ' m',
                'subtitle' => 'Average daily work duration',
                'icon' => 'fa-solid fa-clock',
                'iconBg' => '',
                'iconColor' => 'text-blue-600',
            ],
        ];

        // --- Check today's attendance status for clock in/out buttons ---
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', now()->toDateString())
            ->first();

        $isClockedIn = $todayAttendance && $todayAttendance->clock_out === null;
        $hasAttendanceToday = $todayAttendance !== null;

        // --- Fetch Employee Requests (exclude cancelled) ---
        $employeeRequests = EmployeeRequest::where('employee_id', $employee->id)
            ->where('status', '!=', 'Cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $requestRecords = $employeeRequests->map(function ($req, $index) {
            $statusMap = [
                'Pending' => 'late',      // Yellow/Orange color
                'Approved' => 'present',  // Green color
                'Rejected' => 'absent'    // Red color
            ];

            return [
                // Table display fields
                'status' => $statusMap[$req->status] ?? 'late',
                'date' => Carbon::parse($req->absence_date)->format('F d'),
                'dayOfWeek' => $req->absence_type,
                'timeIn' => $req->time_range,
                'timeInNote' => $req->status,
                'timeOut' => $req->from_time && $req->to_time
                    ? Carbon::parse($req->from_time)->format('g:i a') . ' - ' . Carbon::parse($req->to_time)->format('g:i a')
                    : '-',
                'timeOutNote' => '',
                'hoursWorked' => Str::limit($req->reason, 20),
                'timedIn' => true,
                'isTimedOut' => false,
                'buttonLabel' => 'View',
                // Modal data fields
                'requestIndex' => $index,
                'requestId' => $req->id,
                'requestType' => $req->absence_type,
                'requestStatus' => $req->status,
                'requestDate' => Carbon::parse($req->absence_date)->format('F d, Y'),
                'requestTimeRange' => $req->time_range,
                'requestFromTime' => $req->from_time ? Carbon::parse($req->from_time)->format('h:i A') : null,
                'requestToTime' => $req->to_time ? Carbon::parse($req->to_time)->format('h:i A') : null,
                'requestReason' => $req->reason,
                'requestDescription' => $req->description,
                'requestProofDocument' => $req->proof_document,
                'requestCreatedAt' => $req->created_at->format('M d, Y h:i A'),
            ];
        })->toArray();

        return view('employee.attendance', compact('stats', 'attendanceRecords', 'requestRecords', 'employee', 'isClockedIn', 'hasAttendanceToday'));
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // TRIPLE-CHECK: Prevent multiple clock-ins for the same day
        // Check 1: Is there ANY attendance record for today (regardless of clock_out status)?
        $attendanceToday = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', now()->toDateString())
            ->first();

        if ($attendanceToday) {
            // Already have an attendance record for today
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already clocked in today'
                ], 400);
            }
            return redirect()->back()->with('error', 'You have already clocked in today');
        }

        // Check 2: Is there an active clock-in without clock-out (from previous day)?
        $activeClockIn = Attendance::where('employee_id', $employee->id)
            ->whereNull('clock_out')
            ->first();

        if ($activeClockIn) {
            // There's an unclosed attendance from a previous day
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have an unclosed clock-in from ' . Carbon::parse($activeClockIn->clock_in)->format('F d, Y')
                ], 400);
            }
            return redirect()->back()->with('error', 'You have an unclosed clock-in from ' . Carbon::parse($activeClockIn->clock_in)->format('F d, Y'));
        }

        // Check 3: Validate employee exists
        if (!$employee) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee profile not found'
                ], 400);
            }
            return redirect()->back()->with('error', 'Employee profile not found');
        }

        // All checks passed - Create new attendance record
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Clocked in successfully',
                'attendance' => $attendance
            ]);
        }

        return redirect()->back()->with('success', 'Clocked in successfully');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Check 1: Validate employee exists
        if (!$employee) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee profile not found'
                ], 400);
            }
            return redirect()->back()->with('error', 'Employee profile not found');
        }

        // Check 2: Find today's attendance without clock out
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', now()->toDateString())
            ->whereNull('clock_out')
            ->first();

        if (!$attendance) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active clock-in found for today'
                ], 400);
            }
            return redirect()->back()->with('error', 'No active clock-in found for today');
        }

        // Check 3: Verify clock-in already exists and hasn't been clocked out
        if ($attendance->clock_out !== null) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already clocked out today'
                ], 400);
            }
            return redirect()->back()->with('error', 'You have already clocked out today');
        }

        // Check 4: Ensure clock-out time is after clock-in time
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = now();

        if ($clockOut->lessThanOrEqualTo($clockIn)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Clock-out time must be after clock-in time'
                ], 400);
            }
            return redirect()->back()->with('error', 'Clock-out time must be after clock-in time');
        }

        // Calculate total minutes worked
        $totalMinutes = $clockOut->diffInMinutes($clockIn);

        // Update attendance record
        $attendance->update([
            'clock_out' => $clockOut,
            'total_minutes_worked' => $totalMinutes,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Clocked out successfully',
                'attendance' => $attendance
            ]);
        }

        return redirect()->back()->with('success', 'Clocked out successfully');
    }

    /**
     * Approve an employee request (Admin only)
     */
    public function approveRequest(Request $request, $id)
    {
        $employeeRequest = EmployeeRequest::findOrFail($id);

        if ($employeeRequest->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been processed'
            ], 400);
        }

        $employeeRequest->update([
            'status' => 'Approved',
            'admin_notes' => $request->input('admin_notes', ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully'
        ]);
    }

    /**
     * Reject an employee request (Admin only)
     */
    public function rejectRequest(Request $request, $id)
    {
        $employeeRequest = EmployeeRequest::findOrFail($id);

        if ($employeeRequest->status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been processed'
            ], 400);
        }

        $employeeRequest->update([
            'status' => 'Rejected',
            'admin_notes' => $request->input('admin_notes', ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully'
        ]);
    }

    // private function dispatch($event)
    // {
    //     // This will trigger Livewire components to refresh
    //     // You can use Laravel Echo with broadcasting for real-time across multiple browsers
    //     session()->flash('livewire-refresh', true);
    // }
}