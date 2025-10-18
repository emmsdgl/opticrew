<?php

namespace App\Services\Optimization\PreProcessing;

use App\Models\Task;
use App\Models\Employee;
use Illuminate\Support\Collection;

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
        \Log::info("PreProcessor input", [
            'tasks_count' => $tasks->count(),
            'employees_count' => $employees->count(),
            'employee_ids' => $employees->pluck('id')->toArray()
        ]);
    
        // 1. Filter employees
        $filteredEmployees = $this->employeeFilter->filter($employees);
    
        \Log::info("After employee filter", [
            'filtered_count' => $filteredEmployees->count(),
            'filtered_ids' => $filteredEmployees->pluck('id')->toArray()
        ]);
    
        // 2. Calculate optimal workforce size
        $selectedEmployees = $this->workforceCalculator->selectOptimalWorkforce(
            $tasks,
            $filteredEmployees,
            $constraints
        );
    
        \Log::info("After workforce calculator", [
            'selected_count' => $selectedEmployees->count(),
            'selected_ids' => $selectedEmployees->pluck('id')->toArray()
        ]);

        // 3. Validate tasks
        $taskResults = $this->taskValidator->validate($tasks, $constraints);

        // 4. Allocate employees to client groups
        $clientGroups = $this->groupTasksByClient($taskResults['valid']);
        $employeeAllocations = $this->allocateEmployeesToClients(
            $selectedEmployees,
            $clientGroups
        );

        return [
            'valid_tasks' => $taskResults['valid'],
            'invalid_tasks' => $taskResults['invalid'],
            'employee_allocations' => $employeeAllocations,
            'selected_employees' => $selectedEmployees,
        ];
    }

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
            \Log::error("Cannot allocate: No employees provided");
            throw new \Exception("No employees available for allocation");
        }
    
        $allocations = [];
        $employeeIndex = 0;
        $employeesArray = $employees->values()->all();
    
        \Log::info("Allocating employees to clients", [
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
    
            \Log::info("Client allocation", [
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