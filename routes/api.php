<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskStatusController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Api\CompanySettingsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\TrainingVideoController;
use App\Http\Controllers\Api\EmployeeStatsController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminAppointmentController;
use App\Http\Controllers\Api\AdminFeedbackController;
use App\Http\Controllers\Api\MobileForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Authentication Routes (Mobile App)
|--------------------------------------------------------------------------
*/

// Login (No authentication required)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/google-login', [AuthController::class, 'googleLogin'])->name('api.google-login');

// Real-time login validation (for mobile app)
Route::post('/validate-login', function (Request $request) {
    $login = $request->input('login', '');
    $password = $request->input('password', '');
    $userId = $request->input('user_id');
    $result = ['login_exists' => false, 'password_valid' => false, 'login_checked' => false, 'password_checked' => false, 'user_id' => null];

    if (!$login && !$userId) {
        return response()->json($result);
    }

    // Fast path: password-only check with cached user_id
    if ($userId && $password) {
        $user = \App\Models\User::find($userId);
        if ($user) {
            $result['login_exists'] = true;
            $result['login_checked'] = true;
            $result['user_id'] = $user->id;
            $result['password_valid'] = \Illuminate\Support\Facades\Hash::check($password, $user->password ?? '');
            $result['password_checked'] = true;
        }
        return response()->json($result);
    }

    // Find user by email, alternative_email, username, or name
    $user = null;
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $user = \App\Models\User::where('email', $login)->orWhere('alternative_email', $login)->first();
    } else {
        $user = \App\Models\User::where('username', $login)->orWhere('name', $login)->first();
    }

    $result['login_exists'] = $user !== null;
    $result['login_checked'] = true;
    $result['user_id'] = $user ? $user->id : null;

    if ($user && $password) {
        $result['password_valid'] = \Illuminate\Support\Facades\Hash::check($password, $user->password ?? '');
        $result['password_checked'] = true;
    }

    return response()->json($result);
});

// Forgot Password (3FA - no authentication required)
Route::prefix('forgot-password')->middleware('throttle:10,1')->group(function () {
    Route::post('/request', [MobileForgotPasswordController::class, 'request']);
    Route::post('/verify-otp', [MobileForgotPasswordController::class, 'verifyOtp']);
    Route::post('/reset', [MobileForgotPasswordController::class, 'reset']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Get user profile
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('api.user.profile');

    // Update profile
    Route::put('/user/profile', [AuthController::class, 'updateProfile'])->name('api.user.profile.update');

    // Upload profile picture
    Route::post('/user/profile-picture', [AuthController::class, 'updateProfilePicture'])->name('api.user.profile.picture');

    // Update password
    Route::post('/user/password', [AuthController::class, 'updatePassword'])->name('api.user.password.update');

    // Deactivate account
    Route::post('/user/deactivate', [AuthController::class, 'deactivateAccount'])->name('api.user.deactivate');

    // Legacy route for compatibility
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

/*
|--------------------------------------------------------------------------
| Attendance Routes (Mobile App)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('attendance')->group(function () {
    // Clock in
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('api.attendance.clock-in');

    // Clock out
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('api.attendance.clock-out');

    // Get attendance history
    Route::get('/history', [AttendanceController::class, 'getHistory'])->name('api.attendance.history');

    // Get today's status
    Route::get('/status/today', [AttendanceController::class, 'getTodayStatus'])->name('api.attendance.status.today');
});

/*
|--------------------------------------------------------------------------
| Schedule Routes (Mobile App)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('schedule')->group(function () {
    // Get tasks for a specific date
    Route::get('/tasks', [TaskStatusController::class, 'getScheduleTasks'])->name('api.schedule.tasks');
});

/*
|--------------------------------------------------------------------------
| Employee Dashboard API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('employee')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Get tasks for employee's team
    Route::get('/tasks', [TaskStatusController::class, 'getEmployeeTasks'])
        ->name('api.employee.tasks');

    // Get employee statistics (Stats screen)
    Route::get('/stats', [EmployeeStatsController::class, 'index'])
        ->name('api.employee.stats');

    // Leave Request Management (Employee)
    Route::prefix('leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'getEmployeeRequests'])
            ->name('api.employee.leave-requests.index');
        Route::post('/', [LeaveRequestController::class, 'submitRequest'])
            ->name('api.employee.leave-requests.submit');
        Route::delete('/{requestId}', [LeaveRequestController::class, 'cancelRequest'])
            ->name('api.employee.leave-requests.cancel');
    });

    // Notifications (Employee)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('api.employee.notifications.index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
            ->name('api.employee.notifications.unread-count');
        Route::post('/{notificationId}/read', [NotificationController::class, 'markAsRead'])
            ->name('api.employee.notifications.mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('api.employee.notifications.mark-all-read');
        Route::delete('/{notificationId}', [NotificationController::class, 'destroy'])
            ->name('api.employee.notifications.delete');
        Route::post('/push-token', [NotificationController::class, 'registerPushToken'])
            ->name('api.employee.notifications.register-token');
        Route::delete('/push-token', [NotificationController::class, 'unregisterPushToken'])
            ->name('api.employee.notifications.unregister-token');
    });
});

/*
|--------------------------------------------------------------------------
| Training Videos Routes (Mobile App)
|--------------------------------------------------------------------------
*/

Route::prefix('training-videos')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Get all training videos grouped by category
    Route::get('/', [TrainingVideoController::class, 'index'])
        ->name('api.training-videos.index');

    // Get videos by category
    Route::get('/category/{category}', [TrainingVideoController::class, 'byCategory'])
        ->name('api.training-videos.by-category');

    // Mark video as watched
    Route::post('/{videoId}/watched', [TrainingVideoController::class, 'markAsWatched'])
        ->name('api.training-videos.mark-watched');

    // Unmark video as watched
    Route::delete('/{videoId}/watched', [TrainingVideoController::class, 'unmarkAsWatched'])
        ->name('api.training-videos.unmark-watched');

    // Get watched videos list
    Route::get('/watched', [TrainingVideoController::class, 'getWatchedVideos'])
        ->name('api.training-videos.watched');

    // Get completion stats
    Route::get('/stats', [TrainingVideoController::class, 'getStats'])
        ->name('api.training-videos.stats');
});

// Task Status Management (Employee App)
Route::prefix('tasks')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Get task details
    Route::get('/{taskId}', [TaskStatusController::class, 'getTaskDetails'])
        ->name('api.tasks.details');

    // Start task (In Progress)
    Route::post('/{taskId}/start', [TaskStatusController::class, 'startTask'])
        ->name('api.tasks.start');

    // Put task on hold (with reason)
    Route::post('/{taskId}/hold', [TaskStatusController::class, 'putTaskOnHold'])
        ->name('api.tasks.hold');

    // Complete task
    Route::post('/{taskId}/complete', [TaskStatusController::class, 'completeTask'])
        ->name('api.tasks.complete');

    // Reject task (employee declines an assigned task)
    Route::post('/{taskId}/reject', [TaskStatusController::class, 'rejectTask'])
        ->name('api.tasks.reject');

    // Upload task photo (before/after)
    Route::post('/{taskId}/photo', [TaskStatusController::class, 'uploadTaskPhoto'])
        ->name('api.tasks.photo');

    // Get task checklist with completion status
    Route::get('/{taskId}/checklist', [TaskStatusController::class, 'getTaskChecklist'])
        ->name('api.tasks.checklist');

    // Toggle checklist item completion
    Route::post('/{taskId}/checklist/{itemId}/toggle', [TaskStatusController::class, 'toggleChecklistItem'])
        ->name('api.tasks.checklist.toggle');

    // Submit employee feedback for a completed task
    Route::post('/{taskId}/feedback', [TaskStatusController::class, 'submitTaskFeedback'])
        ->name('api.tasks.feedback');
});

/*
|--------------------------------------------------------------------------
| Admin Dashboard API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Alert Management
    Route::prefix('alerts')->group(function () {
        // Get unacknowledged alerts (for real-time notifications)
        Route::get('/unacknowledged', [AlertController::class, 'getUnacknowledgedAlerts'])
            ->name('api.admin.alerts.unacknowledged');

        // Get all alerts (with optional filters)
        Route::get('/', [AlertController::class, 'getAllAlerts'])
            ->name('api.admin.alerts.all');

        // Acknowledge an alert
        Route::post('/{alertId}/acknowledge', [AlertController::class, 'acknowledgeAlert'])
            ->name('api.admin.alerts.acknowledge');
    });

    // Leave Request Management (Admin)
    Route::prefix('leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'getAllRequests'])
            ->name('api.admin.leave-requests.index');
        Route::post('/{requestId}/approve', [LeaveRequestController::class, 'approveRequest'])
            ->name('api.admin.leave-requests.approve');
        Route::post('/{requestId}/reject', [LeaveRequestController::class, 'rejectRequest'])
            ->name('api.admin.leave-requests.reject');
    });

    // Attendance Management (Admin)
    Route::prefix('attendance')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'getAllAttendance'])
            ->name('api.admin.attendance.index');
        Route::get('/{attendanceId}', [LeaveRequestController::class, 'getAttendanceDetails'])
            ->name('api.admin.attendance.details');
    });

    // Employee Management (Admin)
    Route::get('/employees', [LeaveRequestController::class, 'getAllEmployees'])
        ->name('api.admin.employees.index');

    // Feedback Management (Admin)
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])
        ->name('api.admin.feedback.index');

    // Appointment Management (Admin)
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])
        ->name('api.admin.appointments.index');
    Route::get('/appointments/{id}', [AdminAppointmentController::class, 'show'])
        ->name('api.admin.appointments.show');
    Route::post('/appointments/{id}/approve', [AdminAppointmentController::class, 'approve'])
        ->name('api.admin.appointments.approve');
    Route::post('/appointments/{id}/reject', [AdminAppointmentController::class, 'reject'])
        ->name('api.admin.appointments.reject');
});

/*
|--------------------------------------------------------------------------
| Company/Manager Dashboard API Routes (Mobile App)
|--------------------------------------------------------------------------
*/

Route::prefix('company')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Workforce settings (advance booking days, etc.)
    Route::get('/workforce-settings', function () {
        $settings = \Illuminate\Support\Facades\DB::table('company_settings')
            ->whereIn('key', ['advance_booking_days', 'max_hours_per_day', 'min_team_size'])
            ->pluck('value', 'key');

        return response()->json([
            'advance_booking_days' => (int) ($settings['advance_booking_days'] ?? 0),
            'max_hours_per_day' => (int) ($settings['max_hours_per_day'] ?? 12),
            'min_team_size' => (int) ($settings['min_team_size'] ?? 2),
        ]);
    })->name('api.company.workforce-settings');

    // Dashboard overview
    Route::get('/dashboard', [CompanyController::class, 'dashboard'])
        ->name('api.company.dashboard');

    // Profile stats (for Profile screen)
    Route::get('/profile/stats', [CompanyController::class, 'getProfileStats'])
        ->name('api.company.profile.stats');

    // Locations management
    Route::get('/locations', [CompanyController::class, 'getLocations'])
        ->name('api.company.locations');

    // Tasks management
    Route::get('/tasks', [CompanyController::class, 'getTasks'])
        ->name('api.company.tasks');

    Route::get('/tasks/{taskId}', [CompanyController::class, 'getTaskDetails'])
        ->name('api.company.tasks.details');

    Route::post('/tasks', [CompanyController::class, 'createTask'])
        ->name('api.company.tasks.create');

    Route::put('/tasks/{taskId}', [CompanyController::class, 'updateTask'])
        ->name('api.company.tasks.update');

    Route::post('/tasks/{taskId}/cancel', [CompanyController::class, 'cancelTask'])
        ->name('api.company.tasks.cancel');

    // Employees assigned to company tasks
    Route::get('/employees', [CompanyController::class, 'getEmployees'])
        ->name('api.company.employees');

    // Get all available employees for task assignment
    Route::get('/employees/available', [CompanyController::class, 'getAvailableEmployees'])
        ->name('api.company.employees.available');

    // Reports and analytics
    Route::get('/reports', [CompanyController::class, 'getReports'])
        ->name('api.company.reports');

    // Activities/Notifications (real-time tracking)
    Route::get('/activities', [CompanyController::class, 'getActivities'])
        ->name('api.company.activities');

    // Billing report (client billing estimation)
    Route::get('/billing', [CompanyController::class, 'getBillingReport'])
        ->name('api.company.billing');

    // Schedule optimization (Hybrid Rule-Based + Genetic Algorithm)
    Route::post('/schedule/optimize', [CompanyController::class, 'optimizeSchedule'])
        ->name('api.company.schedule.optimize');

    // Checklist management
    Route::get('/checklist', [CompanyController::class, 'getChecklist'])
        ->name('api.company.checklist');

    Route::post('/checklist', [CompanyController::class, 'createChecklist'])
        ->name('api.company.checklist.create');

    Route::put('/checklist/{checklistId}', [CompanyController::class, 'updateChecklist'])
        ->name('api.company.checklist.update');

    Route::post('/checklist/{checklistId}/categories', [CompanyController::class, 'addCategory'])
        ->name('api.company.checklist.categories.add');

    Route::put('/checklist/categories/{categoryId}', [CompanyController::class, 'updateCategory'])
        ->name('api.company.checklist.categories.update');

    Route::delete('/checklist/categories/{categoryId}', [CompanyController::class, 'deleteCategory'])
        ->name('api.company.checklist.categories.delete');

    Route::post('/checklist/categories/{categoryId}/items', [CompanyController::class, 'addItem'])
        ->name('api.company.checklist.items.add');

    Route::put('/checklist/items/{itemId}', [CompanyController::class, 'updateItem'])
        ->name('api.company.checklist.items.update');

    Route::delete('/checklist/items/{itemId}', [CompanyController::class, 'deleteItem'])
        ->name('api.company.checklist.items.delete');

    // Holiday check (for Extra Task pricing)
    Route::get('/holiday/check', [CompanyController::class, 'checkHoliday'])
        ->name('api.company.holiday.check');

    // Task Reviews
    Route::post('/tasks/{taskId}/review', [CompanyController::class, 'submitTaskReview'])
        ->name('api.company.tasks.review.submit');

    Route::get('/tasks/{taskId}/review', [CompanyController::class, 'checkTaskReview'])
        ->name('api.company.tasks.review.check');

    Route::get('/reviews/statistics', [CompanyController::class, 'getReviewStatistics'])
        ->name('api.company.reviews.statistics');
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

// Chatbot API (Public - No authentication required)
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])
    ->middleware('throttle:30,1')  // 30 requests per minute to prevent abuse
    ->name('api.chatbot.message');
