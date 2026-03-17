<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerEmployeesController extends Controller
{
    /**
     * Display the employees page.
     */
    public function index()
    {
        $contractedClient = ContractedClient::where('user_id', Auth::user()->id)->first();

        if (!$contractedClient) {
            return view('manager.employees', ['employees' => collect()]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Get employees who have been assigned to tasks at this client's locations
        $employees = Employee::whereHas('tasks', function ($query) use ($locationIds) {
            $query->whereIn('location_id', $locationIds);
        })
        ->with('user')
        ->get()
        ->map(function ($employee) use ($locationIds) {
            $totalTasks = Task::whereIn('location_id', $locationIds)
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count();

            $completedTasks = Task::whereIn('location_id', $locationIds)
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
