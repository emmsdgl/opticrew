<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use App\Models\Task;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\TaskPerformanceHistory;
use App\Models\Attendance;


class Dashboard extends Component
{
    public $tasks;
    public $futureTasks;
    public $schedules;
    public $currentDate;
    public $currentAttendance;

    // Hold Task Modal Properties
    public $showHoldModal = false;
    public $holdTaskId = null;
    public $holdReason = '';

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->loadCurrentAttendance();
        $this->loadTasksAndSchedule();
    }

    // New method to check the current clock-in status
    public function loadCurrentAttendance()
    {
        $employeeId = Auth::user()->employee->id;
        $this->currentAttendance = Attendance::where('employee_id', $employeeId)
            ->whereNull('clock_out') // Find an open attendance record
            ->latest('clock_in')
            ->first();
    }

    // New method for the clock-in button
    public function clockIn()
    {
        Attendance::create([
            'employee_id' => Auth::user()->employee->id,
            'clock_in' => now(),
        ]);
        $this->loadCurrentAttendance(); // Refresh the status
    }

    // New method for the clock-out button
    public function clockOut()
    {
        if ($this->currentAttendance) {
            $clockInTime = new Carbon($this->currentAttendance->clock_in);
            $clockOutTime = now();
            $minutesWorked = $clockInTime->diffInMinutes($clockOutTime);

            $this->currentAttendance->update([
                'clock_out' => $clockOutTime,
                'total_minutes_worked' => $minutesWorked,
            ]);
            $this->loadCurrentAttendance(); // Refresh the status
        }
    }

    public function loadTasksAndSchedule()
    {
        $employeeId = Auth::user()->employee->id;
        $today = Carbon::today()->toDateString();

        // --- QUERY 1: GET ONLY TODAY'S TASKS ---
        $this->tasks = Task::where('scheduled_date', $today)
            ->whereHas('optimizationTeam.members', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->with(['location', 'optimizationTeam.members.employee', 'optimizationTeam.car'])
            ->orderBy('scheduled_date')
            ->get();

        // --- QUERY 2: GET ALL FUTURE TASKS ---
        $this->futureTasks = Task::where('scheduled_date', '>', $today)
            ->whereHas('optimizationTeam.members', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->with(['location', 'optimizationTeam.members.employee', 'optimizationTeam.car'])
            ->orderBy('scheduled_date')
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
        try {
            // Call API endpoint
            $response = Http::post("/api/tasks/{$taskId}/start");

            if ($response->successful()) {
                $this->loadTasksAndSchedule();
                session()->flash('success', 'Task started successfully!');
            } else {
                session()->flash('error', 'Failed to start task.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Open hold task modal
     */
    public function openHoldModal($taskId)
    {
        $this->holdTaskId = $taskId;
        $this->holdReason = '';
        $this->showHoldModal = true;
    }

    /**
     * Close hold task modal
     */
    public function closeHoldModal()
    {
        $this->showHoldModal = false;
        $this->holdTaskId = null;
        $this->holdReason = '';
    }

    /**
     * Submit hold task with reason
     */
    public function submitHoldTask()
    {
        $this->validate([
            'holdReason' => 'required|min:3|max:255'
        ]);

        try {
            // Call API endpoint
            $response = Http::post("/api/tasks/{$this->holdTaskId}/hold", [
                'reason' => $this->holdReason
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                $alertTriggered = $data['alert_triggered'] ?? false;

                $this->closeHoldModal();
                $this->loadTasksAndSchedule();

                if ($alertTriggered) {
                    session()->flash('warning', 'Task put on hold. Admin has been notified of the delay.');
                } else {
                    session()->flash('success', 'Task put on hold successfully.');
                }
            } else {
                session()->flash('error', 'Failed to put task on hold.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function completeTask($taskId)
    {
        try {
            // Call API endpoint
            $response = Http::post("/api/tasks/{$taskId}/complete");

            if ($response->successful()) {
                $data = $response->json('data');
                $performanceFlagged = $data['performance_flagged'] ?? false;

                $this->loadTasksAndSchedule();

                if ($performanceFlagged) {
                    session()->flash('warning', 'Task completed! Duration exceeded estimate.');
                } else {
                    session()->flash('success', 'Task completed successfully!');
                }
            } else {
                $message = $response->json('message') ?? 'Failed to complete task.';
                session()->flash('error', $message);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.dashboard')
                ->layout('layouts.app');
    }
}
