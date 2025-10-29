<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Task;
use Carbon\Carbon;

class TaskOverview extends Component
{
    public $selectedTime = 'month';
    public $selectedService = 'all';

    public function render()
    {
        $query = Task::with(['location.contractedClient', 'client.user', 'optimizationTeam.members.employee.user']);

        $query->when($this->selectedTime, function ($q) {
            if ($this->selectedTime === 'day') {
                return $q->whereDate('scheduled_date', Carbon::today());
            } elseif ($this->selectedTime === 'week') {
                return $q->whereBetween('scheduled_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($this->selectedTime === 'month') {
                return $q->whereBetween('scheduled_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            }
            return $q;
        });

        $query->when($this->selectedService !== 'all', function ($q) {
            return $q->where('task_description', $this->selectedService);
        });

        $tasksFromDb = $query->orderBy('scheduled_date', 'desc')->get();

        $tasks = $tasksFromDb->map(function ($task) {
            $title = 'N/A';
            if ($task->location && $task->location->contractedClient) {
                $title = $task->location->contractedClient->name;
            } elseif ($task->client) {
                $title = $task->client->first_name . ' ' . $task->client->last_name;
            }

            // Get team members with their pictures
            $teamMembers = [];
            if ($task->optimizationTeam) {
                $teamMembers = $task->optimizationTeam->members()
                    ->with('employee.user')
                    ->get()
                    ->map(function($member) {
                        $userName = 'Unknown';
                        $profilePicture = null;

                        if ($member->employee && $member->employee->user) {
                            $userName = $member->employee->user->name;
                            $profilePicture = $member->employee->user->profile_picture;
                        }

                        return [
                            'name' => $userName,
                            'picture' => $profilePicture
                        ];
                    })
                    ->toArray();
            }

            return [
                'id' => $task->id,
                'title' => $title,
                'category' => $task->task_description,
                'date' => Carbon::parse($task->scheduled_date)->format('M d'),
                'startTime' => $task->started_at ? Carbon::parse($task->started_at)->format('g:i a') : 'TBD',
                'teamMembers' => $teamMembers,
                'status' => $task->status === 'Completed' ? 'complete' : 'incomplete', // CHANGED: Use 'status' instead of 'done', and string values
            ];
        })->values(); // ADD ->values() to re-index the collection
    
        $taskCount = $tasks->count();
    
        return view('livewire.admin.task-overview', [
            'tasks' => $tasks,
            'taskCount' => $taskCount
        ]);
    }
}