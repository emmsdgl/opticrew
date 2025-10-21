<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use Carbon\Carbon;

class EmployeeTasksController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $today = Carbon::today()->toDateString();

        // Get today's tasks with optimization team relationships
        $todayTasks = Task::where('scheduled_date', $today)
            ->whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->with(['location', 'optimizationTeam.members.employee', 'optimizationTeam.car'])
            ->orderBy('scheduled_date')
            ->get();

        // Get upcoming tasks
        $upcomingTasks = Task::where('scheduled_date', '>', $today)
            ->whereHas('optimizationTeam.members', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->with(['location', 'optimizationTeam.members.employee', 'optimizationTeam.car'])
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();

        return view('employee-tasks', [
            'employee' => $employee,
            'todayTasks' => $todayTasks,
            'upcomingTasks' => $upcomingTasks,
        ]);
    }
}
