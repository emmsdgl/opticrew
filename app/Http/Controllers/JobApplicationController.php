<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationReceivedAfterHours;
use App\Mail\ApplicationStatusUpdate;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

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

        // Scenario #1: Prevent duplicate applications (same email + same job)
        $alreadyApplied = JobApplication::where('email', $validated['email'])
            ->where('job_title', $validated['job_title'])
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'success' => false,
                'message' => 'An applicant with the same email already has the same application submitted for this position.',
            ], 422);
        }

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
            'status_history' => [[
                'from' => null,
                'to' => 'pending',
                'timestamp' => now()->toIso8601String(),
                'by' => 'Applicant',
            ]],
        ]);

        // Notify all admins of the new application
        app(NotificationService::class)->notifyAdminsNewJobApplication($application);

        // Scenario #6: Auto-response if submitted outside business hours (Mon-Fri 8AM-5PM)
        $now = now();
        $hour = (int) $now->format('H');
        $isWeekend = $now->isWeekend();
        $isAfterHours = $isWeekend || $hour < 8 || $hour >= 17;

        if ($isAfterHours) {
            $nextBusinessDay = $now->copy();
            if ($isWeekend) {
                $nextBusinessDay = $nextBusinessDay->next(\Carbon\Carbon::MONDAY);
            } else {
                $nextBusinessDay = $nextBusinessDay->addWeekday();
            }
            $responseEta = $nextBusinessDay->format('l, F d, Y') . ' (next business day)';

            try {
                $recipients = [$application->email];
                if ($application->alternative_email) {
                    $recipients[] = $application->alternative_email;
                }
                Mail::to($recipients)->send(new ApplicationReceivedAfterHours($application, $responseEta));
            } catch (\Exception $e) {
                \Log::error('Failed to send after-hours auto-response: ' . $e->getMessage());
            }
        }

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
        $jobPostings = JobPosting::where('status', '!=', 'archived')->orderBy('created_at', 'desc')->get();

        // Get applicant counts per job title
        $applicantCounts = JobApplication::selectRaw('job_title, COUNT(*) as count')
            ->groupBy('job_title')
            ->pluck('count', 'job_title');

        $cscApiKey = env('CSC_API_KEY', '');

        // Get all applications with profiles for suitability scoring in job posting drawer
        $allApplications = JobApplication::select('id', 'job_title', 'email', 'status', 'applicant_profile', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                $profile = json_decode($app->applicant_profile, true) ?? [];
                return [
                    'id'         => $app->id,
                    'job_title'  => $app->job_title,
                    'email'      => $app->email,
                    'status'     => $app->status,
                    'name'       => trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')),
                    'skills'     => $profile['skills'] ?? '',
                    'city'       => $profile['city'] ?? '',
                    'country'    => $profile['country'] ?? '',
                    'created_at' => $app->created_at->format('M d, Y'),
                ];
            });

        // Get registered applicant users (with Google accounts)
        $applicantUsers = \App\Models\User::where('role', 'applicant')
            ->orderBy('created_at', 'desc')
            ->get();

        // Scenario #5: Get unresolved duplicate applicant notifications for current admin
        $duplicateAlerts = \App\Models\Notification::where('user_id', auth()->id())
            ->where('type', \App\Models\Notification::TYPE_DUPLICATE_APPLICANT)
            ->whereNull('read_at')
            ->get()
            ->map(function ($n) {
                $data = $n->data;
                return [
                    'notification_id' => $n->id,
                    'new_application_id' => $data['new_application_id'] ?? null,
                    'existing_application_id' => $data['existing_application_id'] ?? null,
                    'existing_email' => $data['existing_email'] ?? '',
                    'phone' => $data['phone'] ?? '',
                ];
            })
            ->keyBy('new_application_id');

        return view('admin.recruitment.index', compact('applications', 'jobPostings', 'applicantCounts', 'cscApiKey', 'allApplications', 'applicantUsers', 'duplicateAlerts'));
    }

    /**
     * Interviews calendar page (admin)
     */
    public function interviews()
    {
        $interviewApplications = JobApplication::whereNotNull('interview_date')
            ->where('status', 'interview_scheduled')
            ->orderBy('interview_date', 'asc')
            ->get();

        return view('admin.recruitment.interviews', compact('interviewApplications'));
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
            'interview_date' => 'nullable|date',
            'interview_duration' => 'nullable|integer|min:15|max:480',
        ]);

        $application = JobApplication::findOrFail($id);

        // Scenario #9: Block any status changes on withdrawn applications
        if ($application->status === 'withdrawn') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Cannot update a withdrawn application.'], 422);
            }
            return redirect()->back()->with('error', 'Cannot update a withdrawn application.');
        }

        $oldStatus = $application->status;

        // Server-side overlap check for interview scheduling
        if ($validated['status'] === 'interview_scheduled' && isset($validated['interview_date'])) {
            $newStart = \Carbon\Carbon::parse($validated['interview_date']);
            $newDuration = $validated['interview_duration'] ?? 60;
            $newEnd = $newStart->copy()->addMinutes($newDuration);

            $overlapping = JobApplication::whereNotNull('interview_date')
                ->whereIn('status', ['interview_scheduled', 'hired'])
                ->where('id', '!=', $id)
                ->get()
                ->filter(function ($existing) use ($newStart, $newEnd) {
                    $existStart = $existing->interview_date;
                    $existEnd = $existStart->copy()->addMinutes($existing->interview_duration ?? 60);
                    // Overlap: start1 < end2 AND end1 > start2
                    return $newStart->lt($existEnd) && $newEnd->gt($existStart);
                });

            if ($overlapping->isNotEmpty()) {
                $conflict = $overlapping->first();
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule conflict: overlaps with ' . $conflict->job_title . ' (' . $conflict->email . ') on ' . $conflict->interview_date->format('M d, Y \a\t h:i A'),
                ], 422);
            }
        }

        // Build timeline entry
        $history = $application->status_history ?? [];
        if ($oldStatus !== $validated['status']) {
            $entry = [
                'from' => $oldStatus,
                'to' => $validated['status'],
                'timestamp' => now()->toIso8601String(),
                'by' => auth()->user()->name ?? 'Admin',
            ];
            if ($validated['status'] === 'interview_scheduled' && isset($validated['interview_date'])) {
                $entry['interview_date'] = $validated['interview_date'];
            }
            $history[] = $entry;
        }

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $application->admin_notes,
            'reviewed_at' => now(),
            'status_history' => $history,
        ];

        if (isset($validated['interview_date'])) {
            $updateData['interview_date'] = $validated['interview_date'];
        }
        if (isset($validated['interview_duration'])) {
            $updateData['interview_duration'] = $validated['interview_duration'];
        }

        $application->update($updateData);

        // Scenario #10: Role transition — redirect to employee setup when hired
        if ($validated['status'] === 'hired' && $oldStatus !== 'hired') {
            // Check if an employee account already exists with this email
            $existingEmployee = User::where('email', $application->email)
                ->where('role', 'employee')
                ->first();

            if ($existingEmployee) {
                // Existing employee — check if Gmail is linked
                if (!$existingEmployee->google_id) {
                    // Send email/notification and flash message about Gmail linking
                    session()->flash('gmail_link_prompt', true);
                    session()->flash('gmail_link_user_id', $existingEmployee->id);
                    session()->flash('gmail_link_user_name', $existingEmployee->name);
                }
                // Skip employee creation — already exists
            } else {
                // Redirect admin to employee account setup page (pre-filled from applicant data)
                // Send notifications first
                if ($oldStatus !== $validated['status'] && $application->status !== 'withdrawn') {
                    try {
                        $recipients = [$application->email];
                        if ($application->alternative_email) {
                            $recipients[] = $application->alternative_email;
                        }
                        Mail::to($recipients)->send(new ApplicationStatusUpdate($application));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send application status email: ' . $e->getMessage());
                    }
                    try {
                        app(NotificationService::class)->notifyApplicantStatusChanged($application, $oldStatus, $validated['status']);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send applicant status notification: ' . $e->getMessage());
                    }
                }

                return redirect()->route('admin.recruitment.setup-employee', $application->id)
                    ->with('success', 'Applicant marked as hired. Please complete the employee account setup.');
            }
        }

        // Send email and in-app notification when status changes
        // Scenario #9: Ghosting Prevention — suppress all emails for withdrawn applicants
        if ($oldStatus !== $validated['status'] && $application->status !== 'withdrawn') {
            try {
                $recipients = [$application->email];
                if ($application->alternative_email) {
                    $recipients[] = $application->alternative_email;
                }
                Mail::to($recipients)->send(new ApplicationStatusUpdate($application));
            } catch (\Exception $e) {
                \Log::error('Failed to send application status email: ' . $e->getMessage());
            }

            // In-app notification for applicant
            try {
                app(NotificationService::class)->notifyApplicantStatusChanged($application, $oldStatus, $validated['status']);
            } catch (\Exception $e) {
                \Log::error('Failed to send applicant status notification: ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Application status updated and email notification sent.']);
        }

        return redirect()->back()->with('success', 'Application status updated and email notification sent.');
    }

    /**
     * View resume inline (admin) — serves the file for iframe display
     */
    public function viewResume($id)
    {
        $application = JobApplication::findOrFail($id);

        $filePath = storage_path('app/public/' . $application->resume_path);

        if (!file_exists($filePath)) {
            abort(404, 'Resume file not found.');
        }

        $ext = strtolower(pathinfo($application->resume_original_name, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc'  => 'application/msword',
        ];
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        return response()->file($filePath, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . $application->resume_original_name . '"',
        ]);
    }

    /**
     * Preview DOCX resume as HTML (admin) — converts DOCX to styled HTML for iframe display
     */
    public function previewResume($id)
    {
        $application = JobApplication::findOrFail($id);

        $filePath = storage_path('app/public/' . $application->resume_path);

        if (!file_exists($filePath)) {
            abort(404, 'Resume file not found.');
        }

        $ext = strtolower(pathinfo($application->resume_original_name, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            $base64 = base64_encode(file_get_contents($filePath));
            $fileName = e($application->resume_original_name);
            return response("<!DOCTYPE html>
<html><head><meta charset='UTF-8'><title>{$fileName}</title>
<script src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'></script>
<style>*{margin:0;padding:0;box-sizing:border-box}body{background:#525659;display:flex;flex-direction:column;align-items:center;padding:16px 0;gap:12px}
canvas{background:#fff;box-shadow:0 2px 8px rgba(0,0,0,.3);max-width:100%}</style></head><body>
<script>
const pdfData=atob('{$base64}');
const loadingTask=pdfjsLib.getDocument({data:pdfData});
loadingTask.promise.then(pdf=>{
  for(let i=1;i<=pdf.numPages;i++){
    pdf.getPage(i).then(page=>{
      const scale=1.5;const viewport=page.getViewport({scale});
      const canvas=document.createElement('canvas');
      canvas.width=viewport.width;canvas.height=viewport.height;
      document.body.appendChild(canvas);
      page.render({canvasContext:canvas.getContext('2d'),viewport});
    });
  }
});
</script></body></html>", 200, ['Content-Type' => 'text/html']);
        }

        // For DOCX files, extract text and render as styled HTML
        $html = $this->convertDocxToHtml($filePath);

        $styledHtml = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . e($application->resume_original_name) . '</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.7;
        color: #1a1a2e;
        background: #f8f9fa;
        padding: 0;
    }
    .document-wrapper {
        max-width: 816px;
        margin: 24px auto;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.06);
        border-radius: 4px;
        padding: 60px 72px;
        min-height: calc(100vh - 48px);
    }
    h1 { font-size: 22px; font-weight: 700; margin: 18px 0 10px; color: #16213e; }
    h2 { font-size: 18px; font-weight: 600; margin: 16px 0 8px; color: #16213e; border-bottom: 2px solid #e2e8f0; padding-bottom: 4px; }
    h3 { font-size: 15px; font-weight: 600; margin: 14px 0 6px; color: #334155; }
    p { margin: 6px 0; font-size: 13.5px; }
    ul, ol { margin: 6px 0 6px 24px; font-size: 13.5px; }
    li { margin: 3px 0; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    td, th { border: 1px solid #e2e8f0; padding: 6px 10px; font-size: 13px; text-align: left; }
    th { background: #f1f5f9; font-weight: 600; }
    strong, b { font-weight: 600; }
    em, i { font-style: italic; }
    .empty-notice {
        text-align: center;
        color: #94a3b8;
        padding: 60px 20px;
        font-size: 15px;
    }
</style>
</head>
<body>
<div class="document-wrapper">' . $html . '</div>
</body>
</html>';

        return response($styledHtml, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Convert a DOCX file to HTML by parsing its XML content
     */
    private function convertDocxToHtml(string $filePath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return '<p class="empty-notice">Unable to open document.</p>';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) {
            return '<p class="empty-notice">No content found in document.</p>';
        }

        // Suppress warnings for potentially malformed XML
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $html = '';
        $body = $xpath->query('//w:body')->item(0);

        if (!$body) {
            return '<p class="empty-notice">No content found in document.</p>';
        }

        foreach ($body->childNodes as $node) {
            if ($node->nodeName === 'w:p') {
                $html .= $this->convertParagraph($node, $xpath);
            } elseif ($node->nodeName === 'w:tbl') {
                $html .= $this->convertTable($node, $xpath);
            }
        }

        return $html ?: '<p class="empty-notice">Document appears to be empty.</p>';
    }

    private function convertParagraph(\DOMNode $para, \DOMXPath $xpath): string
    {
        // Check paragraph style for headings
        $pStyle = $xpath->query('.//w:pPr/w:pStyle/@w:val', $para);
        $styleName = $pStyle->length > 0 ? $pStyle->item(0)->nodeValue : '';

        // Check for numbered/bulleted lists
        $numPr = $xpath->query('.//w:pPr/w:numPr', $para);
        $isList = $numPr->length > 0;

        $text = '';
        $runs = $xpath->query('.//w:r', $para);

        foreach ($runs as $run) {
            $runText = '';
            $tNodes = $xpath->query('.//w:t', $run);
            foreach ($tNodes as $t) {
                $runText .= $t->nodeValue;
            }

            if ($runText === '') continue;

            // Check run properties for bold/italic/underline
            $isBold = $xpath->query('.//w:rPr/w:b', $run)->length > 0;
            $isItalic = $xpath->query('.//w:rPr/w:i', $run)->length > 0;
            $isUnderline = $xpath->query('.//w:rPr/w:u', $run)->length > 0;

            $escaped = e($runText);
            if ($isBold) $escaped = '<strong>' . $escaped . '</strong>';
            if ($isItalic) $escaped = '<em>' . $escaped . '</em>';
            if ($isUnderline) $escaped = '<u>' . $escaped . '</u>';

            $text .= $escaped;
        }

        if (trim($text) === '') {
            return '<p>&nbsp;</p>';
        }

        // Map heading styles
        if (preg_match('/^Heading(\d)$/i', $styleName, $m)) {
            $level = min((int)$m[1], 6);
            return "<h{$level}>{$text}</h{$level}>\n";
        }

        if (stripos($styleName, 'Title') !== false) {
            return "<h1>{$text}</h1>\n";
        }

        if (stripos($styleName, 'Subtitle') !== false) {
            return "<h3>{$text}</h3>\n";
        }

        if ($isList) {
            return "<li>{$text}</li>\n";
        }

        return "<p>{$text}</p>\n";
    }

    private function convertTable(\DOMNode $table, \DOMXPath $xpath): string
    {
        $html = '<table>';
        $rows = $xpath->query('.//w:tr', $table);

        foreach ($rows as $ri => $row) {
            $html .= '<tr>';
            $cells = $xpath->query('.//w:tc', $row);
            $tag = $ri === 0 ? 'th' : 'td';

            foreach ($cells as $cell) {
                $cellText = '';
                $paras = $xpath->query('.//w:p', $cell);
                foreach ($paras as $pi => $p) {
                    if ($pi > 0) $cellText .= '<br>';
                    $runs = $xpath->query('.//w:r//w:t', $p);
                    foreach ($runs as $t) {
                        $cellText .= e($t->nodeValue);
                    }
                }
                $html .= "<{$tag}>{$cellText}</{$tag}>";
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html . "\n";
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
        $application->delete();

        return redirect()->route('admin.recruitment.index')->with('success', 'Application deleted successfully.');
    }

    /**
     * Bulk delete applications (admin)
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:job_applications,id',
        ]);

        JobApplication::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => count($validated['ids']) . ' application(s) deleted successfully.',
        ]);
    }

    /**
     * Restore a soft-deleted application (admin)
     */
    public function restore($id)
    {
        $application = JobApplication::onlyTrashed()->findOrFail($id);
        $application->restore();

        return response()->json([
            'success' => true,
            'message' => 'Application restored successfully.',
        ]);
    }

    /**
     * Scenario #5: Merge duplicate applicant — transfer the new application's email to use the existing one.
     */
    public function mergeDuplicate(Request $request)
    {
        $validated = $request->validate([
            'new_application_id' => 'required|integer|exists:job_applications,id',
            'existing_application_id' => 'required|integer|exists:job_applications,id',
        ]);

        $newApp = JobApplication::findOrFail($validated['new_application_id']);
        $existingApp = JobApplication::findOrFail($validated['existing_application_id']);

        // Merge: update the new application to use the existing applicant's email
        $newApp->update([
            'email' => $existingApp->email,
            'admin_notes' => ($newApp->admin_notes ? $newApp->admin_notes . "\n" : '')
                . "[Merged] Originally submitted with email: {$newApp->email}. Merged with existing applicant {$existingApp->email} on " . now()->format('M d, Y h:i A') . ".",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Application merged successfully. New application now linked to {$existingApp->email}.",
        ]);
    }

    /**
     * Scenario #5: Ignore duplicate — admin acknowledges and dismisses the duplicate alert.
     */
    public function ignoreDuplicate(Request $request)
    {
        $validated = $request->validate([
            'new_application_id' => 'required|integer|exists:job_applications,id',
            'existing_application_id' => 'required|integer|exists:job_applications,id',
        ]);

        $newApp = JobApplication::findOrFail($validated['new_application_id']);

        $newApp->update([
            'admin_notes' => ($newApp->admin_notes ? $newApp->admin_notes . "\n" : '')
                . "[Duplicate Ignored] Potential duplicate with application #{$validated['existing_application_id']} was reviewed and ignored on " . now()->format('M d, Y h:i A') . ".",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Duplicate alert dismissed.',
        ]);
    }

    /**
     * Change an applicant user's role.
     */
    public function changeApplicantRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:applicant,external_client,employee',
        ]);

        $user = User::findOrFail($id);

        if (in_array($user->role, ['admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot change admin roles from here.'], 403);
        }

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        return response()->json([
            'success' => true,
            'message' => "Role changed from {$oldRole} to {$validated['role']}.",
        ]);
    }

    /**
     * Ban or unban an applicant user.
     */
    public function toggleBanApplicant($id)
    {
        $user = User::findOrFail($id);

        if (in_array($user->role, ['admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot ban admin users.'], 403);
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'User has been unbanned.' : 'User has been banned.',
            'is_active' => $newStatus,
        ]);
    }

    /**
     * Show the employee account setup form for a hired applicant.
     */
    public function setupEmployee($id)
    {
        $application = JobApplication::findOrFail($id);

        if ($application->status !== 'hired') {
            return redirect()->route('admin.recruitment.show', $id)
                ->with('error', 'Only hired applicants can be set up as employees.');
        }

        // Check if employee already exists
        $existingEmployee = User::where('email', $application->email)
            ->where('role', 'employee')
            ->whereHas('employee')
            ->first();

        if ($existingEmployee) {
            return redirect()->route('admin.recruitment.show', $id)
                ->with('error', 'An employee account already exists for this applicant.');
        }

        // Get applicant profile data
        $profile = json_decode($application->applicant_profile, true) ?? [];

        // Find the applicant user (may have been created via Google OAuth)
        $applicantUser = User::where('email', $application->email)
            ->where('role', 'applicant')
            ->first();

        return view('admin.recruitment.setup-employee', compact('application', 'profile', 'applicantUser'));
    }

    /**
     * Process the employee account creation for a hired applicant.
     */
    public function storeEmployee(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        if ($application->status !== 'hired') {
            return redirect()->route('admin.recruitment.show', $id)
                ->with('error', 'Only hired applicants can be set up as employees.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email_username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'skills' => ['nullable', 'array'],
            'years_of_experience' => ['required', 'integer', 'min:0'],
            'salary_per_hour' => ['required', 'numeric', 'min:0'],
            'has_driving_license' => ['nullable', 'boolean'],
            'efficiency' => ['required', 'numeric', 'min:0.01', 'max:9.99'],
        ]);

        $finnoyEmail = trim($validated['email_username']) . '@finnoys.com';

        // Check if email already in use (by a different user)
        $existingEmail = User::where('email', $finnoyEmail)->first();
        if ($existingEmail) {
            return redirect()->back()->withInput()
                ->withErrors(['email_username' => 'The email ' . $finnoyEmail . ' is already taken.']);
        }

        // Find the applicant user (may exist from Google sign-up)
        $applicantUser = User::where('email', $application->email)
            ->where('role', 'applicant')
            ->first();

        if ($applicantUser) {
            // Convert existing applicant user to employee
            // Store original gmail as alternative_email for Google login, set Fin-noys email as primary
            $applicantUser->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'alternative_email' => $applicantUser->email, // preserve Gmail for Google login
                'email' => $finnoyEmail,
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]);
            $user = $applicantUser;
        } else {
            // Create new user account (applicant had no user account)
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $finnoyEmail,
                'alternative_email' => $application->email, // store their personal gmail
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]);
        }

        // Create the employee profile record
        Employee::create([
            'user_id' => $user->id,
            'skills' => json_encode($validated['skills'] ?? []),
            'is_active' => true,
            'is_day_off' => false,
            'is_busy' => false,
            'efficiency' => $validated['efficiency'],
            'has_driving_license' => $validated['has_driving_license'] ?? false,
            'years_of_experience' => $validated['years_of_experience'],
            'salary_per_hour' => $validated['salary_per_hour'],
            'months_employed' => 0,
        ]);

        // Archive: store conversion note in the application
        $application->update([
            'admin_notes' => ($application->admin_notes ? $application->admin_notes . "\n" : '')
                . "[Employee Created] Converted to employee account (User #{$user->id}, {$finnoyEmail}) on " . now()->format('M d, Y h:i A') . " by " . (auth()->user()->name ?? 'Admin') . ".",
        ]);

        return redirect()->route('admin.accounts.show', $user->id)
            ->with('success', 'Employee account created successfully for ' . $user->name . '. Their applicant history has been archived.');
    }
}
