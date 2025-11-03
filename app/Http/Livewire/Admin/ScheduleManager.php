<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Employee;
use App\Models\DayOff;
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

    /**
     * Toggle day off for an employee on a specific date
     * Uses the DayOff model (replaces deprecated EmployeeSchedule)
     */
    public function toggleDayOff($employeeId, $date)
    {
        $dayOff = DayOff::where('employee_id', $employeeId)
                        ->where('date', $date)
                        ->first();

        if ($dayOff) {
            // If a day off exists, delete it to make it a work day
            $dayOff->delete();
        } else {
            // If no day off exists, create one with default type 'personal'
            DayOff::create([
                'employee_id' => $employeeId,
                'date' => $date,
                'type' => 'personal',
                'reason' => 'Scheduled via admin panel',
            ]);
        }

        // Component will automatically re-render and call the render() method
    }

    public function render()
    {
        // Fetch all employees
        $employees = Employee::all();

        $startDate = $this->currentDate->copy()->startOfMonth();
        $endDate = $this->currentDate->copy()->endOfMonth();

        // Load all day-offs for the current month and format them for easy checking
        // Key format: 'employee_id-date' for quick lookup in the view
        $schedules = DayOff::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->employee_id . '-' . $item->date;
            });

        return view('livewire.admin.schedule-manager', [
            'employees' => $employees,
            'schedules' => $schedules,
        ])->layout('layouts.app');
    }
}