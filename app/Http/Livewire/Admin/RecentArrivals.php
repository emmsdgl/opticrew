<?php

namespace App\Http\Livewire\Admin;

use App\Models\Attendance;
use Livewire\Component;
use Carbon\Carbon;

class RecentArrivals extends Component
{
    public $recentArrivals = [];

    // Automatically refresh every 5 seconds
    public function render()
    {
        $this->loadRecentArrivals();
        return view('livewire.admin.recent-arrivals');
    }

    public function loadRecentArrivals()
    {
        $today = Carbon::today();

        // Get today's clock-ins, most recent first
        $this->recentArrivals = Attendance::with('employee.user')
            ->whereDate('clock_in', $today)
            ->orderBy('clock_in', 'desc')
            ->take(5)
            ->get();
    }

    // Method to manually refresh
    public function refresh()
    {
        $this->loadRecentArrivals();
    }
}