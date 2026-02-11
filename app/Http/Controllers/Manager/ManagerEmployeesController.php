<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerEmployeesController extends Controller
{
    /**
     * Display the employees page.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Get employees who have been assigned to tasks for this client
        $employees = Employee::whereHas('tasks', function ($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })
        ->with('user')
        ->get()
        ->map(function ($employee) use ($clientId) {
            // Calculate efficiency and completed tasks for this client
            $totalTasks = Task::where('client_id', $clientId)
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count();

            $completedTasks = Task::where('client_id', $clientId)
                ->where('status', 'Completed')
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count();

            $employee->completed_tasks = $completedTasks;
            $employee->efficiency = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            $employee->years_experience = $employee->years_of_experience ?? 0;

            return $employee;
        });

        return view('manager.employees', compact('employees'));
    }
}
