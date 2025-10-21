<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\ContractedClient;
use App\Models\Client;
use App\Models\OptimizationRun;
use App\Models\OptimizationGeneration;
use App\Models\Location;
use App\Services\Optimization\OptimizationService; // ✅ FIXED: Correct namespace

class TaskController extends Controller
{
    /**
     * Display the calendar view with clients and existing tasks.
     */
    public function index()
    {
        // --- 1. FETCH ALL TASKS WITH CLIENT RELATIONSHIPS ---
        $rawTasks = Task::with(['location.contractedClient', 'client'])->get();

        // --- 2. COMBINE CONTRACTED + EXTERNAL CLIENTS FOR DROPDOWN ---
        $contractedClients = ContractedClient::with('locations')->get();
        $externalClients = Client::all(['id', 'first_name', 'last_name']);

        $allClients = $contractedClients->map(function ($client) {
            $locations = $client->locations->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->location_name
                ];
            })->values()->toArray();
            
            return [
                'label' => $client->name,
                'value' => 'contracted_' . $client->id,
                'type' => 'contracted',
                'locations' => $locations
            ];
        })->concat($externalClients->map(function ($client) {
            return [
                'label' => $client->first_name . ' ' . $client->last_name,
                'value' => 'client_' . $client->id,
                'type' => 'external',
                'locations' => [] // Empty for now, will be populated from appointments later
            ];
        }))->values()->toArray();

        // --- 3. BUILD EVENTS FOR CALENDAR DISPLAY (WITH COMPLETE DATA) ---
        $events = [];
        foreach ($rawTasks as $task) {
            $dateKey = date('Y-n-j', strtotime($task->scheduled_date));

            // Get employee names for this task from OptimizationTeam
            $employeeNames = [];
            if ($task->assigned_team_id) {
                $optimizationTeam = \App\Models\OptimizationTeam::with('members.employee')
                    ->find($task->assigned_team_id);

                if ($optimizationTeam) {
                    $employeeNames = $optimizationTeam->members()
                        ->with('employee')
                        ->get()
                        ->pluck('employee.full_name')
                        ->toArray();
                }
            }
            
            // \Log::info("Task employee lookup", [
            //     'task_id' => $task->id,
            //     'assigned_team_id' => $task->assigned_team_id,
            //     'employees_found' => $employeeNames
            // ]);
            
            // Get the client name
            $clientName = 'Unknown Client';
            if ($task->location && $task->location->contractedClient) {
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $clientName = $task->client->first_name . ' ' . $task->client->last_name;
            }

            // ✅ Parse task_description to extract service type and cabin type
            // Format: "ServiceType (CabinType)" e.g. "Daily Room Cleaning (Arrival)"
            $pureServiceType = $task->task_description;
            $cabinType = null;

            if (preg_match('/^(.+?)\s*\((.+?)\)\s*$/', $task->task_description, $matches)) {
                $pureServiceType = trim($matches[1]); // "Daily Room Cleaning"
                $cabinType = trim($matches[2]); // "Arrival"
            }

            $events[$dateKey][] = [
                'title' => $clientName . ' - ' . $task->task_description,
                'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                'status' => $task->status,
                'serviceType' => $task->task_description, // Full description for backward compatibility
                'pureServiceType' => $pureServiceType, // ✅ Just the service type
                'cabinType' => $cabinType, // ✅ Just the cabin type (Arrival/Departure/Daily Clean)
                'client' => $clientName, // ✅ Client name
                'location' => $task->location->location_name ?? 'Unknown',
                'employees' => $employeeNames, // ✅ Add employee names
                'team_id' => $task->assigned_team_id,
                'arrival_status' => $task->arrival_status ?? false, // ✅ Include arrival status
            ];
        }
        
        // --- 4. (NEW) PREPARE BOOKED LOCATIONS DATA FOR THE FRONTEND ---
        $bookedLocationsByDate = [];
        foreach ($rawTasks as $task) {
            if ($task->location_id) {
                $dateKey = date('Y-m-d', strtotime($task->scheduled_date));
                if (!isset($bookedLocationsByDate[$dateKey])) {
                    $bookedLocationsByDate[$dateKey] = [];
                }
                $bookedLocationsByDate[$dateKey][] = $task->location_id;
            }
        }

        // --- 5. BUILD TASKS FOR KANBAN BOARD ---
        $tasks = $rawTasks->map(function ($task) {
            $clientName = 'Unknown Client';
            if ($task->location && $task->location->contractedClient) {
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $clientName = trim($task->client->first_name . ' ' . $task->client->last_name);
            }

            return [
                'id' => $task->id,
                'title' => $clientName . ' - ' . $task->task_description,
                'status' => $task->status,
                'scheduled_date' => $task->scheduled_date,
                'arrival_status' => $task->arrival_status ?? false, // ✅ Include
            ];
        });

        // --- 6. RETURN TO VIEW ---
        return view('admin-tasks', [
            'tasks' => $tasks,
            'clients' => $allClients,
            'events' => $events,
            'bookedLocationsByDate' => $bookedLocationsByDate // Pass the new data
        ]);
    }

    /**
     * ✅ RULE 3 & 8: Store task with arrival_status and real-time handling
     */
    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string',
            'serviceDate' => 'required|date',
            'cabinsList' => 'required|array|min:1',
            'cabinsList.*.cabin' => 'required|string',
            'cabinsList.*.serviceType' => 'required|string',
            'cabinsList.*.cabinType' => 'required|string',
            'rateType' => 'nullable|string',
            'extraTasks' => 'nullable|array'
        ]);
    
        try {
            DB::beginTransaction();
    
            // Parse client type and ID
            $clientParts = explode('_', $request->client);
            $clientType = $clientParts[0]; // 'contracted' or 'client'
            $clientId = $clientParts[1];
    
            // ✅ NEW: Collect location IDs AND CREATE TASKS
            $newLocationIds = [];
            $createdTasks = [];
            
            foreach ($request->cabinsList as $cabinInfo) {
                // Find location by name
                $location = Location::where('location_name', $cabinInfo['cabin'])->first();

                if (!$location) {
                    Log::warning("Location not found: {$cabinInfo['cabin']}");
                    continue;
                }

                $newLocationIds[] = $location->id;

                // ✅ CREATE THE TASK RECORD
                $task = new Task();
                $task->location_id = $location->id;

                // Set client_id based on type
                if ($clientType === 'contracted') {
                    $task->client_id = null; // Contracted clients don't use client_id
                } else {
                    $task->client_id = $clientId;
                }

                // ✅ Option B: Each cabin has own serviceType and cabinType
                // Format: "ServiceType (CabinType)" e.g., "Daily Room Cleaning (Arrival)"
                $task->task_description = $cabinInfo['serviceType'] . ' (' . $cabinInfo['cabinType'] . ')';
                $task->scheduled_date = $request->serviceDate;
                $task->scheduled_time = '08:00:00'; // ✅ ADD THIS LINE - Start of work day... Remove this, to get the proper time, after development phase

                // Use location's base duration or default
                $task->duration = $location->base_cleaning_duration_minutes ?? 60;
                $task->estimated_duration_minutes = $task->duration;

                // Set travel time (default 30 minutes)
                $task->travel_time = 30;

                // ✅ RULE 3: Set arrival status based on cabinType (Arrival/Departure/Daily Clean)
                // If cabin's type is "Arrival", mark as high priority
                $task->arrival_status = ($cabinInfo['cabinType'] === 'Arrival') ? true : false;
                            
                // Set coordinates from location query
                // Get coordinates based on contracted client
                if ($clientType === 'contracted') {
                    if ($clientId == 1) { // Kakslauttanen
                        $task->latitude = 68.33470361;
                        $task->longitude = 27.33426652;
                    } elseif ($clientId == 2) { // Aikamatkat
                        $task->latitude = 68.42573267;
                        $task->longitude = 27.41235379;
                    }
                }
                
                $task->status = 'Pending'; // Start as Pending, optimization will change to Scheduled
                $task->save();
                
                $createdTasks[] = $task;
                
                Log::info("Created task", [
                    'id' => $task->id,
                    'location' => $location->location_name,
                    'duration' => $task->duration,
                    'arrival_status' => $task->arrival_status,
                    'coordinates' => ['lat' => $task->latitude, 'lon' => $task->longitude]
                ]);
            }
    
            // Validate we have locations and tasks
            if (empty($newLocationIds)) {
                throw new \Exception('No valid locations found for the selected cabins');
            }
            
            if (empty($createdTasks)) {
                throw new \Exception('No tasks were created');
            }
    
            DB::commit();
    
            // ✅ RULE 8: Trigger optimization (will auto-detect real-time)
            Log::info("Triggering optimization", [
                'service_date' => $request->serviceDate,
                'location_ids' => $newLocationIds,
                'task_count' => count($createdTasks),
                'is_today' => $request->serviceDate === Carbon::now()->format('Y-m-d')
            ]);
            
            $optimizationService = app(OptimizationService::class);
            
            // Pass the first created task ID for real-time detection
            $optimizationResult = $optimizationService->optimizeSchedule(
                $request->serviceDate,
                $newLocationIds,
                $createdTasks[0]->id // ✅ Pass task ID
            );

            // Handle the response
            if ($optimizationResult['status'] === 'success') {
                return response()->json([
                    'message' => 'Tasks created and optimized successfully!',
                    'tasks_created' => count($createdTasks),
                    'schedules' => $optimizationResult['schedules'] ?? null,
                    'statistics' => $optimizationResult['statistics'] ?? null,
                    'is_real_time_addition' => isset($optimizationResult['assigned_team_id']),
                ]);
            } else {
                return response()->json([
                    'message' => 'Tasks created but optimization failed',
                    'tasks_created' => count($createdTasks),
                    'error' => $optimizationResult['message'] ?? 'Unknown error'
                ], 422);
            }
    
        } catch (\Exception $e) {
            // Only rollback if we haven't committed yet
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            } 

            Log::error('Task creation failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error creating tasks: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ✅ RULE 9: Save schedule (mark as saved)
     */
    public function saveSchedule(Request $request)
    {
        $request->validate([
            'optimization_run_id' => 'required|integer|exists:optimization_runs,id'
        ]);

        try {
            $optimizationRun = OptimizationRun::findOrFail($request->optimization_run_id);

            if ($optimizationRun->is_saved) {
                return response()->json([
                    'message' => 'This schedule is already saved'
                ], 400);
            }

            // ✅ Mark as saved
            $optimizationRun->update(['is_saved' => true]);

            Log::info("Schedule saved", [
                'optimization_run_id' => $optimizationRun->id,
                'service_date' => $optimizationRun->service_date
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Schedule saved successfully',
                'optimization_run_id' => $optimizationRun->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to save schedule', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error saving schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addExternalClientFromOrder(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $existingClient = Client::where('first_name', $request->first_name)
            ->where('last_name', $request->last_name)
            ->first();

        if ($existingClient) {
            return response()->json([
                'message' => 'Client already exists',
                'client' => [
                    'label' => $existingClient->first_name . ' ' . $existingClient->last_name,
                    'value' => 'client_' . $existingClient->id
                ]
            ]);
        }

        $client = new Client();
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->save();

        return response()->json([
            'message' => 'External client added successfully',
            'client' => [
                'label' => $client->first_name . ' ' . $client->last_name,
                'value' => 'client_' . $client->id
            ]
        ]);
    }

    /**
     * Get all available clients (contracted + external)
     * Useful for refreshing dropdown after adding new external client
     */
    public function getClients()
    {
        $contractedClients = ContractedClient::all();
        $externalClients = Client::all(['id', 'first_name', 'last_name']);

        $allClients = $contractedClients->map(function ($client) {
            return [
                'label' => $client->name,
                'value' => 'contracted_' . $client->id,
                'type' => 'contracted'
            ];
        })->concat($externalClients->map(function ($client) {
            return [
                'label' => $client->first_name . ' ' . $client->last_name,
                'value' => 'client_' . $client->id,
                'type' => 'external'
            ];
        }));

        return response()->json([
            'clients' => $allClients
        ]);
    }

    public function getOptimizationResults($optimizationRunId)
    {
        try {
            $optimizationRun = OptimizationRun::findOrFail($optimizationRunId);
            
            return response()->json([
                'optimization_run' => [
                    'id' => $optimizationRun->id,
                    'service_date' => $optimizationRun->service_date,
                    'status' => $optimizationRun->status,
                    'is_saved' => (bool) $optimizationRun->is_saved, // ✅ Include
                    'total_tasks' => (int) $optimizationRun->total_tasks,
                    'total_teams' => (int) $optimizationRun->total_teams,
                    'total_employees' => (int) $optimizationRun->total_employees,
                    'final_fitness_score' => $optimizationRun->final_fitness_score 
                        ? (float) $optimizationRun->final_fitness_score 
                        : null,
                    'generations_run' => (int) $optimizationRun->generations_run,
                    'employee_allocation' => $optimizationRun->employee_allocation_data,
                    'greedy_result' => $optimizationRun->greedy_result_data,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load optimization results',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ RULE 4: Re-optimize (will delete unsaved runs automatically)
     */
    public function reoptimize(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date'
        ]);

        $pendingTasks = Task::where('scheduled_date', $request->service_date)
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->get();

        if ($pendingTasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found to re-optimize for this date.'
            ], 404);
        }

        $locationIds = $pendingTasks->pluck('location_id')->unique()->toArray();

        $optimizationService = app(OptimizationService::class);
        
        // ✅ Will automatically delete unsaved runs
        $optimizationResult = $optimizationService->optimizeSchedule(
            $request->service_date,
            $locationIds,
            null
        );

        return response()->json([
            'status' => $optimizationResult['status'],
            'message' => $optimizationResult['status'] === 'success' 
                ? 'Re-optimization completed successfully' 
                : 'Re-optimization failed',
            'statistics' => $optimizationResult['statistics'] ?? null,
            'schedules' => $optimizationResult['schedules'] ?? null
        ]);
    }
}