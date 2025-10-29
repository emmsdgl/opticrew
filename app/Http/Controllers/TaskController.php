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
use App\Models\Holiday;

class TaskController extends Controller
{
    /**
     * Display the calendar view with clients and existing tasks.
     */
    public function index()
    {
        // --- 1. FETCH TASKS WITH CLIENT RELATIONSHIPS (OPTIMIZED) ---
        // Only load tasks within a reasonable date range for calendar view (3 months back, 3 months forward)
        $startDate = now()->subMonths(3)->startOfDay();
        $endDate = now()->addMonths(3)->endOfDay();

        // Eager load ALL relationships to avoid N+1 queries
        $rawTasks = Task::with([
            'location.contractedClient',
            'client',
            'optimizationTeam.members.employee.user'  // ✅ FIX: Eager load optimization team members
        ])
        ->whereBetween('scheduled_date', [$startDate, $endDate])
        ->orderBy('scheduled_date', 'asc')
        ->get();

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

            // ✅ FIX: Get employee names from eager-loaded relationship (NO additional queries!)
            $employeeNames = [];
            if ($task->optimizationTeam && $task->optimizationTeam->members) {
                $employeeNames = $task->optimizationTeam->members
                    ->map(function($member) {
                        return $member->employee && $member->employee->user
                            ? $member->employee->user->name
                            : null;
                    })
                    ->filter()  // Remove nulls
                    ->values()
                    ->toArray();
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
            // OR for client appointments: "ServiceType - CabinName" e.g. "Deep Cleaning - Kelo A"
            $pureServiceType = $task->task_description;
            $cabinType = null;

            if (preg_match('/^(.+?)\s*\((.+?)\)\s*$/', $task->task_description, $matches)) {
                $pureServiceType = trim($matches[1]); // "Daily Room Cleaning"
                $cabinType = trim($matches[2]); // "Arrival"
            }

            // ✅ Get location name (for contracted clients) or cabin name (for external clients)
            $locationName = 'Unknown';
            if ($task->location && $task->location->location_name) {
                // Contracted client - use location name
                $locationName = $task->location->location_name;
            } elseif ($task->client) {
                // External client - extract cabin name from task_description
                // Format: "Service Type - Cabin Name"
                if (preg_match('/^.+?\s*-\s*(.+)$/', $task->task_description, $matches)) {
                    $locationName = trim($matches[1]); // "Kelo A"
                }
            }

            $events[$dateKey][] = [
                'title' => $clientName . ' - ' . $task->task_description,
                'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                'status' => $task->status,
                'serviceType' => $task->task_description, // Full description for backward compatibility
                'pureServiceType' => $pureServiceType, // ✅ Just the service type
                'cabinType' => $cabinType, // ✅ Just the cabin type (Arrival/Departure/Daily Clean)
                'client' => $clientName, // ✅ Client name
                'location' => $locationName, // ✅ Location name or cabin name
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
            // Get client name
            $clientName = 'Unknown Client';
            if ($task->location && $task->location->contractedClient) {
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $clientName = trim($task->client->first_name . ' ' . $task->client->last_name);
            }

            // Get team members for this task
            $teamMembers = [];
            $teamName = null;
            if ($task->assigned_team_id) {
                $optimizationTeam = \App\Models\OptimizationTeam::with('members.employee.user')
                    ->find($task->assigned_team_id);

                if ($optimizationTeam) {
                    $teamName = $optimizationTeam->team_name;
                    $teamMembers = $optimizationTeam->members()
                        ->with('employee.user')
                        ->get()
                        ->map(function($member) {
                            // Get profile picture and name from user
                            $profilePicture = null;
                            $userName = 'Unknown';

                            if ($member->employee && $member->employee->user) {
                                $userName = $member->employee->user->name;
                                $profilePicture = $member->employee->user->profile_picture;
                            }

                            return [
                                'id' => $member->employee->id,
                                'name' => $userName,
                                'role' => $member->employee->role ?? 'employee',
                                'picture' => $profilePicture
                            ];
                        })
                        ->toArray();
                }
            }

            // Determine priority based on arrival_status and scheduled date
            $priority = 'Normal';
            $priorityColor = 'bg-[#2FBC0020] text-[#2FBC00]'; // Green for normal

            if ($task->arrival_status) {
                $priority = 'Urgent';
                $priorityColor = 'bg-[#FE1E2820] text-[#FE1E28]'; // Red for urgent
            } else {
                // Check if task is today or tomorrow
                $taskDate = Carbon::parse($task->scheduled_date);
                $today = Carbon::today();
                $tomorrow = Carbon::tomorrow();

                if ($taskDate->isSameDay($today)) {
                    $priority = 'High';
                    $priorityColor = 'bg-[#FF7F0020] text-[#FF7F00]'; // Orange for high
                } elseif ($taskDate->isSameDay($tomorrow)) {
                    $priority = 'Medium';
                    $priorityColor = 'bg-[#FFB70020] text-[#FFB700]'; // Yellow for medium
                }
            }

            // Map database status to Kanban status
            $kanbanStatus = 'todo';
            switch (strtolower($task->status)) {
                case 'pending':
                    $kanbanStatus = 'todo';
                    break;
                case 'in progress':
                case 'in-progress':
                    $kanbanStatus = 'inprogress';
                    break;
                case 'completed':
                    $kanbanStatus = 'completed';
                    break;
            }

            // ✅ Get location name (for contracted clients) or cabin name (for external clients)
            $locationName = null;
            if ($task->location && $task->location->location_name) {
                // Contracted client - use location name
                $locationName = $task->location->location_name;
            } elseif ($task->client) {
                // External client - extract cabin name from task_description
                // Format: "Service Type - Cabin Name"
                if (preg_match('/^.+?\s*-\s*(.+)$/', $task->task_description, $matches)) {
                    $locationName = trim($matches[1]); // "Kelo A"
                }
            }

            return [
                'id' => $task->id,
                'client' => $clientName,
                'title' => $task->task_description,
                'team' => $teamName ?? 'Unassigned',
                'teamMembers' => $teamMembers,
                'date' => Carbon::parse($task->scheduled_date)->format('F j, Y'),
                'time' => Carbon::parse($task->scheduled_time)->format('g:i A'),
                'priority' => $priority,
                'priorityColor' => $priorityColor,
                'status' => $kanbanStatus,
                'arrival_status' => $task->arrival_status ?? false,
                'location' => $locationName, // ✅ Location name or cabin name
            ];
        });

        // --- 6. FETCH HOLIDAYS ---
        $rawHolidays = Holiday::all();
        $holidays = [];
        foreach ($rawHolidays as $holiday) {
            $dateKey = date('Y-n-j', strtotime($holiday->date));
            $holidays[$dateKey] = [
                'id' => $holiday->id,
                'name' => $holiday->name,
                'date' => $holiday->date,
            ];
        }

        // --- 7. RETURN TO VIEW ---
        return view('admin.tasks', [
            'tasks' => $tasks,
            'clients' => $allClients,
            'events' => $events,
            'bookedLocationsByDate' => $bookedLocationsByDate,
            'holidays' => $holidays // Pass holidays data
        ]);
    }

    /**
     * ✅ RULE 3 & 8: Store task with arrival_status and real-time handling
     */
    public function store(Request $request)
    {
        // Custom validation: Either cabinsList OR extraTasks must be provided
        $request->validate([
            'client' => 'required|string',
            'serviceDate' => 'required|date',
            'cabinsList' => 'nullable|array',
            'cabinsList.*.cabin' => 'required_with:cabinsList|string',
            'cabinsList.*.serviceType' => 'required_with:cabinsList|string',
            'cabinsList.*.cabinType' => 'required_with:cabinsList|string',
            'rateType' => 'nullable|string',
            'extraTasks' => 'nullable|array',
            'extraTasks.*.type' => 'required_with:extraTasks|string',
            'extraTasks.*.basePrice' => 'nullable|numeric',
            'extraTasks.*.finalPrice' => 'nullable|numeric',
            'extraTasks.*.duration' => 'required_with:extraTasks|integer|in:30,60,150',
            'extraTasks.*.price' => 'nullable|numeric' // Keep for backward compatibility
        ]);

        // Ensure at least one of cabinsList or extraTasks is provided
        if (empty($request->cabinsList) && empty($request->extraTasks)) {
            return response()->json([
                'message' => 'You must select at least one cabin or add at least one extra task.'
            ], 422);
        }
    
        try {
            DB::beginTransaction();
    
            // Parse client type and ID
            $clientParts = explode('_', $request->client);
            $clientType = $clientParts[0]; // 'contracted' or 'client'
            $clientId = $clientParts[1];
    
            // ✅ NEW: Collect location IDs AND CREATE TASKS
            $newLocationIds = [];
            $createdTasks = [];

            // Process regular cabin tasks
            if (!empty($request->cabinsList)) {
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
                $task->rate_type = $request->rateType ?? 'Normal'; // Save the rate type (Normal or Student)
                $task->scheduled_date = $request->serviceDate;
                $task->scheduled_time = '08:00:00'; // ✅ ADD THIS LINE - Start of work day... Remove this, to get the proper time, after development phase

                // Use location's base duration or default
                $task->duration = $location->base_cleaning_duration_minutes ?? 60;
                $task->estimated_duration_minutes = $task->duration;

                // Travel time is 0 per task (calculated once per team based on client destination)
                $task->travel_time = 0;

                // ✅ RULE 3: Set arrival status based on cabinType (Arrival/Departure/Daily Clean)
                // If cabin's type is "Arrival", mark as high priority
                $task->arrival_status = ($cabinInfo['cabinType'] === 'Arrival') ? true : false;

                $task->status = 'Pending'; // Start as Pending, optimization will change to Scheduled
                $task->save();

                $createdTasks[] = $task;

                    Log::info("Created cabin task", [
                        'id' => $task->id,
                        'location' => $location->location_name,
                        'duration' => $task->duration,
                        'arrival_status' => $task->arrival_status
                    ]);
                }
            }

            // Process extra tasks (assign to a location from the contracted client)
            if (!empty($request->extraTasks)) {
                // Get a location from the contracted client for extra tasks
                $clientLocation = null;
                if ($clientType === 'contracted') {
                    $clientLocation = Location::where('contracted_client_id', $clientId)->first();

                    if (!$clientLocation) {
                        throw new \Exception('No location found for this contracted client. Please add a location first.');
                    }
                }

                foreach ($request->extraTasks as $extraTask) {
                    $task = new Task();

                    // Assign extra task to a location from the contracted client
                    if ($clientType === 'contracted' && $clientLocation) {
                        $task->location_id = $clientLocation->id;

                        // Add location to the list if not already there
                        if (!in_array($clientLocation->id, $newLocationIds)) {
                            $newLocationIds[] = $clientLocation->id;
                        }
                    } else {
                        // For non-contracted clients, location_id remains null
                        $task->location_id = null;
                    }

                    // Set client_id based on type
                    if ($clientType === 'contracted') {
                        $task->client_id = null;
                    } else {
                        $task->client_id = $clientId;
                    }

                    // Task description is the extra task type
                    $task->task_description = $extraTask['type'];
                    $task->rate_type = $request->rateType ?? 'Normal'; // Save the rate type (Normal or Student)
                    $task->scheduled_date = $request->serviceDate;
                    $task->scheduled_time = '08:00:00';

                    // Use the actual duration provided in the form (30, 60, or 150 minutes)
                    $task->duration = isset($extraTask['duration']) ? (int)$extraTask['duration'] : 60; // Default to 60 if not provided
                    $task->estimated_duration_minutes = $task->duration;

                    // Travel time is 0 per task (calculated once per team based on client destination)
                    $task->travel_time = 0;

                    // Extra tasks are not arrival-related
                    $task->arrival_status = false;

                    $task->status = 'Pending';
                    $task->save();

                    $createdTasks[] = $task;

                    Log::info("Created extra task", [
                        'id' => $task->id,
                        'type' => $extraTask['type'],
                        'location_id' => $task->location_id,
                        'base_price' => $extraTask['basePrice'] ?? 'N/A',
                        'final_price' => $extraTask['finalPrice'] ?? $extraTask['price'] ?? 'N/A',
                        'duration' => $task->duration
                    ]);
                }
            }

            // Validate we have locations (both cabin tasks and extra tasks now have locations)
            if (empty($newLocationIds)) {
                throw new \Exception('No valid locations found. Please ensure the client has at least one location.');
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
                'is_today' => $request->serviceDate === Carbon::now()->format('Y-m-d'),
                'has_cabin_tasks' => !empty($request->cabinsList),
                'has_extra_tasks' => !empty($request->extraTasks)
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
                    'optimization_run_id' => $optimizationResult['optimization_run_id'] ?? null, // Pass the run ID
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
     * Update task status (for Kanban board drag & drop)
     */
    public function updateStatus(Request $request, $taskId)
    {
        $request->validate([
            'status' => 'required|string|in:Pending,In Progress,Completed,On Hold'
        ]);

        try {
            $task = Task::findOrFail($taskId);
            $task->status = $request->status;
            $task->save();

            Log::info("Task status updated", [
                'task_id' => $taskId,
                'new_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update task status', [
                'task_id' => $taskId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update task status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for unsaved optimization runs
     */
    public function checkUnsavedSchedule(Request $request)
    {
        try {
            // Get the most recent unsaved optimization run
            $unsavedRun = OptimizationRun::where('is_saved', false)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($unsavedRun) {
                return response()->json([
                    'has_unsaved' => true,
                    'optimization_run_id' => $unsavedRun->id,
                    'service_date' => $unsavedRun->service_date,
                    'created_at' => $unsavedRun->created_at->format('Y-m-d H:i:s')
                ]);
            } else {
                return response()->json([
                    'has_unsaved' => false
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to check unsaved schedule', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'has_unsaved' => false
            ]);
        }
    }

    /**
     * ✅ RULE 9: Save schedule (mark as saved)
     */
    public function saveSchedule(Request $request)
    {
        // ✅ SUPPORT BOTH: Single run ID (old) OR service_date (new - saves all runs for that date)
        $request->validate([
            'optimization_run_id' => 'nullable|integer|exists:optimization_runs,id',
            'service_date' => 'nullable|date'
        ]);

        try {
            $savedRuns = [];
            $alreadySavedRuns = [];

            // ✅ NEW: Save all runs for a service date
            if ($request->service_date) {
                $optimizationRuns = OptimizationRun::where('service_date', $request->service_date)
                    ->where('is_saved', false)
                    ->get();

                if ($optimizationRuns->isEmpty()) {
                    // Check if already saved
                    $allRuns = OptimizationRun::where('service_date', $request->service_date)->get();
                    if ($allRuns->isNotEmpty() && $allRuns->every(fn($r) => $r->is_saved)) {
                        return response()->json([
                            'message' => 'All schedules for this date are already saved',
                            'service_date' => $request->service_date
                        ], 400);
                    }

                    return response()->json([
                        'message' => 'No unsaved schedules found for this date',
                        'service_date' => $request->service_date
                    ], 404);
                }

                // Save all unsaved runs for this date
                foreach ($optimizationRuns as $run) {
                    $run->update(['is_saved' => true]);
                    $savedRuns[] = [
                        'id' => $run->id,
                        'total_tasks' => $run->total_tasks,
                        'total_teams' => $run->total_teams,
                        'total_employees' => $run->total_employees
                    ];
                }

                Log::info("✅ Saved ALL schedules for service date", [
                    'service_date' => $request->service_date,
                    'runs_saved' => count($savedRuns),
                    'run_ids' => collect($savedRuns)->pluck('id')->toArray()
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'All schedules for ' . $request->service_date . ' saved successfully',
                    'service_date' => $request->service_date,
                    'runs_saved' => count($savedRuns),
                    'saved_runs' => $savedRuns
                ]);
            }

            // ✅ OLD: Save single run by ID (backward compatibility)
            if ($request->optimization_run_id) {
                $optimizationRun = OptimizationRun::findOrFail($request->optimization_run_id);

                if ($optimizationRun->is_saved) {
                    return response()->json([
                        'message' => 'This schedule is already saved'
                    ], 400);
                }

                $optimizationRun->update(['is_saved' => true]);

                Log::info("Schedule saved (single run)", [
                    'optimization_run_id' => $optimizationRun->id,
                    'service_date' => $optimizationRun->service_date
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Schedule saved successfully',
                    'optimization_run_id' => $optimizationRun->id
                ]);
            }

            // Neither parameter provided
            return response()->json([
                'message' => 'Either optimization_run_id or service_date is required'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Failed to save schedule', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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

        // ✅ Will automatically delete ALL previous runs for this date (saved and unsaved)
        // Only the most recent re-optimized schedule will be kept
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