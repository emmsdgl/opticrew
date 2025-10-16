<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\ContractedClient;
use App\Models\Client;
use App\Models\OptimizationRun;

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

            // Create tasks with "Pending" status (will be optimized)
            $newLocationIds = [];
            foreach ($request->cabinsList as $cabinInfo) {
                $location = DB::table('locations')
                    ->where('location_name', $cabinInfo['cabin'])
                    ->first();
                
                if (!$location) {
                    continue;
                }

                $newLocationIds[] = $location->id;

                // Create task with Pending status
                $task = new Task();
                $task->location_id = $location->id;
                
                if ($clientType === 'contracted') {
                    $task->contracted_client_id = $clientId;
                    $task->client_id = null;
                } else {
                    $task->client_id = $clientId;
                    $task->contracted_client_id = null;
                }
                
                $task->task_description = $request->serviceType . ' - ' . $cabinInfo['type'] . ' (' . $request->rateType . ')';
                $task->scheduled_date = $request->serviceDate;
                $task->status = 'Pending'; // Will be changed to "Scheduled" after optimization
                $task->estimated_duration_minutes = 120;
                
                $task->save();
                $firstTaskId = $task->id; // Keep track for optimization trigger
            }

            // Handle extra tasks
            if ($request->has('extraTasks') && is_array($request->extraTasks)) {
                foreach ($request->extraTasks as $extraTask) {
                    if (!empty($extraTask['type'])) {
                        $task = new Task();
                        
                        if ($clientType === 'contracted') {
                            $task->contracted_client_id = $clientId;
                        } else {
                            $task->client_id = $clientId;
                        }
                        
                        $task->task_description = 'Extra: ' . $extraTask['type'];
                        $task->scheduled_date = $request->serviceDate;
                        $task->status = 'Pending';
                        $task->estimated_duration_minutes = 180;
                        
                        $task->save();
                    }
                }
            }

            DB::commit();

            // === TRIGGER OPTIMIZATION ===
            $optimizationService = new \App\Services\OptimizationService();
            $optimizationResult = $optimizationService->run(
                $request->serviceDate,
                $newLocationIds,
                $firstTaskId ?? null
            );

            if ($optimizationResult['status'] === 'success') {
                return response()->json([
                    'message' => 'Tasks created and optimized successfully!',
                    'optimization_run_id' => $optimizationResult['optimization_run_id'],
                    'details' => $optimizationResult['message']
                ]);
            } else {
                return response()->json([
                    'message' => 'Tasks created but optimization failed: ' . $optimizationResult['message'],
                    'optimization_run_id' => $optimizationResult['optimization_run_id']
                ], 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating tasks: ' . $e->getMessage()
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
        $optimizationRun = OptimizationRun::with([
            'generations.schedules',
            'generations' => function($query) {
                $query->orderBy('generation_number', 'asc');
            }
        ])->findOrFail($optimizationRunId);

        return response()->json([
            'optimization_run' => [
                'id' => $optimizationRun->id,
                'service_date' => $optimizationRun->service_date,
                'status' => $optimizationRun->status,
                'total_tasks' => $optimizationRun->total_tasks,
                'total_teams' => $optimizationRun->total_teams,
                'total_employees' => $optimizationRun->total_employees,
                'final_fitness_score' => $optimizationRun->final_fitness_score,
                'generations_run' => $optimizationRun->generations_run,
                'employee_allocation' => $optimizationRun->employee_allocation_data,
                'greedy_result' => $optimizationRun->greedy_result_data,
            ],
            'generations' => $optimizationRun->generations->map(function($generation) {
                return [
                    'generation_number' => $generation->generation_number,
                    'best_fitness' => $generation->best_fitness,
                    'average_fitness' => $generation->average_fitness,
                    'worst_fitness' => $generation->worst_fitness,
                    'is_improvement' => $generation->is_improvement,
                    'best_schedule' => $generation->best_schedule_data,
                    'population_summary' => $generation->population_summary,
                    'schedules' => $generation->schedules->map(function($schedule) {
                        return [
                            'schedule_index' => $schedule->schedule_index,
                            'fitness_score' => $schedule->fitness_score,
                            'team_assignments' => $schedule->team_assignments,
                            'workload_distribution' => $schedule->workload_distribution,
                            'is_elite' => $schedule->is_elite,
                            'is_final_result' => $schedule->is_final_result,
                            'created_by' => $schedule->created_by
                        ];
                    })
                ];
            })
        ]);
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