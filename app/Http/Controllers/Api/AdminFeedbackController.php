<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Feedback;

class AdminFeedbackController extends Controller
{
    /**
     * List all client feedback with summary stats.
     * Supports optional filters: ?rating=5, ?sort=latest|oldest
     */
    public function index(Request $request)
    {
        try {
            $query = Feedback::with(['client', 'task', 'appointment'])
                ->whereNotNull('client_id')
                ->orderBy('created_at', $request->get('sort', 'latest') === 'oldest' ? 'asc' : 'desc');

            // Filter by rating (use the modal 'rating' field, fallback to 'overall_rating')
            if ($request->has('rating') && $request->rating) {
                $ratingValue = (int) $request->rating;
                $query->where(function ($q) use ($ratingValue) {
                    $q->where('rating', $ratingValue)
                      ->orWhere(function ($q2) use ($ratingValue) {
                          $q2->whereNull('rating')
                             ->where('overall_rating', $ratingValue);
                      });
                });
            }

            $feedback = $query->get()->map(function ($item) {
                // Use the modal rating if available, otherwise fall back to overall_rating
                $displayRating = $item->rating ?? $item->overall_rating ?? 0;

                // Build client name
                $clientName = 'Unknown Client';
                if ($item->client) {
                    $clientName = trim(
                        ($item->client->first_name ?? '') . ' ' . ($item->client->last_name ?? '')
                    );
                    if (empty(trim($clientName)) && $item->client->company_name) {
                        $clientName = $item->client->company_name;
                    }
                }

                // Use feedback_text if available, otherwise comments
                $comment = $item->feedback_text ?? $item->comments ?? '';

                // Tags/keywords
                $tags = $item->keywords ?? [];

                // Task/service info
                $serviceInfo = $item->service_type ?? null;
                $taskInfo = null;
                if ($item->task) {
                    $taskInfo = $item->task->title ?? $item->task->name ?? null;
                }

                return [
                    'id' => $item->id,
                    'client_name' => $clientName,
                    'client_initials' => strtoupper(
                        substr($item->client->first_name ?? 'U', 0, 1) .
                        substr($item->client->last_name ?? '', 0, 1)
                    ),
                    'rating' => $displayRating,
                    'comment' => $comment,
                    'tags' => $tags,
                    'service_type' => $serviceInfo,
                    'task_name' => $taskInfo,
                    'would_recommend' => $item->would_recommend ?? null,
                    'created_at' => $item->created_at ? $item->created_at->toIso8601String() : null,
                ];
            });

            // Calculate summary stats from ALL client feedback (unfiltered)
            $allFeedback = Feedback::whereNotNull('client_id')->get();
            $totalReviews = $allFeedback->count();

            // Calculate ratings using the modal 'rating' field with fallback to 'overall_rating'
            $ratings = $allFeedback->map(function ($item) {
                return $item->rating ?? $item->overall_rating ?? 0;
            })->filter(function ($r) {
                return $r > 0;
            });

            $averageRating = $ratings->count() > 0 ? round($ratings->avg(), 1) : 0;

            $ratingCounts = [];
            for ($i = 5; $i >= 1; $i--) {
                $ratingCounts[$i] = $allFeedback->filter(function ($item) use ($i) {
                    $r = $item->rating ?? $item->overall_rating ?? 0;
                    return $r == $i;
                })->count();
            }

            return response()->json([
                'success' => true,
                'feedback' => $feedback,
                'summary' => [
                    'average_rating' => $averageRating,
                    'total_reviews' => $totalReviews,
                    'rating_counts' => $ratingCounts,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AdminFeedbackController@index error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feedback.',
            ], 500);
        }
    }
}
