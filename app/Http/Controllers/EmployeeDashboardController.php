<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Holiday;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $today = Carbon::today();

        // --- Daily Schedule & To-Do List (Updated to use NEW optimization_teams tables) ---
        $dailySchedule = DB::table('tasks')
            ->join('locations', 'tasks.location_id', '=', 'locations.id')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
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
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
            ->whereIn('tasks.status', ['Pending', 'Scheduled', 'In Progress'])
            ->select('tasks.task_description as title', 'locations.location_name as company', DB::raw("'Cleaning Task' as subtitle"), 'tasks.scheduled_date as date', DB::raw("'N/A' as dueTime"), DB::raw("'bg-blue-100' as iconBg"))
            ->get();

        // --- UPDATED: Tasks Summary Logic ---
        $period = $request->input('period', 'All'); // Get 'period' from URL, default to 'Today'

        $tasksSummaryQuery = DB::table('tasks')
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id);

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

        return view('employee.dashboard', [
            'employee' => $employee,
            'dailySchedule' => $dailySchedule,
            'todoList' => $todoList,
            'tasksSummary' => (array) $tasksSummary,
            'period' => $period, // Pass the current period back to the view
            'holidays' => $holidays, // Pass holidays to the view
        ]);
    }
}