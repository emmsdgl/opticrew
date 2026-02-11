<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Employee;
use App\Models\Location;
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

        // Get tasks for this contracted client
        $clientId = $user->id;

        // Today's tasks
        $todayTasks = Task::where('client_id', $clientId)
            ->whereDate('scheduled_date', $today)
            ->with(['location', 'assignedEmployees'])
            ->orderBy('scheduled_time')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'todayTasks' => Task::where('client_id', $clientId)
                ->whereDate('scheduled_date', $today)
                ->count(),
            'completedToday' => Task::where('client_id', $clientId)
                ->whereDate('scheduled_date', $today)
                ->where('status', 'Completed')
                ->count(),
            'onDuty' => Employee::whereHas('tasks', function ($query) use ($clientId, $today) {
                $query->where('client_id', $clientId)
                    ->whereDate('scheduled_date', $today)
                    ->whereIn('status', ['In Progress', 'Scheduled']);
            })->count(),
            'totalEmployees' => Employee::whereHas('tasks', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })->count(),
            'weekTasks' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->count(),
            'locations' => Location::where('contracted_client_id', $clientId)->count(),
        ];

        // Task overview for the week
        $taskOverview = [
            'completed' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'Completed')
                ->count(),
            'inProgress' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'In Progress')
                ->count(),
            'scheduled' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->whereIn('status', ['Scheduled', 'Pending'])
                ->count(),
            'onHold' => Task::where('client_id', $clientId)
                ->whereBetween('scheduled_date', [$startOfWeek, $endOfWeek])
                ->where('status', 'On Hold')
                ->count(),
        ];

        // Recent activity (placeholder - can be enhanced with actual activity logging)
        $recentActivity = [
            [
                'icon' => 'check-circle',
                'message' => 'Task completed at Location A',
                'time' => '2 hours ago',
            ],
            [
                'icon' => 'user-plus',
                'message' => 'New employee assigned to task',
                'time' => '4 hours ago',
            ],
            [
                'icon' => 'calendar-plus',
                'message' => 'New task scheduled for tomorrow',
                'time' => 'Yesterday',
            ],
        ];

        return view('manager.dashboard', compact(
            'todayTasks',
            'stats',
            'taskOverview',
            'recentActivity'
        ));
    }
}
