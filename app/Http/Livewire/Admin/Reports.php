<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Reports extends Component
{
    public $startDate;
    public $endDate;
    public $reportData = [];

    public function mount()
    {
        // Default to the current month
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
    }

    public function generateReport()
    {
        $this->reportData = []; // Clear previous report

        // --- 1. Task Summary Report (like your Snow Out & Firewood example) ---
        $taskSummary = Task::where('task_description', 'like', 'Snow%') // Example filter
            ->orWhere('task_description', 'like', 'Firewood%')
            ->whereBetween('scheduled_date', [$this->startDate, $this->endDate])
            ->get();
        
        // --- 2. Daily Cleaning Summary Report (like your Kakslauttanen example) ---
        // This is a complex query that groups tasks by type and counts them per day.
        $dailyCleaningSummary = Task::whereBetween('scheduled_date', [$this->startDate, $this->endDate])
            ->join('locations', 'tasks.location_id', '=', 'locations.id')
            ->select(
                'tasks.scheduled_date',
                'locations.location_type',
                DB::raw('count(tasks.id) as total_cleaned')
            )
            ->groupBy('tasks.scheduled_date', 'locations.location_type')
            ->orderBy('tasks.scheduled_date')
            ->get()
            ->groupBy('scheduled_date'); // Group the final results by date for easy display

        $this->reportData = [
            'task_summary' => $taskSummary,
            'daily_summary' => $dailyCleaningSummary,
            // We can add payroll report data here in the future
        ];
    }

    public function render()
    {
        return view('livewire.admin.reports')->layout('layouts.app');
    }
}