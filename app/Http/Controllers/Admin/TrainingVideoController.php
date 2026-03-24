<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingVideo;
use Illuminate\Http\Request;

class TrainingVideoController extends Controller
{
    public function index()
    {
        $trainingVideos = TrainingVideo::orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $categories = TrainingVideo::getCategories();

        return view('admin.training.index', compact('trainingVideos', 'categories'));
    }

    public function store(Request $request)
    {
        $isDraft = !filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => $isDraft ? 'nullable|string' : 'required|string|min:180',
            'video_id' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,webm,mov,avi|max:512000',
            'platform' => $isDraft ? 'nullable|string|in:youtube,upload' : 'required|string|in:youtube,upload',
            'category' => $isDraft ? 'nullable|in:cleaning_techniques,body_safety,hazard_prevention,chemical_safety' : 'required|in:cleaning_techniques,body_safety,hazard_prevention,chemical_safety',
            'duration' => 'nullable|string|max:50',
            'required' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['platform'] = $validated['platform'] ?? 'youtube';
        $validated['category'] = $validated['category'] ?? 'cleaning_techniques';

        if (!$isDraft && $validated['platform'] === 'youtube' && empty($validated['video_id'])) {
            return response()->json(['success' => false, 'message' => 'YouTube Video ID is required.'], 422);
        }

        if (!$isDraft && $validated['platform'] === 'upload' && !$request->hasFile('video_file')) {
            return response()->json(['success' => false, 'message' => 'Video file is required.'], 422);
        }

        if ($request->hasFile('video_file')) {
            $validated['video_path'] = $request->file('video_file')->store('training-videos', 'public');
        }

        unset($validated['video_file']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $video = TrainingVideo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Training video created successfully.',
            'data' => $video,
        ]);
    }

    public function update(Request $request, $id)
    {
        $video = TrainingVideo::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:180',
            'video_id' => 'nullable|string|max:255',
            'video_file' => 'nullable|file|mimes:mp4,webm,mov,avi|max:512000',
            'platform' => 'required|string|in:youtube,upload',
            'category' => 'required|in:cleaning_techniques,body_safety,hazard_prevention,chemical_safety',
            'duration' => 'nullable|string|max:50',
            'required' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validated['platform'] === 'youtube' && empty($validated['video_id'])) {
            return response()->json(['success' => false, 'message' => 'YouTube Video ID is required.'], 422);
        }

        if ($request->hasFile('video_file')) {
            // Delete old file if exists
            if ($video->video_path && \Storage::disk('public')->exists($video->video_path)) {
                \Storage::disk('public')->delete($video->video_path);
            }
            $validated['video_path'] = $request->file('video_file')->store('training-videos', 'public');
        }

        // Clear video_path when switching to youtube
        if ($validated['platform'] === 'youtube' && $video->platform === 'upload') {
            if ($video->video_path && \Storage::disk('public')->exists($video->video_path)) {
                \Storage::disk('public')->delete($video->video_path);
            }
            $validated['video_path'] = null;
        }

        // Clear video_id when using upload
        if ($validated['platform'] === 'upload') {
            $validated['video_id'] = null;
        }

        unset($validated['video_file']);
        $video->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Training video updated successfully.',
            'data' => $video,
        ]);
    }

    public function destroy($id)
    {
        $video = TrainingVideo::findOrFail($id);

        if ($video->video_path && \Storage::disk('public')->exists($video->video_path)) {
            \Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Training video deleted successfully.',
        ]);
    }
}
