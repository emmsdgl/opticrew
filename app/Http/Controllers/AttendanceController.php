<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
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

            return [
                'status' => $status,
                'date' => $clockIn->format('F d'),
                'dayOfWeek' => $clockIn->format('l'),
                'timeIn' => $clockIn->format('g:i a'),
                'timeInNote' => $timeInNote ?? '',
                'timeOut' => $clockOut ? $clockOut->format('g:i a') : null,
                'timeOutNote' => '',
                'mealBreak' => '1:00 pm',
                'mealBreakDuration' => '30 mins',
                'timedIn' => true,
                'isTimedOut' => $clockOut !== null
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
                'iconColor' => 'text-green-600',
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
                'iconColor' => 'text-purple-600',
            ],
        ];

        return view('employee.attendance', compact('stats', 'attendanceRecords'));
    }

    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Check if already clocked in today
        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', now()->toDateString())
            ->whereNull('clock_out')
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'You are already clocked in');
        }

        // Create new attendance record
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'clock_in' => now(),
        ]);

        return redirect()->back()->with('success', 'Clocked in successfully');
    }

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        // Find today's attendance without clock out
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', now()->toDateString())
            ->whereNull('clock_out')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'No active clock-in found');
        }

        // Calculate total minutes worked
        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = now();
        $totalMinutes = $clockOut->diffInMinutes($clockIn);

        // Update attendance record
        $attendance->update([
            'clock_out' => $clockOut,
            'total_minutes_worked' => $totalMinutes,
        ]);

        return redirect()->back()->with('success', 'Clocked out successfully');
    }

    // private function dispatch($event)
    // {
    //     // This will trigger Livewire components to refresh
    //     // You can use Laravel Echo with broadcasting for real-time across multiple browsers
    //     session()->flash('livewire-refresh', true);
    // }
}