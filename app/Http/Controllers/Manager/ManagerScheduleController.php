<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ManagerScheduleController extends Controller
{
    /**
     * Display the schedule page.
     */
    public function index()
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Get location types with counts
        $locationTypes = Location::where('contracted_client_id', $clientId)
            ->selectRaw('location_type, COUNT(*) as count')
            ->groupBy('location_type')
            ->pluck('count', 'location_type')
            ->toArray();

        return view('manager.schedule', compact('locationTypes'));
    }

    /**
     * Get tasks for a specific date (API endpoint).
     */
    public function getTasks(Request $request)
    {
        $user = Auth::user();
        $clientId = $user->id;
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $tasks = Task::where('client_id', $clientId)
            ->whereDate('scheduled_date', $date)
            ->with(['location', 'assignedEmployees'])
            ->orderBy('scheduled_time')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'location_name' => $task->location->name ?? 'Unknown Location',
                    'scheduled_time' => $task->scheduled_time ? Carbon::parse($task->scheduled_time)->format('H:i') : null,
                    'duration' => $task->duration,
                    'status' => $task->status,
                    'employee_count' => $task->assignedEmployees->count(),
                ];
            });

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Get locations for the manager (API endpoint).
     */
    public function getLocations()
    {
        $user = Auth::user();
        $clientId = $user->id;

        $locations = Location::where('contracted_client_id', $clientId)
            ->orderBy('location_name')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'type' => $location->location_type,
                    'duration' => $location->base_cleaning_duration_minutes,
                ];
            });

        return response()->json(['locations' => $locations]);
    }

    /**
     * Store a new task (API endpoint).
     */
    public function storeTask(Request $request)
    {
        $user = Auth::user();
        $clientId = $user->id;

        // Validate the request
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:locations,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'rate_type' => 'required|in:Normal,Student',
            'service_type' => 'nullable|string',
            'task_description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify the location belongs to this manager
        $location = Location::where('id', $request->location_id)
            ->where('contracted_client_id', $clientId)
            ->first();

        if (!$location) {
            return response()->json([
                'message' => 'Invalid location selected'
            ], 403);
        }

        // Create the task
        $task = Task::create([
            'location_id' => $request->location_id,
            'client_id' => $clientId,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'rate_type' => $request->rate_type,
            'task_description' => $request->task_description ?? ($request->service_type ?? 'Regular Cleaning'),
            'status' => 'Scheduled',
            'duration' => $location->base_cleaning_duration_minutes ?? 60,
            'estimated_duration_minutes' => $location->base_cleaning_duration_minutes ?? 60,
        ]);

        return response()->json([
            'message' => 'Task created successfully',
            'task' => [
                'id' => $task->id,
                'location_name' => $location->name,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'scheduled_time' => $task->scheduled_time,
                'status' => $task->status,
            ]
        ], 201);
    }
}
