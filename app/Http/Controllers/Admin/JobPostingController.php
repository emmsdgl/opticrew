<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\Embedding\EmbeddingService;
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
            'status' => 'nullable|in:published,draft,archived',
            'required_skills' => 'nullable|array',
            'required_docs' => 'nullable|array',
            'benefits' => 'nullable|array',
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

        // Default status to published if not provided
        if (empty($validated['status'])) {
            $validated['status'] = 'published';
        }

        // Drafts should not be active
        if ($validated['status'] === 'draft') {
            $validated['is_active'] = false;
        }

        $jobPosting = JobPosting::create($validated);

        return response()->json([
            'success' => true,
            'message' => $validated['status'] === 'draft'
                ? 'Job posting saved as draft.'
                : 'Job posting created successfully.',
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
            'status' => 'nullable|in:published,draft,archived',
            'required_skills' => 'nullable|array',
            'required_docs' => 'nullable|array',
            'benefits' => 'nullable|array',
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
     * Archive a job posting
     */
    public function archive($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->update(['status' => 'archived', 'is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Job posting archived successfully.',
        ]);
    }

    /**
     * Restore an archived job posting
     */
    public function restore($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->update(['status' => 'published', 'is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Job posting restored successfully.',
        ]);
    }

    /**
     * Get archived job postings and deleted applications
     */
    public function archived()
    {
        $archivedPostings = JobPosting::archived()->orderBy('updated_at', 'desc')->get();

        // Get applicant counts per job title
        $applicantCounts = \App\Models\JobApplication::selectRaw('job_title, COUNT(*) as count')
            ->groupBy('job_title')
            ->pluck('count', 'job_title');

        // Get soft-deleted applications
        $deletedApplications = \App\Models\JobApplication::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        // Get hired applications (archived from recruitment pipeline)
        $hiredApplications = \App\Models\JobApplication::where('status', 'hired')
            ->orderBy('reviewed_at', 'desc')
            ->get();

        return view('admin.recruitment.archived', compact('archivedPostings', 'applicantCounts', 'deletedApplications', 'hiredApplications'));
    }

    /**
     * Delete a job posting (moves to archived)
     */
    public function destroy($id)
    {
        $jobPosting = JobPosting::findOrFail($id);
        $jobPosting->update(['status' => 'archived', 'is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Job posting moved to archived.',
        ]);
    }

    /**
     * Bulk delete job postings (moves to archived)
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:job_postings,id',
        ]);

        JobPosting::whereIn('id', $validated['ids'])->update(['status' => 'archived', 'is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' job posting(s) moved to archived.',
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

    /**
     * Rank applicants for a job posting using SBERT semantic matching.
     * Falls back to fuzzy substring matching if SBERT API is unavailable.
     */
    public function rankApplicants($id)
    {
        $job = JobPosting::findOrFail($id);
        $requiredSkills = $job->required_skills ?? [];
        $applications = JobApplication::where('job_title', $job->title)->get();

        if (empty($requiredSkills) || $applications->isEmpty()) {
            return response()->json([
                'ranked' => $applications->map(fn($a) => [
                    'application_id' => $a->id,
                    'score' => 0,
                    'matches' => [],
                    'method' => 'none',
                ])->toArray(),
            ]);
        }

        $embeddingService = new EmbeddingService();
        $useSbert = $embeddingService->isAvailable();

        if ($useSbert) {
            // Build applicants array for batch scoring
            $applicantsData = [];
            foreach ($applications as $app) {
                $profile = is_string($app->applicant_profile)
                    ? json_decode($app->applicant_profile, true)
                    : ($app->applicant_profile ?? []);
                $skills = array_filter(
                    array_map('trim', explode(',', $profile['skills'] ?? '')),
                    fn($s) => strlen($s) > 0
                );
                $applicantsData[] = [
                    'id'     => $app->id,
                    'skills' => array_values($skills),
                ];
            }

            $results = $embeddingService->batchScore($requiredSkills, $applicantsData);

            return response()->json([
                'ranked' => array_map(fn($r) => [
                    'application_id' => $r['id'],
                    'score'          => $r['score'],
                    'matches'        => $r['matches'] ?? [],
                    'method'         => 'sbert',
                ], $results),
            ]);
        }

        // Fallback: fuzzy substring matching (current behavior)
        $ranked = [];
        foreach ($applications as $app) {
            $profile = is_string($app->applicant_profile)
                ? json_decode($app->applicant_profile, true)
                : ($app->applicant_profile ?? []);
            $applicantSkills = array_filter(
                array_map('trim', explode(',', $profile['skills'] ?? '')),
                fn($s) => strlen($s) > 0
            );

            $matched = [];
            foreach ($requiredSkills as $reqSkill) {
                $reqLower = strtolower($reqSkill);
                foreach ($applicantSkills as $appSkill) {
                    $appLower = strtolower($appSkill);
                    if (str_contains($appLower, $reqLower) || str_contains($reqLower, $appLower)) {
                        $matched[] = [
                            'required'   => $reqSkill,
                            'matched'    => $appSkill,
                            'similarity' => 1.0,
                        ];
                        break;
                    }
                }
            }

            $score = count($requiredSkills) > 0
                ? (int) round((count($matched) / count($requiredSkills)) * 100)
                : 0;

            $ranked[] = [
                'application_id' => $app->id,
                'score'          => $score,
                'matches'        => $matched,
                'method'         => 'fuzzy',
            ];
        }

        usort($ranked, fn($a, $b) => $b['score'] - $a['score']);

        return response()->json(['ranked' => $ranked]);
    }
}
