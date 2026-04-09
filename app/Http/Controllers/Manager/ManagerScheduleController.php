<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Location;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ManagerScheduleController extends Controller
{
    private function getContractedClient()
    {
        return ContractedClient::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Display the schedule page.
     */
    public function index()
    {
        $contractedClient = $this->getContractedClient();

        $locationTypes = [];
        $totalLocations = 0;
        $companyAddress = 'N/A';
        $companyCityState = '';
        $companyStreetAddress = '';
        $locationsByType = [];
        $typesAddedLastMonth = 0;
        $locationsAddedLastMonth = 0;

        if ($contractedClient) {
            $locationTypes = Location::where('contracted_client_id', $contractedClient->id)
                ->selectRaw('location_type, COUNT(*) as count')
                ->groupBy('location_type')
                ->pluck('count', 'location_type')
                ->toArray();

            $totalLocations = array_sum($locationTypes);
            $companyAddress = $contractedClient->address ?? 'N/A';

            // Parse address into city/state and street
            $addressParts = array_map('trim', explode(',', $companyAddress));
            if (count($addressParts) >= 3) {
                // Assume format: Street, City, State/Country (or similar)
                $companyStreetAddress = $addressParts[0];
                $companyCityState = implode(', ', array_slice($addressParts, 1));
            } elseif (count($addressParts) === 2) {
                $companyStreetAddress = $addressParts[0];
                $companyCityState = $addressParts[1];
            } else {
                $companyCityState = $companyAddress;
                $companyStreetAddress = '';
            }

            // Get location names grouped by type for tooltips
            $locationsByType = Location::where('contracted_client_id', $contractedClient->id)
                ->select('location_type', 'location_name')
                ->orderBy('location_type')
                ->orderBy('location_name')
                ->get()
                ->groupBy('location_type')
                ->map(fn($group) => $group->pluck('location_name')->toArray())
                ->toArray();

            // Count locations added in the last month
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $locationsAddedLastMonth = Location::where('contracted_client_id', $contractedClient->id)
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->count();

            // Count distinct types added in the last month
            $typesAddedLastMonth = Location::where('contracted_client_id', $contractedClient->id)
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->distinct('location_type')
                ->count('location_type');
        }

        $minimumBookingNoticeDays = \App\Services\CompanySettingService::get('minimum_booking_notice_days', 3);
        return view('manager.schedule', compact(
            'locationTypes',
            'totalLocations',
            'companyAddress',
            'companyCityState',
            'companyStreetAddress',
            'locationsByType',
            'typesAddedLastMonth',
            'locationsAddedLastMonth',
            'minimumBookingNoticeDays'
        ));
    }

    /**
     * Get tasks for a specific date (API endpoint).
     */
    public function getTasks(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['tasks' => []]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $tasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $date)
            ->with(['location', 'assignedEmployees.user'])
            ->orderBy('scheduled_time')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'location_id' => $task->location_id,
                    'location_name' => $task->location->name ?? 'Unknown Location',
                    'location_type' => $task->location->location_type ?? '',
                    'scheduled_time' => $task->scheduled_time ? Carbon::parse($task->scheduled_time)->format('H:i') : null,
                    // ✅ STAGE 2: GA-computed start/end times (HH:MM format) so the
                    //   schedule list can show the real per-task timeline.
                    //   Falls back to scheduled_time when the task hasn't been optimized yet.
                    'optimized_start' => $task->optimized_start_minutes !== null
                        ? sprintf('%02d:%02d', intdiv($task->optimized_start_minutes, 60), $task->optimized_start_minutes % 60)
                        : null,
                    'optimized_end' => $task->optimized_end_minutes !== null
                        ? sprintf('%02d:%02d', intdiv($task->optimized_end_minutes, 60), $task->optimized_end_minutes % 60)
                        : null,
                    'duration' => $task->duration,
                    'status' => $task->status,
                    'cabin_status' => $task->cabin_status ?? null,
                    'rate_type' => $task->rate_type ?? 'Normal',
                    'task_description' => $task->task_description ?? '',
                    'arrival_status' => $task->arrival_status ?? 0,
                    'extra_bed' => $task->extra_bed ?? 0,
                    'assigned_team_id' => $task->assigned_team_id,
                    'employee_approved' => $task->employee_approved,
                    'declined_by' => $task->employee_approved === false
                        ? optional(\App\Models\User::find($task->approved_by))->name
                        : null,
                    'employee_count' => $task->assignedEmployees->count(),
                    'employees' => $task->assignedEmployees->map(fn($e) => [
                        'id' => $e->id,
                        'name' => $e->user->name ?? 'Unknown',
                    ]),
                ];
            });

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * Get locations grouped by type with occupancy status for a date.
     */
    public function getLocations(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['locations' => [], 'grouped' => []]);
        }

        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $locations = Location::where('contracted_client_id', $contractedClient->id)
            ->orderBy('location_name')
            ->get();

        // Get tasks for the date to check occupancy
        $locationIds = $locations->pluck('id');
        $occupiedTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $date)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->get()
            ->groupBy('location_id');

        $locationData = $locations->map(function ($location) use ($occupiedTasks) {
            $task = $occupiedTasks->get($location->id)?->first();
            return [
                'id' => $location->id,
                'location_name' => $location->location_name,
                'name' => $location->name,
                'location_type' => $location->location_type,
                'base_cleaning_duration_minutes' => $location->base_cleaning_duration_minutes ?? 60,
                'is_occupied' => $occupiedTasks->has($location->id),
                'task_status' => $task ? $task->status : null,
            ];
        });

        // Group locations by type (extract base name)
        $grouped = [];
        foreach ($locationData as $loc) {
            // Extract base name: "Small Cabin #1" → "Small Cabin"
            if (preg_match('/^(.+?)\s*#?\d*$/', $loc['location_name'], $match)) {
                $groupName = trim($match[1]);
            } else {
                $groupName = $loc['location_name'];
            }

            if (!isset($grouped[$groupName])) {
                $grouped[$groupName] = [
                    'name' => $groupName,
                    'type' => $loc['location_type'],
                    'duration' => $loc['base_cleaning_duration_minutes'],
                    'items' => [],
                ];
            }
            $grouped[$groupName]['items'][] = $loc;
        }

        // Natural sort items within each group so #1, #2, #10 display in numeric order
        foreach ($grouped as &$group) {
            usort($group['items'], function ($a, $b) {
                return strnatcasecmp($a['location_name'], $b['location_name']);
            });
        }
        unset($group);

        // Natural sort the groups themselves
        uksort($grouped, 'strnatcasecmp');

        return response()->json([
            'locations' => $locationData,
            'grouped' => array_values($grouped),
        ]);
    }

    /**
     * Get available employees (API endpoint).
     */
    public function getAvailableEmployees(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $employees = Employee::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            })
            ->get()
            ->map(function ($employee) use ($date) {
                $taskCount = $employee->tasks()
                    ->whereDate('scheduled_date', $date)
                    ->count();

                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'Unknown',
                    'has_driving_license' => $employee->has_driving_license ?? false,
                    'tasks_today' => $taskCount,
                ];
            });

        return response()->json(['employees' => $employees]);
    }

    /**
     * Check if a date is a holiday or Sunday.
     */
    public function checkHoliday(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);

        $holiday = Holiday::whereDate('date', $date)->first();
        $isSunday = $carbonDate->isSunday();

        return response()->json([
            'date' => $date,
            'is_holiday' => $holiday !== null,
            'holiday_name' => $holiday?->name,
            'is_sunday' => $isSunday,
            'is_sunday_or_holiday' => $isSunday || $holiday !== null,
        ]);
    }

    /**
     * Store new task(s) - supports batch creation for multiple locations.
     */
    public function storeTask(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'location_ids' => 'required|array|min:1',
            'location_ids.*' => 'exists:locations,id',
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'rate_type' => 'required|in:Normal,Student,Holiday,Extra',
            'cabin_status' => 'required|in:departure,daily_clean,arrival',
            'arrival_status' => 'nullable|integer|min:0|max:2',
            'extra_bed' => 'nullable|boolean',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'auto_assign' => 'nullable|boolean',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
            // Extra task fields
            'extra_task_enabled' => 'nullable|boolean',
            'extra_task_name' => 'nullable|string|max:255',
            'extra_task_price' => 'nullable|numeric|min:0',
            'extra_task_duration' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // Scenario #1: Enforce minimum booking notice (configurable, default 3 days)
        $minimumNoticeDays = \App\Services\CompanySettingService::get('minimum_booking_notice_days', 3);
        $scheduledDate = Carbon::parse($request->scheduled_date)->startOfDay();
        $earliestAllowed = Carbon::today()->addDays($minimumNoticeDays)->startOfDay();

        if ($scheduledDate->lt($earliestAllowed)) {
            return response()->json([
                'message' => "Tasks must be booked at least {$minimumNoticeDays} days in advance. Earliest available date: {$earliestAllowed->format('M d, Y')}.",
                'errors' => ['scheduled_date' => ["Minimum {$minimumNoticeDays}-day booking notice required."]],
            ], 422);
        }

        // Verify all locations belong to this contracted client
        $validLocationIds = Location::where('contracted_client_id', $contractedClient->id)
            ->whereIn('id', $request->location_ids)
            ->pluck('id', 'id');

        $invalidLocations = array_diff($request->location_ids, $validLocationIds->keys()->toArray());
        if (!empty($invalidLocations)) {
            return response()->json(['message' => 'Some locations are invalid'], 403);
        }

        $serviceTypeLabel = match ($request->cabin_status) {
            'departure' => 'Departure',
            'arrival' => 'Arrival',
            'daily_clean' => 'Daily Clean',
            default => 'Cleaning',
        };

        $createdTasks = [];
        $totalAssigned = 0;

        // Create a task for each selected location
        foreach ($request->location_ids as $locationId) {
            $location = Location::find($locationId);
            if (!$location) continue;

            $taskData = [
                'location_id' => $locationId,
                'client_id' => $contractedClient->id,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'rate_type' => $request->rate_type,
                'task_description' => $serviceTypeLabel,
                'status' => 'Scheduled',
                'cabin_status' => $request->cabin_status,
                'arrival_status' => $request->arrival_status ?? 0,
                'extra_bed' => $request->extra_bed ? 1 : 0,
                'duration' => $request->estimated_duration_minutes ?? $location->base_cleaning_duration_minutes ?? 60,
                'estimated_duration_minutes' => $request->estimated_duration_minutes ?? $location->base_cleaning_duration_minutes ?? 60,
            ];

            if ($request->notes) {
                $taskData['task_description'] = $serviceTypeLabel . ' - ' . $request->notes;
            }

            $task = Task::create($taskData);

            // Handle employee assignment
            if ($request->auto_assign) {
                $taskData['auto_assign'] = true;
            } elseif ($request->employee_ids && count($request->employee_ids) > 0) {
                // Manual employee assignment via optimization_team_members if the relationship supports it
                foreach ($request->employee_ids as $employeeId) {
                    try {
                        \DB::table('optimization_team_members')->insert([
                            'task_id' => $task->id,
                            'employee_id' => $employeeId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $totalAssigned++;
                    } catch (\Exception $e) {
                        Log::warning('Could not assign employee to task', [
                            'task_id' => $task->id,
                            'employee_id' => $employeeId,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            $createdTasks[] = [
                'id' => $task->id,
                'location_name' => $location->name,
                'status' => $task->status,
            ];
        }

        // Create Extra Task if enabled
        $extraTaskCreated = null;
        if ($request->extra_task_enabled && $request->extra_task_name) {
            $firstLocationId = $request->location_ids[0];
            $firstLocation = Location::find($firstLocationId);

            // Check holiday for price calculation
            $date = Carbon::parse($request->scheduled_date);
            $holiday = Holiday::whereDate('date', $request->scheduled_date)->first();
            $isSundayOrHoliday = $date->isSunday() || $holiday !== null;

            $basePrice = $request->extra_task_price ?? 16;
            $finalPrice = $isSundayOrHoliday ? $basePrice * 1.5 : $basePrice;

            $extraDescription = $request->extra_task_name . "\nExtra Task\n" . ($firstLocation->name ?? '') . ' • ' . ($request->extra_task_duration ?? 30) . ' min';

            $extraTaskData = [
                'location_id' => $firstLocationId,
                'client_id' => $contractedClient->id,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'rate_type' => $request->rate_type,
                'task_description' => $extraDescription,
                'status' => 'Scheduled',
                'cabin_status' => 'extra_task',
                'arrival_status' => 0,
                'extra_bed' => 0,
                'duration' => $request->extra_task_duration ?? 30,
                'estimated_duration_minutes' => $request->extra_task_duration ?? 30,
                'price' => $finalPrice,
            ];

            $extraTask = Task::create($extraTaskData);
            $extraTaskCreated = [
                'id' => $extraTask->id,
                'name' => $request->extra_task_name,
                'final_price' => number_format($finalPrice, 2),
            ];
        }

        // If auto-assign, trigger optimization
        if ($request->auto_assign && count($createdTasks) > 0) {
            try {
                if (class_exists(\App\Services\Optimization\OptimizationService::class)) {
                    $optimizationService = app(\App\Services\Optimization\OptimizationService::class);
                    $locationIds = $contractedClient->locations()->pluck('id')->toArray();
                    $optimizationService->optimizeSchedule($request->scheduled_date, $locationIds);
                }
            } catch (\Exception $e) {
                Log::warning('Auto-assign optimization failed', ['error' => $e->getMessage()]);
            }
        }

        $message = count($createdTasks) . ' task(s) created successfully';
        if ($request->auto_assign) {
            $message .= ' and sent for optimization';
        }
        if ($extraTaskCreated) {
            $message .= '. Extra task "' . $request->extra_task_name . '" also created';
        }

        return response()->json([
            'message' => $message,
            'tasks' => $createdTasks,
            'extra_task' => $extraTaskCreated,
        ], 201);
    }

    /**
     * Cancel a task (API endpoint).
     */
    public function cancelTask(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)
            ->where('id', $taskId)
            ->whereIn('status', ['Scheduled', 'Pending'])
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found or cannot be cancelled'], 404);
        }

        $task->update(['status' => 'Cancelled']);

        return response()->json(['message' => 'Task cancelled successfully']);
    }

    /**
     * Run schedule optimization for a specific date.
     */
    public function optimize(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $request->validate(['date' => 'required|date']);

        $serviceDate = $request->date;
        $locationIds = $contractedClient->locations()->pluck('id')->toArray();

        $pendingTasks = Task::whereIn('location_id', $locationIds)
            ->whereDate('scheduled_date', $serviceDate)
            ->whereIn('status', ['Pending', 'Scheduled'])
            ->count();

        if ($pendingTasks === 0) {
            return response()->json([
                'message' => 'No pending or scheduled tasks for this date to optimize',
                'tasks_count' => 0,
            ], 400);
        }

        try {
            Log::info('Schedule optimization triggered from web', [
                'contracted_client_id' => $contractedClient->id,
                'service_date' => $serviceDate,
                'pending_tasks' => $pendingTasks,
            ]);

            if (class_exists(\App\Services\Optimization\OptimizationService::class)) {
                $optimizationService = app(\App\Services\Optimization\OptimizationService::class);
                $result = $optimizationService->optimizeSchedule($serviceDate, $locationIds);
            }

            $tasks = Task::whereIn('location_id', $locationIds)
                ->whereDate('scheduled_date', $serviceDate)
                ->with(['location', 'assignedEmployees.user'])
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'location_name' => $task->location->name ?? 'Unknown',
                        'status' => $task->status,
                        'employee_count' => $task->assignedEmployees->count(),
                    ];
                });

            return response()->json([
                'message' => "Optimization completed. {$pendingTasks} tasks processed.",
                'tasks' => $tasks,
            ]);
        } catch (\Exception $e) {
            Log::error('Schedule optimization failed', [
                'error' => $e->getMessage(),
                'contracted_client_id' => $contractedClient->id,
            ]);

            return response()->json(['message' => 'Optimization failed: ' . $e->getMessage()], 500);
        }
    }
}
