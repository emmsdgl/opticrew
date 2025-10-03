<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use App\Models\Task;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\TaskPerformanceHistory; // <-- ADD THIS AT THE TOP


class Dashboard extends Component
{
    public $tasks;
    public $futureTasks; // <-- ADD THIS NEW PROPERTY
    public $schedules;
    public $currentDate;

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->loadTasksAndSchedule();
    }

    public function loadTasksAndSchedule()
    {
        $employeeId = Auth::user()->employee->id;
        $today = Carbon::today()->toDateString();

        // --- QUERY 1: GET ONLY TODAY'S TASKS ---
        $this->tasks = Task::where('scheduled_date', $today)
            ->whereHas('team.members', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->with(['location', 'team.members.employee', 'team.car']) // <-- ADD 'team.car'
            ->orderBy('scheduled_date') // Order by date
            ->get();

        // --- QUERY 2: GET ALL FUTURE TASKS ---
        $this->futureTasks = Task::where('scheduled_date', '>', $today)
            ->whereHas('team.members', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->with(['location', 'team.members.employee', 'team.car']) // <-- ADD 'team.car'
            ->orderBy('scheduled_date') // Order by date
            ->get();

        // Load the schedule for the current month for this employee only
        $startDate = $this->currentDate->copy()->startOfMonth();
        $endDate = $this->currentDate->copy()->endOfMonth();

        $this->schedules = EmployeeSchedule::where('employee_id', $employeeId)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->get()
            ->keyBy('work_date');
    }

    public function startTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->status === 'Scheduled') {
            // When a task starts, record the current time
            $task->update([
                'status' => 'In-Progress',
                'started_at' => Carbon::now() // <-- RECORD START TIME
            ]);
            $this->loadTasksAndSchedule();
        }
    }

    public function completeTask($taskId)
    {
        $task = Task::find($taskId);
        // Ensure the task is In-Progress and has a start time
        if ($task && $task->status === 'In-Progress' && $task->started_at) {
            $completedAt = Carbon::now();
            
            // Calculate the actual duration in minutes
            $startedAt = new Carbon($task->started_at);
            $actualDuration = $startedAt->diffInMinutes($completedAt);

            // Update the task status
            $task->update(['status' => 'Completed']);

            // CREATE THE PERFORMANCE HISTORY RECORD
            TaskPerformanceHistory::create([
                'task_id' => $task->id,
                'estimated_duration_minutes' => $task->estimated_duration_minutes,
                'actual_duration_minutes' => $actualDuration,
                'completed_at' => $completedAt,
            ]);

            $this->loadTasksAndSchedule();
        }
    }

    public function render()
    {
        return view('livewire.employee.dashboard')
                ->layout('layouts.app');
    }
}
