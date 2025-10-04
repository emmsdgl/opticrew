<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Employee;
use App\Models\TeamMember;              // <-- ADD THIS LINE
use App\Models\Task;                     // <-- ADD THIS LINE
use App\Models\TaskPerformanceHistory; // <-- ADD THIS LINE

class EmployeeAnalytics extends Component
{
    public function render()
    {
        $employees = Employee::with(['schedules' => function ($query) {
            $query->where('is_day_off', true)->whereMonth('work_date', now()->month);
        }])->get();
    
        // After loading employees, we will manually attach their performance history.
        // This is more explicit and easier to debug.
        foreach ($employees as $employee) {
            // Find all team IDs this employee has ever been a part of.
            $teamIds = TeamMember::where('employee_id', $employee->id)->pluck('daily_team_id');
            
            // Find all tasks assigned to those teams.
            $taskIds = Task::whereIn('assigned_team_id', $teamIds)->pluck('id');
            
            // Now, get all performance history for those tasks.
            $employee->performanceHistories = TaskPerformanceHistory::whereIn('task_id', $taskIds)->get();
        }
    
        return view('livewire.admin.employee-analytics', [
            'employees' => $employees,
        ])->layout('layouts.app');
    }
}
