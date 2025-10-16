<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\ContractedClient;
use App\Models\Client;

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
        $contractedClients = ContractedClient::all(['id', 'name']);
        $externalClients = Client::all(['id', 'first_name', 'last_name']);

        $allClients = $contractedClients->map(function ($client) {
            return [
                'label' => $client->name,
                'value' => 'contracted_' . $client->id
            ];
        })->concat($externalClients->map(function ($client) {
            return [
                'label' => $client->first_name . ' ' . $client->last_name,
                'value' => 'client_' . $client->id
            ];
        }));

        // --- 3. BUILD EVENTS FOR CALENDAR DISPLAY ---
        $events = [];
        foreach ($rawTasks as $task) {
            $dateKey = Carbon::parse($task->scheduled_date)->format('Y-n-j');
            
            // 2. This is the new, corrected logic to get the client name
            $clientName = 'Unknown Client'; // A default fallback name
        
            if ($task->location && $task->location->contractedClient) {
                // This is a task for a contracted client (e.g., Kakslauttanen)
                $clientName = $task->location->contractedClient->name;
            } elseif ($task->client) {
                // This is a task for an external client
                $clientName = $task->client->first_name;
            }
        
            if (!isset($events[$dateKey])) {
                $events[$dateKey] = [];
            }
            
            $events[$dateKey][] = [
                'title' => $clientName . ' - ' . $task->task_description,
                'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
            ];
        }
        
        // --- 4. BUILD TASKS FOR KANBAN BOARD ---
        $tasks = $rawTasks->map(function ($task) {
            $clientName =
                optional($task->contractedClient)->name ??
                trim(optional($task->client)->first_name . ' ' . optional($task->client)->last_name) ??
                'Unknown Client';

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
     * Store a new task in the database.
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

        // Parse client type and ID
        $clientParts = explode('_', $request->client);
        $clientType = $clientParts[0]; // 'contracted' or 'client'
        $clientId = $clientParts[1];

        // Create tasks for each cabin
        $createdTasks = [];
        foreach ($request->cabinsList as $cabinInfo) {
            // Find the location_id from the location name (cabin)
            $location = DB::table('locations')
                ->where('location_name', $cabinInfo['cabin'])
                ->first();
            
            if (!$location) {
                continue; // Skip if location not found
            }

            $task = new Task();
            $task->location_id = $location->id;
            
            // Set client relationship based on type
            if ($clientType === 'contracted') {
                $task->contracted_client_id = $clientId;
                $task->client_id = null;
            } else {
                $task->client_id = $clientId;
                $task->contracted_client_id = null;
            }
            
            // Build task description
            $task->task_description = $request->serviceType . ' - ' . $cabinInfo['type'] . ' (' . $request->rateType . ')';
            $task->scheduled_date = $request->serviceDate;
            $task->status = 'Pending';
            $task->estimated_duration_minutes = 120; // 2 hours base per cabin
            
            $task->save();
            $createdTasks[] = $task;
        }

        // Handle extra tasks if provided
        if ($request->has('extraTasks') && is_array($request->extraTasks)) {
            foreach ($request->extraTasks as $extraTask) {
                if (!empty($extraTask['type'])) {
                    // Create extra task entry (you may need to adjust based on your schema)
                    $task = new Task();
                    
                    if ($clientType === 'contracted') {
                        $task->contracted_client_id = $clientId;
                    } else {
                        $task->client_id = $clientId;
                    }
                    
                    $task->task_description = 'Extra: ' . $extraTask['type'];
                    $task->scheduled_date = $request->serviceDate;
                    $task->status = 'Pending';
                    $task->estimated_duration_minutes = 180; // 3 hours for extra tasks
                    
                    // You might want to store the price somewhere
                    // $task->additional_price = $extraTask['price'] ?? 0;
                    
                    $task->save();
                    $createdTasks[] = $task;
                }
            }
        }

        return response()->json([
            'message' => 'Tasks created successfully!',
            'tasks_created' => count($createdTasks)
        ]);
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
}