<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Holiday;
use App\Models\EmployeeRequest;
use App\Models\UserCourseProgress;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $today = Carbon::today();

        // --- Daily Schedule (Grouped by Company) ---
        $dailySchedule = DB::table('tasks')
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
            ->where('tasks.employee_approved', true)
            ->whereDate('tasks.scheduled_date', $today)
            ->select(
                DB::raw("COALESCE(contracted_clients.name, 'No Client Assigned') as client_name"),
                DB::raw("MIN(tasks.scheduled_time) as start_time"),
                DB::raw("MAX(ADDTIME(tasks.scheduled_time, SEC_TO_TIME(tasks.estimated_duration_minutes * 60))) as end_time"),
                DB::raw("SUM(tasks.estimated_duration_minutes) as total_duration"),
                DB::raw("COUNT(*) as task_count")
            )
            ->groupBy('contracted_clients.name')
            ->orderBy('start_time')
            ->get()
            ->map(function ($item) {
                return $item;
            });

        // --- To-Do List (Detailed Task Information) ---
        $todoList = DB::table('tasks')
            ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
            ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
            ->where('tasks.employee_approved', true)
            ->whereIn('tasks.status', ['Pending', 'Scheduled', 'In Progress'])
            ->select(
                'tasks.id',
                DB::raw("COALESCE(contracted_clients.name, 'No Client Assigned') as client_name"),
                'tasks.scheduled_date as date',
                'locations.location_name as cabin_name',
                'tasks.task_description',
                'tasks.estimated_duration_minutes as duration',
                'tasks.status'
            )
            ->orderBy('tasks.scheduled_date')
            ->get();

        // --- UPDATED: Tasks Summary Logic ---
        $period = $request->input('period', 'All'); // Get 'period' from URL, default to 'Today'

        $tasksSummaryQuery = DB::table('tasks')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
            ->where('tasks.employee_approved', true);

        // Apply date filtering based on the selected period
        switch ($period) {
            case 'Today':
                $tasksSummaryQuery->whereDate('tasks.scheduled_date', Carbon::today());
                break;
            case 'Yesterday':
                $tasksSummaryQuery->whereDate('tasks.scheduled_date', Carbon::yesterday());
                break;
            case 'Last 7 days':
                $tasksSummaryQuery->whereBetween('tasks.scheduled_date', [Carbon::now()->subDays(7), Carbon::now()]);
                break;
            case 'Last 30 days':
                $tasksSummaryQuery->whereBetween('tasks.scheduled_date', [Carbon::now()->subDays(30), Carbon::now()]);
                break;
            case 'All':
            // For 'All' or any other value, we simply don't add a date filter
            default:
                break;
        }

        $tasksSummary = $tasksSummaryQuery->select(
                DB::raw("COUNT(CASE WHEN tasks.status = 'Completed' THEN 1 END) as done"),
                DB::raw("COUNT(CASE WHEN tasks.status = 'In Progress' THEN 1 END) as inProgress"),
                DB::raw("COUNT(CASE WHEN tasks.status IN ('Pending', 'Scheduled') THEN 1 END) as toDo")
            )
            ->first();

        // --- Fetch holidays for calendar display ---
        $holidays = Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        // --- Check if employee has ANY attendance record for today ---
        // This matches the validation logic in AttendanceController
        $todayAttendance = \App\Models\Attendance::where('employee_id', $employee->id)
            ->whereDate('clock_in', Carbon::today())
            ->first();

        // Check if there's an active clock-in (not clocked out yet)
        $isClockedIn = $todayAttendance && $todayAttendance->clock_out === null;

        // Check if employee already has attendance record for today (prevents duplicate clock-ins)
        $hasAttendanceToday = $todayAttendance !== null;

        // --- Fetch Recent Employee Requests (exclude cancelled) ---
        $employeeRequests = EmployeeRequest::where('employee_id', $employee->id)
            ->where('status', '!=', 'Cancelled')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($request) {
                return [
                    'id' => $request->id,
                    'type' => $request->absence_type,
                    'date' => Carbon::parse($request->absence_date)->format('M d, Y'),
                    'time_range' => $request->time_range,
                    'from_time' => $request->from_time ? Carbon::parse($request->from_time)->format('h:i A') : null,
                    'to_time' => $request->to_time ? Carbon::parse($request->to_time)->format('h:i A') : null,
                    'reason' => $request->reason,
                    'description' => $request->description,
                    'status' => $request->status,
                    'proof_document' => $request->proof_document,
                    'created_at' => $request->created_at->format('M d, Y h:i A'),
                ];
            })->toArray();

        // --- Lessons: Get course progress for the logged-in user ---
        $courseInfo = [
            1 => ['title' => 'Deep Cleaning Fundamentals', 'description' => 'Master the essential techniques of deep cleaning for residential and commercial spaces', 'duration' => '8 lectures • 1.5 hours'],
            2 => ['title' => 'Professional Window Cleaning', 'description' => 'Learn advanced window cleaning methods and safety protocols', 'duration' => '12 lectures • 2 hours'],
            3 => ['title' => 'Eco-Friendly Cleaning Solutions', 'description' => 'Discover sustainable and environmentally safe cleaning methods', 'duration' => '10 lectures • 1.5 hours'],
            4 => ['title' => 'Industrial Floor Care & Maintenance', 'description' => 'Master the art of maintaining various floor types', 'duration' => '15 lectures • 3 hours'],
            5 => ['title' => 'Sanitization & Disinfection Protocols', 'description' => 'Learn industry-standard sanitization practices', 'duration' => '14 lectures • 2.5 hours'],
        ];

        $watchedLessons = UserCourseProgress::where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->get()
            ->map(function ($record) use ($courseInfo) {
                $info = $courseInfo[$record->course_id] ?? null;
                if (!$info) return null;

                return [
                    'course_id' => $record->course_id,
                    'title' => $info['title'],
                    'description' => $info['description'],
                    'duration' => $info['duration'],
                    'progress' => $record->progress,
                    'status' => $record->status,
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        return view('employee.dashboard', [
            'employee' => $employee,
            'dailySchedule' => $dailySchedule,
            'todoList' => $todoList,
            'tasksSummary' => (array) $tasksSummary,
            'period' => $period,
            'holidays' => $holidays,
            'isClockedIn' => $isClockedIn,
            'hasAttendanceToday' => $hasAttendanceToday,
            'employeeRequests' => $employeeRequests,
            'watchedLessons' => $watchedLessons,
        ]);
    }
}