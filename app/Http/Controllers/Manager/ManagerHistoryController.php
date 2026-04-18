<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskReview;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerHistoryController extends Controller
{
    private function getContractedClient()
    {
        return ContractedClient::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Display the history page.
     */
    public function index(Request $request)
    {
        $contractedClient = $this->getContractedClient();

        if (!$contractedClient) {
            return view('manager.history', [
                'services' => collect(),
                'activities' => collect(),
                'accountLogs' => collect(),
                'filter' => 'all',
                'sort' => 'recent',
                'reviewStats' => 0,
                'toReviewCount' => 0,
            ]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');
        $filter = $request->get('filter', 'all');
        $sort = $request->get('sort', 'recent');

        $query = Task::whereIn('location_id', $locationIds)
            ->whereIn('status', ['Completed', 'Cancelled', 'Closed'])
            ->with(['location', 'review']);

        if ($filter === 'services') {
            $query->where('status', 'Completed');
        } elseif ($filter === 'to_review') {
            $query->where('status', 'Completed')->whereDoesntHave('review');
        }

        switch ($sort) {
            case 'oldest': $query->orderBy('scheduled_date', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'price_low': $query->orderBy('price', 'asc'); break;
            default: $query->orderBy('scheduled_date', 'desc');
        }

        $completedTasks = $query->paginate(10);

        $services = collect($completedTasks->items())->map(function ($task) {
            $type = 'default';
            $icon = 'broom';
            $color = '#6366f1'; // indigo

            if (stripos($task->task_description ?? '', 'deep clean') !== false) {
                $type = 'deep_clean'; $icon = 'broom'; $color = '#8b5cf6'; // purple
            } elseif (stripos($task->task_description ?? '', 'snow') !== false) {
                $type = 'snow'; $icon = 'snowflake'; $color = '#06b6d4'; // cyan
            } elseif (stripos($task->task_description ?? '', 'daily') !== false) {
                $type = 'daily'; $icon = 'calendar-day'; $color = '#10b981'; // emerald
            }

            return [
                'id' => $task->id,
                'name' => $task->location->name ?? 'Service',
                'room' => $task->location->name ?? 'Room',
                'category' => $type === 'deep_clean' ? 'Deep Clean' : ($type === 'snow' ? 'Snow Removal' : ($type === 'daily' ? 'Daily Cleaning' : 'Service')),
                'task' => $type === 'deep_clean' ? 'Deep Clean' : ($type === 'snow' ? 'Snow Removal' : ($type === 'daily' ? 'Daily Cleaning' : 'Service')),
                'type' => $type,
                'icon' => $icon,
                'color' => $color,
                'location' => $task->location->address ?? '',
                'date' => Carbon::parse($task->scheduled_date)->format('M d, Y'),
                'started_at' => $task->started_at ? Carbon::parse($task->started_at)->format('M d, Y g:i A') : null,
                'ended_at' => $task->completed_at ? Carbon::parse($task->completed_at)->format('M d, Y g:i A') : null,
                'price' => number_format($task->price ?? 0, 2) . ' EUR',
                'status' => strtolower($task->status),
                'reviewed' => $task->review !== null,
                'review' => $task->review ? [
                    'rating' => $task->review->rating,
                    'feedback_tags' => $task->review->feedback_tags,
                    'review_text' => $task->review->review_text,
                ] : null,
            ];
        });

        $activities = $services->map(function ($service) {
            return [
                'id' => $service['id'],
                'title' => $service['name'],
                'room' => $service['room'] ?? $service['name'],
                'task' => $service['task'] ?? $service['name'],
                'category' => $service['category'] ?? $service['task'] ?? $service['name'],
                'description' => $service['location'],
                'time' => $service['date'],
                'started_at' => $service['started_at'] ?? null,
                'ended_at' => $service['ended_at'] ?? null,
                'type' => 'task',
                'status' => $service['status'],
                'icon' => $service['icon'],
                'color' => $service['color'] ?? '#6366f1',
                'price' => $service['price'],
                'date' => $service['date'],
                'statusRaw' => $service['status'],
                'reviewed' => $service['reviewed'] ?? false,
                'review' => $service['review'] ?? null,
            ];
        })->values();

        $accountLogs = collect();

        $reviewStats = TaskReview::where('contracted_client_id', $contractedClient->id)->count();
        $toReviewCount = Task::whereIn('location_id', $locationIds)
            ->where('status', 'Completed')
            ->whereDoesntHave('review')
            ->count();

        return view('manager.history', compact('services', 'activities', 'accountLogs', 'filter', 'sort', 'reviewStats', 'toReviewCount'));
    }

    /**
     * Submit a review for a task.
     */
    public function submitReview(Request $request, $taskId)
    {
        $contractedClient = $this->getContractedClient();
        if (!$contractedClient) {
            return response()->json(['message' => 'No contracted client found'], 404);
        }

        $locationIds = $contractedClient->locations()->pluck('id');

        $task = Task::whereIn('location_id', $locationIds)
            ->where('id', $taskId)
            ->where('status', 'Completed')
            ->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found or cannot be reviewed'], 404);
        }

        if ($task->review) {
            return response()->json(['message' => 'This task has already been reviewed'], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_tags' => 'nullable|array',
            'feedback_tags.*' => 'string',
            'review_text' => 'nullable|string|max:1000',
        ]);

        $review = TaskReview::create([
            'task_id' => $task->id,
            'contracted_client_id' => $contractedClient->id,
            'reviewer_user_id' => Auth::user()->id,
            'rating' => $request->rating,
            'feedback_tags' => $request->feedback_tags ?? [],
            'review_text' => $request->review_text,
            'metadata' => [
                'task_description' => $task->task_description,
                'location_name' => $task->location->name ?? null,
                'scheduled_date' => $task->scheduled_date ? Carbon::parse($task->scheduled_date)->format('Y-m-d') : null,
                'submitted_at' => now()->toIso8601String(),
            ],
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => [
                'id' => $review->id,
                'rating' => $review->rating,
                'feedback_tags' => $review->feedback_tags,
                'review_text' => $review->review_text,
            ],
        ], 201);
    }
}
