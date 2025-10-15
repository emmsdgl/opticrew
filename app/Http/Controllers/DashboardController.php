<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // === ATTENDANCE DATA ===
        $totalEmployees = User::where('role', 'employee')->count();
        $presentEmployees = Attendance::whereDate('clock_in', Carbon::today())
            ->distinct('employee_id')
            ->count('employee_id');
        $absentEmployees = $totalEmployees - $presentEmployees;
        $attendanceRate = ($totalEmployees > 0) ? ($presentEmployees / $totalEmployees) * 100 : 0;

        // === TASK DATA (This is the new part) ===
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
    
        // === PASS ALL DATA TO THE VIEW ===
        return view('admin-dash', [
            'totalEmployees' => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees' => $absentEmployees,
            'attendanceRate' => number_format($attendanceRate, 2),
            'tasks' => $tasks,             // <-- Pass tasks
            'taskCount' => $taskCount,       // <-- Pass taskCount
            'admin' => $admin              // <-- Pass admin info
        ]);
    }
}