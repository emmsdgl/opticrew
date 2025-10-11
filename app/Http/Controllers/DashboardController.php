<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Get the total number of employees (expected workforce)
        // Adjust the query if you need to filter by role, etc.
        $totalEmployees = User::where('role', 'employee')->count();

        // 2. Get the number of employees present today
        $presentEmployees = Attendance::whereDate('created_at', Carbon::today())
                                    ->distinct('employee_id')
                                    ->count('employee_id');

        // 3. Calculate absent employees
        $absentEmployees = $totalEmployees - $presentEmployees;

        // 4. Calculate attendance rate (handle division by zero)
        $attendanceRate = ($totalEmployees > 0) ? ($presentEmployees / $totalEmployees) * 100 : 0;

        // 5. Pass all the data to the view
        return view('admin-dash', [
            'totalEmployees'   => $totalEmployees,
            'presentEmployees' => $presentEmployees,
            'absentEmployees'  => $absentEmployees,
            'attendanceRate'   => $attendanceRate,
        ]);
    }
}