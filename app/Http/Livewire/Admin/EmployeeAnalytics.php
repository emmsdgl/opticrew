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
    
        foreach ($employees as $employee) {
            // Find all team IDs this employee has ever been a part of.
            $teamIds = TeamMember::where('employee_id', $employee->id)->pluck('daily_team_id');
            
            // Find all COMPLETED tasks assigned to those teams.
            $completedTasks = Task::whereIn('assigned_team_id', $teamIds)
                                  ->where('status', 'Completed')
                                  ->with('performanceHistory') // Eager load the history
                                  ->get();
            
            // Now, we attach the tasks themselves, not just the history.
            $employee->completedTasks = $completedTasks;
        }
    
        return view('livewire.admin.employee-analytics', [
            'employees' => $employees,
        ])->layout('layouts.app');
    }
}
