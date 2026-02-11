<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerHistoryController extends Controller
{
    /**
     * Display the history page.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Get completed tasks as services
        $completedTasks = Task::where('client_id', $clientId)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->with('location')
            ->orderBy('scheduled_date', 'desc')
            ->paginate(10);

        $services = $completedTasks->map(function ($task) {
            // Determine service type based on task or location
            $type = 'default';
            $icon = 'broom';

            if (stripos($task->description ?? '', 'deep clean') !== false) {
                $type = 'deep_clean';
                $icon = 'broom';
            } elseif (stripos($task->description ?? '', 'snow') !== false) {
                $type = 'snow';
                $icon = 'snowflake';
            } elseif (stripos($task->description ?? '', 'daily') !== false) {
                $type = 'daily';
                $icon = 'calendar-day';
            }

            return [
                'id' => $task->id,
                'name' => $task->location->name ?? 'Service',
                'type' => $type,
                'icon' => $icon,
                'location' => $task->location->address ?? '',
                'date' => Carbon::parse($task->scheduled_date)->format('M d, Y'),
                'price' => number_format($task->price ?? 0, 2) . ' EUR',
                'status' => strtolower($task->status),
                'reviewed' => false, // Can be enhanced with actual review tracking
            ];
        });

        return view('manager.history', compact('services'));
    }
}
