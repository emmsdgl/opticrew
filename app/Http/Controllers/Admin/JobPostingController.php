<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Get all job postings (API)
     */
    public function index()
    {
        $jobPostings = JobPosting::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $jobPostings
        ]);
    }

    /**
     * Store a new job posting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:100',
            'type' => 'required|in:full-time,part-time,remote',
            'type_badge' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'icon_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'required_skills' => 'nullable|array',
            'required_docs' => 'nullable|array',
        ]);

        // Set type badge based on type if not provided
        if (empty($validated['type_badge'])) {
            $validated['type_badge'] = match($validated['type']) {
                'full-time' => 'Full-time Employee',
                'part-time' => 'Part-time Employee',
                'remote' => 'Remote',
                default => 'Full-time Employee',
            };
        }

        $jobPosting = JobPosting::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job posting created successfully.',
            'data' => $jobPosting
        ]);
    }

    /**
     * Update a job posting
     */
    public function update(Request $request, $id)
    {
        $jobPosting = JobPosting::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:100',
            'type' => 'required|in:full-time,part-time,remote',
            'type_badge' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'icon_color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'required_skills' => 'nullable|array',
            'required_docs' => 'nullable|array',
        ]);

        // Set type badge based on type if not provided
        if (empty($validated['type_badge'])) {
            $validated['type_badge'] = match($validated['type']) {
                'full-time' => 'Full-time Employee',
                'part-time' => 'Part-time Employee',
                'remote' => 'Remote',
                default => 'Full-time Employee',
            };
        }

        $jobPosting->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Job posting updated successfully.',
            'data' => $jobPosting
        ]);
    }

    /**
     * Delete a job posting
     */
    public function destroy($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job posting deleted successfully.'
        ]);
    }

    /**
     * Get active job postings (public API for landing page)
     */
    public function getActivePostings()
    {
        $jobPostings = JobPosting::active()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $jobPostings
        ]);
    }
}
