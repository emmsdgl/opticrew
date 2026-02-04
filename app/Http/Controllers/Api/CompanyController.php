<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContractedClient;
use App\Models\Location;
use App\Models\Task;
use App\Models\Employee;
use App\Models\OptimizationTeam;
use App\Models\OptimizationTeamMember;
use App\Models\CompanyChecklist;
use App\Models\ChecklistCategory;
use App\Models\ChecklistItem;
use App\Models\TaskReview;
use App\Services\Optimization\OptimizationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Get the contracted client for the authenticated user
     */
    private function getContractedClient(Request $request)
    {
        return ContractedClient::where('user_id', $request->user()->id)->first();
    }

    /**
     * Get dashboard data for company
     */
    public function dashboard(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        // Get location IDs for this contracted client
        $locationIds = $contractedClient->locations()->pluck('id');

        // Get today's date
        $today = Carbon::today();

        // Get task statistics
        $todayTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $today)
            ->get();

        $taskStats = [
            'scheduled' => $todayTasks->where('status', 'Scheduled')->count(),
            'in_progress' => $todayTasks->where('status', 'In Progress')->count(),
            'completed' => $todayTasks->where('status', 'Completed')->count(),
            'on_hold' => $todayTasks->where('status', 'On Hold')->count(),
            'total' => $todayTasks->count(),
        ];

        // Get weekly task count
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $weeklyTasks = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$weekStart, $weekEnd])
            ->count();

        // Get unique employees assigned to today's tasks
        $teamIds = $todayTasks->pluck('assigned_team_id')->filter()->unique();
        $employeesOnDuty = 0;

        if ($teamIds->isNotEmpty()) {
            $employeesOnDuty = \DB::table('optimization_team_members')
                ->whereIn('optimization_team_id', $teamIds)
                ->distinct('employee_id')
                ->count('employee_id');
        }

        // Get total locations
        $totalLocations = $contractedClient->locations()->count();

        return response()->json([
            'company' => [
                'id' => $contractedClient->id,
                'name' => $contractedClient->name,
                'email' => $contractedClient->email,
            ],
            'today' => [
                'date' => $today->format('Y-m-d'),
                'formatted_date' => $today->format('d M Y'),
                'tasks' => $taskStats,
                'employees_on_duty' => $employeesOnDuty,
            ],
            'weekly_tasks' => $weeklyTasks,
            'total_locations' => $totalLocations,
        ]);
    }

    /**
     * Get all locations for the contracted client
     */
    public function getLocations(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locations = $contractedClient->locations()
            ->select([
                'id',
                'location_name',
                'location_type',
                'base_cleaning_duration_minutes',
                'normal_rate_per_hour'
            ])
            ->get();

        return response()->json([
            'locations' => $locations,
            'count' => $locations->count(),
        ]);
    }

    /**
     * Get tasks for the contracted client with optional filters
     */
    public function getTasks(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $query = Task::whereIn('location_id', $locationIds)
            ->with(['location:id,location_name,location_type', 'optimizationTeam.members.employee.user:id,name', 'review:id,task_id']);

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('scheduled_date', $request->date);
        } elseif ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('scheduled_date', [$request->start_date, $request->end_date]);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->has('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $tasks = $query->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'asc')
            ->get()
            ->map(function ($task) {
                // Get assigned employees
                $employees = [];
                if ($task->optimizationTeam && $task->optimizationTeam->members) {
                    foreach ($task->optimizationTeam->members as $member) {
                        if ($member->employee && $member->employee->user) {
                            $employees[] = [
                                'id' => $member->employee->id,
                                'name' => $member->employee->user->name,
                            ];
                        }
                    }
                }

                return [
                    'id' => $task->id,
                    'location_id' => $task->location_id,
                    'location_name' => $task->location->location_name ?? 'Unknown',
                    'location_type' => $task->location->location_type ?? null,
                    'task_description' => $task->task_description,
                    'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                    'scheduled_time' => $task->scheduled_time,
                    'status' => $task->status,
                    'rate_type' => $task->rate_type,
                    'estimated_duration' => $task->estimated_duration_minutes ?? $task->duration,
                    'arrival_status' => $task->arrival_status,
                    'started_at' => $task->started_at?->format('H:i'),
                    'completed_at' => $task->completed_at?->format('H:i'),
                    'on_hold_reason' => $task->on_hold_reason,
                    'assigned_employees' => $employees,
                    'employee_count' => count($employees),
                    'has_review' => $task->review !== null,
                ];
            });

        return response()->json([
            'tasks' => $tasks,
            'count' => $tasks->count(),
        ]);
    }

    /**
     * Get task details
     */
    public function getTaskDetails(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)
            ->with([
                'location',
                'optimizationTeam.members.employee.user',
                'performanceFlags',
                'alerts'
            ])
            ->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found or access denied'
            ], 404);
        }

        // Get assigned employees
        $employees = [];
        if ($task->optimizationTeam && $task->optimizationTeam->members) {
            foreach ($task->optimizationTeam->members as $member) {
                if ($member->employee && $member->employee->user) {
                    $employees[] = [
                        'id' => $member->employee->id,
                        'name' => $member->employee->user->name,
                        'skills' => $member->employee->skills,
                        'efficiency' => $member->employee->efficiency,
                    ];
                }
            }
        }

        return response()->json([
            'task' => [
                'id' => $task->id,
                'location' => [
                    'id' => $task->location->id,
                    'name' => $task->location->location_name,
                    'type' => $task->location->location_type,
                    'base_duration' => $task->location->base_cleaning_duration_minutes,
                ],
                'description' => $task->task_description,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'scheduled_time' => $task->scheduled_time,
                'status' => $task->status,
                'rate_type' => $task->rate_type,
                'estimated_duration' => $task->estimated_duration_minutes ?? $task->duration,
                'actual_duration' => $task->actual_duration,
                'arrival_status' => $task->arrival_status,
                'started_at' => $task->started_at?->format('Y-m-d H:i:s'),
                'completed_at' => $task->completed_at?->format('Y-m-d H:i:s'),
                'on_hold_reason' => $task->on_hold_reason,
                'on_hold_timestamp' => $task->on_hold_timestamp?->format('Y-m-d H:i:s'),
                'assigned_employees' => $employees,
                'performance_flags' => $task->performanceFlags,
                'alerts' => $task->alerts,
            ],
        ]);
    }

    /**
     * Create a new task
     * Supports two assignment modes:
     * 1. Manual: Pass employee_ids array
     * 2. Auto: Pass auto_assign=true to use the optimization algorithm
     */
    public function createTask(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $request->validate([
            'location_id' => 'required|exists:locations,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'task_description' => 'required|string|max:500',
            'rate_type' => 'nullable|in:Normal,Student',
            'estimated_duration_minutes' => 'nullable|integer|min:15',
            'arrival_status' => 'nullable|boolean',
            'scheduled_time' => 'nullable|date_format:H:i',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            'auto_assign' => 'nullable|boolean', // New: Use optimization algorithm
        ]);

        // Verify location belongs to this contracted client
        $location = Location::where('id', $request->location_id)
            ->where('contracted_client_id', $contractedClient->id)
            ->first();

        if (!$location) {
            return response()->json([
                'message' => 'Location not found or access denied'
            ], 403);
        }

        // Create the task
        $task = Task::create([
            'location_id' => $location->id,
            'task_description' => $request->task_description,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'rate_type' => $request->rate_type ?? 'Normal',
            'estimated_duration_minutes' => $request->estimated_duration_minutes ?? $location->base_cleaning_duration_minutes,
            'duration' => $request->estimated_duration_minutes ?? $location->base_cleaning_duration_minutes,
            'arrival_status' => $request->arrival_status ?? false,
            'status' => 'Pending',
            'travel_time' => 0, // Default travel time
        ]);

        $assignedEmployees = [];
        $optimizationResult = null;

        // Check assignment mode
        if ($request->boolean('auto_assign')) {
            // ✅ AUTO-ASSIGN: Use Hybrid Rule-Based + Genetic Algorithm
            // ✅ OPTION B: Keep existing teams BUT create new teams if unused employees exist

            // Get ALL existing teams for this client/date
            $existingTeamIds = Task::whereIn('location_id', $contractedClient->locations()->pluck('id'))
                ->whereDate('scheduled_date', $request->scheduled_date)
                ->whereNotNull('assigned_team_id')
                ->whereNotIn('status', ['Cancelled'])
                ->pluck('assigned_team_id')
                ->unique();

            $existingTeams = OptimizationTeam::with('members.employee.user')
                ->whereIn('id', $existingTeamIds)
                ->get();

            // Check if there are unused employees who could form new teams
            $usedEmployeeIds = [];
            foreach ($existingTeams as $team) {
                foreach ($team->members as $member) {
                    $usedEmployeeIds[] = $member->employee_id;
                }
            }

            // Get all available employees for this date
            $allAvailableEmployees = Employee::where('is_active', true)
                ->whereDoesntHave('dayOffs', fn($q) => $q->whereDate('date', $request->scheduled_date))
                ->whereHas('user', fn($q) => $q->whereNull('deleted_at'))
                ->get();

            $unusedEmployees = $allAvailableEmployees->filter(fn($e) => !in_array($e->id, $usedEmployeeIds));
            $unusedDrivers = $unusedEmployees->filter(fn($e) => $e->has_driving_license);

            Log::info('Employee utilization check', [
                'task_id' => $task->id,
                'scheduled_date' => $request->scheduled_date,
                'total_available_employees' => $allAvailableEmployees->count(),
                'employees_in_existing_teams' => count($usedEmployeeIds),
                'unused_employees' => $unusedEmployees->count(),
                'unused_drivers' => $unusedDrivers->count(),
                'existing_teams_count' => $existingTeams->count(),
            ]);

            // ✅ DECISION: Run optimization if unused employees can form new teams
            // A new team needs at least 1 driver + 1 other person (min 2 people)
            $canFormNewTeams = $unusedDrivers->count() >= 1 && $unusedEmployees->count() >= 2;

            if ($canFormNewTeams) {
                // ✅ Run full optimization to create additional teams with unused employees
                Log::info('Running optimization to utilize unused employees', [
                    'task_id' => $task->id,
                    'unused_employees' => $unusedEmployees->pluck('id')->toArray(),
                    'reason' => 'Unused employees can form new teams',
                ]);

                try {
                    $optimizationService = app(OptimizationService::class);
                    $result = $optimizationService->optimizeSchedule(
                        $request->scheduled_date,
                        [$location->id],
                        $task->id
                    );

                    $optimizationResult = $result;

                    // Refresh task to get updated assignment
                    $task->refresh();
                    $task->load('optimizationTeam.members.employee.user');

                    if ($task->optimizationTeam && $task->optimizationTeam->members) {
                        foreach ($task->optimizationTeam->members as $member) {
                            if ($member->employee && $member->employee->user) {
                                $assignedEmployees[] = [
                                    'id' => $member->employee->id,
                                    'name' => $member->employee->user->name,
                                    'has_driving_license' => $member->employee->has_driving_license ?? false,
                                    'efficiency' => $member->employee->efficiency ?? 1.0,
                                ];
                            }
                        }
                    }

                    Log::info('Optimization completed - new teams created', [
                        'task_id' => $task->id,
                        'assigned_employees_count' => count($assignedEmployees),
                        'status' => $result['status'] ?? 'unknown',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Optimization failed', [
                        'task_id' => $task->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            } elseif ($existingTeams->isNotEmpty()) {
                // ✅ No unused employees - distribute to existing teams fairly
                // Find team with FEWEST TASKS (fair distribution)
                $selectedTeam = null;
                $minTaskCount = PHP_INT_MAX;

                foreach ($existingTeams as $team) {
                    $taskCount = Task::where('assigned_team_id', $team->id)
                        ->whereNotIn('status', ['Cancelled'])
                        ->count();

                    if ($taskCount < $minTaskCount) {
                        $minTaskCount = $taskCount;
                        $selectedTeam = $team;
                    }
                }

                if ($selectedTeam) {
                    Log::info('Assigning to existing team with fewest tasks', [
                        'task_id' => $task->id,
                        'selected_team_id' => $selectedTeam->id,
                        'team_task_count' => $minTaskCount,
                        'total_existing_teams' => $existingTeams->count(),
                    ]);

                    $task->update(['assigned_team_id' => $selectedTeam->id, 'status' => 'Scheduled']);

                    // Get employees from selected team
                    foreach ($selectedTeam->members as $member) {
                        if ($member->employee && $member->employee->user) {
                            $assignedEmployees[] = [
                                'id' => $member->employee->id,
                                'name' => $member->employee->user->name,
                                'has_driving_license' => $member->employee->has_driving_license ?? false,
                                'efficiency' => $member->employee->efficiency ?? 1.0,
                            ];
                        }
                    }

                    $optimizationResult = [
                        'status' => 'distributed_to_existing',
                        'message' => 'Task assigned to team with fewest tasks (all employees already utilized)',
                    ];
                }
            } else {
                // ✅ No existing teams - run full optimization
                try {
                    Log::info('No existing teams - running full optimization', [
                        'task_id' => $task->id,
                        'scheduled_date' => $request->scheduled_date,
                    ]);

                    $optimizationService = app(OptimizationService::class);
                    $result = $optimizationService->optimizeSchedule(
                        $request->scheduled_date,
                        [$location->id],
                        $task->id
                    );

                    $optimizationResult = $result;

                    // Refresh task to get updated assignment
                    $task->refresh();
                    $task->load('optimizationTeam.members.employee.user');

                    if ($task->optimizationTeam && $task->optimizationTeam->members) {
                        foreach ($task->optimizationTeam->members as $member) {
                            if ($member->employee && $member->employee->user) {
                                $assignedEmployees[] = [
                                    'id' => $member->employee->id,
                                    'name' => $member->employee->user->name,
                                    'has_driving_license' => $member->employee->has_driving_license ?? false,
                                    'efficiency' => $member->employee->efficiency ?? 1.0,
                                ];
                            }
                        }
                    }

                    Log::info('Full optimization completed', [
                        'task_id' => $task->id,
                        'assigned_employees_count' => count($assignedEmployees),
                        'status' => $result['status'] ?? 'unknown',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Auto-assign failed', [
                        'task_id' => $task->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } elseif ($request->has('employee_ids') && !empty($request->employee_ids)) {
            // ✅ MANUAL ASSIGN: Use provided employee IDs
            $team = OptimizationTeam::create([
                'optimization_run_id' => null,
                'car_id' => null,
            ]);

            foreach ($request->employee_ids as $employeeId) {
                OptimizationTeamMember::create([
                    'optimization_team_id' => $team->id,
                    'employee_id' => $employeeId,
                ]);

                $employee = Employee::with('user:id,name')->find($employeeId);
                if ($employee && $employee->user) {
                    $assignedEmployees[] = [
                        'id' => $employee->id,
                        'name' => $employee->user->name,
                    ];
                }
            }

            $task->update(['assigned_team_id' => $team->id]);
        }

        return response()->json([
            'message' => 'Task created successfully',
            'task' => [
                'id' => $task->id,
                'location_name' => $location->location_name,
                'description' => $task->task_description,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'status' => $task->status,
                'assigned_employees' => $assignedEmployees,
            ],
            'optimization' => $request->boolean('auto_assign') ? [
                'used' => true,
                'status' => $optimizationResult['status'] ?? 'unknown',
                'message' => $optimizationResult['message'] ?? null,
            ] : null,
        ], 201);
    }

    /**
     * Update a task
     */
    public function updateTask(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found or access denied'
            ], 404);
        }

        // Only allow updates if task is not completed
        if ($task->status === 'Completed') {
            return response()->json([
                'message' => 'Cannot update a completed task'
            ], 400);
        }

        $request->validate([
            'task_description' => 'nullable|string|max:500',
            'rate_type' => 'nullable|in:Normal,Student',
            'estimated_duration_minutes' => 'nullable|integer|min:15',
            'arrival_status' => 'nullable|boolean',
            'scheduled_time' => 'nullable|date_format:H:i',
        ]);

        $task->update($request->only([
            'task_description',
            'rate_type',
            'estimated_duration_minutes',
            'arrival_status',
            'scheduled_time',
        ]));

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => [
                'id' => $task->id,
                'description' => $task->task_description,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'status' => $task->status,
            ],
        ]);
    }

    /**
     * Cancel a task
     */
    public function cancelTask(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found or access denied'
            ], 404);
        }

        // Only allow cancellation if task is not completed or in progress
        if (in_array($task->status, ['Completed', 'In Progress'])) {
            return response()->json([
                'message' => 'Cannot cancel a task that is in progress or completed'
            ], 400);
        }

        $task->update(['status' => 'Cancelled']);

        return response()->json([
            'message' => 'Task cancelled successfully',
        ]);
    }

    /**
     * Get employees assigned to company's tasks
     */
    public function getEmployees(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Get date range (default: this week)
        $startDate = $request->get('start_date', Carbon::today()->startOfWeek()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->endOfWeek()->format('Y-m-d'));

        // Get team IDs from tasks for this contracted client
        $teamIds = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->whereNotNull('assigned_team_id')
            ->pluck('assigned_team_id')
            ->unique();

        // Get employees from these teams
        $employeeIds = \DB::table('optimization_team_members')
            ->whereIn('optimization_team_id', $teamIds)
            ->pluck('employee_id')
            ->unique();

        $employees = Employee::whereIn('id', $employeeIds)
            ->with('user:id,name,email,phone,profile_picture')
            ->get()
            ->map(function ($employee) use ($locationIds, $startDate, $endDate) {
                // Get task count for this employee in the date range
                $teamIds = \DB::table('optimization_team_members')
                    ->where('employee_id', $employee->id)
                    ->pluck('optimization_team_id');

                $taskCount = Task::whereIn('location_id', $locationIds)
                    ->whereIn('assigned_team_id', $teamIds)
                    ->whereBetween('scheduled_date', [$startDate, $endDate])
                    ->count();

                $completedCount = Task::whereIn('location_id', $locationIds)
                    ->whereIn('assigned_team_id', $teamIds)
                    ->whereBetween('scheduled_date', [$startDate, $endDate])
                    ->where('status', 'Completed')
                    ->count();

                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'Unknown',
                    'email' => $employee->user->email ?? null,
                    'phone' => $employee->user->phone ?? null,
                    'profile_picture' => $employee->user->profile_picture ?? null,
                    'skills' => $employee->skills,
                    'efficiency' => $employee->efficiency,
                    'years_of_experience' => $employee->years_of_experience,
                    'is_active' => $employee->is_active,
                    'task_count' => $taskCount,
                    'completed_count' => $completedCount,
                ];
            });

        return response()->json([
            'employees' => $employees,
            'count' => $employees->count(),
            'date_range' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
        ]);
    }

    /**
     * Get all available (active) employees for task assignment
     */
    public function getAvailableEmployees(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        // Get all active employees
        $employees = Employee::where('is_active', true)
            ->with('user:id,name,email,phone,profile_picture')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'Unknown',
                    'email' => $employee->user->email ?? null,
                    'phone' => $employee->user->phone ?? null,
                    'profile_picture' => $employee->user->profile_picture ?? null,
                    'skills' => $employee->skills ?? [],
                    'has_driving_license' => $employee->has_driving_license ?? false,
                    'efficiency' => $employee->efficiency ?? 1.0,
                ];
            });

        return response()->json([
            'employees' => $employees,
            'count' => $employees->count(),
        ]);
    }

    /**
     * Optimize schedule for a specific date using Hybrid Rule-Based + Genetic Algorithm
     * This endpoint triggers the full optimization workflow:
     * - Rule 1: Exclusive employee allocation per client
     * - Rule 2: Each team must have at least 1 driver
     * - Rule 3: Tasks sorted by priority (arrival_status) and time
     * - Rule 5: Maximize employee utilization
     * - Rule 6: Fair task distribution across teams
     * - Rule 7: 12-hour daily maximum per team
     */
    public function optimizeSchedule(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $serviceDate = $request->date;
        $locationIds = $contractedClient->locations()->pluck('id')->toArray();

        // Check if there are pending/scheduled tasks for this date
        $pendingTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $serviceDate)
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->count();

        if ($pendingTasks === 0) {
            return response()->json([
                'message' => 'No pending or scheduled tasks for this date',
                'date' => $serviceDate,
                'tasks_count' => 0,
            ], 400);
        }

        try {
            Log::info('Optimize schedule triggered from mobile', [
                'contracted_client_id' => $contractedClient->id,
                'service_date' => $serviceDate,
                'pending_tasks' => $pendingTasks,
            ]);

            $optimizationService = app(OptimizationService::class);
            $result = $optimizationService->optimizeSchedule($serviceDate, $locationIds);

            // Get updated tasks with assignments
            $tasks = Task::whereIn('location_id', $locationIds)
                ->whereDate('scheduled_date', $serviceDate)
                ->with(['optimizationTeam.members.employee.user'])
                ->get()
                ->map(function ($task) {
                    $employees = [];
                    if ($task->optimizationTeam && $task->optimizationTeam->members) {
                        foreach ($task->optimizationTeam->members as $member) {
                            if ($member->employee && $member->employee->user) {
                                $employees[] = [
                                    'id' => $member->employee->id,
                                    'name' => $member->employee->user->name,
                                    'has_driving_license' => $member->employee->has_driving_license ?? false,
                                ];
                            }
                        }
                    }
                    return [
                        'id' => $task->id,
                        'description' => $task->task_description,
                        'location_name' => $task->location->location_name ?? 'Unknown',
                        'status' => $task->status,
                        'assigned_employees' => $employees,
                    ];
                });

            return response()->json([
                'message' => 'Schedule optimized successfully',
                'date' => $serviceDate,
                'optimization' => [
                    'status' => $result['status'] ?? 'completed',
                    'statistics' => $result['statistics'] ?? null,
                ],
                'tasks' => $tasks,
                'total_tasks' => $tasks->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Optimize schedule failed', [
                'contracted_client_id' => $contractedClient->id,
                'service_date' => $serviceDate,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to optimize schedule: ' . $e->getMessage(),
                'date' => $serviceDate,
            ], 500);
        }
    }

    /**
     * Get reports/analytics for company
     */
    public function getReports(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Determine date range based on period
        $period = $request->get('period', 'month');
        $today = Carbon::today();

        switch ($period) {
            case 'week':
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
                break;
            case 'quarter':
                $startDate = $today->copy()->startOfQuarter();
                $endDate = $today->copy()->endOfQuarter();
                break;
            case 'year':
                $startDate = $today->copy()->startOfYear();
                $endDate = $today->copy()->endOfYear();
                break;
            case 'month':
            default:
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
                break;
        }

        // Override with custom dates if provided
        if ($request->has('start_date')) {
            $startDate = Carbon::parse($request->start_date);
        }
        if ($request->has('end_date')) {
            $endDate = Carbon::parse($request->end_date);
        }

        // Task statistics
        $tasks = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->get();

        $completedTasks = $tasks->where('status', 'Completed')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $pendingTasks = $tasks->whereIn('status', ['Pending', 'Scheduled'])->count();
        $totalTasks = $tasks->count();

        // Calculate completion rate
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 0) : 0;

        // Calculate total hours (from actual_duration or estimated_duration_minutes)
        $totalMinutes = $tasks->sum(function ($task) {
            return $task->actual_duration ?? $task->estimated_duration_minutes ?? 0;
        });
        $totalHours = round($totalMinutes / 60, 1);

        // Monthly data for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $today->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $today->copy()->subMonths($i)->endOfMonth();

            $monthTasks = Task::whereIn('location_id', $locationIds)
                ->whereBetween('scheduled_date', [$monthStart, $monthEnd])
                ->get();

            $monthlyData[] = [
                'month' => $monthStart->format('M'),
                'tasks' => $monthTasks->count(),
                'completed' => $monthTasks->where('status', 'Completed')->count(),
            ];
        }

        // Location stats
        $locationStats = $tasks->groupBy('location_id')
            ->map(function ($locationTasks, $locationId) use ($contractedClient) {
                $location = $contractedClient->locations()->find($locationId);
                $total = $locationTasks->count();
                $completed = $locationTasks->where('status', 'Completed')->count();
                $completionRate = $total > 0 ? round(($completed / $total) * 100, 0) : 0;

                return [
                    'location_id' => $locationId,
                    'name' => $location->location_name ?? 'Unknown',
                    'total' => $total,
                    'completed' => $completed,
                    'completion_rate' => $completionRate,
                ];
            })->values();

        // Top performers - employees with most completed tasks
        $teamIds = $tasks->pluck('assigned_team_id')->filter()->unique();

        $employeeTaskCounts = [];
        if ($teamIds->isNotEmpty()) {
            $teamMembers = \DB::table('optimization_team_members')
                ->whereIn('optimization_team_id', $teamIds)
                ->get();

            foreach ($teamMembers as $member) {
                $employeeTeamIds = \DB::table('optimization_team_members')
                    ->where('employee_id', $member->employee_id)
                    ->pluck('optimization_team_id');

                $empTasks = $tasks->whereIn('assigned_team_id', $employeeTeamIds->toArray());
                $taskCount = $empTasks->count();
                $completedCount = $empTasks->where('status', 'Completed')->count();

                if (!isset($employeeTaskCounts[$member->employee_id])) {
                    $employeeTaskCounts[$member->employee_id] = [
                        'employee_id' => $member->employee_id,
                        'task_count' => $taskCount,
                        'completed_count' => $completedCount,
                    ];
                }
            }
        }

        // Get employee details for top performers
        $topPerformers = collect($employeeTaskCounts)
            ->sortByDesc('completed_count')
            ->take(5)
            ->map(function ($data) {
                $employee = Employee::with('user:id,name')->find($data['employee_id']);
                return [
                    'id' => $data['employee_id'],
                    'name' => $employee && $employee->user ? $employee->user->name : 'Unknown',
                    'task_count' => $data['task_count'],
                    'completed_count' => $data['completed_count'],
                    'efficiency' => $employee ? $employee->efficiency : 0,
                ];
            })->values();

        return response()->json([
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'completion_rate' => $completionRate,
                'total_hours' => $totalHours,
            ],
            'monthly_data' => $monthlyData,
            'location_stats' => $locationStats,
            'top_performers' => $topPerformers,
        ]);
    }

    // ==================== CHECKLIST MANAGEMENT ====================

    /**
     * Get checklist for the company
     */
    public function getChecklist(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $checklist = CompanyChecklist::where('contracted_client_id', $contractedClient->id)
            ->where('is_active', true)
            ->with(['categories.items'])
            ->first();

        if (!$checklist) {
            return response()->json([
                'checklist' => null,
                'message' => 'No checklist found. Create one to get started.'
            ]);
        }

        return response()->json([
            'checklist' => [
                'id' => $checklist->id,
                'name' => $checklist->name,
                'important_reminders' => $checklist->important_reminders,
                'categories' => $checklist->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'sort_order' => $category->sort_order,
                        'items' => $category->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'quantity' => $item->quantity,
                                'sort_order' => $item->sort_order,
                            ];
                        }),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create a new checklist
     */
    public function createChecklist(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'important_reminders' => 'nullable|string',
        ]);

        // Deactivate existing checklists
        CompanyChecklist::where('contracted_client_id', $contractedClient->id)
            ->update(['is_active' => false]);

        $checklist = CompanyChecklist::create([
            'contracted_client_id' => $contractedClient->id,
            'name' => $request->name,
            'important_reminders' => $request->important_reminders,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Checklist created successfully',
            'checklist' => [
                'id' => $checklist->id,
                'name' => $checklist->name,
            ],
        ], 201);
    }

    /**
     * Update checklist details
     */
    public function updateChecklist(Request $request, $checklistId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $checklist = CompanyChecklist::where('id', $checklistId)
            ->where('contracted_client_id', $contractedClient->id)
            ->first();

        if (!$checklist) {
            return response()->json([
                'message' => 'Checklist not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'important_reminders' => 'nullable|string',
        ]);

        $checklist->update($request->only(['name', 'important_reminders']));

        return response()->json([
            'message' => 'Checklist updated successfully',
        ]);
    }

    /**
     * Add a new category to checklist
     */
    public function addCategory(Request $request, $checklistId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $checklist = CompanyChecklist::where('id', $checklistId)
            ->where('contracted_client_id', $contractedClient->id)
            ->first();

        if (!$checklist) {
            return response()->json([
                'message' => 'Checklist not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $maxOrder = $checklist->categories()->max('sort_order') ?? -1;

        $category = ChecklistCategory::create([
            'checklist_id' => $checklist->id,
            'name' => $request->name,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'message' => 'Category added successfully',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'sort_order' => $category->sort_order,
                'items' => [],
            ],
        ], 201);
    }

    /**
     * Update a category
     */
    public function updateCategory(Request $request, $categoryId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->find($categoryId);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $category->update($request->only(['name', 'sort_order']));

        return response()->json([
            'message' => 'Category updated successfully',
        ]);
    }

    /**
     * Delete a category
     */
    public function deleteCategory(Request $request, $categoryId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->find($categoryId);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Add a new item to a category
     */
    public function addItem(Request $request, $categoryId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $category = ChecklistCategory::whereHas('checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->find($categoryId);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|string|max:100',
        ]);

        $maxOrder = $category->items()->max('sort_order') ?? -1;

        $item = ChecklistItem::create([
            'category_id' => $category->id,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'message' => 'Item added successfully',
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'sort_order' => $item->sort_order,
            ],
        ], 201);
    }

    /**
     * Update an item
     */
    public function updateItem(Request $request, $itemId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $item = ChecklistItem::whereHas('category.checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->find($itemId);

        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $item->update($request->only(['name', 'quantity', 'sort_order']));

        return response()->json([
            'message' => 'Item updated successfully',
        ]);
    }

    /**
     * Delete an item
     */
    public function deleteItem(Request $request, $itemId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $item = ChecklistItem::whereHas('category.checklist', function ($q) use ($contractedClient) {
            $q->where('contracted_client_id', $contractedClient->id);
        })->find($itemId);

        if (!$item) {
            return response()->json([
                'message' => 'Item not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully',
        ]);
    }

    // ==================== HOLIDAY CHECK ====================

    /**
     * Check if a date is a holiday
     * Returns holiday status and name if applicable
     */
    public function checkHoliday(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $dateObj = Carbon::parse($request->date);

        // Check if the date is a Sunday
        $isSunday = $dateObj->dayOfWeek === Carbon::SUNDAY;

        // Check if the date is a holiday in the database
        $holiday = \DB::table('holidays')
            ->where('date', $date)
            ->first();

        $isHoliday = !is_null($holiday);

        return response()->json([
            'date' => $date,
            'is_sunday' => $isSunday,
            'is_holiday' => $isHoliday,
            'is_sunday_or_holiday' => $isSunday || $isHoliday,
            'holiday_name' => $holiday ? $holiday->name : null,
        ]);
    }

    // ==================== ACTIVITIES/NOTIFICATIONS ====================

    /**
     * Get real-time activities for the manager
     * Tracks: task status changes, checklist edits, task assignments
     */
    public function getActivities(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Get limit from request (default 20)
        $limit = $request->get('limit', 20);

        // Get activities from the last 7 days
        $since = Carbon::now()->subDays(7);

        $activities = collect();

        // 1. Get recent task status changes (from tasks table - based on updated_at)
        $recentTasks = Task::whereIn('location_id', $locationIds)
            ->where('updated_at', '>=', $since)
            ->with(['location:id,location_name', 'optimizationTeam.members.employee.user:id,name'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        foreach ($recentTasks as $task) {
            $activityType = $this->mapTaskStatusToActivityType($task->status);
            $employees = [];
            if ($task->optimizationTeam && $task->optimizationTeam->members) {
                foreach ($task->optimizationTeam->members as $member) {
                    if ($member->employee && $member->employee->user) {
                        $employees[] = $member->employee->user->name;
                    }
                }
            }

            $description = $task->task_description . ' at ' . ($task->location->location_name ?? 'Unknown');
            if (!empty($employees)) {
                $description .= ' - ' . implode(', ', $employees);
            }

            $activities->push([
                'id' => 'task_' . $task->id . '_' . $task->updated_at->timestamp,
                'type' => $activityType,
                'category' => 'tasks',
                'description' => $description,
                'timestamp' => $task->updated_at->toIso8601String(),
                'meta' => [
                    'task_id' => $task->id,
                    'status' => $task->status,
                    'location' => $task->location->location_name ?? 'Unknown',
                ],
            ]);
        }

        // 2. Get recent checklist changes
        $checklist = CompanyChecklist::where('contracted_client_id', $contractedClient->id)
            ->where('is_active', true)
            ->with(['categories.items'])
            ->first();

        if ($checklist) {
            // Check checklist updates
            if ($checklist->updated_at >= $since) {
                $activities->push([
                    'id' => 'checklist_' . $checklist->id . '_' . $checklist->updated_at->timestamp,
                    'type' => 'checklist_updated',
                    'category' => 'checklist',
                    'description' => 'Checklist "' . $checklist->name . '" was updated',
                    'timestamp' => $checklist->updated_at->toIso8601String(),
                    'meta' => [
                        'checklist_id' => $checklist->id,
                        'checklist_name' => $checklist->name,
                    ],
                ]);
            }

            // Check category changes
            foreach ($checklist->categories as $category) {
                if ($category->updated_at >= $since && $category->updated_at != $category->created_at) {
                    $activities->push([
                        'id' => 'category_' . $category->id . '_' . $category->updated_at->timestamp,
                        'type' => 'checklist_category_updated',
                        'category' => 'checklist',
                        'description' => 'Category "' . $category->name . '" was updated',
                        'timestamp' => $category->updated_at->toIso8601String(),
                        'meta' => [
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                        ],
                    ]);
                }

                if ($category->created_at >= $since) {
                    $activities->push([
                        'id' => 'category_created_' . $category->id,
                        'type' => 'checklist_category_added',
                        'category' => 'checklist',
                        'description' => 'Category "' . $category->name . '" was added',
                        'timestamp' => $category->created_at->toIso8601String(),
                        'meta' => [
                            'category_id' => $category->id,
                            'category_name' => $category->name,
                        ],
                    ]);
                }

                // Check item changes
                foreach ($category->items as $item) {
                    if ($item->updated_at >= $since && $item->updated_at != $item->created_at) {
                        $activities->push([
                            'id' => 'item_' . $item->id . '_' . $item->updated_at->timestamp,
                            'type' => 'checklist_item_updated',
                            'category' => 'checklist',
                            'description' => 'Item "' . $item->name . '" in "' . $category->name . '" was updated',
                            'timestamp' => $item->updated_at->toIso8601String(),
                            'meta' => [
                                'item_id' => $item->id,
                                'item_name' => $item->name,
                                'category_name' => $category->name,
                            ],
                        ]);
                    }

                    if ($item->created_at >= $since) {
                        $activities->push([
                            'id' => 'item_created_' . $item->id,
                            'type' => 'checklist_item_added',
                            'category' => 'checklist',
                            'description' => 'Item "' . $item->name . '" was added to "' . $category->name . '"',
                            'timestamp' => $item->created_at->toIso8601String(),
                            'meta' => [
                                'item_id' => $item->id,
                                'item_name' => $item->name,
                                'category_name' => $category->name,
                            ],
                        ]);
                    }
                }
            }
        }

        // Sort by timestamp descending and limit
        $sortedActivities = $activities->sortByDesc('timestamp')->take($limit)->values();

        return response()->json([
            'activities' => $sortedActivities,
            'count' => $sortedActivities->count(),
        ]);
    }

    /**
     * Map task status to activity type
     */
    private function mapTaskStatusToActivityType(string $status): string
    {
        return match (strtolower($status)) {
            'pending' => 'task_created',
            'scheduled' => 'task_scheduled',
            'in-progress' => 'task_in_progress',
            'completed' => 'task_completed',
            'on hold' => 'task_on_hold',
            'cancelled' => 'task_cancelled',
            default => 'task_updated',
        };
    }

    // ==================== BILLING REPORT ====================

    /**
     * Get billing report for the contracted client
     * Shows estimated billing based on completed tasks and rates
     */
    public function getBillingReport(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        // Determine date range (default to current month)
        $startDate = $request->has('start_date')
            ? Carbon::parse($request->start_date)
            : Carbon::now()->startOfMonth();
        $endDate = $request->has('end_date')
            ? Carbon::parse($request->end_date)
            : Carbon::now()->endOfMonth();

        // Get locations for this client with rates
        $locations = $contractedClient->locations()->get();
        $locationIds = $locations->pluck('id');

        // Get holidays in the date range
        $holidays = \DB::table('holidays')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Get all tasks for this client in the date range
        $tasks = Task::whereIn('location_id', $locationIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with('location')
            ->get();

        // Calculate billing for each task
        $taskBilling = [];
        $totalBilling = 0;
        $billingByLocation = [];
        $billingByRateType = [
            'normal_weekday' => 0,
            'normal_sunday_holiday' => 0,
            'student_weekday' => 0,
            'student_sunday_holiday' => 0,
        ];

        foreach ($tasks as $task) {
            $location = $task->location;
            if (!$location) continue;

            $scheduledDate = Carbon::parse($task->scheduled_date);
            $isSunday = $scheduledDate->dayOfWeek === Carbon::SUNDAY;
            $isHoliday = in_array($scheduledDate->format('Y-m-d'), $holidays);
            $isSundayOrHoliday = $isSunday || $isHoliday;

            // Determine rate based on rate_type and day
            $rate = 0;
            $rateCategory = '';

            if ($task->rate_type === 'Student') {
                if ($isSundayOrHoliday) {
                    $rate = $location->student_sunday_holiday_rate ?? 0;
                    $rateCategory = 'student_sunday_holiday';
                } else {
                    $rate = $location->student_rate ?? 0;
                    $rateCategory = 'student_weekday';
                }
            } else {
                // Normal rate
                if ($isSundayOrHoliday) {
                    $rate = $location->sunday_holiday_rate ?? 0;
                    $rateCategory = 'normal_sunday_holiday';
                } else {
                    $rate = $location->normal_rate_per_hour ?? 0;
                    $rateCategory = 'normal_weekday';
                }
            }

            // Only count completed tasks for actual billing
            $billableAmount = $task->status === 'Completed' ? $rate : 0;

            $taskBilling[] = [
                'task_id' => $task->id,
                'location' => $location->location_name,
                'date' => $scheduledDate->format('Y-m-d'),
                'rate_type' => $task->rate_type ?? 'Normal',
                'is_sunday_holiday' => $isSundayOrHoliday,
                'rate' => $rate,
                'status' => $task->status,
                'billable_amount' => $billableAmount,
            ];

            // Accumulate totals (only for completed tasks)
            if ($task->status === 'Completed') {
                $totalBilling += $billableAmount;
                $billingByRateType[$rateCategory] += $billableAmount;

                if (!isset($billingByLocation[$location->id])) {
                    $billingByLocation[$location->id] = [
                        'location_id' => $location->id,
                        'location_name' => $location->location_name,
                        'total_tasks' => 0,
                        'completed_tasks' => 0,
                        'total_amount' => 0,
                    ];
                }
                $billingByLocation[$location->id]['completed_tasks']++;
                $billingByLocation[$location->id]['total_amount'] += $billableAmount;
            }

            // Count total tasks per location
            if (isset($billingByLocation[$location->id])) {
                $billingByLocation[$location->id]['total_tasks']++;
            } else {
                $billingByLocation[$location->id] = [
                    'location_id' => $location->id,
                    'location_name' => $location->location_name,
                    'total_tasks' => 1,
                    'completed_tasks' => 0,
                    'total_amount' => 0,
                ];
            }
        }

        // Summary statistics
        $summary = [
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'Completed')->count(),
            'pending_tasks' => $tasks->whereIn('status', ['Pending', 'Scheduled'])->count(),
            'in_progress_tasks' => $tasks->where('status', 'In Progress')->count(),
            'total_billing' => round($totalBilling, 2),
        ];

        return response()->json([
            'company' => [
                'id' => $contractedClient->id,
                'name' => $contractedClient->name,
            ],
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => $summary,
            'billing_by_rate_type' => $billingByRateType,
            'billing_by_location' => array_values($billingByLocation),
            'task_details' => $taskBilling,
        ]);
    }

    // ==================== TASK REVIEWS ====================

    /**
     * Submit a review for a completed task
     */
    public function submitTaskReview(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Find the task and verify ownership
        $task = Task::whereIn('location_id', $locationIds)->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found or access denied'
            ], 404);
        }

        // Only allow reviews for completed tasks
        if ($task->status !== 'Completed') {
            return response()->json([
                'message' => 'Only completed tasks can be reviewed'
            ], 400);
        }

        // Check if task already has a review
        if ($task->hasReview()) {
            return response()->json([
                'message' => 'This task has already been reviewed'
            ], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_tags' => 'nullable|array',
            'feedback_tags.*' => 'string',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Create the review
        $review = TaskReview::create([
            'task_id' => $task->id,
            'contracted_client_id' => $contractedClient->id,
            'reviewer_user_id' => $request->user()->id,
            'rating' => $request->rating,
            'feedback_tags' => $request->feedback_tags ?? [],
            'review_text' => $request->review_text,
            'metadata' => [
                'task_description' => $task->task_description,
                'location_name' => $task->location->location_name ?? null,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'submitted_at' => now()->toIso8601String(),
            ],
        ]);

        Log::info('Task review submitted', [
            'task_id' => $task->id,
            'contracted_client_id' => $contractedClient->id,
            'rating' => $request->rating,
            'reviewer_user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => [
                'id' => $review->id,
                'task_id' => $review->task_id,
                'rating' => $review->rating,
                'feedback_tags' => $review->feedback_tags,
                'review_text' => $review->review_text,
                'created_at' => $review->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Check if a task has been reviewed
     */
    public function checkTaskReview(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)
            ->with('review')
            ->find($taskId);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found or access denied'
            ], 404);
        }

        $hasReview = $task->review !== null;

        return response()->json([
            'task_id' => $task->id,
            'has_review' => $hasReview,
            'review' => $hasReview ? [
                'id' => $task->review->id,
                'rating' => $task->review->rating,
                'feedback_tags' => $task->review->feedback_tags,
                'review_text' => $task->review->review_text,
                'created_at' => $task->review->created_at->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * Get review statistics for the company
     * For admin monitoring of service quality
     */
    public function getReviewStatistics(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $statistics = TaskReview::getStatisticsForClient($contractedClient->id);

        // Get recent reviews
        $recentReviews = TaskReview::where('contracted_client_id', $contractedClient->id)
            ->with(['task.location'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'task_id' => $review->task_id,
                    'task_description' => $review->task->task_description ?? 'Unknown',
                    'location_name' => $review->task->location->location_name ?? 'Unknown',
                    'rating' => $review->rating,
                    'feedback_tags' => $review->feedback_tags,
                    'review_text' => $review->review_text,
                    'created_at' => $review->created_at->toIso8601String(),
                ];
            });

        // Calculate tag frequency
        $allTags = TaskReview::where('contracted_client_id', $contractedClient->id)
            ->whereNotNull('feedback_tags')
            ->pluck('feedback_tags')
            ->flatten()
            ->countBy()
            ->sortDesc();

        return response()->json([
            'company' => [
                'id' => $contractedClient->id,
                'name' => $contractedClient->name,
            ],
            'statistics' => $statistics,
            'tag_frequency' => $allTags,
            'recent_reviews' => $recentReviews,
        ]);
    }

    /**
     * Get profile statistics for company/manager dashboard
     * Returns task counts for History, Approved, Ongoing, and Completed
     */
    public function getProfileStats(Request $request)
    {
        $contractedClient = $this->getContractedClient($request);

        if (!$contractedClient) {
            return response()->json([
                'message' => 'No contracted client found for this user'
            ], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        // Get all tasks for this company's locations
        $tasks = Task::whereIn('location_id', $locationIds)->get();

        // Calculate stats
        $totalTasks = $tasks->count();
        $ongoingTasks = $tasks->where('status', 'In Progress')->count();
        $completedTasks = $tasks->where('status', 'Completed')->count();

        return response()->json([
            'stats' => [
                'history' => $totalTasks,      // Total tasks created
                'approved' => $totalTasks,     // Number of tasks created/approved
                'ongoing' => $ongoingTasks,    // In Progress tasks
                'completed' => $completedTasks, // Completed tasks
            ],
        ]);
    }
}
