<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerActivityController extends Controller
{
    /**
     * Display the activity page.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Get recent activities based on task updates
        // This is a simplified version - you can enhance with actual activity logging
        $recentTasks = Task::where('client_id', $clientId)
            ->orderBy('updated_at', 'desc')
            ->limit(30)
            ->get();

        $activities = $recentTasks->map(function ($task) {
            $type = 'task';
            $icon = 'list-check';
            $status = null;

            switch ($task->status) {
                case 'Completed':
                    $icon = 'check-circle';
                    $status = 'completed';
                    $title = 'Task Completed';
                    $description = "Task at {$task->location->name} was completed";
                    break;
                case 'In Progress':
                    $icon = 'spinner';
                    $status = 'pending';
                    $title = 'Task Started';
                    $description = "Task at {$task->location->name} is in progress";
                    break;
                case 'On Hold':
                    $icon = 'pause';
                    $type = 'warning';
                    $title = 'Task On Hold';
                    $description = "Task at {$task->location->name} was put on hold";
                    break;
                default:
                    $title = 'Task Updated';
                    $description = "Task at {$task->location->name} was updated";
            }

            return [
                'type' => $type,
                'icon' => $icon,
                'title' => $title,
                'description' => $description,
                'time' => Carbon::parse($task->updated_at)->diffForHumans(),
                'status' => $status,
            ];
        });

        return view('manager.activity', compact('activities'));
    }
}
