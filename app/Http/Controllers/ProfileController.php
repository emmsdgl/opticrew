<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Task;
use App\Models\QuotationSetting;
use App\Services\CompanySettingService;

class ProfileController extends Controller
{
    /**
     * Show the profile page with task statistics
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $role = $user->role;

        // These roles use the profile modal in the layout — redirect to their dashboards
        if ($role === 'client' || $role === 'external_client') {
            return Redirect::route('client.dashboard');
        }
        if ($role === 'company') {
            return Redirect::route('manager.dashboard');
        }
        if ($role === 'employee') {
            return Redirect::route('employee.dashboard');
        }

        // Initialize task stats
        $totalTasksCompleted = 0;
        $incompleteTasks = 0;
        $pendingTasks = 0;

        // Only fetch task data for employees
        if ($role === 'employee' && $user->employee) {
            $employee = $user->employee;

            // Build base query for tasks assigned to this employee
            $tasksQuery = Task::join('optimization_teams', 'tasks.assigned_team_id', '=', 'optimization_teams.id')
                ->join('optimization_team_members', 'optimization_teams.id', '=', 'optimization_team_members.optimization_team_id')
                ->where('optimization_team_members.employee_id', $employee->id);

            // Get task counts by status
            $totalTasksCompleted = (clone $tasksQuery)->where('tasks.status', 'Completed')->count();
            $incompleteTasks = (clone $tasksQuery)->whereIn('tasks.status', ['In Progress', 'On Hold'])->count();
            $pendingTasks = (clone $tasksQuery)->whereIn('tasks.status', ['Pending', 'Scheduled'])->count();
        }

        // Create cards data array
        $cards = [
            [
                'label' => 'Total Tasks Completed',
                'amount' => (string)$totalTasksCompleted,
                'description' => 'Boost your productivity today',
                'icon' => '<i class="fas fa-check-circle"></i>',
                'iconColor' => '#10b981',
                'labelColor' => '#059669',
                'percentage' => '',
                'percentageColor' => '#10b981',
                'bgColor' => '#fef3c7',
            ],
            [
                'label' => 'Incomplete Tasks',
                'amount' => (string)$incompleteTasks,
                'description' => 'Check out your list',
                'icon' => '<i class="fas fa-times-circle"></i>',
                'iconColor' => '#ef4444',
                'labelColor' => '#dc2626',
                'percentage' => '',
                'percentageColor' => '#ef4444',
            ],
            [
                'label' => 'Pending Tasks',
                'amount' => (string)$pendingTasks,
                'description' => 'Your tasks await',
                'icon' => '<i class="fas fa-hourglass-half"></i>',
                'iconColor' => '#f59e0b',
                'labelColor' => '#d97706',
                'percentage' => '',
                'percentageColor' => '#f59e0b',
            ],
        ];

        // Return appropriate view based on role
        if ($role === 'admin') {
            return view('admin.profile', compact('cards'));
        } elseif ($role === 'company') {
            return view('manager.profile', compact('cards'));
        } else {
            return view('employee.profile', compact('cards'));
        }
    }

    /**
     * Show the edit profile page based on user role
     */
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $role = $user->role;

        // These roles use the profile modal — redirect to dashboard
        if ($role === 'company') {
            return Redirect::route('manager.dashboard');
        }
        if ($role === 'employee') {
            return Redirect::route('employee.dashboard');
        }

        if ($role === 'admin') {
            return view('admin.profile-edit', compact('user'));
        } else {
            return view('client.profile-edit', compact('user'));
        }
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // JSON request from profile modal (employee/client)
        if ($request->expectsJson()) {
            $hasPassword = !empty($user->password);

            $rules = [
                'phone'        => ['nullable', 'string', 'max:50'],
                'username'     => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
                'location'     => ['nullable', 'string', 'max:255'],
                'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ];

            if ($request->filled('new_password') && $hasPassword) {
                $rules['current_password'] = ['required', 'string'];
            }

            $request->validate($rules);

            // Handle password change
            $passwordChanged = false;
            if ($request->filled('new_password')) {
                if ($hasPassword) {
                    if (!Hash::check($request->current_password, $user->password)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Current password is incorrect.',
                        ], 422);
                    }
                }

                $user->password = Hash::make($request->new_password);
                $passwordChanged = true;
            }

            $user->fill($request->only(['phone', 'username', 'location']));
            $user->save();

            if ($passwordChanged) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => true,
                    'password_changed' => true,
                    'message' => 'Password changed successfully. Please log in again.',
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        // Standard form request (admin profile page)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $changedFields = $user->getDirty();
        $user->save();

        UserActivityLog::log(
            $user->id,
            UserActivityLog::TYPE_PROFILE_UPDATED,
            'Profile information updated',
            ['changed_fields' => array_keys($changedFields)],
            $request->ip()
        );

        // Redirect back to profile page based on role
        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.profile')->with('success', 'Profile updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.profile')->with('success', 'Profile updated successfully!');
        } else {
            return Redirect::route('client.profile')->with('success', 'Profile updated successfully!');
        }
    }

    /**
     * Upload or update profile picture
     */
    public function uploadPicture(Request $request)
    {
        $request->validate([
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old profile picture if exists (skip external/Google URLs)
        if ($user->profile_picture && !str_starts_with($user->profile_picture, 'http')) {
            $oldPath = public_path($user->profile_picture);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        } elseif ($user->profile_picture && str_starts_with($user->profile_picture, url('uploads/profile_pictures'))) {
            $relativePath = str_replace(url('/'), '', $user->profile_picture);
            $oldPath = public_path(ltrim($relativePath, '/'));
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = public_path('uploads/profile_pictures');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $file = $request->file('profile_picture');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Move file to public/uploads/profile_pictures
        $file->move($uploadDir, $filename);

        // Store full URL so mobile app can display it directly
        $path = 'uploads/profile_pictures/' . $filename;
        $fullUrl = url($path);

        // Update user record
        $user->profile_picture = $fullUrl;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Profile picture updated successfully!', 'path' => $fullUrl]);
        }

        // Redirect back to profile page based on role
        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.profile')->with('success', 'Profile picture updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.profile')->with('success', 'Profile picture updated successfully!');
        } elseif ($role === 'company') {
            return Redirect::route('manager.profile')->with('success', 'Profile picture updated successfully!');
        } else {
            return Redirect::route('client.profile')->with('success', 'Profile picture updated successfully!');
        }
    }

    /**
     * Upload or update cover photo
     */
    public function uploadCoverPhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'cover_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $user = $request->user();

        // Delete old cover photo if exists
        if ($user->cover_photo) {
            $oldPath = public_path($user->cover_photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = public_path('uploads/cover_photos');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $file = $request->file('cover_photo');
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Move file to public/uploads/cover_photos
        $file->move($uploadDir, $filename);

        // Store relative path in database
        $path = 'uploads/cover_photos/' . $filename;

        // Update user record
        $user->cover_photo = $path;
        $user->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Cover photo updated successfully!', 'path' => asset($path)]);
        }

        $role = $user->role;
        if ($role === 'admin') {
            return Redirect::route('admin.profile')->with('success', 'Cover photo updated successfully!');
        } elseif ($role === 'employee') {
            return Redirect::route('employee.profile')->with('success', 'Cover photo updated successfully!');
        } elseif ($role === 'company') {
            return Redirect::route('manager.profile')->with('success', 'Cover photo updated successfully!');
        } else {
            return Redirect::route('client.profile')->with('success', 'Cover photo updated successfully!');
        }
    }

    /**
     * Show settings page based on user role
     */
    public function settings(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === 'admin') {
            $quotationSettings = [
                'auto_send_enabled' => QuotationSetting::getValue('auto_send_enabled', '1') === '1',
                'pdf_deep_cleaning' => QuotationSetting::getPdfPath('deep_cleaning'),
                'pdf_final_cleaning' => QuotationSetting::getPdfPath('final_cleaning'),
                'pdf_daily_cleaning' => QuotationSetting::getPdfPath('daily_cleaning'),
                'pdf_snowout_cleaning' => QuotationSetting::getPdfPath('snowout_cleaning'),
                'pdf_general_cleaning' => QuotationSetting::getPdfPath('general_cleaning'),
                'pdf_hotel_cleaning' => QuotationSetting::getPdfPath('hotel_cleaning'),
            ];
            $companySettings = [
                'minimum_booking_notice_days' => CompanySettingService::get('minimum_booking_notice_days', 3),
                'minimum_leave_notice_days' => CompanySettingService::get('minimum_leave_notice_days', 4),
                'task_approval_grace_period_minutes' => CompanySettingService::get('task_approval_grace_period_minutes', 30),
                'reassignment_grace_period_minutes' => CompanySettingService::get('reassignment_grace_period_minutes', 30),
                'unstaffed_escalation_timeout_minutes' => CompanySettingService::get('unstaffed_escalation_timeout_minutes', 60),
                'overtime_threshold_hours' => CompanySettingService::get('overtime_threshold_hours', 8),
                'geofence_radius' => CompanySettingService::get('geofence_radius', 110),
            ];
            $salarySettings = [
                'salary_full_time' => CompanySettingService::get('salary_full_time', 2500),
                'salary_part_time' => CompanySettingService::get('salary_part_time', 1200),
                'salary_remote' => CompanySettingService::get('salary_remote', 2000),
            ];
            return view('admin.settings', compact('user', 'quotationSettings', 'companySettings', 'salarySettings'));
        } elseif ($role === 'company') {
            return view('manager.settings', compact('user'));
        } elseif ($role === 'employee') {
            return view('employee.settings', compact('user'));
        } else {
            return view('client.settings', compact('user'));
        }
    }

    /**
     * Update quotation automation settings
     */
    public function updateQuotationSettings(Request $request)
    {
        QuotationSetting::setValue('auto_send_enabled', $request->boolean('auto_send_enabled') ? '1' : '0');

        $services = ['deep_cleaning', 'final_cleaning', 'daily_cleaning', 'snowout_cleaning', 'general_cleaning', 'hotel_cleaning'];

        foreach ($services as $service) {
            if ($request->hasFile("pdf_{$service}")) {
                $file = $request->file("pdf_{$service}");
                $path = $file->storeAs('quotation-pdfs', $service . '_' . time() . '.pdf', 'public');

                // Delete old file
                $oldPath = QuotationSetting::getPdfPath($service);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                QuotationSetting::setValue('pdf_' . $service, $path);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Quotation settings updated successfully!']);
        }

        return Redirect::route('admin.settings')->with('success', 'Quotation settings updated successfully!');
    }

    /**
     * Update workforce configuration (company_settings)
     */
    public function updateCompanySettings(Request $request)
    {
        $request->validate([
            'minimum_booking_notice_days' => 'required|integer|min:1|max:30',
            'minimum_leave_notice_days' => 'required|integer|min:1|max:30',
            'task_approval_grace_period_minutes' => 'required|integer|min:5|max:240',
            'reassignment_grace_period_minutes' => 'required|integer|min:5|max:240',
            'unstaffed_escalation_timeout_minutes' => 'required|integer|min:10|max:480',
            'overtime_threshold_hours' => 'required|integer|min:1|max:24',
            'geofence_radius' => 'required|integer|min:10|max:1000',
        ]);

        $settings = [
            'minimum_booking_notice_days' => ['type' => 'integer', 'desc' => 'Scenario #1: Minimum days notice required for booking'],
            'minimum_leave_notice_days' => ['type' => 'integer', 'desc' => 'Scenario #13: Minimum days notice for standard leave requests'],
            'task_approval_grace_period_minutes' => ['type' => 'integer', 'desc' => 'Scenario #19: Grace period (minutes) for employee to approve/start assigned task'],
            'reassignment_grace_period_minutes' => ['type' => 'integer', 'desc' => 'Scenario #18: Grace period (minutes) for task reassignment after leave approval'],
            'unstaffed_escalation_timeout_minutes' => ['type' => 'integer', 'desc' => 'Scenario #15: Minutes before CRITICAL_ESCALATION for unaccepted tasks'],
            'overtime_threshold_hours' => ['type' => 'integer', 'desc' => 'Scenario #16: Hours after which overtime pay is computed'],
            'geofence_radius' => ['type' => 'integer', 'desc' => 'Geofence radius in meters for clock-in verification'],
        ];

        foreach ($settings as $key => $meta) {
            CompanySettingService::set($key, $request->input($key), $meta['type'], $meta['desc']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Workforce configuration updated successfully!']);
        }

        return Redirect::route('admin.settings')->with('success', 'Workforce configuration updated successfully!');
    }

    /**
     * Update job posting salary configuration
     */
    public function updateSalarySettings(Request $request)
    {
        $request->validate([
            'salary_full_time' => 'required|numeric|min:0',
            'salary_part_time' => 'required|numeric|min:0',
            'salary_remote' => 'required|numeric|min:0',
        ]);

        $settings = [
            'salary_full_time' => ['type' => 'decimal', 'desc' => 'Default base salary for full-time job postings (EUR)'],
            'salary_part_time' => ['type' => 'decimal', 'desc' => 'Default base salary for part-time job postings (EUR)'],
            'salary_remote' => ['type' => 'decimal', 'desc' => 'Default base salary for remote job postings (EUR)'],
        ];

        foreach ($settings as $key => $meta) {
            CompanySettingService::set($key, $request->input($key), $meta['type'], $meta['desc']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Salary configuration updated successfully!']);
        }

        return Redirect::route('admin.settings')->with('success', 'Salary configuration updated successfully!');
    }

    /**
     * Show help center page based on user role
     */
    public function helpcenter(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;

        if ($role === 'admin') {
            return view('admin.help-center', compact('user'));
        } elseif ($role === 'company') {
            return view('manager.help-center', compact('user'));
        } elseif ($role === 'employee') {
            return view('employee.help-center', compact('user'));
        } else {
            return view('client.help-center', compact('user'));
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first(),
                ], 422);
            }
            throw $e;
        }

        $user = $request->user();
        $user->password = bcrypt($request->password);
        $user->save();

        // Log out the user after password change
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully! You will be redirected to log in again.',
                'redirect' => url('/'),
            ]);
        }

        return Redirect::to('/')->with('success', 'Password updated successfully! Please log in again.');
    }

    /**
     * Set password for Google-auth users who don't have one yet
     */
    public function setPassword(Request $request)
    {
        $user = $request->user();

        // Only allow if user signed up via Google (has google_id and no password)
        if (!$user->google_id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'This action is not allowed for your account.'], 403);
            }
            return back()->withErrors(['password' => 'This action is not allowed for your account.']);
        }

        try {
            $request->validate([
                'password' => ['required', 'confirmed', 'min:8'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first(),
                ], 422);
            }
            throw $e;
        }

        $user->password = bcrypt($request->password);
        $user->save();

        // Log out the user after setting password
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password set successfully! You can now log in with your email and password.',
                'redirect' => url('/'),
            ]);
        }

        return Redirect::to('/')->with('success', 'Password set successfully! Please log in again.');
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        // Delete profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
