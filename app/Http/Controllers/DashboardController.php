<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === ATTENDANCE DATA ===
        $totalEmployees = Employee::count();
        $presentEmployees = Attendance::whereDate('clock_in', Carbon::today())
            ->distinct('employee_id')
            ->count('employee_id');
        $absentEmployees = $totalEmployees - $presentEmployees;
        $attendanceRate = ($totalEmployees > 0) ? ($presentEmployees / $totalEmployees) * 100 : 0;

        // === RECENT ARRIVALS DATA ===
        $recentArrivals = Attendance::with('employee.user')
            ->whereDate('clock_in', Carbon::today())
            ->whereNotNull('clock_in')
            ->orderBy('clock_in', 'desc')
            ->take(4)
            ->get();
            
        // === TASK DATA ===
        $tasksFromDb = Task::with(['location.contractedClient', 'client.user'])
            ->orderBy('scheduled_date', 'desc')
            ->take(10)
            ->get();

        $tasks = $tasksFromDb->map(function ($task) {
            $title = 'Task without Client';

            if ($task->location && $task->location->contractedClient) {
                $title = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $title = $task->client->first_name . ' ' . $task->client->last_name;
            }

            return [
                'id' => $task->id,
                'title' => $title,
                'category' => $task->task_description,
                'date' => Carbon::parse($task->scheduled_date)->format('M d'),
                'startTime' => $task->started_at ? Carbon::parse($task->started_at)->format('g:i a') : 'TBD',
                'avatar' => 'https://i.pravatar.cc/30?u=' . $task->id,
                'done' => $task->status === 'Completed',
            ];
        });
        
        $taskCount = Task::count();
    
        // === ADMIN DATA ===
        $admin = Auth::user()->employee;

        // === HOLIDAYS DATA ===
        $holidays = Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        // === PASS ALL DATA TO THE VIEW ===
        return view('admin-dash', [
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'attendanceRate' => number_format($attendanceRate, 2),
            'tasks' => $tasks,
            'taskCount' => $taskCount,
            'admin' => $admin,
            'recentArrivals' => $recentArrivals,
            'holidays' => $holidays // Pass holidays to the view
        ]);
    }
}