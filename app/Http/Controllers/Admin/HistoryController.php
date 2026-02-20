<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\ClientAppointment;
use App\Models\TaskChecklistCompletion;
use App\Models\Feedback;
use Carbon\Carbon;

class HistoryController extends Controller
{
    /**
     * Checklist templates organized by service type
     */
    private $checklistTemplates = [
        'daily_cleaning' => [
            'Sweep and mop floors',
            'Vacuum carpets/rugs',
            'Dust furniture and surfaces',
            'Wipe tables and countertops',
            'Empty trash bins',
            'Wipe kitchen counters',
            'Clean sink',
            'Wash visible dishes',
            'Wipe appliance exteriors',
            'Clean toilet and sink',
            'Wipe mirrors',
            'Mop floor',
            'Organize cluttered areas',
            'Light deodorizing',
        ],
        'snowout_cleaning' => [
            'Remove mud, water, and debris',
            'Clean door mats',
            'Mop and dry floors',
            'Deep vacuum carpets',
            'Mop with disinfectant solution',
            'Wipe walls near entrances',
            'Dry wet surfaces',
            'Check for water accumulation',
            'Clean and sanitize affected areas',
            'Dispose of tracked-in debris',
            'Replace trash liners',
        ],
        'deep_cleaning' => [
            'Dust high and low areas (vents, corners, baseboards)',
            'Clean behind and under furniture',
            'Wash walls and remove stains',
            'Deep vacuum carpets',
            'Clean inside microwave',
            'Degrease stove and range hood',
            'Clean inside refrigerator (if included)',
            'Scrub tile grout',
            'Remove limescale and mold buildup',
            'Deep scrub tiles and grout',
            'Sanitize all fixtures thoroughly',
            'Clean window interiors',
            'Polish handles and knobs',
            'Disinfect frequently touched surfaces',
        ],
        'general_cleaning' => [
            'Dust surfaces',
            'Sweep/vacuum floors',
            'Mop hard floors',
            'Clean glass and mirrors',
            'Wipe countertops',
            'Clean sink',
            'Take out trash',
            'Clean toilet, sink, and mirror',
            'Mop floor',
            'Arrange items neatly',
            'Dispose of garbage',
            'Light air freshening',
        ],
        'hotel_cleaning' => [
            'Make bed with fresh linens',
            'Replace pillowcases and sheets',
            'Dust all surfaces (tables, headboard, shelves)',
            'Vacuum carpet / sweep & mop floor',
            'Clean mirrors and glass surfaces',
            'Check under bed for trash/items',
            'Empty trash bins and replace liners',
            'Clean and disinfect toilet',
            'Scrub shower walls, tub, and floor',
            'Clean sink and countertop',
            'Polish fixtures',
            'Replace towels, bath mat, tissue, and toiletries',
            'Mop bathroom floor',
            'Refill water, coffee, and room amenities',
            'Replace slippers and hygiene kits',
            'Check minibar (if applicable)',
            'Ensure lights, AC, TV working',
            'Arrange curtains neatly',
            'Deodorize room',
        ],
    ];

    /**
     * Get the service type from task description
     */
    private function getServiceType($taskDescription)
    {
        $taskDescription = strtolower($taskDescription ?? '');

        if (str_contains($taskDescription, 'daily') || str_contains($taskDescription, 'routine')) {
            return 'daily_cleaning';
        } elseif (str_contains($taskDescription, 'snowout') || str_contains($taskDescription, 'weather')) {
            return 'snowout_cleaning';
        } elseif (str_contains($taskDescription, 'deep')) {
            return 'deep_cleaning';
        } elseif (str_contains($taskDescription, 'hotel') || str_contains($taskDescription, 'room turnover')) {
            return 'hotel_cleaning';
        }

        return 'general_cleaning';
    }

    /**
     * Get checklist items for a task based on its service type
     */
    private function getChecklistItemsForTask($task)
    {
        $serviceType = $this->getServiceType($task->task_description);
        $template = $this->checklistTemplates[$serviceType] ?? $this->checklistTemplates['general_cleaning'];

        // Get completions keyed by item index
        $completions = TaskChecklistCompletion::where('task_id', $task->id)
            ->get()
            ->keyBy('checklist_item_id');

        return collect($template)->map(function ($itemName, $index) use ($completions) {
            $completion = $completions->get($index);
            return [
                'name' => $itemName,
                'completed' => $completion ? $completion->is_completed : false,
            ];
        })->values()->toArray();
    }

    public function index()
    {
        // Fetch all tasks with their relationships
        $tasks = Task::with([
            'location',
            'client',
            'contractedClient',
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
            // Get checklist items for this task from template
            $checklistItems = $this->getChecklistItemsForTask($task);

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

            // Get ALL employee feedback for this task (multiple employees per team)
            $employeeFeedbacks = Feedback::where('task_id', $task->id)
                ->where('user_type', 'employee')
                ->with('employee.user')
                ->get();

            $employeeRating = null;
            if ($employeeFeedbacks->isNotEmpty()) {
                // Calculate average rating from all employee feedback
                $averageRating = round($employeeFeedbacks->avg('rating'), 1);

                // Collect all unique tags from all employee feedback
                $allTags = $employeeFeedbacks->pluck('keywords')->filter()->flatten()->unique()->values()->toArray();

                // Build individual feedback entries
                $individualFeedbacks = $employeeFeedbacks->map(function ($fb) {
                    return [
                        'rating' => $fb->rating,
                        'tags' => $fb->keywords ?? [],
                        'comment' => $fb->feedback_text ?? $fb->comments,
                        'employeeName' => $fb->employee?->user?->name ?? 'Employee',
                        'submittedAt' => $fb->created_at?->format('d M Y, g:i A'),
                    ];
                })->toArray();

                $employeeRating = [
                    'averageRating' => $averageRating,
                    'totalResponses' => $employeeFeedbacks->count(),
                    'tags' => $allTags,
                    'feedbacks' => $individualFeedbacks,
                ];
            }

            // Get client feedback for this task
            $clientFeedback = Feedback::where('task_id', $task->id)
                ->where('user_type', 'client')
                ->with('client.user')
                ->first();

            $clientRating = null;
            if ($clientFeedback) {
                $clientRating = [
                    'rating' => $clientFeedback->rating ?? $clientFeedback->overall_rating,
                    'tags' => $clientFeedback->keywords ?? [],
                    'comment' => $clientFeedback->feedback_text ?? $clientFeedback->comments,
                    'clientName' => $clientFeedback->client?->user?->name ?? 'Client',
                    'submittedAt' => $clientFeedback->created_at?->format('d M Y, g:i A'),
                ];
            }

            // Determine if needs rating (completed tasks without any review)
            $needsRating = $task->status === 'Completed' && $employeeFeedbacks->isEmpty() && !$clientFeedback;

            // Format the activity
            $scheduledDateTime = Carbon::parse($task->scheduled_date)->format('Y-m-d');
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
                'clientName' => $task->client ? $task->client->name : ($task->contractedClient?->name ?? 'Unknown'),
                'totalAmount' => '€' . number_format($task->estimated_duration_minutes * 2, 2),
                'payableAmount' => '€' . number_format($task->estimated_duration_minutes * 2, 2),
                'assignedMembers' => $assignedMembers,
                'checklist' => $checklistItems,
                'employeeRating' => $employeeRating,
                'clientRating' => $clientRating,
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

            // Get checklist items for appointment based on service type template
            $serviceType = $this->getServiceType($appointment->service_type);
            $template = $this->checklistTemplates[$serviceType] ?? $this->checklistTemplates['general_cleaning'];
            $checklistItems = collect($template)->map(function ($itemName) {
                return [
                    'name' => $itemName,
                    'completed' => false,
                ];
            })->values()->toArray();

            $activities->push([
                'id' => $appointment->id,
                'type' => 'appointment',
                'icon' => $this->getServiceIcon($appointment->service_type),
                'title' => $appointment->service_type . ' - ' . ($appointment->cabin_name ?? 'Booking'),
                'date' => Carbon::parse(Carbon::parse($appointment->service_date)->format('Y-m-d') . ' ' . ($appointment->service_time ? Carbon::parse($appointment->service_time)->format('H:i:s') : '00:00:00'))->format('d M Y, g:i a'),
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
                'checklist' => $checklistItems,
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
