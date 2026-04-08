<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerDashboardController extends Controller
{
    /**
     * Display the manager dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $contractedClient = ContractedClient::where('user_id', $user->id)->first();

        if (!$contractedClient) {
            return view('manager.dashboard', [
                'todayTasks' => collect(),
                'allTasks' => collect(),
                'taskDates' => [],
                'holidays' => [],
                'stats' => ['todayTasks' => 0, 'completedToday' => 0, 'onDuty' => 0, 'totalEmployees' => 0, 'weekTasks' => 0, 'locations' => 0],
                'taskOverview' => ['completed' => 0, 'inProgress' => 0, 'scheduled' => 0, 'onHold' => 0],
                'recentActivity' => [],
            ]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Today's tasks (default view)
        $todayTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $today)
            ->with(['location', 'assignedEmployees'])
            ->orderBy('scheduled_time')
            ->get();

        // All upcoming tasks (used by calendar-driven date filter)
        $allTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', '>=', $today->copy()->subDays(7))
            ->whereDate('scheduled_date', '<=', $today->copy()->addDays(60))
            ->with(['location', 'assignedEmployees.user', 'checklistCompletions'])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // List of unique dates that have at least one task (for calendar dot indicators)
        $taskDates = $allTasks
            ->map(fn ($task) => Carbon::parse($task->scheduled_date)->format('Y-m-d'))
            ->unique()
            ->values()
            ->toArray();

        // Holidays (reuse the same source the client dashboard uses, if available)
        $holidays = [];
        if (class_exists(\App\Models\Holiday::class)) {
            $holidays = \App\Models\Holiday::orderBy('date')->get(['date', 'name'])->toArray();
        }

        // Statistics
        $stats = [
            'todayTasks' => Task::whereIn('location_id', $locationIds)
                ->whereDate('scheduled_date', $today)
                ->count(),
            'completedToday' => Task::whereIn('location_id', $locationIds)
                ->whereDate('scheduled_date', $today)
                ->where('status', 'Completed')
                ->count(),
            'onDuty' => Employee::whereHas('tasks', function ($query) use ($locationIds, $today) {
                $query->whereIn('location_id', $locationIds)
                    ->whereDate('scheduled_date', $today)
                    ->whereIn('status', ['In Progress', 'Scheduled']);
            })->count(),
            'totalEmployees' => Employee::whereHas('tasks', function ($query) use ($locationIds) {
                $query->whereIn('location_id', $locationIds);
            })->count(),
            'weekTasks' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->count(),
            'locations' => $contractedClient->locations()->count(),
        ];

        // Task overview for the week
        $taskOverview = [
            'completed' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'Completed')
                ->count(),
            'inProgress' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'In Progress')
                ->count(),
            'scheduled' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->whereIn('status', ['Scheduled', 'Pending'])
                ->count(),
            'onHold' => Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'On Hold')
                ->count(),
        ];

        // Real recent activity from task updates
        $recentActivity = Task::whereIn('location_id', $locationIds)
            ->with('location')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                $locationName = $task->location->name ?? 'Unknown';

                switch ($task->status) {
                    case 'Completed':
                        return ['icon' => 'check-circle', 'message' => "Task completed at {$locationName}", 'time' => $task->updated_at->diffForHumans()];
                    case 'In Progress':
                        return ['icon' => 'spinner', 'message' => "Task in progress at {$locationName}", 'time' => $task->updated_at->diffForHumans()];
                    case 'On Hold':
                        return ['icon' => 'pause', 'message' => "Task on hold at {$locationName}", 'time' => $task->updated_at->diffForHumans()];
                    case 'Cancelled':
                        return ['icon' => 'xmark-circle', 'message' => "Task cancelled at {$locationName}", 'time' => $task->updated_at->diffForHumans()];
                    default:
                        return ['icon' => 'calendar-plus', 'message' => "New task scheduled at {$locationName}", 'time' => $task->updated_at->diffForHumans()];
                }
            })
            ->toArray();

        return view('manager.dashboard', compact(
            'todayTasks',
            'allTasks',
            'taskDates',
            'holidays',
            'stats',
            'taskOverview',
            'recentActivity'
        ));
    }
}
