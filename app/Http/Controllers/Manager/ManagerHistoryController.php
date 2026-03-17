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

        $services = $completedTasks->map(function ($task) {
            $type = 'default';
            $icon = 'broom';

            if (stripos($task->task_description ?? '', 'deep clean') !== false) {
                $type = 'deep_clean'; $icon = 'broom';
            } elseif (stripos($task->task_description ?? '', 'snow') !== false) {
                $type = 'snow'; $icon = 'snowflake';
            } elseif (stripos($task->task_description ?? '', 'daily') !== false) {
                $type = 'daily'; $icon = 'calendar-day';
            }

            return [
                'id' => $task->id,
                'name' => $task->location->name ?? 'Service',
                'type' => $type,
                'icon' => $icon,
                'location' => $task->location->address ?? '',
                'date' => Carbon::parse($task->scheduled_date)->format('M d, Y'),
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

        $reviewStats = TaskReview::where('contracted_client_id', $contractedClient->id)->count();
        $toReviewCount = Task::whereIn('location_id', $locationIds)
            ->where('status', 'Completed')
            ->whereDoesntHave('review')
            ->count();

        return view('manager.history', compact('services', 'filter', 'sort', 'reviewStats', 'toReviewCount'));
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
                'scheduled_date' => $task->scheduled_date ? $task->scheduled_date->format('Y-m-d') : null,
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
