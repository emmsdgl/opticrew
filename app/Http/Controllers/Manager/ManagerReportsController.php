<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerReportsController extends Controller
{
    /**
     * Display the reports page.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Default to this month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Summary statistics
        $summary = [
            'totalTasks' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->count(),
            'completionRate' => $this->calculateCompletionRate($clientId, $startDate, $endDate),
            'inProgress' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->where('status', 'In Progress')
                ->count(),
            'totalHours' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->sum('duration') / 60, // Convert minutes to hours
        ];

        // Tasks by location
        $tasksByLocation = Location::where('contracted_client_id', $clientId)
            ->withCount(['tasks' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('scheduled_date', [$startDate, $endDate]);
            }])
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get()
            ->map(function ($location) use ($summary) {
                return [
                    'name' => $location->name,
                    'count' => $location->tasks_count,
                    'percentage' => $summary['totalTasks'] > 0
                        ? round(($location->tasks_count / $summary['totalTasks']) * 100)
                        : 0,
                ];
            });

        // Top performers
        $topPerformers = $this->getTopPerformers($clientId, $startDate, $endDate);

        // Chart data for the month
        $chartData = $this->getChartData($clientId, $startDate, $endDate);

        return view('manager.reports', compact(
            'summary',
            'tasksByLocation',
            'topPerformers',
            'chartData'
        ));
    }

    private function calculateCompletionRate($clientId, $startDate, $endDate)
    {
        $total = Task::where('client_id', $clientId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->count();

        if ($total === 0) return 0;

        $completed = Task::where('client_id', $clientId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->count();

        return round(($completed / $total) * 100);
    }

    private function getTopPerformers($clientId, $startDate, $endDate)
    {
        return Employee::whereHas('tasks', function ($query) use ($clientId, $startDate, $endDate) {
            $query->where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate]);
        })
        ->with('user')
        ->get()
        ->map(function ($employee) use ($clientId, $startDate, $endDate) {
            $totalTasks = Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count();

            $completedTasks = Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->where('status', 'Completed')
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })
                ->count();

            return [
                'name' => $employee->user->name ?? 'Unknown',
                'tasksCompleted' => $completedTasks,
                'efficiency' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            ];
        })
        ->sortByDesc('efficiency')
        ->take(3)
        ->values()
        ->toArray();
    }

    private function getChartData($clientId, $startDate, $endDate)
    {
        $labels = [];
        $completed = [];
        $scheduled = [];

        // Get weekly data for the month
        $current = $startDate->copy();
        $weekNum = 1;

        while ($current <= $endDate) {
            $weekEnd = $current->copy()->endOfWeek();
            if ($weekEnd > $endDate) {
                $weekEnd = $endDate->copy();
            }

            $labels[] = 'Week ' . $weekNum;

            $completed[] = Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$current, $weekEnd])
                ->where('status', 'Completed')
                ->count();

            $scheduled[] = Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$current, $weekEnd])
                ->count();

            $current = $weekEnd->copy()->addDay();
            $weekNum++;
        }

        return [
            'labels' => $labels,
            'completed' => $completed,
            'scheduled' => $scheduled,
        ];
    }
}
