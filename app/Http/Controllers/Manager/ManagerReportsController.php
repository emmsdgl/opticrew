<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Location;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerReportsController extends Controller
{
    private function getContractedClient()
    {
        return ContractedClient::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Display the reports page.
     */
    public function index(Request $request)
    {
        $contractedClient = $this->getContractedClient();

        if (!$contractedClient) {
            return view('manager.reports', [
                'summary' => ['totalTasks' => 0, 'completionRate' => 0, 'inProgress' => 0, 'totalHours' => 0],
                'tasksByLocation' => collect(),
                'topPerformers' => [],
                'chartData' => ['labels' => [], 'completed' => [], 'scheduled' => []],
                'period' => $request->get('period', 'month'),
            ]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');
        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->getPeriodDates($period);

        $summary = [
            'totalTasks' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->count(),
            'completionRate' => $this->calculateCompletionRate($locationIds, $startDate, $endDate),
            'inProgress' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->where('status', 'In Progress')
                ->count(),
            'totalHours' => round(Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->sum('duration') / 60, 1),
        ];

        $tasksByLocation = Location::where('contracted_client_id', $contractedClient->id)
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
                        ? round(($location->tasks_count / $summary['totalTasks']) * 100) : 0,
                ];
            });

        $topPerformers = $this->getTopPerformers($locationIds, $startDate, $endDate);
        $chartData = $this->getChartData($locationIds, $startDate, $endDate);

        return view('manager.reports', compact('summary', 'tasksByLocation', 'topPerformers', 'chartData', 'period'));
    }

    /**
     * Generate billing report data (AJAX).
     */
    public function billingReport(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $locationIds = $contractedClient->locations()->pluck('id');
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $tasks = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->with('location')
            ->orderBy('scheduled_date')
            ->get();

        $totalAmount = $tasks->sum('price');
        $totalHours = round($tasks->sum('duration') / 60, 1);

        $tasksByDate = $tasks->groupBy(function ($task) {
            return Carbon::parse($task->scheduled_date)->format('Y-m-d');
        })->map(function ($dayTasks, $date) {
            return [
                'date' => Carbon::parse($date)->format('M d, Y'),
                'tasks' => $dayTasks->map(function ($task) {
                    return [
                        'location' => $task->location->name ?? 'Unknown',
                        'description' => $task->task_description ?? 'Cleaning Service',
                        'duration' => $task->duration ?? 0,
                        'price' => number_format($task->price ?? 0, 2),
                    ];
                }),
                'subtotal' => number_format($dayTasks->sum('price'), 2),
            ];
        })->values();

        return response()->json([
            'billing' => [
                'company' => $contractedClient->name,
                'period' => Carbon::parse($request->start_date)->format('M d, Y') . ' - ' . Carbon::parse($request->end_date)->format('M d, Y'),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_tasks' => $tasks->count(),
                'total_hours' => $totalHours,
                'total_amount' => number_format($totalAmount, 2),
                'tasks_by_date' => $tasksByDate,
            ],
        ]);
    }

    /**
     * Generate billing PDF.
     */
    public function billingPdf(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            abort(404, 'No contracted client found');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $locationIds = $contractedClient->locations()->pluck('id');
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $tasks = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->with('location')
            ->orderBy('scheduled_date')
            ->get();

        $totalAmount = $tasks->sum('price');
        $totalHours = round($tasks->sum('duration') / 60, 1);

        $html = view('manager.billing-pdf', [
            'company' => $contractedClient->name,
            'period' => Carbon::parse($request->start_date)->format('M d, Y') . ' - ' . Carbon::parse($request->end_date)->format('M d, Y'),
            'tasks' => $tasks,
            'totalAmount' => number_format($totalAmount, 2),
            'totalHours' => $totalHours,
            'generatedAt' => now()->format('M d, Y H:i'),
        ])->render();

        return response($html)->header('Content-Type', 'text/html');
    }

    private function getPeriodDates($period)
    {
        switch ($period) {
            case 'week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'quarter':
                return [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()];
            case 'year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }

    private function calculateCompletionRate($locationIds, $startDate, $endDate)
    {
        $total = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->count();
        if ($total === 0) return 0;

        $completed = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->count();

        return round(($completed / $total) * 100);
    }

    private function getTopPerformers($locationIds, $startDate, $endDate)
    {
        return Employee::whereHas('tasks', function ($query) use ($locationIds, $startDate, $endDate) {
            $query->whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate]);
        })
        ->with('user')
        ->get()
        ->map(function ($employee) use ($locationIds, $startDate, $endDate) {
            $totalTasks = Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })->count();

            $completedTasks = Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startDate, $endDate])
                ->where('status', 'Completed')
                ->whereHas('assignedEmployees', function ($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                })->count();

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

    private function getChartData($locationIds, $startDate, $endDate)
    {
        $labels = [];
        $completed = [];
        $scheduled = [];

        $current = $startDate->copy();
        $weekNum = 1;

        while ($current <= $endDate) {
            $weekEnd = $current->copy()->endOfWeek();
            if ($weekEnd > $endDate) $weekEnd = $endDate->copy();

            $labels[] = 'Week ' . $weekNum;

            $completed[] = Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$current, $weekEnd])
                ->where('status', 'Completed')
                ->count();

            $scheduled[] = Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$current, $weekEnd])
                ->count();

            $current = $weekEnd->copy()->addDay();
            $weekNum++;
        }

        return ['labels' => $labels, 'completed' => $completed, 'scheduled' => $scheduled];
    }
}
