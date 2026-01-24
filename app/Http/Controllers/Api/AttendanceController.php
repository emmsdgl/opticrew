<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Clock in
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:5120', // 5MB max
        ]);

        $user = $request->user();

        // Get employee record
        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        // Check if already clocked in today
        $today = Carbon::today();
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'message' => 'You are already clocked in',
                'attendance' => $existingAttendance
            ], 400);
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance_photos', 'public');
        }

        // Create attendance record
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => now(),
            'clock_in_latitude' => $request->latitude,
            'clock_in_longitude' => $request->longitude,
            'clock_in_photo' => $photoPath,
            'status' => 'present',
        ]);

        return response()->json([
            'message' => 'Clocked in successfully',
            'attendance' => $attendance
        ], 201);
    }

    /**
     * Clock out
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        // Find today's attendance record
        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No active clock-in record found'
            ], 404);
        }

        // Update attendance record
        $attendance->update([
            'clock_out' => now(),
            'clock_out_latitude' => $request->latitude,
            'clock_out_longitude' => $request->longitude,
        ]);

        // Calculate total hours worked
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);
        $hoursWorked = $clockOut->diffInHours($clockIn, true);

        $attendance->hours_worked = round($hoursWorked, 2);
        $attendance->save();

        return response()->json([
            'message' => 'Clocked out successfully',
            'attendance' => $attendance,
            'hours_worked' => $hoursWorked
        ]);
    }

    /**
     * Get attendance history for current user
     */
    public function getHistory(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found'
            ], 404);
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('clock_in', 'desc')
            ->take(30) // Last 30 records
            ->get();

        return response()->json([
            'attendances' => $attendances
        ]);
    }

    /**
     * Get today's attendance status
     */
    public function getTodayStatus(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'message' => 'Employee record not found',
                'is_clocked_in' => false
            ], 200);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', $today)
            ->whereNull('clock_out')
            ->first();

        return response()->json([
            'is_clocked_in' => $attendance !== null,
            'attendance' => $attendance
        ]);
    }
}
