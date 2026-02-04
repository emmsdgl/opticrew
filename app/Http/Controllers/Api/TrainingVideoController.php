<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingVideoController extends Controller
{
    /**
     * Get all training videos grouped by category
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Get all active videos ordered by category and sort order
            $videos = TrainingVideo::active()
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

        // Get watched video IDs for current user
        $watchedVideoIds = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->pluck('training_video_id')
            ->toArray();

        // Format videos with watched status
        $formattedVideos = $videos->map(function ($video) use ($watchedVideoIds) {
            return [
                'id' => $video->id,
                'category' => $video->category,
                'title' => $video->title,
                'titleFi' => $video->title_fi,
                'description' => $video->description,
                'descriptionFi' => $video->description_fi,
                'videoId' => $video->video_id,
                'platform' => $video->platform,
                'duration' => $video->duration,
                'required' => $video->required,
                'thumbnailUrl' => $video->thumbnail,
                'isWatched' => in_array($video->id, $watchedVideoIds),
            ];
        });

        // Get category info
        $categories = TrainingVideo::getCategories();

        // Calculate completion stats
        $totalVideos = $videos->count();
        $watchedCount = count($watchedVideoIds);
        $requiredTotal = $videos->where('required', true)->count();
        $requiredWatched = $videos->where('required', true)
            ->whereIn('id', $watchedVideoIds)->count();

        return response()->json([
            'videos' => $formattedVideos,
            'categories' => $categories,
            'stats' => [
                'total' => $totalVideos,
                'watched' => $watchedCount,
                'requiredTotal' => $requiredTotal,
                'requiredWatched' => $requiredWatched,
                'completionPercentage' => $totalVideos > 0
                    ? round(($watchedCount / $totalVideos) * 100)
                    : 0,
            ],
        ]);
        } catch (\Exception $e) {
            // Return empty data with 200 so app doesn't crash
            // Log the error for debugging
            \Log::error('Training videos error: ' . $e->getMessage());

            return response()->json([
                'videos' => [],
                'categories' => [
                    'cleaning_techniques' => [
                        'title' => 'Cleaning Techniques',
                        'titleFi' => 'Puhdistustekniikat',
                        'color' => '#2563eb',
                    ],
                    'body_safety' => [
                        'title' => 'Body Safety',
                        'titleFi' => 'Kehon turvallisuus',
                        'color' => '#22c55e',
                    ],
                    'hazard_prevention' => [
                        'title' => 'Hazard Prevention',
                        'titleFi' => 'Vaarojen ehkäisy',
                        'color' => '#f59e0b',
                    ],
                    'chemical_safety' => [
                        'title' => 'Chemical Safety',
                        'titleFi' => 'Kemikaaliturvallisuus',
                        'color' => '#ef4444',
                    ],
                ],
                'stats' => [
                    'total' => 0,
                    'watched' => 0,
                    'requiredTotal' => 0,
                    'requiredWatched' => 0,
                    'completionPercentage' => 0,
                ],
            ]);
        }
    }

    /**
     * Get videos by category
     */
    public function byCategory(Request $request, $category)
    {
        $user = $request->user();

        $videos = TrainingVideo::active()
            ->byCategory($category)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Get watched video IDs for current user
        $watchedVideoIds = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->pluck('training_video_id')
            ->toArray();

        $formattedVideos = $videos->map(function ($video) use ($watchedVideoIds) {
            return [
                'id' => $video->id,
                'category' => $video->category,
                'title' => $video->title,
                'titleFi' => $video->title_fi,
                'description' => $video->description,
                'descriptionFi' => $video->description_fi,
                'videoId' => $video->video_id,
                'platform' => $video->platform,
                'duration' => $video->duration,
                'required' => $video->required,
                'thumbnailUrl' => $video->thumbnail,
                'isWatched' => in_array($video->id, $watchedVideoIds),
            ];
        });

        return response()->json([
            'videos' => $formattedVideos,
        ]);
    }

    /**
     * Mark a video as watched
     */
    public function markAsWatched(Request $request, $videoId)
    {
        $user = $request->user();

        $video = TrainingVideo::findOrFail($videoId);

        // Check if already watched
        $exists = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->where('training_video_id', $videoId)
            ->exists();

        if (!$exists) {
            DB::table('employee_watched_videos')->insert([
                'user_id' => $user->id,
                'training_video_id' => $videoId,
                'watched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Video marked as watched',
            'success' => true,
        ]);
    }

    /**
     * Unmark a video as watched
     */
    public function unmarkAsWatched(Request $request, $videoId)
    {
        $user = $request->user();

        DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->where('training_video_id', $videoId)
            ->delete();

        return response()->json([
            'message' => 'Video unmarked as watched',
            'success' => true,
        ]);
    }

    /**
     * Get watched videos list
     */
    public function getWatchedVideos(Request $request)
    {
        $user = $request->user();

        $watchedVideoIds = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->pluck('training_video_id')
            ->toArray();

        return response()->json([
            'watchedVideoIds' => $watchedVideoIds,
        ]);
    }

    /**
     * Get completion stats for current user
     */
    public function getStats(Request $request)
    {
        $user = $request->user();

        $totalVideos = TrainingVideo::active()->count();
        $watchedCount = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->count();

        $requiredVideos = TrainingVideo::active()->where('required', true)->get();
        $requiredTotal = $requiredVideos->count();
        $requiredWatched = DB::table('employee_watched_videos')
            ->where('user_id', $user->id)
            ->whereIn('training_video_id', $requiredVideos->pluck('id'))
            ->count();

        // Stats by category
        $categories = TrainingVideo::getCategories();
        $categoryStats = [];

        foreach ($categories as $key => $info) {
            $categoryVideos = TrainingVideo::active()->byCategory($key)->get();
            $categoryTotal = $categoryVideos->count();
            $categoryWatched = DB::table('employee_watched_videos')
                ->where('user_id', $user->id)
                ->whereIn('training_video_id', $categoryVideos->pluck('id'))
                ->count();

            $categoryStats[$key] = [
                'total' => $categoryTotal,
                'watched' => $categoryWatched,
                'percentage' => $categoryTotal > 0
                    ? round(($categoryWatched / $categoryTotal) * 100)
                    : 0,
            ];
        }

        return response()->json([
            'total' => $totalVideos,
            'watched' => $watchedCount,
            'requiredTotal' => $requiredTotal,
            'requiredWatched' => $requiredWatched,
            'completionPercentage' => $totalVideos > 0
                ? round(($watchedCount / $totalVideos) * 100)
                : 0,
            'categoryStats' => $categoryStats,
        ]);
    }
}
