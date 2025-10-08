<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\SchedulingLog as SchedulingLogModel;
use Carbon\Carbon;

class SchedulingLog extends Component
{
    // Add the query string property to manage the date in the URL
    protected $queryString = ['selectedDate'];

    public $selectedDate;

    // The mount() method now only sets the default IF no date is in the URL
    public function mount()
    {
        if (empty($this->selectedDate)) {
            $this->selectedDate = Carbon::today()->toDateString();
        }
    }

    public function render()
    {
        $log = SchedulingLogModel::where('schedule_date', $this->selectedDate)
                                ->latest()
                                ->first();

        $logData = $log ? json_decode($log->log_data, true) : null;

        return view('livewire.admin.scheduling-log', [
            'logData' => $logData,
        ])->layout('layouts.app');
    }
}