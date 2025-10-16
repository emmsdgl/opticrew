<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $today = Carbon::today();

        // --- Daily Schedule & To-Do List (Keep the queries from the previous step) ---
        $dailySchedule = DB::table('tasks')
            ->join('locations', 'tasks.location_id', '=', 'locations.id')
            ->join('daily_team_assignments', 'tasks.assigned_team_id', '=', 'daily_team_assignments.id')
            ->join('team_members', 'daily_team_assignments.id', '=', 'team_members.daily_team_id')
            ->where('team_members.employee_id', $employee->id)
            ->whereDate('tasks.scheduled_date', $today)
            ->select('tasks.id', 'tasks.task_description as title', 'locations.location_name as location', 'tasks.status', 'tasks.estimated_duration_minutes as duration', 'tasks.started_at as time')
            ->get()
            ->map(function ($item) {
                $item->duration = $item->duration . ' m';
                $item->time = $item->time ? Carbon::parse($item->time)->format('h:i A') : 'N/A';
                return $item;
            });

        $todoList = DB::table('tasks')
            ->join('locations', 'tasks.location_id', '=', 'locations.id')
            ->join('daily_team_assignments', 'tasks.assigned_team_id', '=', 'daily_team_assignments.id')
            ->join('team_members', 'daily_team_assignments.id', '=', 'team_members.daily_team_id')
            ->where('team_members.employee_id', $employee->id)
            ->whereIn('tasks.status', ['Pending', 'Scheduled', 'In-Progress'])
            ->select('tasks.task_description as title', 'locations.location_name as company', DB::raw("'Cleaning Task' as subtitle"), 'tasks.scheduled_date as date', DB::raw("'N/A' as dueTime"), DB::raw("'bg-blue-100' as iconBg"))
            ->get();

        // --- UPDATED: Tasks Summary Logic ---
        $period = $request->input('period', 'All'); // Get 'period' from URL, default to 'Today'

        $tasksSummaryQuery = DB::table('tasks')
            ->join('daily_team_assignments', 'tasks.assigned_team_id', '=', 'daily_team_assignments.id')
            ->join('team_members', 'daily_team_assignments.id', '=', 'team_members.daily_team_id')
            ->where('team_members.employee_id', $employee->id);

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
                DB::raw("COUNT(CASE WHEN tasks.status = 'In-Progress' THEN 1 END) as inProgress"),
                DB::raw("COUNT(CASE WHEN tasks.status IN ('Pending', 'Scheduled') THEN 1 END) as toDo")
            )
            ->first();

        return view('employee-dash', [
            'employee' => $employee,
            'dailySchedule' => $dailySchedule,
            'todoList' => $todoList,
            'tasksSummary' => (array) $tasksSummary,
            'period' => $period, // Pass the current period back to the view
        ]);
    }
}