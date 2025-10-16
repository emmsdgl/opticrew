<?php

namespace App\Http\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Employee;
use Livewire\Component;
use Carbon\Carbon;

class AttendanceChart extends Component
{
    public $totalEmployees;
    public $presentEmployees;
    public $absentEmployees;
    public $attendanceRate;

    public function mount()
    {
        $this->loadAttendanceData();
    }

    public function render()
    {
        $this->loadAttendanceData();
        return view('livewire.admin.attendance-chart');
    }

    public function loadAttendanceData()
    {
        $today = Carbon::today();

        // Get total number of employees
        $this->totalEmployees = Employee::count();

        // Get employees who clocked in today
        $this->presentEmployees = Attendance::whereDate('clock_in', $today)
            ->distinct('employee_id')
            ->count('employee_id');

        // Calculate absent employees
        $this->absentEmployees = $this->totalEmployees - $this->presentEmployees;

        // Calculate attendance rate
        $this->attendanceRate = $this->totalEmployees > 0 
            ? round(($this->presentEmployees / $this->totalEmployees) * 100, 1) 
            : 0;
    }

    public function refresh()
    {
        $this->loadAttendanceData();
    }
}