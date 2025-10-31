<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\DayOff;
use Carbon\Carbon;

class EmployeePerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Get filter period (default to 'Last 30 days')
        $period = $request->input('period', 'Last 30 days');

        // --- Performance Stats (Tasks) ---
        $tasksQuery = Task::join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id);

        // Apply date filtering
        $tasksQuery = $this->applyDateFilter($tasksQuery, $period, 'tasks.scheduled_date');

        $totalTasksCompleted = (clone $tasksQuery)->where('tasks.status', 'Completed')->count();
        $incompleteTasks = (clone $tasksQuery)->whereIn('tasks.status', ['In Progress', 'On Hold'])->count();
        $pendingTasks = (clone $tasksQuery)->whereIn('tasks.status', ['Pending', 'Scheduled'])->count();

        // --- Recently Completed Tasks (Last 5) ---
        $recentlyCompletedTasks = Task::with(['location.contractedClient', 'optimizationTeam.members.employee.user'])
            ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
            ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
            ->where('optimization_team_members.employee_id', $employee->id)
            ->where('tasks.status', 'Completed')
            ->orderBy('tasks.completed_at', 'desc')
            ->select('tasks.*')
            ->limit(5)
            ->get()
            ->map(function($task) {
                $clientName = 'No Client';
                if ($task->location && $task->location->contractedClient) {
                    $clientName = $task->location->contractedClient->name;
                }

                // Get team members
                $teamMembers = [];
                if ($task->optimizationTeam) {
                    $teamMembers = $task->optimizationTeam->members()
                        ->with('employee.user')
                        ->get()
                        ->map(function($member) {
                            return [
                                'name' => $member->employee && $member->employee->user ? $member->employee->user->name : 'Unknown',
                                'picture' => $member->employee && $member->employee->user ? $member->employee->user->profile_picture : null
                            ];
                        })
                        ->toArray();
                }

                // Calculate percentage (100% if completed)
                $percentage = 100;

                return [
                    'label' => strtoupper(substr($clientName, 0, 1)),
                    'name' => $clientName,
                    'subtitle' => $task->task_description,
                    'color' => $this->getRandomColor(),
                    'start' => $task->scheduled_date ? Carbon::parse($task->scheduled_date)->format('Y-m-d') : null,
                    'end' => $task->completed_at ? Carbon::parse($task->completed_at)->format('Y-m-d') : null,
                    'percentage' => $percentage,
                    'due_date' => $task->completed_at ? Carbon::parse($task->completed_at)->format('Y-m-d') : null,
                    'due_time' => $task->completed_at ? Carbon::parse($task->completed_at)->format('g:i a') : null,
                    'team_name' => $task->optimizationTeam ? $task->optimizationTeam->team_name : 'Team',
                    'team_members' => $teamMembers
                ];
            });

        // --- Attendance Summary (This Month) ---
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $totalWorkingDays = $this->getWorkingDaysInMonth($currentMonth);

        // Get attendance records for current month
        $presentDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
            ->count();

        // Get days off for current month
        $daysOff = DayOff::where('employee_id', $employee->id)
            ->whereBetween('day_off_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Calculate absent days (working days - present days - days off)
        $absentDays = max(0, $totalWorkingDays - $presentDays - $daysOff);

        // On leave (we'll use days_off for now)
        $onLeave = $daysOff;

        $attendanceData = [
            ['label' => 'Present', 'current' => $presentDays, 'total' => $totalWorkingDays, 'color' => 'blue'],
            ['label' => 'Days Off', 'current' => $daysOff, 'total' => $totalWorkingDays, 'color' => 'navy'],
            ['label' => 'Absent', 'current' => $absentDays, 'total' => $totalWorkingDays, 'color' => 'cyan'],
            ['label' => 'On Leave', 'current' => $onLeave, 'total' => $totalWorkingDays, 'color' => 'yellow'],
        ];

        // --- Performance Chart Data (Hours Worked) ---
        $performanceData = $this->getPerformanceChartData($employee->id, $period);

        return view('employee.performance', [
            'employee' => $employee,
            'period' => $period,
            'totalTasksCompleted' => $totalTasksCompleted,
            'incompleteTasks' => $incompleteTasks,
            'pendingTasks' => $pendingTasks,
            'recentlyCompletedTasks' => $recentlyCompletedTasks,
            'attendanceData' => $attendanceData,
            'performanceData' => $performanceData,
        ]);
    }

    private function applyDateFilter($query, $period, $dateColumn)
    {
        switch ($period) {
            case 'Today':
                return $query->whereDate($dateColumn, Carbon::today());
            case 'Yesterday':
                return $query->whereDate($dateColumn, Carbon::yesterday());
            case 'Last 7 days':
                return $query->whereBetween($dateColumn, [Carbon::now()->subDays(7), Carbon::now()]);
            case 'Last 30 days':
                return $query->whereBetween($dateColumn, [Carbon::now()->subDays(30), Carbon::now()]);
            case 'All':
            default:
                return $query;
        }
    }

    private function getPerformanceChartData($employeeId, $period)
    {
        $data = [];

        switch ($period) {
            case 'Today':
                // Get hours worked today broken down by 4-hour intervals
                $data = $this->getTodayHoursData($employeeId);
                break;
            case 'Yesterday':
                $data = $this->getYesterdayHoursData($employeeId);
                break;
            case 'Last 7 days':
                $data = $this->getLastWeekHoursData($employeeId);
                break;
            case 'Last 30 days':
                $data = $this->getLastMonthHoursData($employeeId);
                break;
            case 'All':
            default:
                $data = $this->getAllTimeHoursData($employeeId);
                break;
        }

        return $data;
    }

    private function getTodayHoursData($employeeId)
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('clock_in', Carbon::today())
            ->first();

        $totalMinutes = $attendance ? ($attendance->total_minutes_worked ?? 0) : 0;
        $totalHours = round($totalMinutes / 60, 2);

        return [
            'currentValue' => $totalHours,
            'changeValue' => 0,
            'changePercent' => 0,
            'values' => [$totalHours],
            'labels' => ['Today'],
            'dateRange' => Carbon::today()->format('M d, Y')
        ];
    }

    private function getYesterdayHoursData($employeeId)
    {
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereDate('clock_in', Carbon::yesterday())
            ->first();

        $totalMinutes = $attendance ? ($attendance->total_minutes_worked ?? 0) : 0;
        $totalHours = round($totalMinutes / 60, 2);

        return [
            'currentValue' => $totalHours,
            'changeValue' => 0,
            'changePercent' => 0,
            'values' => [$totalHours],
            'labels' => ['Yesterday'],
            'dateRange' => Carbon::yesterday()->format('M d, Y')
        ];
    }

    private function getLastWeekHoursData($employeeId)
    {
        $days = [];
        $hours = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('clock_in', $date)
                ->first();

            $totalMinutes = $attendance ? ($attendance->total_minutes_worked ?? 0) : 0;
            $hours[] = round($totalMinutes / 60, 2);
            $labels[] = $date->format('D');
        }

        $totalHours = array_sum($hours);

        return [
            'currentValue' => $totalHours,
            'changeValue' => 0,
            'changePercent' => 0,
            'values' => $hours,
            'labels' => $labels,
            'dateRange' => Carbon::now()->subDays(6)->format('M d') . ' - ' . Carbon::now()->format('M d')
        ];
    }

    private function getLastMonthHoursData($employeeId)
    {
        $weeks = [];
        $hours = [];
        $labels = [];

        for ($i = 4; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $totalMinutes = Attendance::where('employee_id', $employeeId)
                ->whereBetween('clock_in', [$startOfWeek, $endOfWeek])
                ->sum('total_minutes_worked');

            $hours[] = round($totalMinutes / 60, 2);
            $labels[] = 'Week ' . (5 - $i);
        }

        $totalHours = array_sum($hours);

        return [
            'currentValue' => $totalHours,
            'changeValue' => 0,
            'changePercent' => 0,
            'values' => $hours,
            'labels' => $labels,
            'dateRange' => Carbon::now()->subWeeks(4)->format('M d') . ' - ' . Carbon::now()->format('M d')
        ];
    }

    private function getAllTimeHoursData($employeeId)
    {
        $months = [];
        $hours = [];
        $labels = [];

        $currentYear = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $totalMinutes = Attendance::where('employee_id', $employeeId)
                ->whereBetween('clock_in', [$startOfMonth, $endOfMonth])
                ->sum('total_minutes_worked');

            $hours[] = round($totalMinutes / 60, 2);
            $labels[] = $startOfMonth->format('M');
        }

        $totalHours = array_sum($hours);

        return [
            'currentValue' => $totalHours,
            'changeValue' => 0,
            'changePercent' => 0,
            'values' => $hours,
            'labels' => $labels,
            'dateRange' => 'Jan - Dec ' . $currentYear
        ];
    }

    private function getWorkingDaysInMonth($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $workingDays = 0;

        while ($startOfMonth->lte($endOfMonth)) {
            // Count Monday to Friday as working days
            if ($startOfMonth->isWeekday()) {
                $workingDays++;
            }
            $startOfMonth->addDay();
        }

        return $workingDays;
    }

    private function getRandomColor()
    {
        $colors = ['#3B82F6', '#9333EA', '#EC4899', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'];
        return $colors[array_rand($colors)];
    }
}
