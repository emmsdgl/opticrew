<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Services\OptimizationService;
use App\Models\Location;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\TaskPerformanceHistory;


class SimulationDashboard extends Component
{
    public $simulationResults = [];
    public $isRunning = false;

public function runSimulation()
{
    $this->isRunning = true;
    $this->simulationResults = [];

    // ... (The top part of the function remains the same) ...
    $numberOfRuns = 10;
    $serviceDate = Carbon::today()->addDay()->toDateString();
    $locationIds = Location::inRandomOrder()->limit(20)->pluck('id')->toArray();
    $employees = Employee::whereDoesntHave('schedules', fn($q) => $q->where('work_date', $serviceDate)->where('is_day_off', true))->get();
    
    $allBestFitnessValues = [];
    $optimizer = new OptimizationService();

    for ($i = 0; $i < $numberOfRuns; $i++) {
        $result = $optimizer->runForSimulation($serviceDate, $locationIds, $employees);
        if (isset($result['fitness'])) {
            $allBestFitnessValues[] = $result['fitness'];
        }
    }

    // --- CALCULATE FINAL METRICS ---
    
    // ... (Fitness, Convergence, Robustness calculations remain the same) ...
    $bestFitness = !empty($allBestFitnessValues) ? max($allBestFitnessValues) : 0;
    $averageConvergence = !empty($allBestFitnessValues) ? array_sum($allBestFitnessValues) / count($allBestFitnessValues) : 0;
    $robustness = $this->calculateLocalStandardDeviation($allBestFitnessValues);
    $representativeRun = $optimizer->runForSimulation($serviceDate, $locationIds, $employees);

    // ======================== NEW LOGIC FOR PREDICTION ACCURACY ========================
    $history = TaskPerformanceHistory::all();
    $mae = 0;
    $rmse = 0;
    if ($history->isNotEmpty()) {
        $absoluteErrors = [];
        $squaredErrors = [];
        foreach ($history as $record) {
            $error = $record->actual_duration_minutes - $record->estimated_duration_minutes;
            $absoluteErrors[] = abs($error);
            $squaredErrors[] = $error ** 2;
        }
        $mae = array_sum($absoluteErrors) / $history->count();
        $rmse = sqrt(array_sum($squaredErrors) / $history->count());
    }
    // =================================================================================

    $this->simulationResults = [
        'fitness_value' => $bestFitness,
        'convergence_rate' => $averageConvergence,
        'robustness' => $robustness,
        'solution_quality' => [
            'total_workforce_cost' => $representativeRun['total_cost'] ?? 'N/A',
            'task_completion_rate' => '100%',
            'workload_balance' => $representativeRun['workload_std_dev'] ?? 'N/A',
        ],
        // Update with calculated values
        'prediction_accuracy' => [
            'mae' => round($mae, 2) . ' minutes',
            'rmse' => round($rmse, 2) . ' minutes',
        ]
    ];

    $this->isRunning = false;
}

private function calculateLocalStandardDeviation(array $values): float
{
    if (count($values) < 2) {
        return 0.0;
    }
    $mean = array_sum($values) / count($values);
    $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $values)) / (count($values) - 1);
    return sqrt($variance);
}

    // private function calculateStandardDeviation(array $values): float
    // {
    //     if (count($values) < 2) {
    //         return 0.0;
    //     }
    //     $mean = array_sum($values) / count($values);
    //     $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $values)) / (count($values) - 1);
    //     return sqrt($variance);
    // }

    public function render()
    {
        return view('livewire.admin.simulation-dashboard')->layout('layouts.app');
    }
}