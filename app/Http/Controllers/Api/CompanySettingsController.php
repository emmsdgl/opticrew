<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompanySettingsController extends Controller
{
    /**
     * Get geofencing settings - Returns contracted client's location via task relationship
     */
    public function index(Request $request)
    {
        // Get default geofence radius from settings
        $radiusSetting = DB::table('company_settings')
            ->where('key', 'geofence_radius')
            ->first();

        $radius = $radiusSetting ? $radiusSetting->value : '100';

        // Check if user is authenticated via session
        $user = Auth::user();

        if ($user) {

            // Get employee record
            $employee = DB::table('employees')
                ->where('user_id', $user->id)
                ->first();

            if ($employee) {
                // Get today's task for this employee's team
                $today = now()->format('Y-m-d');

                // Fetch task with contracted_client coordinates via location relationship
                $task = DB::table('tasks')
                    ->join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
                    ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
                    ->leftJoin('locations', 'tasks.location_id', '=', 'locations.id')
                    ->leftJoin('contracted_clients', 'locations.contracted_client_id', '=', 'contracted_clients.id')
                    ->where('optimization_team_members.employee_id', $employee->id)
                    ->where('tasks.scheduled_date', $today)
                    ->whereNull('tasks.deleted_at')
                    ->whereNotNull('contracted_clients.latitude')
                    ->whereNotNull('contracted_clients.longitude')
                    ->select(
                        'tasks.id',
                        'tasks.task_description',
                        'contracted_clients.latitude',
                        'contracted_clients.longitude',
                        'contracted_clients.name as client_name'
                    )
                    ->first();

                if ($task) {
                    \Log::info('Geofencing API: Returning coordinates', [
                        'task_id' => $task->id,
                        'client_name' => $task->client_name,
                        'latitude' => $task->latitude,
                        'longitude' => $task->longitude,
                        'employee_id' => $employee->id
                    ]);

                    return response()->json([
                        'office_latitude' => $task->latitude,
                        'office_longitude' => $task->longitude,
                        'geofence_radius' => $radius,
                        'location_type' => 'client_location',
                        'location_name' => $task->client_name,
                        'task_id' => $task->id,
                        'task_description' => $task->task_description,
                    ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                      ->header('Pragma', 'no-cache')
                      ->header('Expires', '0');
                } else {
                    // Debug: No task found for this employee today
                    \Log::info('Geofencing: No task found', [
                        'employee_id' => $employee->id,
                        'today' => $today,
                        'user_id' => $user->id
                    ]);
                }
            } else {
                // Debug: No employee record found
                \Log::info('Geofencing: No employee record', ['user_id' => $user->id]);
            }
        } else {
            // Debug: User not authenticated
            \Log::info('Geofencing: User not authenticated');
        }

        // Fallback: No task found
        return response()->json([
            'office_latitude' => null,
            'office_longitude' => null,
            'geofence_radius' => $radius,
            'location_type' => 'none',
            'message' => 'No task assigned for today',
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache')
          ->header('Expires', '0');
    }
}
