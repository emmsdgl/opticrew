<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use Livewire\WithPagination; // Import this for pagination

class TaskList extends Component
{
    use WithPagination; // Use the pagination trait

    public $search = '';
    public $statusFilter = '';
    public $dateFilter = '';

    // This tells Livewire to reset to page 1 whenever a filter is changed
    protected $updatesQueryString = ['search', 'statusFilter', 'dateFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Start the query
        $tasksQuery = Task::with(['location.contractedClient', 'team.members.employee', 'team.car']) // <-- ADD 'team.car'
                            ->orderBy('scheduled_date', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $tasksQuery->where(function ($query) {
                $query->where('task_description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('location', function ($q) {
                          $q->where('location_name', 'like', '%' . $this->search . '%');
                      });
            });
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $tasksQuery->where('status', $this->statusFilter);
        }

        // Apply date filter
        if (!empty($this->dateFilter)) {
            $tasksQuery->where('scheduled_date', $this->dateFilter);
        }
        
        // Paginate the results
        $tasks = $tasksQuery->paginate(15); // Show 15 tasks per page

        return view('livewire.admin.task-list', [
            'tasks' => $tasks
        ])->layout('layouts.app');
    }
}