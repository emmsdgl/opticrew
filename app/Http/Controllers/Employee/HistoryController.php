<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Feedback;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        // Get current employee
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return view('employee.history', [
                'activities' => [],
                'ratings' => []
            ]);
        }

        // Fetch tasks assigned to teams where this employee is a member
        $tasks = Task::whereHas('optimizationTeam.members', function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        })
        ->with([
            'location',
            'client',
            'optimizationTeam.members.employee.user',
            'checklistCompletions.checklistItem'
        ])
        ->orderBy('scheduled_date', 'desc')
        ->orderBy('scheduled_time', 'desc')
        ->get();

        // Transform tasks into activities format
        $activities = $tasks->map(function ($task) {
            // Get checklist items
            $checklistItems = $task->checklistCompletions->map(function ($completion) {
                return [
                    'name' => $completion->checklistItem->item_text ?? 'Task item',
                    'completed' => $completion->is_completed,
                ];
            });

            // Determine if needs rating
            $needsRating = $task->status === 'Completed' && !Feedback::where('task_id', $task->id)
                ->where('user_type', 'employee')
                ->exists();

            // Format the activity
            $dateStr = Carbon::parse($task->scheduled_date)->format('Y-m-d');
            $timeStr = $task->scheduled_time ? Carbon::parse($task->scheduled_time)->format('H:i:s') : '00:00:00';
            $scheduledDateTime = $dateStr . ' ' . $timeStr;

            return [
                'id' => $task->id,
                'type' => 'task',
                'icon' => $this->getServiceIcon($task->task_description),
                'title' => $task->task_description . ' - ' . ($task->location->location_name ?? 'External'),
                'date' => Carbon::parse($scheduledDateTime)->format('d M Y, g:i a'),
                'status' => $task->status,
                'needsRating' => $needsRating,
                'taskId' => 'TASK-' . str_pad($task->id, 6, '0', STR_PAD_LEFT),
                'serviceDate' => Carbon::parse($task->scheduled_date)->format('Y-m-d'),
                'serviceTime' => $task->scheduled_time ? Carbon::parse($task->scheduled_time)->format('g:i A') : 'TBD',
                'serviceType' => $task->task_description,
                'location' => $task->location ? ($task->location->address ?? $task->location->location_name) : 'External Client',
                'clientName' => $task->client ? $task->client->name : ($task->contractedClient->name ?? 'Unknown'),
                'checklist' => $checklistItems->toArray(),
            ];
        });

        // Fetch employee's submitted feedback/ratings
        $feedbacks = Feedback::where('employee_id', $employee->id)
            ->where('user_type', 'employee')
            ->with(['task.location', 'task.client'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ratings = $feedbacks->map(function ($feedback) {
            return [
                'id' => $feedback->id,
                'task_id' => $feedback->task_id,
                'taskName' => $feedback->task ? $feedback->task->task_description : 'Unknown Task',
                'location' => $feedback->task && $feedback->task->location ? $feedback->task->location->location_name : 'N/A',
                'clientName' => $feedback->task && $feedback->task->client ? $feedback->task->client->name : 'Unknown',
                'rating' => $feedback->rating,
                'keywords' => $feedback->keywords ?? [],
                'feedback_text' => $feedback->feedback_text,
                'submitted_at' => Carbon::parse($feedback->created_at)->format('d M Y, g:i a'),
                'icon' => $this->getServiceIcon($feedback->task ? $feedback->task->task_description : ''),
            ];
        });

        return view('employee.history', [
            'activities' => $activities->toArray(),
            'ratings' => $ratings->toArray()
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

    /**
     * Store employee feedback for a task
     */
    public function storeFeedback(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'rating' => 'required|integer|min:1|max:5',
            'keywords' => 'nullable|array',
            'feedback_text' => 'nullable|string',
        ]);

        // Check if feedback already exists
        $existingFeedback = Feedback::where('task_id', $validated['task_id'])
            ->where('employee_id', $employee->id)
            ->where('user_type', 'employee')
            ->first();

        if ($existingFeedback) {
            return response()->json(['success' => false, 'message' => 'Feedback already submitted for this task'], 400);
        }

        // Get the task to retrieve service type
        $task = Task::find($validated['task_id']);

        // Create new feedback
        $feedback = Feedback::create([
            'task_id' => $validated['task_id'],
            'employee_id' => $employee->id,
            'user_type' => 'employee',
            'rating' => $validated['rating'],
            'keywords' => $validated['keywords'] ?? [],
            'feedback_text' => $validated['feedback_text'],
            'service_type' => $task ? $task->task_description : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback
        ]);
    }
}
