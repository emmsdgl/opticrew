<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\CompanyChecklist;
use App\Models\TaskReview;
use App\Models\ContractedClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ManagerActivityController extends Controller
{
    private function getContractedClient()
    {
        return ContractedClient::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Display the activity page.
     */
    public function index(Request $request)
    {
        $contractedClient = $this->getContractedClient();
        $filter = $request->get('filter', 'all');

        if (!$contractedClient) {
            return view('manager.activity', ['activities' => collect(), 'filter' => $filter]);
        }

        $locationIds = $contractedClient->locations()->pluck('id');
        $since = Carbon::now()->subDays(7);
        $activities = collect();

        // Task activities
        if (in_array($filter, ['all', 'tasks'])) {
            $recentTasks = Task::whereIn('location_id', $locationIds)
                ->where('updated_at', '>=', $since)
                ->with('location')
                ->orderBy('updated_at', 'desc')
                ->limit(30)
                ->get();

            foreach ($recentTasks as $task) {
                $locationName = $task->location->location_name ?? $task->location->name ?? 'Unknown';
                $activity = $this->buildTaskActivity($task, $locationName);
                $activity['category'] = 'tasks';
                $activity['sort_time'] = $task->updated_at;
                $activities->push($activity);
            }
        }

        // Checklist activities (granular — mirrors mobile API)
        if (in_array($filter, ['all', 'checklist'])) {
            $checklist = CompanyChecklist::where('contracted_client_id', $contractedClient->id)
                ->where('is_active', true)
                ->with('categories.items')
                ->first();

            if ($checklist) {
                // Checklist itself updated
                if ($checklist->updated_at && $checklist->updated_at >= $since) {
                    $activities->push([
                        'type' => 'checklist',
                        'icon' => 'clipboard-list',
                        'title' => 'Checklist updated',
                        'description' => 'Checklist "' . $checklist->name . '" was updated',
                        'time' => $checklist->updated_at->diffForHumans(),
                        'status' => 'updated',
                        'category' => 'checklist',
                        'sort_time' => $checklist->updated_at,
                    ]);
                }

                foreach ($checklist->categories as $category) {
                    // Category was added
                    if ($category->created_at && $category->created_at >= $since) {
                        $activities->push([
                            'type' => 'checklist',
                            'icon' => 'circle-plus',
                            'title' => 'Category added',
                            'description' => 'Category "' . $category->name . '" was added',
                            'time' => $category->created_at->diffForHumans(),
                            'status' => 'added',
                            'category' => 'checklist',
                            'sort_time' => $category->created_at,
                        ]);
                    }

                    // Category was updated (only if updated_at differs from created_at)
                    if ($category->updated_at && $category->updated_at >= $since
                        && $category->created_at && $category->updated_at->ne($category->created_at)) {
                        $activities->push([
                            'type' => 'checklist',
                            'icon' => 'pen-to-square',
                            'title' => 'Category updated',
                            'description' => 'Category "' . $category->name . '" was updated',
                            'time' => $category->updated_at->diffForHumans(),
                            'status' => 'updated',
                            'category' => 'checklist',
                            'sort_time' => $category->updated_at,
                        ]);
                    }

                    // Items within each category
                    foreach ($category->items as $item) {
                        // Item was added
                        if ($item->created_at && $item->created_at >= $since) {
                            $activities->push([
                                'type' => 'checklist',
                                'icon' => 'circle-plus',
                                'title' => 'Item added',
                                'description' => 'Item "' . $item->name . '" was added to "' . $category->name . '"',
                                'time' => $item->created_at->diffForHumans(),
                                'status' => 'added',
                                'category' => 'checklist',
                                'sort_time' => $item->created_at,
                            ]);
                        }

                        // Item was updated (only if updated_at differs from created_at)
                        if ($item->updated_at && $item->updated_at >= $since
                            && $item->created_at && $item->updated_at->ne($item->created_at)) {
                            $activities->push([
                                'type' => 'checklist',
                                'icon' => 'pen-to-square',
                                'title' => 'Item updated',
                                'description' => 'Item "' . $item->name . '" in "' . $category->name . '" was updated',
                                'time' => $item->updated_at->diffForHumans(),
                                'status' => 'updated',
                                'category' => 'checklist',
                                'sort_time' => $item->updated_at,
                            ]);
                        }
                    }
                }
            }
        }

        // Review/Report activities
        if (in_array($filter, ['all', 'reports'])) {
            $reviews = TaskReview::where('contracted_client_id', $contractedClient->id)
                ->with('task.location')
                ->where('created_at', '>=', $since)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            foreach ($reviews as $review) {
                $locationName = $review->task?->location?->name ?? 'Service';
                $activities->push([
                    'type' => 'report',
                    'icon' => 'star',
                    'title' => 'Review Submitted',
                    'description' => "Rated {$locationName} {$review->rating}/5 stars",
                    'time' => $review->created_at->diffForHumans(),
                    'status' => 'completed',
                    'category' => 'reports',
                    'sort_time' => $review->created_at,
                ]);
            }
        }

        $activities = $activities->sortByDesc('sort_time')->take(50)->values();

        return view('manager.activity', compact('activities', 'filter'));
    }

    private function buildTaskActivity($task, $locationName)
    {
        switch ($task->status) {
            case 'Completed':
                return ['type' => 'task', 'icon' => 'check-circle', 'title' => 'Task Completed', 'description' => "Task at {$locationName} was completed", 'time' => $task->updated_at->diffForHumans(), 'status' => 'completed'];
            case 'In Progress':
                return ['type' => 'task', 'icon' => 'spinner', 'title' => 'Task Started', 'description' => "Task at {$locationName} is in progress", 'time' => $task->updated_at->diffForHumans(), 'status' => 'pending'];
            case 'On Hold':
                return ['type' => 'warning', 'icon' => 'pause', 'title' => 'Task On Hold', 'description' => "Task at {$locationName} was put on hold", 'time' => $task->updated_at->diffForHumans(), 'status' => null];
            case 'Cancelled':
                return ['type' => 'warning', 'icon' => 'xmark-circle', 'title' => 'Task Cancelled', 'description' => "Task at {$locationName} was cancelled", 'time' => $task->updated_at->diffForHumans(), 'status' => null];
            case 'Scheduled':
                return ['type' => 'task', 'icon' => 'calendar-plus', 'title' => 'Task Scheduled', 'description' => "New task scheduled at {$locationName}", 'time' => $task->updated_at->diffForHumans(), 'status' => null];
            default:
                return ['type' => 'task', 'icon' => 'list-check', 'title' => 'Task Updated', 'description' => "Task at {$locationName} was updated", 'time' => $task->updated_at->diffForHumans(), 'status' => null];
        }
    }
}
