<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\ClientAppointment;
use App\Models\TaskChecklistCompletion;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        // Fetch all tasks with their relationships
        $tasks = Task::with([
            'location',
            'client',
            'optimizationTeam.members.employee.user',
            'optimizationTeam.car',
        ])
        ->orderBy('scheduled_date', 'desc')
        ->orderBy('scheduled_time', 'desc')
        ->get();

        // Fetch all client appointments with relationships
        $appointments = ClientAppointment::with([
            'client',
            'assignedTeam.members.employee.user',
        ])
        ->orderBy('service_date', 'desc')
        ->orderBy('service_time', 'desc')
        ->get();

        // Transform tasks into the format expected by the frontend
        $activities = collect();

        // Add tasks to activities
        foreach ($tasks as $task) {
            // Get checklist items for this task
            $checklistItems = TaskChecklistCompletion::where('task_id', $task->id)
                ->with('checklistItem')
                ->get()
                ->map(function ($completion) {
                    return [
                        'name' => $completion->checklistItem->item_text ?? 'Task item',
                        'completed' => $completion->is_completed,
                    ];
                });

            // Get assigned members
            $assignedMembers = [];
            if ($task->optimizationTeam && $task->optimizationTeam->members) {
                $assignedMembers = $task->optimizationTeam->members->map(function ($member) {
                    $name = $member->employee->user->name ?? 'Unknown';
                    return [
                        'name' => $name,
                        'initial' => strtoupper(substr($name, 0, 1)),
                    ];
                })->toArray();
            }

            // Determine if needs rating (completed tasks without review)
            $needsRating = $task->status === 'Completed' && !$task->hasReview();

            // Format the activity
            $scheduledDateTime = $task->scheduled_date;
            if ($task->scheduled_time) {
                $scheduledDateTime .= ' ' . Carbon::parse($task->scheduled_time)->format('H:i:s');
            }

            $activities->push([
                'id' => $task->id,
                'type' => 'task',
                'icon' => $this->getServiceIcon($task->task_description),
                'title' => $task->task_description . ' - ' . ($task->location->location_name ?? 'External'),
                'date' => Carbon::parse($scheduledDateTime)->format('d M Y, g:i a'),
                'price' => '€ ' . number_format($task->estimated_duration_minutes * 2, 2), // Rough estimate
                'status' => $task->status,
                'needsRating' => $needsRating,
                'appointmentId' => 'TASK-' . str_pad($task->id, 6, '0', STR_PAD_LEFT),
                'serviceDate' => Carbon::parse($task->scheduled_date)->format('Y-m-d'),
                'serviceTime' => $task->scheduled_time ? Carbon::parse($task->scheduled_time)->format('g:i A') : 'TBD',
                'serviceType' => $task->task_description,
                'location' => $task->location ? ($task->location->address ?? $task->location->location_name) : 'External Client',
                'clientName' => $task->client ? $task->client->name : ($task->contractedClient->name ?? 'Unknown'),
                'totalAmount' => '€' . number_format($task->estimated_duration_minutes * 2, 2),
                'payableAmount' => '€' . number_format($task->estimated_duration_minutes * 2, 2),
                'assignedMembers' => $assignedMembers,
                'checklist' => $checklistItems->toArray(),
            ]);
        }

        // Add appointments to activities
        foreach ($appointments as $appointment) {
            // Get assigned members
            $assignedMembers = [];
            if ($appointment->assignedTeam && $appointment->assignedTeam->members) {
                $assignedMembers = $appointment->assignedTeam->members->map(function ($member) {
                    $name = $member->employee->user->name ?? 'Unknown';
                    return [
                        'name' => $name,
                        'initial' => strtoupper(substr($name, 0, 1)),
                    ];
                })->toArray();
            }

            // Determine if needs rating (appointments might not have reviews implemented yet)
            $needsRating = $appointment->status === 'completed';

            $activities->push([
                'id' => $appointment->id,
                'type' => 'appointment',
                'icon' => $this->getServiceIcon($appointment->service_type),
                'title' => $appointment->service_type . ' - ' . ($appointment->cabin_name ?? 'Booking'),
                'date' => Carbon::parse($appointment->service_date . ' ' . ($appointment->service_time ?? '00:00:00'))->format('d M Y, g:i a'),
                'price' => '€ ' . number_format($appointment->total_amount ?? 0, 2),
                'status' => ucfirst($appointment->status),
                'needsRating' => $needsRating,
                'appointmentId' => 'APT-' . str_pad($appointment->id, 6, '0', STR_PAD_LEFT),
                'serviceDate' => Carbon::parse($appointment->service_date)->format('Y-m-d'),
                'serviceTime' => $appointment->service_time ? Carbon::parse($appointment->service_time)->format('g:i A') : 'TBD',
                'serviceType' => $appointment->service_type,
                'location' => $appointment->cabin_name ?? 'N/A',
                'clientName' => $appointment->client->name ?? 'Unknown',
                'totalAmount' => '€' . number_format($appointment->total_amount ?? 0, 2),
                'payableAmount' => '€' . number_format($appointment->quotation ?? 0, 2),
                'assignedMembers' => $assignedMembers,
                'checklist' => [], // Appointments don't have checklists in the current structure
            ]);
        }

        // Sort activities by date (most recent first)
        $activities = $activities->sortByDesc(function ($activity) {
            return $activity['serviceDate'] . ' ' . $activity['serviceTime'];
        })->values();

        return view('admin.history', [
            'activities' => $activities->toArray()
        ]);
    }

    /**
     * Get an appropriate icon based on service type
     */
    private function getServiceIcon($serviceType)
    {
        $serviceType = strtolower($serviceType ?? '');

        if (str_contains($serviceType, 'deep')) {
            return asset('images/icons/cleaning/deep-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'daily') && str_contains($serviceType, 'room')) {
            return asset('images/icons/cleaning/daily-room-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'daily')) {
            return asset('images/icons/cleaning/daily-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'hotel')) {
            return asset('images/icons/cleaning/hotel-cleaning-icon.svg');
        } elseif (str_contains($serviceType, 'snow') || str_contains($serviceType, 'move') || str_contains($serviceType, 'out')) {
            return asset('images/icons/cleaning/snowout-cleaning-icon.svg');
        }

        return asset('images/icons/cleaning/daily-cleaning-icon.svg'); // Default icon
    }
}
