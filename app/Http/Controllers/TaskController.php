<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\ContractedClient;
use App\Models\Client;
use App\Models\OptimizationRun;
use App\Models\OptimizationGeneration;

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
            $dateKey = Carbon::parse($task->scheduled_date)->format('Y-n-j');
            
            // Get the client name
            $clientName = 'Unknown Client';
            if ($task->location && $task->location->contractedClient) {
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $clientName = $task->client->first_name . ' ' . $task->client->last_name;
            }
        
            // Get location/cabin name
            $locationName = $task->location ? $task->location->location_name : 'N/A';
            
            if (!isset($events[$dateKey])) {
                $events[$dateKey] = [];
            }
            
            // FIXED: Include all necessary fields for the frontend
            $events[$dateKey][] = [
                'title' => $clientName . ' - ' . $task->task_description,
                'serviceType' => $task->task_description, // The service type
                'status' => $task->status ?? 'Incomplete', // Task status
                'cabins' => $locationName, // Location/cabin name
                'location' => $locationName, // Alternative field name
                'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                'id' => $task->id // Useful for future actions
            ];
        }
        
        // --- 4. BUILD TASKS FOR KANBAN BOARD ---
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
            ];
        });

        // --- 5. RETURN TO VIEW ---
        return view('admin-tasks', [
            'tasks' => $tasks,
            'clients' => $allClients,
            'events' => $events,
        ]);
    }


    /**
     * Store a new task in the database and trigger optimization.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client' => 'required|string',
            'serviceDate' => 'required|date',
            'serviceType' => 'required|string',
            'cabinsList' => 'required|array|min:1',
            'rateType' => 'nullable|string',
            'extraTasks' => 'nullable|array'
        ]);
    
        try {
            DB::beginTransaction();
    
            // Parse client type and ID
            $clientParts = explode('_', $request->client);
            $clientType = $clientParts[0];
            $clientId = $clientParts[1];
    
            // ✅ COLLECT location IDs without creating tasks yet
            $newLocationIds = [];
            foreach ($request->cabinsList as $cabinInfo) {
                $location = DB::table('locations')
                    ->where('location_name', $cabinInfo['cabin'])
                    ->first();
                
                if (!$location) {
                    continue;
                }
    
                $newLocationIds[] = $location->id;
            }
    
            // Validate we have locations
            if (empty($newLocationIds)) {
                throw new \Exception('No valid locations found for the selected cabins');
            }
    
            // ❌ REMOVED: Don't create Pending tasks here
            // The OptimizationService will create Scheduled tasks directly
    
            DB::commit();
    
            // === TRIGGER OPTIMIZATION ===
            // This will create the tasks with "Scheduled" status
            $optimizationService = new \App\Services\OptimizationService();
            $optimizationResult = $optimizationService->run(
                $request->serviceDate,
                $newLocationIds,
                null // No triggering task ID since we're not creating tasks yet
            );
    
            if ($optimizationResult['status'] === 'success') {
                return response()->json([
                    'message' => 'Tasks created and optimized successfully!',
                    'optimization_run_id' => $optimizationResult['optimization_run_id'],
                    'details' => $optimizationResult['message']
                ]);
            } else {
                return response()->json([
                    'message' => 'Optimization failed: ' . $optimizationResult['message'],
                    'optimization_run_id' => $optimizationResult['optimization_run_id']
                ], 422);
            }
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating tasks: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Add external client from order and return updated client list
     * This method can be called when a new order is placed by an external client
     */
    public function addExternalClientFromOrder(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        // Check if client already exists
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

        // Create new external client
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

    /**
     * Get optimization results for visualization
     */
    public function getOptimizationResults($optimizationRunId)
    {
        try {
            $optimizationRun = OptimizationRun::findOrFail($optimizationRunId);
            
            $generations = OptimizationGeneration::where('optimization_run_id', $optimizationRunId)
                ->orderBy('generation_number', 'asc')
                ->get();

            return response()->json([
                'optimization_run' => [
                    'id' => $optimizationRun->id,
                    'service_date' => $optimizationRun->service_date,
                    'status' => $optimizationRun->status,
                    'total_tasks' => (int) $optimizationRun->total_tasks,
                    'total_teams' => (int) $optimizationRun->total_teams,
                    'total_employees' => (int) $optimizationRun->total_employees,
                    // ✅ FIXED: Explicitly cast to float, handle null
                    'final_fitness_score' => $optimizationRun->final_fitness_score 
                        ? (float) $optimizationRun->final_fitness_score 
                        : null,
                    'generations_run' => (int) $optimizationRun->generations_run,
                    'employee_allocation' => $optimizationRun->employee_allocation_data,
                    'greedy_result' => $optimizationRun->greedy_result_data,
                ],
                'generations' => $generations->map(function($generation) {
                    return [
                        'generation_number' => (int) $generation->generation_number,
                        'best_fitness' => (float) $generation->best_fitness,
                        'average_fitness' => (float) $generation->average_fitness,
                        'worst_fitness' => (float) $generation->worst_fitness,
                        'is_improvement' => (bool) $generation->is_improvement,
                        'best_schedule' => $generation->best_schedule_data,
                        'population_summary' => $generation->population_summary,
                        'schedules' => []
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load optimization results',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Re-run optimization for a specific date
     */
    public function reoptimize(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date'
        ]);

        // Get all locations that need to be scheduled for this date
        $pendingTasks = Task::where('scheduled_date', $request->service_date)
            ->where('status', 'Scheduled')
            ->get();

        if ($pendingTasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found to re-optimize for this date.'
            ], 404);
        }

        $locationIds = $pendingTasks->pluck('location_id')->unique()->toArray();

        // Trigger optimization
        $optimizationService = new \App\Services\OptimizationService();
        $optimizationResult = $optimizationService->run(
            $request->service_date,
            $locationIds,
            null
        );

        return response()->json([
            'status' => $optimizationResult['status'],
            'message' => $optimizationResult['message'],
            'optimization_run_id' => $optimizationResult['optimization_run_id'] ?? null
        ]);
    }
}