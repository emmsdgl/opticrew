<?php

namespace App\Services\Optimization\PreProcessing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

// use App\Models\Task;
// use App\Models\Employee;

class RuleBasedPreProcessor
{
    protected EmployeeFilter $employeeFilter;
    protected TaskValidator $taskValidator;
    protected WorkforceCalculator $workforceCalculator;

    public function __construct(
        EmployeeFilter $employeeFilter,
        TaskValidator $taskValidator,
        WorkforceCalculator $workforceCalculator
    ) {
        $this->employeeFilter = $employeeFilter;
        $this->taskValidator = $taskValidator;
        $this->workforceCalculator = $workforceCalculator;
    }

    public function process(Collection $tasks, Collection $employees, array $constraints): array
    {
        Log::info("Starting pre-processing", [
            'total_tasks' => $tasks->count(),
            'total_employees' => $employees->count()
        ]);

        // ✅ RULE 3: Sort tasks by priority (Arrival Status first, then by scheduled_time)
        $sortedTasks = $tasks->sort(function($a, $b) {
            // First, sort by arrival_status DESC (TRUE first, FALSE second)
            if ($a->arrival_status != $b->arrival_status) {
                return $b->arrival_status <=> $a->arrival_status;
            }
            // Then, sort by scheduled_time ASC
            return $a->scheduled_time <=> $b->scheduled_time;
        })->values();

        Log::info("Tasks sorted by arrival status and time", [
            'total_tasks' => $sortedTasks->count(),
            'arrival_tasks' => $sortedTasks->where('arrival_status', true)->count(),
            'first_task' => $sortedTasks->first() ? [
                'id' => $sortedTasks->first()->id,
                'arrival_status' => $sortedTasks->first()->arrival_status,
                'scheduled_time' => $sortedTasks->first()->scheduled_time
            ] : null
        ]);

        // ✅ Validate tasks
        $validTasks = $sortedTasks->filter(function($task) {
            return $this->isValidTask($task);
        });

        $invalidTasks = $sortedTasks->diff($validTasks);

        // ✅ 5-STEP WORKFORCE CALCULATION: Calculate minimum workforce needed (for validation)
        $minimumWorkforceNeeded = $this->workforceCalculator->selectOptimalWorkforce(
            $validTasks,
            $employees,
            $constraints
        );

        Log::info("Workforce calculation applied", [
            'employees_available' => $employees->count(),
            'minimum_workforce_calculated' => $minimumWorkforceNeeded->count(),
            'distribution_mode' => 'Using ALL available employees for task distribution'
        ]);

        // ✅ DISTRIBUTION STRATEGY: Use ALL available employees for optimal distribution
        // Instead of limiting to minimum workforce, distribute tasks across all employees
        // This maximizes speed, quality, and workload balance
        $employeesForDistribution = $employees; // Use ALL employees, not just minimum

        // ✅ Group employees by client/company
        $employeeAllocations = $this->allocateEmployeesByClient(
            $employeesForDistribution,
            $validTasks
        );

        // Collect all allocated employees from all client allocations
        $allAllocatedEmployees = collect($employeeAllocations)->flatten(1)->unique('id')->values();

        Log::info("Pre-processing complete", [
            'valid_tasks' => $validTasks->count(),
            'invalid_tasks' => $invalidTasks->count(),
            'minimum_workforce_needed' => $minimumWorkforceNeeded->count(),
            'total_employees_allocated' => $allAllocatedEmployees->count(),
            'allocations_by_client' => array_map('count', $employeeAllocations)
        ]);

        return [
            'valid_tasks' => $validTasks,
            'invalid_tasks' => $invalidTasks,
            'selected_employees' => $allAllocatedEmployees->all(),
            'employee_allocations' => $employeeAllocations,
        ];
    }

    /**
     * ✅ Validate task (location, date, etc.)
     * Updated to support client appointments (client_id) without location_id
     */
    protected function isValidTask($task): bool
    {
        // Must have either a location (contracted client) OR a client (external appointment)
        if (!$task->location_id && !$task->client_id) {
            Log::warning("Task {$task->id} has neither location nor client");
            return false;
        }

        if (!$task->scheduled_date) {
            Log::warning("Task {$task->id} has no scheduled date");
            return false;
        }

        return true;
    }

    /**
     * ✅ Allocate employees by client (Kakslauttanen vs Aikamatkat vs External)
     * EXCLUSIVE allocation - each employee assigned to ONE client only
     */
    protected function allocateEmployeesByClient(
        Collection $employees,
        Collection $tasks
    ): array {
        $allocations = [];

        // Group tasks by client
        $tasksByClient = $tasks->groupBy(function($task) {
            if ($task->location && $task->location->contracted_client_id) {
                return 'contracted_' . $task->location->contracted_client_id;
            } elseif ($task->client_id) {
                return 'client_' . $task->client_id;
            }
            return 'unassigned';
        });

        // ✅ RULE 1: Distribute employees EXCLUSIVELY across clients
        // Strategy: Maximize distribution - allocate enough employees for parallel task execution
        $totalTasks = $tasks->count();
        $availableEmployees = $employees->values(); // Make a copy we can modify

        // ✅ FIRST PASS: Calculate proportional allocation for each client
        $clientAllocations = [];
        $totalEmployeesToAllocate = $employees->count();

        foreach ($tasksByClient as $clientId => $clientTasks) {
            $taskRatio = $clientTasks->count() / $totalTasks;
            $tasksForClient = $clientTasks->count();

            // Calculate proportional share based on task count
            $proportionalCount = round($totalEmployeesToAllocate * $taskRatio);

            // Ensure minimum of 2 employees (1 pair) per client
            $proportionalCount = max(2, $proportionalCount);

            // Ensure even number for pairing
            if ($proportionalCount % 2 !== 0) {
                $proportionalCount++;
            }

            $clientAllocations[$clientId] = [
                'tasks' => $clientTasks,
                'task_count' => $tasksForClient,
                'employee_count' => $proportionalCount
            ];
        }

        // ✅ SECOND PASS: Adjust if total exceeds available employees
        $totalAllocated = array_sum(array_column($clientAllocations, 'employee_count'));

        if ($totalAllocated > $totalEmployeesToAllocate) {
            Log::info("Adjusting allocations - exceed available", [
                'total_allocated' => $totalAllocated,
                'total_available' => $totalEmployeesToAllocate
            ]);

            // Reduce each client proportionally
            $reductionFactor = $totalEmployeesToAllocate / $totalAllocated;
            foreach ($clientAllocations as $clientId => &$allocation) {
                $adjustedCount = max(2, floor($allocation['employee_count'] * $reductionFactor));
                // Keep even for pairing
                if ($adjustedCount % 2 !== 0) {
                    $adjustedCount = max(2, $adjustedCount - 1);
                }
                $allocation['employee_count'] = $adjustedCount;
            }
        }

        Log::info("Employee allocation plan", [
            'total_employees' => $totalEmployeesToAllocate,
            'total_tasks' => $totalTasks,
            'clients' => array_map(function($alloc) {
                return [
                    'tasks' => $alloc['task_count'],
                    'employees' => $alloc['employee_count']
                ];
            }, $clientAllocations)
        ]);

        // ✅ THIRD PASS: Actually allocate employees to each client
        foreach ($clientAllocations as $clientId => $allocation) {
            if ($availableEmployees->isEmpty()) {
                Log::warning("No more employees available for client", [
                    'client_id' => $clientId
                ]);
                break;
            }

            $employeeCount = min($allocation['employee_count'], $availableEmployees->count());

            // ✅ RULE 2: Ensure at least ONE driver per client group
            // ⚠️ IMPORTANT: Select from AVAILABLE pool, then REMOVE from pool
            $clientEmployees = $this->selectEmployeesWithDriver(
                $availableEmployees,
                $employeeCount
            );

            $allocations[$clientId] = $clientEmployees;

            // ✅ CRITICAL: Remove allocated employees from available pool
            $allocatedIds = $clientEmployees->pluck('id')->toArray();
            $availableEmployees = $availableEmployees->reject(function($employee) use ($allocatedIds) {
                return in_array($employee->id, $allocatedIds);
            })->values(); // Re-index

            Log::info("Allocated employees to client (PROPORTIONAL DISTRIBUTION)", [
                'client_id' => $clientId,
                'task_count' => $allocation['task_count'],
                'planned_employee_count' => $allocation['employee_count'],
                'actual_employee_count' => count($clientEmployees),
                'employee_ids' => $allocatedIds,
                'has_driver' => $clientEmployees->contains(fn($e) => $e->has_driving_license),
                'remaining_employees' => $availableEmployees->count(),
                'employees_per_task' => $allocation['task_count'] > 0 ? round(count($clientEmployees) / $allocation['task_count'], 2) : 0
            ]);
        }

        // ✅ Final summary: Check employee utilization
        $totalAllocated = collect($allocations)->flatten(1)->count();
        $utilizationRate = $employees->count() > 0 ? ($totalAllocated / $employees->count()) * 100 : 0;

        Log::info("Employee allocation summary", [
            'total_employees_available' => $employees->count(),
            'total_employees_allocated' => $totalAllocated,
            'employees_unutilized' => $employees->count() - $totalAllocated,
            'utilization_rate' => round($utilizationRate, 2) . '%',
            'clients_served' => count($allocations),
            'unutilized_employee_ids' => $availableEmployees->pluck('id')->toArray()
        ]);

        return $allocations;
    }

    /**
     * ✅ RULE 2: Select employees ensuring at least one driver
     * Selects employees from the available pool in order (deterministic)
     */
    protected function selectEmployeesWithDriver(Collection $employees, int $count): Collection
    {
        if ($employees->isEmpty()) {
            Log::warning("No employees available for selection");
            return collect();
        }

        // Get drivers and non-drivers
        $drivers = $employees->filter(fn($e) => $e->has_driving_license)->values();
        $nonDrivers = $employees->filter(fn($e) => !$e->has_driving_license)->values();

        if ($drivers->isEmpty()) {
            Log::warning("No drivers available! Selecting employees anyway.", [
                'requested_count' => $count,
                'available_count' => $employees->count()
            ]);
            return $employees->take($count);
        }

        // Strategy: Take drivers first, then non-drivers
        // This ensures at least one driver and distributes them fairly
        $selected = collect();

        // Calculate how many drivers vs non-drivers
        $driverCount = min($drivers->count(), ceil($count / 2)); // At least half should be drivers if possible
        $nonDriverCount = $count - $driverCount;

        // Take drivers first
        $selected = $selected->merge($drivers->take($driverCount));

        // Fill remaining slots with non-drivers
        $nonDriversTaken = 0;
        if ($nonDriverCount > 0 && $nonDrivers->isNotEmpty()) {
            $nonDriversToTake = $nonDrivers->take($nonDriverCount);
            $selected = $selected->merge($nonDriversToTake);
            $nonDriversTaken = $nonDriversToTake->count();
        }

        // If we couldn't get enough non-drivers, fill remaining spots with more drivers
        $remainingNeeded = $nonDriverCount - $nonDriversTaken;
        if ($remainingNeeded > 0 && $drivers->count() > $driverCount) {
            $selected = $selected->merge($drivers->skip($driverCount)->take($remainingNeeded));
        }

        Log::info("Selected employees with driver", [
            'requested' => $count,
            'selected' => $selected->count(),
            'drivers' => $selected->filter(fn($e) => $e->has_driving_license)->count(),
            'employee_ids' => $selected->pluck('id')->toArray()
        ]);

        return $selected->take($count); // Ensure we don't exceed requested count
    }

    // public function process(Collection $tasks, Collection $employees, array $constraints): array
    // {
    //     \Log::info("PreProcessor input", [
    //         'tasks_count' => $tasks->count(),
    //         'employees_count' => $employees->count(),
    //         'employee_ids' => $employees->pluck('id')->toArray()
    //     ]);
    
    //     // 1. Filter employees
    //     $filteredEmployees = $this->employeeFilter->filter($employees);
    
    //     \Log::info("After employee filter", [
    //         'filtered_count' => $filteredEmployees->count(),
    //         'filtered_ids' => $filteredEmployees->pluck('id')->toArray()
    //     ]);
    
    //     // 2. Calculate optimal workforce size
    //     $selectedEmployees = $this->workforceCalculator->selectOptimalWorkforce(
    //         $tasks,
    //         $filteredEmployees,
    //         $constraints
    //     );
    
    //     \Log::info("After workforce calculator", [
    //         'selected_count' => $selectedEmployees->count(),
    //         'selected_ids' => $selectedEmployees->pluck('id')->toArray()
    //     ]);

    //     // 3. Validate tasks
    //     $taskResults = $this->taskValidator->validate($tasks, $constraints);

    //     // 4. Allocate employees to client groups
    //     $clientGroups = $this->groupTasksByClient($taskResults['valid']);
    //     $employeeAllocations = $this->allocateEmployeesToClients(
    //         $selectedEmployees,
    //         $clientGroups
    //     );

    //     return [
    //         'valid_tasks' => $taskResults['valid'],
    //         'invalid_tasks' => $taskResults['invalid'],
    //         'employee_allocations' => $employeeAllocations,
    //         'selected_employees' => $selectedEmployees,
    //     ];
    // }

    protected function groupTasksByClient(Collection $tasks): Collection
    {
        return $tasks->groupBy(function($task) {
            // Group by client (handle both contracted and external)
            if ($task->location && $task->location->contractedClient) {
                return 'contracted_' . $task->location->contracted_client_id;
            } elseif ($task->client_id) {
                return 'client_' . $task->client_id;
            }
            return 'unassigned';
        })
        ->map(function ($clientTasks) {
            return [
                'tasks' => $clientTasks,
                'total_workload' => $clientTasks->sum(function ($task) {
                    return ($task->duration + $task->travel_time) / 60;
                }),
            ];
        })
        ->sortByDesc('total_workload');
    }

    protected function allocateEmployeesToClients(
        Collection $employees,
        Collection $clientGroups
    ): array {
        if ($employees->isEmpty()) {
            Log::error("Cannot allocate: No employees provided");
            throw new \Exception("No employees available for allocation");
        }
    
        $allocations = [];
        $employeeIndex = 0;
        $employeesArray = $employees->values()->all();
    
        Log::info("Allocating employees to clients", [
            'total_employees' => count($employeesArray),
            'total_clients' => $clientGroups->count()
        ]);
    
        foreach ($clientGroups as $clientId => $group) {
            $workload = $group['total_workload'];
            
            // ✅ CALCULATE employees needed, but MINIMUM 2 for pairing constraint
            $requiredEmployees = max(2, ceil($workload / 8)); // At least 2 employees per client
            
            // ✅ Allocate employees
            $allocations[$clientId] = [];
            for ($i = 0; $i < $requiredEmployees && $employeeIndex < count($employeesArray); $i++) {
                $allocations[$clientId][] = $employeesArray[$employeeIndex++];
            }
            
            // ✅ ENSURE at least 2 employees were allocated
            if (count($allocations[$clientId]) < 2 && $employeeIndex < count($employeesArray)) {
                // Add one more employee to make it a pair
                $allocations[$clientId][] = $employeesArray[$employeeIndex++];
            }
    
            Log::info("Client allocation", [
                'client_id' => $clientId,
                'workload_hours' => $workload,
                'required' => $requiredEmployees,
                'allocated' => count($allocations[$clientId]),
                'employee_ids' => collect($allocations[$clientId])->pluck('id')->toArray()
            ]);
        }
    
        return $allocations;
    }
}