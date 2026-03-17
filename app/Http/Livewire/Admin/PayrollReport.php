<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class PayrollReport extends Component
{
    public $startDate;
    public $endDate;
    public $payrollData = [];
    public $baseRate = 13.00; // The confirmed base rate

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
    }

    public function generatePayroll()
    {
        $this->payrollData = [];
        $employees = Employee::all();
    
        foreach ($employees as $employee) {
            // Group attendance records by the date they were clocked in on.
            $attendancesByDate = Attendance::where('employee_id', $employee->id)
                ->whereBetween('clock_in', [$this->startDate, $this->endDate])
                ->get()
                ->groupBy(function($att) {
                    return Carbon::parse($att->clock_in)->toDateString();
                });
    
            $totalPay = 0;
            $totalHours = 0;
            $totalRegularHours = 0;
            $totalOvertimeHours = 0;
            $totalSundayHolidayHours = 0;
    
            foreach ($attendancesByDate as $date => $attendances) {
                // Calculate total minutes worked for this specific day.
                $dailyMinutesWorked = $attendances->sum('total_minutes_worked');
                $dailyHoursWorked = $dailyMinutesWorked / 60;
                
                $clockInDate = new Carbon($date);
                $isSundayOrHoliday = $clockInDate->isSunday() || $this->isPublicHoliday($clockInDate);
    
                // Separate regular hours and overtime hours for the day.
                $regularHoursToday = 0;
                $overtimeHoursToday = 0;
    
                if ($dailyHoursWorked > 8) {
                    $regularHoursToday = 8;
                    $overtimeHoursToday = $dailyHoursWorked - 8;
                } else {
                    $regularHoursToday = $dailyHoursWorked;
                }
                
                // Add to totals (only count non-premium OT here; premium OT handled separately)
                if ($isSundayOrHoliday) {
                    $totalSundayHolidayHours += $dailyHoursWorked;
                } else {
                    $totalRegularHours += $regularHoursToday;
                    $totalOvertimeHours += $overtimeHoursToday;
                }
            }
    
            // --- FINAL PAY CALCULATION ---

            // 1. Regular hours at base rate (€13.00/hr)
            $regularPay = $totalRegularHours * $this->baseRate;

            // 2. Overtime hours at overtime rate (base + €0.50 = €13.50/hr)
            $overtimeRate = $this->baseRate + 0.50;
            $overtimePay = $totalOvertimeHours * $overtimeRate;

            // 3. Sunday/Holiday: double rate for regular hours, double + €0.50 for overtime
            // Note: totalSundayHolidayHours includes both regular and OT on those days
            // We need to split Sunday/Holiday hours into regular vs overtime
            $sundayRegularHours = 0;
            $sundayOvertimeHours = 0;
            foreach ($attendancesByDate as $date => $attendances) {
                $clockInDate = new Carbon($date);
                $isSundayOrHoliday = $clockInDate->isSunday() || $this->isPublicHoliday($clockInDate);
                if (!$isSundayOrHoliday) continue;

                $dailyMinutesWorked = $attendances->sum('total_minutes_worked');
                $dailyHoursWorked = $dailyMinutesWorked / 60;
                if ($dailyHoursWorked > 8) {
                    $sundayRegularHours += 8;
                    $sundayOvertimeHours += $dailyHoursWorked - 8;
                } else {
                    $sundayRegularHours += $dailyHoursWorked;
                }
            }
            $sundayHolidayPay = ($sundayRegularHours * $this->baseRate * 2) + ($sundayOvertimeHours * ($this->baseRate * 2 + 0.50));

            // 4. Sum everything up.
            $totalPay = $regularPay + $overtimePay + $sundayHolidayPay;
            $totalHours = $totalRegularHours + $totalOvertimeHours + $totalSundayHolidayHours;
            
            $this->payrollData[] = [
                'employee_name' => $employee->user->name,
                'total_hours' => round($totalHours, 2),
                'regular_hours' => round($totalRegularHours, 2),
                'overtime_hours' => round($totalOvertimeHours, 2), // New data point for the report
                'sunday_holiday_hours' => round($totalSundayHolidayHours, 2),
                'total_pay' => round($totalPay, 2),
            ];
        }
    }
    
    // Helper function to check for public holidays in Finland (example)
    private function isPublicHoliday(Carbon $date): bool
    {
        $year = $date->year;
        // List of fixed public holidays in Finland
        $publicHolidays = [
            "{$year}-01-01", // New Year's Day
            "{$year}-01-06", // Epiphany
            "{$year}-05-01", // May Day (Vappu)
            "{$year}-12-06", // Independence Day
            "{$year}-12-25", // Christmas Day
            "{$year}-12-26", // St. Stephen's Day
        ];
        // Note: Easter and Midsummer are variable and require more complex calculations.
        
        return in_array($date->toDateString(), $publicHolidays);
    }


    public function render()
    {
        return view('livewire.admin.payroll-report')->layout('layouts.app');
    }
}