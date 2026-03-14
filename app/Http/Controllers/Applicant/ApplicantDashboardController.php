<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\SavedJob;
use App\Services\Notification\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicantDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $jobPostings = JobPosting::active()->orderBy('created_at', 'desc')->get();

        $myApplications = JobApplication::where('email', $user->email)
            ->where('status', '!=', 'withdrawn')
            ->orderBy('created_at', 'desc')
            ->get();

        $withdrawnCount = JobApplication::where('email', $user->email)
            ->where('status', 'withdrawn')
            ->count();

        $savedJobIds = SavedJob::where('user_id', $user->id)->pluck('job_posting_id')->toArray();

        // Check if redirected from landing page recruitment (Google OAuth flow)
        $pendingApply = session()->pull('recruitment_data');

        return view('applicant.dashboard', compact('user', 'jobPostings', 'myApplications', 'savedJobIds', 'pendingApply', 'withdrawnCount'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'alternative_email' => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:50',
            'location'          => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update($request->only(['alternative_email', 'phone', 'location']));

        return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
    }

    public function withdrawn()
    {
        $user = Auth::user();

        $withdrawnApplications = JobApplication::where('email', $user->email)
            ->where('status', 'withdrawn')
            ->orderBy('updated_at', 'desc')
            ->get();

        $jobPostings = JobPosting::orderBy('created_at', 'desc')->get();

        return view('applicant.withdrawn', compact('user', 'withdrawnApplications', 'jobPostings'));
    }

    public function saved()
    {
        $user = Auth::user();

        $savedJobs = SavedJob::where('user_id', $user->id)
            ->with('jobPosting')
            ->orderBy('created_at', 'desc')
            ->get();

        $jobPostings = $savedJobs->map->jobPosting->filter();

        $myApplications = JobApplication::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        $savedJobIds = $savedJobs->pluck('job_posting_id')->toArray();

        return view('applicant.saved', compact('user', 'jobPostings', 'myApplications', 'savedJobIds'));
    }

    public function toggleSaveJob($id)
    {
        $user = Auth::user();
        $existing = SavedJob::where('user_id', $user->id)->where('job_posting_id', $id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        SavedJob::create(['user_id' => $user->id, 'job_posting_id' => $id]);
        return response()->json(['saved' => true]);
    }

    /**
     * Extract applicant fields from an uploaded resume.
     *
     * All file types → scripts/ocr_extract.py (uses ocr-env Python + PaddleOCR)
     * PDFs also try PHP-native FlateDecode extraction as fallback.
     */
    public function extractResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:docx|max:10240',
        ]);

        $file     = $request->file('resume');
        $ext      = strtolower($file->getClientOriginalExtension());
        $tempPath = $file->store('temp-resumes');
        $fullPath = storage_path('app/' . str_replace('/', DIRECTORY_SEPARATOR, $tempPath));

        // 1. For DOCX: extract text natively from XML (cleanest output)
        $text = '';
        if ($ext === 'docx') {
            $text = $this->extractDocxText($fullPath);
        }

        // 2. Fall back to Python OCR script if native extraction returned nothing
        if (!trim($text)) {
            $text = $this->callOcrExtract($fullPath);
        }

        // 3. For PDFs: fall back to PHP-native extraction if Python returned nothing
        if (!trim($text) && $ext === 'pdf') {
            $text = $this->extractPdfText($fullPath);
        }

        $fields = trim($text) ? $this->extractFieldsFromText($text) : $this->emptyFields();

        \Log::debug('Resume extract', [
            'ext'    => $ext,
            'chars'  => strlen($text),
            'fields' => $fields,
        ]);

        Storage::delete($tempPath);

        return response()->json(['success' => true, 'fields' => $fields]);
    }

    /**
     * Save the job application with the applicant's full profile data.
     */
    public function submitApplication(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'job_type'  => 'nullable|string|max:50',
            'email'     => 'required|email|max:255',
            'resume'    => 'required|file|mimes:docx|max:10240',
        ]);

        $alreadyApplied = JobApplication::where('email', $request->email)
            ->where('job_title', $request->job_title)
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this position.',
            ]);
        }

        $file = $request->file('resume');
        $path = $file->store('job-applications', 'public');

        $profile = [
            'first_name'        => $request->first_name        ?? '',
            'last_name'         => $request->last_name         ?? '',
            'middle_initial'    => $request->middle_initial    ?? '',
            'birthdate'         => $request->birthdate         ?? '',
            'phone'             => $request->phone             ?? '',
            'email'             => $request->email             ?? '',
            'alternative_email' => $request->alternative_email ?? '',
            'city'              => $request->city              ?? '',
            'country'           => $request->country           ?? '',
            'linkedin'          => $request->linkedin          ?? '',
            'skills'            => $request->skills            ?? '',
            'languages'         => $request->languages         ?? '',
        ];

        $user = Auth::user();
        if ($user) {
            $location = array_filter([$profile['city'], $profile['country']]);
            $updates = array_filter([
                'phone'    => $profile['phone']                        ?: null,
                'location' => $location ? implode(', ', $location) : null,
            ]);
            if ($updates) {
                $user->update($updates);
            }
        }

        $application = JobApplication::create([
            'job_title'            => $request->job_title,
            'job_type'             => $request->job_type ?? null,
            'email'                => $user ? $user->email : $request->email,
            'alternative_email'    => $request->alternative_email ?? null,
            'resume_path'          => $path,
            'resume_original_name' => $file->getClientOriginalName(),
            'applicant_profile'    => json_encode($profile),
            'status'               => 'pending',
            'status_history'       => [[
                'from' => null,
                'to' => 'pending',
                'timestamp' => now()->toIso8601String(),
                'by' => $user ? $user->name : 'Applicant',
            ]],
        ]);

        $notificationService = app(NotificationService::class);
        $notificationService->notifyAdminsNewJobApplication($application);

        // In-app notification for the applicant
        if ($user) {
            $notificationService->notifyApplicantApplicationSubmitted($application, $user);
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Application submitted successfully!',
            'application_id' => $application->id,
        ]);
    }

    /**
     * Withdraw (soft-delete) an application.
     */
    public function withdrawApplication($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to withdraw an application.',
            ], 401);
        }

        $application = JobApplication::where('email', $user->email)->findOrFail($id);

        // Only allow withdrawal up to interview_scheduled
        if (!in_array($application->status, ['pending', 'reviewed', 'interview_scheduled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This application can no longer be withdrawn.',
            ], 422);
        }

        $withdrawReason = $request->input('withdraw_reason', '');
        $withdrawDetails = $request->input('withdraw_details', '');

        // Update status history
        $history = $application->status_history ?? [];
        $history[] = [
            'from' => $application->status,
            'to' => 'withdrawn',
            'timestamp' => now()->toIso8601String(),
            'by' => $user->name ?? 'Applicant',
            'reason' => $withdrawReason,
            'details' => $withdrawDetails,
        ];

        $application->update([
            'status' => 'withdrawn',
            'status_history' => $history,
            'withdraw_reason' => $withdrawReason,
            'withdraw_details' => $withdrawDetails,
        ]);

        // Notifications
        $notificationService = app(NotificationService::class);
        $notificationService->notifyApplicantApplicationWithdrawn($application, $user);
        $notificationService->notifyAdminsApplicationWithdrawn($application);

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully.',
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // Private helpers
    // ═══════════════════════════════════════════════════════════════════════

    private function emptyFields(): array
    {
        return [
            'first_name'        => '',
            'last_name'         => '',
            'middle_initial'    => '',
            'birthdate'         => '',
            'phone'             => '',
            'email'             => '',
            'alternative_email' => '',
            'city'              => '',
            'country'           => '',
            'linkedin'          => '',
            'skills'            => '',
            'languages'         => '',
        ];
    }

    /**
     * Extract plain text from a DOCX file by reading its word/document.xml.
     */
    private function extractDocxText(string $filePath): string
    {
        if (!class_exists('ZipArchive')) return '';

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) return '';

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) return '';

        // Strip XML tags but preserve paragraph boundaries as newlines
        $xml = str_replace('</w:p>', "\n", $xml);
        $xml = str_replace('</w:r>', ' ', $xml);
        $text = strip_tags($xml);
        // Decode XML/HTML entities like &amp; → &, &lt; → <, etc.
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

        // Normalize whitespace
        $text = preg_replace('/[^\S\n]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Call scripts/ocr_extract.py using the local ocr-env virtual environment.
     * Returns plain text (stdout only; Python logs go to stderr and are discarded).
     */
    private function callOcrExtract(string $filePath): string
    {
        $python = base_path('ocr-env' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe');
        $script = base_path('scripts' . DIRECTORY_SEPARATOR . 'ocr_extract.py');

        if (!file_exists($python) || !file_exists($script)) {
            return '';
        }

        $cmd = escapeshellarg($python) . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($filePath);

        if (function_exists('proc_open')) {
            $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $proc = proc_open($cmd, $desc, $pipes);
            if (!is_resource($proc)) {
                return '';
            }
            fclose($pipes[0]);
            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($proc);
            return trim($out ?? '');
        }

        if (function_exists('shell_exec')) {
            // 2>NUL discards stderr on Windows so only real stdout is captured
            return trim(@shell_exec($cmd . ' 2>NUL') ?? '');
        }

        return '';
    }

    /**
     * PHP-native PDF text extraction (FlateDecode + raw stream fallback).
     * Used when the Python script is unavailable or returns empty.
     */
    private function extractPdfText(string $filePath): string
    {
        $raw = @file_get_contents($filePath);
        if (!$raw) {
            return '';
        }

        $text = '';

        // ── Decompress FlateDecode streams (the majority of modern PDFs) ──
        preg_match_all('/<<(.*?)>>\s*stream\r?\n(.*?)\r?\nendstream/s', $raw, $objs, PREG_SET_ORDER);

        foreach ($objs as $obj) {
            $dict   = $obj[1];
            $stream = $obj[2];

            if (preg_match('/\/Filter\s*(?:\/FlateDecode|\[.*?\/FlateDecode.*?\])/s', $dict)) {
                $decoded = @gzuncompress($stream);
                if ($decoded === false) {
                    $decoded = @gzinflate($stream);
                }
                if ($decoded === false) {
                    continue;
                }
            } elseif (preg_match('/\/Filter/', $dict)) {
                continue; // Other filters (ASCII85, LZW…) — skip
            } else {
                $decoded = $stream; // Uncompressed stream
            }

            $text .= $this->textFromPdfStream($decoded) . "\n";
        }

        // ── Fallback: scan raw bytes for uncompressed Tj/TJ operators ─────
        if (!trim($text)) {
            preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)\s*Tj/s', $raw, $tj);
            foreach ($tj[1] as $chunk) {
                $text .= $this->decodePdfString($chunk) . ' ';
            }
        }

        return $text;
    }

    private function textFromPdfStream(string $stream): string
    {
        $text = '';
        preg_match_all('/BT\s+(.*?)\s*ET/s', $stream, $blocks);

        foreach ($blocks[1] as $block) {
            // (string)Tj
            preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)\s*Tj/s', $block, $tj);
            foreach ($tj[1] as $s) {
                $text .= $this->decodePdfString($s) . ' ';
            }
            // [(string)…]TJ
            preg_match_all('/\[(.*?)\]\s*TJ/s', $block, $arr);
            foreach ($arr[1] as $a) {
                preg_match_all('/\(((?:[^()\\\\]|\\\\.)*)\)/', $a, $strs);
                foreach ($strs[1] as $s) {
                    $text .= $this->decodePdfString($s);
                }
                $text .= ' ';
            }
            // Newline hints
            if (preg_match('/\b(?:T\*|TD?|Tm)\b/', $block)) {
                $text .= "\n";
            }
        }
        return $text;
    }

    private function decodePdfString(string $s): string
    {
        $s = stripslashes($s);
        // Octal escapes  \ddd
        $s = preg_replace_callback('/\\\\(\d{3})/', fn($m) => chr(octdec($m[1])), $s);
        return $s;
    }

    /**
     * Normalize a date string to YYYY-MM-DD for use with <input type="date">.
     */
    private function normalizeDateString(string $raw): string
    {
        $raw = trim($raw);
        if (!$raw) return '';

        // M/D/YYYY or D/M/YYYY (slash or dash)
        if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/', $raw, $m)) {
            return sprintf('%s-%02d-%02d', $m[3], (int)$m[1], (int)$m[2]);
        }
        // YYYY/M/D or YYYY-M-D
        if (preg_match('/^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/', $raw, $m)) {
            return sprintf('%s-%02d-%02d', $m[1], (int)$m[2], (int)$m[3]);
        }
        // "Jan 15, 1990" / "January 15 1990"
        $months = ['jan'=>1,'feb'=>2,'mar'=>3,'apr'=>4,'may'=>5,'jun'=>6,
                   'jul'=>7,'aug'=>8,'sep'=>9,'oct'=>10,'nov'=>11,'dec'=>12];
        if (preg_match('/^([A-Za-z]{3})[a-z]*\.?\s+(\d{1,2}),?\s+(\d{4})$/i', $raw, $m)) {
            $mo = $months[strtolower(substr($m[1], 0, 3))] ?? 0;
            if ($mo) return sprintf('%s-%02d-%02d', $m[3], $mo, (int)$m[2]);
        }
        // "15 Jan 1990" / "15 January 1990"
        if (preg_match('/^(\d{1,2})\s+([A-Za-z]{3})[a-z]*\.?\s+(\d{4})$/i', $raw, $m)) {
            $mo = $months[strtolower(substr($m[2], 0, 3))] ?? 0;
            if ($mo) return sprintf('%s-%02d-%02d', $m[3], $mo, (int)$m[1]);
        }
        // Last resort: strtotime
        $ts = strtotime($raw);
        if ($ts !== false && $ts > 0) return date('Y-m-d', $ts);

        return '';
    }

    /**
     * Extract structured fields from raw resume text using regex.
     */
    private function extractFieldsFromText(string $text): array
    {
        $fields = $this->emptyFields();

        if (!trim($text)) {
            return $fields;
        }

        // Normalise whitespace
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);

        // ── Email ────────────────────────────────────────────────────────
        preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $emails);
        if ($emails[0]) {
            $fields['email'] = $emails[0][0];
        }

        // ── Phone ────────────────────────────────────────────────────────
        if (preg_match(
            '/(?:\+?\d{1,3}[\s\-.]?)?\(?\d{3,4}\)?[\s\-.]?\d{3,4}[\s\-.]?\d{4}/',
            $text, $ph
        )) {
            $fields['phone'] = trim($ph[0]);
        }

        // ── LinkedIn ─────────────────────────────────────────────────────
        if (preg_match('/linkedin\.com\/in\/[\w\-]+/i', $text, $li)) {
            $fields['linkedin'] = 'https://www.' . $li[0];
        }

        // ── Birthdate ────────────────────────────────────────────────────
        $dobPatterns = [
            '/\b\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}\b/',
            '/\b\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}\b/',
            '/\b(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[a-z]*\.?\s+\d{1,2},?\s+\d{4}\b/i',
            '/\bborn[:\s]+(.+?\d{4})/i',
        ];
        foreach ($dobPatterns as $pat) {
            if (preg_match($pat, $text, $dob)) {
                $raw = isset($dob[1]) ? trim($dob[1]) : $dob[0];
                $fields['birthdate'] = $this->normalizeDateString($raw) ?: $raw;
                break;
            }
        }

        // ── Name (first non-email, non-phone line of ≤5 words) ───────────
        $lines = array_values(array_filter(
            explode("\n", $text),
            fn($l) => strlen(trim($l)) > 0
        ));
        foreach (array_slice($lines, 0, 8) as $line) {
            $line  = trim($line);
            $words = preg_split('/\s+/', $line);
            if (
                count($words) >= 2 && count($words) <= 5 &&
                !preg_match('/[@\d\(\)\+#\/:\.]{2,}/', $line) &&
                !preg_match('/resume|curriculum|cv\b|objective|summary/i', $line)
            ) {
                $fields['first_name'] = $words[0];
                $fields['last_name']  = end($words);
                // Extract middle initial from middle words (e.g. "John A. Doe", "John A Doe", "John Andrew Doe")
                if (count($words) >= 3) {
                    $middle = $words[1];
                    // Strip trailing period (e.g. "A." → "A")
                    $middle = rtrim($middle, '.');
                    // Take only the first letter
                    $fields['middle_initial'] = mb_strtoupper(mb_substr($middle, 0, 1));
                }
                break;
            }
        }

        // ── Languages (extract BEFORE skills so we can exclude it from skills) ─
        // Use a dedicated regex: match "Languages" / "Languages Spoken" as a
        // line-start header, capture until the next section header line.
        $langsSectionHeaders = 'skills?|education|experience|work|employment|references?|certif|awards?|projects?|interests?|summary|objective|portfolio|training|courses?|volunteer|achievements?|personal|contact|profile';
        if (preg_match(
            '/(?:^|\n)\s*(?:languages?\s+spoken|languages?|dialects?)[\s:•\-–—\n]+(.+?)(?=(?:^|\n)\s*(?:' . $langsSectionHeaders . ')\b|\n{2,}|\z)/is',
            $text,
            $m
        )) {
            $items = preg_split('/[,\n•◦■★\-–—|]+/', $m[1]);
            $clean = array_filter(
                array_map('trim', $items),
                fn($s) => strlen($s) > 1 && strlen($s) < 35 && !preg_match('/^\d+\.?$/', $s)
            );
            $fields['languages'] = implode(', ', array_slice(array_values($clean), 0, 8));
        }

        // ── Skills (via extractSection helper) ──────────────────────────
        $skillsRaw = $this->extractSection($text, [
            'technical skills', 'core competencies', 'key skills',
            'skills and competencies', 'professional skills',
            'hard skills', 'soft skills', 'competencies', 'expertise', 'skills',
        ]);
        if ($skillsRaw) {
            // Labels to strip — section headers that may leak into items
            $skipLabels = [
                'skills', 'skill', 'hard skills', 'soft skills', 'technical skills',
                'key skills', 'professional skills', 'core competencies',
                'competencies', 'expertise', 'skills and competencies',
                'language', 'languages', 'languages spoken', 'dialects',
            ];
            // Languages already extracted — exclude them from skills
            $langItems = $fields['languages']
                ? array_map('trim', explode(',', mb_strtolower($fields['languages'])))
                : [];

            $items = preg_split('/[,\n•◦■★\-–—|]+/', $skillsRaw);
            $clean = array_filter(
                array_map('trim', $items),
                function ($s) use ($skipLabels, $langItems) {
                    if (strlen($s) <= 2 || strlen($s) >= 60) return false;
                    if (preg_match('/^\d+\.?$/', $s)) return false;
                    $lower = mb_strtolower($s);
                    if (in_array($lower, $skipLabels, true)) return false;
                    if (in_array($lower, $langItems, true)) return false;
                    return true;
                }
            );
            $fields['skills'] = implode(', ', array_slice(array_values($clean), 0, 20));
        }

        // ── City & Country ──────────────────────────────────────────────
        // Strategy: Try explicit labels first, then fall back to scanning
        // address-like lines for known country names.
        $knownCountries = [
            'Philippines', 'United States', 'United Kingdom', 'Canada', 'Australia',
            'Singapore', 'Japan', 'South Korea', 'India', 'Germany', 'France',
            'United Arab Emirates', 'Saudi Arabia', 'Qatar', 'New Zealand',
            'Malaysia', 'Indonesia', 'Thailand', 'Vietnam', 'China', 'Taiwan',
            'Hong Kong', 'Italy', 'Spain', 'Netherlands', 'Brazil', 'Mexico',
            'Ireland', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Switzerland',
            'Belgium', 'Austria', 'Portugal', 'Poland', 'Czech Republic',
            'South Africa', 'Nigeria', 'Egypt', 'Kenya', 'Israel', 'Turkey',
            'Pakistan', 'Bangladesh', 'Sri Lanka', 'Nepal',
        ];

        // 1. Try explicit labels
        if (preg_match('/(?:city|municipality)[\s:]+([^\n,]+)/i', $text, $m)) {
            $fields['city'] = trim($m[1]);
        }
        if (preg_match('/(?:country|nationality)[\s:]+([^\n,]+)/i', $text, $m)) {
            $fields['country'] = trim($m[1]);
        }

        // 2. Fallback: scan for known country names in address lines or top lines
        if (!$fields['city'] || !$fields['country']) {
            // Collect candidate lines in priority order:
            // a) Explicitly labelled address/location lines (highest priority)
            // b) Top lines from the resume (lower priority, filtered)
            $priorityLines = [];
            $fallbackLines = [];

            if (preg_match('/(?:address|location|residing)[\s:]+([^\n]+)/i', $text, $m)) {
                $priorityLines[] = trim($m[1]);
            }

            $allLines = array_values(array_filter(explode("\n", $text), fn($l) => strlen(trim($l)) > 0));
            foreach (array_slice($allLines, 0, 15) as $l) {
                $trimmed = trim($l);
                // Skip lines that look like job/experience entries
                if ($this->looksLikeJobLine($trimmed)) continue;
                $fallbackLines[] = $trimmed;
            }

            // Search priority lines first, then fallback lines
            foreach (array_merge($priorityLines, $fallbackLines) as $line) {
                foreach ($knownCountries as $country) {
                    if (stripos($line, $country) === false) continue;

                    if (!$fields['country']) {
                        $fields['country'] = $country;
                    }
                    if (!$fields['city']) {
                        $extracted = $this->extractCityFromLine($line, $country);
                        // Validate: city should not contain dates or job-like text
                        if ($extracted && !$this->looksLikeJobLine($extracted)) {
                            $fields['city'] = $extracted;
                        }
                    }
                    if ($fields['city'] && $fields['country']) break 2;
                }
            }
        }

        return $fields;
    }

    /**
     * Extract the city name from a line that contains a known country.
     *
     * Tries comma-separated segments first (takes the segment immediately
     * before the country). Falls back to the first segment if the line has
     * commas, or strips the country from the line as a last resort.
     */
    private function extractCityFromLine(string $line, string $country): string
    {
        $parts = array_map('trim', explode(',', $line));

        // Find which segment contains the country
        $countryIdx = null;
        foreach ($parts as $i => $part) {
            if (stripos($part, $country) !== false) {
                $countryIdx = $i;
                break;
            }
        }

        if ($countryIdx !== null && count($parts) > 1) {
            // Take the segment immediately before the country
            // e.g. "123 Main St, Quezon City, Metro Manila, Philippines"
            //       → segment before country index is "Metro Manila"
            //       → but we want the city, so try the segment closest to country
            //         that looks like a city name (2+ words or ends in "City/Town")
            $candidates = [];
            for ($i = 0; $i < $countryIdx; $i++) {
                $seg = trim($parts[$i]);
                if (strlen($seg) > 2 && strlen($seg) < 50) {
                    $candidates[] = $seg;
                }
            }

            if ($candidates) {
                // Prefer a segment containing "City", "Town", "Municipality"
                foreach ($candidates as $c) {
                    if (preg_match('/\b(city|town|municipality|metro)\b/i', $c)) {
                        return $c;
                    }
                }
                // Otherwise take the segment right before the country
                return end($candidates);
            }
        }

        // No commas or single segment — strip the country name and clean up
        $city = trim(str_ireplace($country, '', $line));
        $city = trim($city, " ,.\t\n\r");
        return (strlen($city) > 2 && strlen($city) < 60) ? $city : '';
    }

    /**
     * Detect if a line looks like a job/experience entry rather than an address.
     * Job lines typically contain dates, pipes with dates, or role-like keywords.
     */
    private function looksLikeJobLine(string $line): bool
    {
        // Contains date patterns: "Jan 2023", "2023 - Present", "01/2023", "March 2024"
        if (preg_match('/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|june|july|august|september|october|november|december)\b[\s.,]*\d{2,4}/i', $line)) {
            return true;
        }
        if (preg_match('/\b\d{4}\s*[-–—]\s*(present|\d{4}|current)/i', $line)) {
            return true;
        }
        if (preg_match('/\b(0?[1-9]|1[0-2])\/\d{4}\b/', $line)) {
            return true;
        }
        // Contains job-related keywords alongside pipes or dashes (common in experience sections)
        if (preg_match('/\b(engineer|developer|manager|analyst|designer|intern|associate|consultant|specialist|coordinator|officer|director|supervisor|lead|senior|junior)\b/i', $line)
            && preg_match('/[|–—]/', $line)) {
            return true;
        }
        return false;
    }

    /**
     * Find keyword positions that look like section headers (at start of line).
     */
    private function findSectionHeaderPos(string $textLower, string $keyword, int $searchFrom = 0): ?int
    {
        $kwLower = mb_strtolower($keyword);
        $offset  = $searchFrom;

        while (($pos = mb_strpos($textLower, $kwLower, $offset)) !== false) {
            // Must be at start of text, or preceded by a newline (with optional whitespace)
            if ($pos === 0) return $pos;
            $before = mb_substr($textLower, 0, $pos);
            if (preg_match('/(?:^|\n)\s*$/u', $before)) {
                return $pos;
            }
            $offset = $pos + 1;
        }
        return null;
    }

    private function extractSection(string $text, array $keywords): string
    {
        // All known section headers that mark boundaries
        $sectionHeaders = [
            'education', 'experience', 'work experience', 'employment',
            'skills', 'technical skills', 'core competencies', 'key skills',
            'professional skills', 'hard skills', 'soft skills', 'competencies', 'expertise',
            'languages', 'languages spoken', 'dialects',
            'references', 'certifications', 'certificates', 'awards',
            'projects', 'interests', 'hobbies', 'summary', 'objective',
            'portfolio', 'training', 'courses', 'volunteer', 'achievements',
            'personal information', 'contact', 'profile',
        ];

        $textLower = mb_strtolower($text);

        // Find the earliest matching keyword that appears as a section header (start of line)
        $bestPos = PHP_INT_MAX;
        $bestLen = 0;

        foreach ($keywords as $kw) {
            $pos = $this->findSectionHeaderPos($textLower, $kw);
            if ($pos !== null && $pos < $bestPos) {
                $bestPos = $pos;
                $bestLen = mb_strlen($kw);
            }
        }

        if ($bestPos === PHP_INT_MAX) {
            return '';
        }

        // Move past the header + any trailing colons, dashes, spaces, newlines
        $contentStart = $bestPos + $bestLen;
        $afterHeader  = mb_substr($text, $contentStart);
        $afterHeader  = preg_replace('/^[\s:•\-–—\n]+/u', '', $afterHeader);
        $contentStart = mb_strlen($text) - mb_strlen($afterHeader);

        // Find the next section header after our content starts
        $nextSectionPos = mb_strlen($text);

        foreach ($sectionHeaders as $header) {
            // Skip headers that match our own keywords
            $isOwn = false;
            foreach ($keywords as $kw) {
                if (mb_strtolower($kw) === mb_strtolower($header)) {
                    $isOwn = true;
                    break;
                }
            }
            if ($isOwn) continue;

            $hPos = $this->findSectionHeaderPos($textLower, $header, $contentStart);
            if ($hPos !== null && $hPos < $nextSectionPos) {
                $nextSectionPos = $hPos;
            }
        }

        $section = mb_substr($text, $contentStart, $nextSectionPos - $contentStart);
        return trim($section);
    }
}
