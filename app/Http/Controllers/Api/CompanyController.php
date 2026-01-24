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
            'in_progress' => $todayTasks->where('status', 'In-Progress')->count(),
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
            ->with(['location:id,location_name,location_type', 'optimizationTeam.members.employee.user:id,name']);

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
            try {
                Log::info('Auto-assign triggered for task', [
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

                Log::info('Auto-assign completed', [
                    'task_id' => $task->id,
                    'assigned_employees_count' => count($assignedEmployees),
                    'status' => $result['status'] ?? 'unknown',
                ]);
            } catch (\Exception $e) {
                Log::error('Auto-assign failed', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
                // Task is still created, just not auto-assigned
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
        if (in_array($task->status, ['Completed', 'In-Progress'])) {
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
        $inProgressTasks = $tasks->where('status', 'In-Progress')->count();
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
}
