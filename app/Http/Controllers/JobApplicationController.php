<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Store a new job application (public route)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_type' => 'nullable|string|max:50',
            'email' => 'required|email|max:255',
            'alternative_email' => 'nullable|email|max:255',
            'pdf_document' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Store the PDF file
        $file = $request->file('pdf_document');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('job-applications', 'public');

        // Create the application record
        $application = JobApplication::create([
            'job_title' => $validated['job_title'],
            'job_type' => $validated['job_type'] ?? null,
            'email' => $validated['email'],
            'alternative_email' => $validated['alternative_email'] ?? null,
            'resume_path' => $path,
            'resume_original_name' => $originalName,
            'status' => 'pending',
        ]);

        // Notify all admins of the new application
        app(NotificationService::class)->notifyAdminsNewJobApplication($application);

        return response()->json([
            'success' => true,
            'message' => 'Your application has been submitted successfully! We will contact you soon through the email provided.',
            'application_id' => $application->id,
        ]);
    }

    /**
     * Display list of applications (admin)
     */
    public function index(Request $request)
    {
        $query = JobApplication::query();

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by email or job title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);
        $jobPostings = JobPosting::orderBy('created_at', 'desc')->get();

        // Get applicant counts per job title
        $applicantCounts = JobApplication::selectRaw('job_title, COUNT(*) as count')
            ->groupBy('job_title')
            ->pluck('count', 'job_title');

        return view('admin.recruitment.index', compact('applications', 'jobPostings', 'applicantCounts'));
    }

    /**
     * Show a single application (admin)
     */
    public function show($id)
    {
        $application = JobApplication::findOrFail($id);
        return view('admin.recruitment.show', compact('application'));
    }

    /**
     * Update application status (admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,interview_scheduled,hired,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $application = JobApplication::findOrFail($id);
        $application->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $application->admin_notes,
            'reviewed_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Application status updated successfully.']);
        }

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Download resume (admin)
     */
    public function downloadResume($id)
    {
        $application = JobApplication::findOrFail($id);

        $filePath = storage_path('app/public/' . $application->resume_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        return response()->download($filePath, $application->resume_original_name);
    }

    /**
     * Delete application (admin)
     */
    public function destroy($id)
    {
        $application = JobApplication::findOrFail($id);

        // Delete the resume file
        if (Storage::disk('public')->exists($application->resume_path)) {
            Storage::disk('public')->delete($application->resume_path);
        }

        $application->delete();

        return redirect()->route('admin.recruitment.index')->with('success', 'Application deleted successfully.');
    }
}
