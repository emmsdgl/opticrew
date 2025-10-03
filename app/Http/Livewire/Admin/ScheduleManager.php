<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use Carbon\Carbon;

class ScheduleManager extends Component
{
    public $currentDate;

    public function mount()
    {
        // Only set the date to today if it hasn't been set already
        if (!$this->currentDate) {
            $this->currentDate = Carbon::now()->startOfMonth();
        }
    }

    public function previousMonth()
    {
        $this->currentDate->subMonth();
    }



    public function nextMonth()
    {
        $this->currentDate->addMonth();
    }

    public function toggleDayOff($employeeId, $date)
    {
        $schedule = EmployeeSchedule::where('employee_id', $employeeId)
                                    ->where('work_date', $date)
                                    ->first();

        if ($schedule) {
            // If a day off exists, delete it to make it a work day.
            $schedule->delete();
        } else {
            // If no day off exists, create one.
            EmployeeSchedule::create([
                'employee_id' => $employeeId,
                'work_date' => $date,
                'is_day_off' => true,
            ]);
        }
        
        // We no longer need to call loadSchedules() here.
        // The component will automatically re-render and call the render() method.
    }

    public function render()
    {
        // --- THIS IS THE KEY CHANGE ---
        // We fetch all the data needed for the view directly inside the render method.
        // This guarantees that every time the component updates, we get the latest data.
        
        $employees = Employee::all();
        
        $startDate = $this->currentDate->copy()->startOfMonth();
        $endDate = $this->currentDate->copy()->endOfMonth();

        // Load all "day off" schedules for the current month and format them for easy checking.
        $schedules = EmployeeSchedule::whereBetween('work_date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->employee_id . '-' . $item->work_date;
            });
            
        return view('livewire.admin.schedule-manager', [
            'employees' => $employees,
            'schedules' => $schedules,
        ])->layout('layouts.app');
    }
}