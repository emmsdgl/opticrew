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

        // ✅ 5-STEP WORKFORCE CALCULATION: Calculate optimal workforce size
        $selectedEmployees = $this->workforceCalculator->selectOptimalWorkforce(
            $validTasks,
            $employees,
            $constraints
        );

        Log::info("Workforce calculation applied", [
            'employees_available' => $employees->count(),
            'employees_selected' => $selectedEmployees->count()
        ]);

        // ✅ Group employees by client/company
        $employeeAllocations = $this->allocateEmployeesByClient(
            $selectedEmployees,
            $validTasks
        );

        Log::info("Pre-processing complete", [
            'valid_tasks' => $validTasks->count(),
            'invalid_tasks' => $invalidTasks->count(),
            'total_employees_selected' => $selectedEmployees->count(),
            'allocations' => array_map('count', $employeeAllocations)
        ]);

        return [
            'valid_tasks' => $validTasks,
            'invalid_tasks' => $invalidTasks,
            'selected_employees' => $selectedEmployees->all(),
            'employee_allocations' => $employeeAllocations,
        ];
    }

    /**
     * ✅ Validate task (location, date, etc.)
     */
    protected function isValidTask($task): bool
    {
        if (!$task->location_id) {
            Log::warning("Task {$task->id} has no location");
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

        // ✅ RULE 1: Distribute employees across clients
        // Strategy: Divide employees proportionally by task count
        $totalTasks = $tasks->count();
        
        foreach ($tasksByClient as $clientId => $clientTasks) {
            $taskRatio = $clientTasks->count() / $totalTasks;
            $employeeCount = max(2, round($employees->count() * $taskRatio)); // Min 2 (1 pair)

            // ✅ RULE 2: Ensure at least ONE driver per client group
            $clientEmployees = $this->selectEmployeesWithDriver(
                $employees, 
                $employeeCount
            );

            $allocations[$clientId] = $clientEmployees;

            Log::info("Allocated employees to client", [
                'client_id' => $clientId,
                'task_count' => $clientTasks->count(),
                'employee_count' => count($clientEmployees),
                'has_driver' => $clientEmployees->contains(fn($e) => $e->has_driving_license)
            ]);
        }

        return $allocations;
    }

    /**
     * ✅ RULE 2: Select employees ensuring at least one driver
     */
    protected function selectEmployeesWithDriver(Collection $employees, int $count): Collection
    {
        // Get drivers
        $drivers = $employees->filter(fn($e) => $e->has_driving_license);

        // Get non-drivers
        $nonDrivers = $employees->filter(fn($e) => !$e->has_driving_license);

        if ($drivers->isEmpty()) {
            Log::warning("No drivers available! Selecting employees anyway.");
            return $employees->take($count);
        }

        // Strategy: 1 driver + (count-1) others
        $selected = collect();
        
        // Add at least 1 driver
        $selected->push($drivers->random());
        
        // Fill remaining slots
        $remaining = $employees->diff($selected)->shuffle()->take($count - 1);
        
        return $selected->merge($remaining);
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