<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\EmployeeTasksController;
use App\Http\Controllers\Auth\ClientRegistrationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AppointmentList;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\ClientAppointmentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FeedbackController;

use App\Http\Controllers\EmployeeRequestsController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Livewire\Admin\EmployeeAnalytics;

// Manager Controllers
use App\Http\Controllers\Manager\ManagerDashboardController;
use App\Http\Controllers\Manager\ManagerScheduleController;
use App\Http\Controllers\Manager\ManagerEmployeesController;
use App\Http\Controllers\Manager\ManagerReportsController;
use App\Http\Controllers\Manager\ManagerActivityController;
use App\Http\Controllers\Manager\ManagerHistoryController;

Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage']);

// --- LANDING PAGE ROUTES (Public) ---
Route::get('/', function () {
    // If user is already authenticated, redirect to their dashboard
    if (Auth::check()) {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'employee') {
            return redirect()->route('employee.dashboard');
        } elseif ($role === 'external_client') {
            return redirect()->route('client.dashboard');
        } elseif ($role === 'company') {
            return redirect()->route('manager.dashboard');
        }
    }

    // Show landing page for guests
    return view('landingpage-home');
})->name('home');

// About Page
Route::get('/about', function () {
    return view('landingpage-about');
})->name('about');

// Services Page
Route::get('/services', function () {
    return view('landingpage-services');
})->name('services');

// Price Quotation Page
Route::get('/quotation', function () {
    return view('landingpage-quotation');
})->name('quotation');

// Submit Quotation Form
Route::post('/quotation/submit', [\App\Http\Controllers\Admin\QuotationController::class, 'store'])->name('quotation.submit');

// Contact Page
Route::get('/contact', function () {
    return view('landingpage-contact');
})->name('contact');

// Documentation
Route::get('/documentation', function () {
    return view('landingpage-documentation');
})->name('documentation');

// Terms and Conditions
Route::get('/termscondition', function () {
    return view('landingpage-termscondition');
})->name('termscondition');

// Privacy Policy
Route::get('/privacypolicy', function () {
    return view('landingpage-privacypolicy');
})->name('privacypolicy');

// Careers in Fin-noys
Route::get('/recruitment', function () {
    $jobPostings = \App\Models\JobPosting::active()->orderBy('created_at', 'desc')->get();
    return view('landingpage-recruitment', compact('jobPostings'));
})->name('recruitment');

// Job Application Submission (Public)
Route::post('/recruitment/apply', [JobApplicationController::class, 'store'])->name('recruitment.apply');

// Public API for active job postings
Route::get('/api/job-postings', [\App\Http\Controllers\Admin\JobPostingController::class, 'getActivePostings'])->name('api.job-postings');

// System
Route::get('/castcrew', function () {
    return view('landingpage-castcrew');
})->name('castcrew');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

/*
|-------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- LANGUAGE SWITCHING ROUTES ---
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::post('/api/language/switch', [LanguageController::class, 'switchApi'])->name('language.switch.api');

// --- AUTHENTICATED ROUTES ---

// --- NOTIFICATION ROUTES (Available to all authenticated users) ---
Route::middleware(['auth'])->group(function () {
    // View all notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Get unread count (for AJAX/header badge)
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    // Mark single notification as read
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // Mark all notifications as read
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');

    // Delete notification
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');

    // TEST ROUTE - Create sample notifications (REMOVE THIS IN PRODUCTION)
    Route::get('/notifications/test/create', [NotificationController::class, 'createTestNotifications'])->name('notifications.test');
});

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/history', [\App\Http\Controllers\Admin\HistoryController::class, 'index'])->name('admin.history');

    Route::get('/admin/attendance', [AttendanceController::class, 'adminIndex'])->name('admin.attendance');

    // Admin Employee Request Approval Routes
    Route::post('/admin/employee-requests/{id}/approve', [AttendanceController::class, 'approveRequest'])->name('admin.employee-requests.approve');
    Route::post('/admin/employee-requests/{id}/reject', [AttendanceController::class, 'rejectRequest'])->name('admin.employee-requests.reject');

    // Display task calendar and kanban board
    Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');

    // Display task details
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('admin.tasks.show');

    // Create new task from calendar
    Route::post('/tasks', [TaskController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('tasks.store');

    // Update task status (Kanban board drag & drop)
    Route::patch('/tasks/{taskId}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Get all clients (for refreshing dropdown)
    Route::get('/tasks/clients', [TaskController::class, 'getClients'])->name('tasks.clients');

    // Add external client from order
    Route::post('/tasks/add-external-client', [TaskController::class, 'addExternalClientFromOrder'])
        ->name('tasks.add-external-client');

    Route::get('/admin/optimization/{optimizationRunId}/results', [TaskController::class, 'getOptimizationResults']);
    Route::post('/admin/optimization/reoptimize', [TaskController::class, 'reoptimize'])
        ->middleware('throttle:5,1');

    // ✅ NEW: Check for unsaved schedules
    Route::get('/admin/optimization/check-unsaved', [TaskController::class, 'checkUnsavedSchedule'])
        ->name('admin.optimization.check-unsaved');

    // ✅ NEW: Save schedule (mark as saved
    Route::post('/admin/optimization/save-schedule', [TaskController::class, 'saveSchedule'])
        ->name('admin.optimization.save');

    // --- ADMIN APPOINTMENT ROUTES ---
    Route::prefix('admin/appointments')->name('admin.appointments.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\AppointmentController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\AppointmentController::class, 'reject'])->name('reject');
        Route::post('/{id}/assign-team', [\App\Http\Controllers\Admin\AppointmentController::class, 'assignTeam'])->name('assign-team');
    });

    // --- ADMIN QUOTATION ROUTES ---
    Route::prefix('admin/quotations')->name('admin.quotations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\QuotationController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\QuotationController::class, 'show'])->name('show');
    });

    // --- ADMIN RECRUITMENT ROUTES ---
    Route::prefix('admin/recruitment')->name('admin.recruitment.')->group(function () {
        Route::get('/', [JobApplicationController::class, 'index'])->name('index');
        Route::get('/{id}', [JobApplicationController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [JobApplicationController::class, 'updateStatus'])->name('update-status');
        Route::get('/{id}/download', [JobApplicationController::class, 'downloadResume'])->name('download');
        Route::delete('/{id}', [JobApplicationController::class, 'destroy'])->name('destroy');
    });

    // --- ADMIN JOB POSTINGS ROUTES ---
    Route::prefix('admin/job-postings')->name('admin.job-postings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\JobPostingController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\JobPostingController::class, 'store'])->name('store');
        Route::put('/{id}', [\App\Http\Controllers\Admin\JobPostingController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\JobPostingController::class, 'destroy'])->name('destroy');
    });

    // --- ADMIN HOLIDAY ROUTES ---
    Route::prefix('admin/holidays')->name('admin.holidays.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\HolidayController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'destroy'])->name('destroy');
        Route::get('/by-date', [\App\Http\Controllers\Admin\HolidayController::class, 'getByDate'])->name('by-date');
    });

    // --- ADMIN REPORT ROUTES ---
    Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');

        // Client Reports
        Route::get('/clients', [ReportController::class, 'clientReports'])->name('clients');
        Route::get('/clients/{clientId}', [ReportController::class, 'clientDetail'])->name('client-detail');
        Route::get('/clients/export/csv', [ReportController::class, 'exportClientReport'])->name('clients.export');

        // Employee Payroll Reports
        Route::get('/payroll', [ReportController::class, 'employeePayroll'])->name('payroll');
        Route::get('/payroll/{employeeId}', [ReportController::class, 'employeeDetail'])->name('employee-detail');
        Route::get('/payroll/export/csv', [ReportController::class, 'exportPayrollReport'])->name('payroll.export');
    });

    // --- ADMIN ACCOUNT ROUTES ---
    Route::prefix('admin/accounts')->name('admin.accounts.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AccountController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AccountController::class, 'store'])->name('store');
        Route::post('/verify-password', [\App\Http\Controllers\Admin\AccountController::class, 'verifyPassword'])->name('verify-password');
        Route::get('/archived', [\App\Http\Controllers\Admin\AccountController::class, 'archived'])->name('archived');
        Route::post('/{id}/restore', [\App\Http\Controllers\Admin\AccountController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [\App\Http\Controllers\Admin\AccountController::class, 'forceDelete'])->name('force-delete');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AccountController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AccountController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AccountController::class, 'destroy'])->name('destroy');

        // Company account location management (legacy - individual locations)
        Route::post('/{userId}/locations', [\App\Http\Controllers\Admin\AccountController::class, 'addLocation'])->name('locations.add');
        Route::put('/{userId}/locations/{locationId}', [\App\Http\Controllers\Admin\AccountController::class, 'updateLocation'])->name('locations.update');
        Route::delete('/{userId}/locations/{locationId}', [\App\Http\Controllers\Admin\AccountController::class, 'deleteLocation'])->name('locations.delete');

        // Company account cabin type management (grouped locations)
        Route::post('/{userId}/cabin-types', [\App\Http\Controllers\Admin\AccountController::class, 'addCabinType'])->name('cabin-types.add');
        Route::put('/{userId}/cabin-types/{locationType}', [\App\Http\Controllers\Admin\AccountController::class, 'updateCabinType'])->name('cabin-types.update');
        Route::delete('/{userId}/cabin-types/{locationType}', [\App\Http\Controllers\Admin\AccountController::class, 'deleteCabinType'])->name('cabin-types.delete');

        Route::post('/{userId}/update-details', [\App\Http\Controllers\Admin\AccountController::class, 'updateContractedClient'])->name('update-details');
    });

    Route::get('/admin/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('admin.analytics');

    Route::get('/admin/profile', [ProfileController::class, 'show'])->name('admin.profile');

    Route::get('/admin/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/admin/profile/update', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/admin/profile/upload-picture', [ProfileController::class, 'uploadPicture'])->name('admin.profile.upload-picture');
    Route::get('/admin/settings', [ProfileController::class, 'settings'])->name('admin.settings');
    Route::get('/admin/help-center', [ProfileController::class, 'helpcenter'])->name('admin.helpcenter');
    Route::post('/admin/settings/update-password', [ProfileController::class, 'updatePassword'])->name('admin.settings.update-password');

    // Analytics dashboard for optimization metrics
    Route::get('/optimization-result', EmployeeAnalytics::class)->name('optimization.result');

    Route::prefix('schedules')->group(function () {
        Route::post('/optimize', [ScheduleController::class, 'optimize'])
            ->middleware('throttle:5,1');
        Route::get('/', [ScheduleController::class, 'getSchedule']);
        Route::get('/statistics', [ScheduleController::class, 'getStatistics']);
    });

    // What-If Scenario Routes
    Route::prefix('scenarios')->group(function () {
        Route::get('/types', [ScenarioController::class, 'getScenarioTypes']);
        Route::post('/analyze', [ScenarioController::class, 'analyze'])
            ->middleware('throttle:10,1');
        Route::post('/compare', [ScenarioController::class, 'compareScenarios'])
            ->middleware('throttle:10,1');
    });
});


// --- EMPLOYEE ROUTES ---
Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])
        ->name('employee.dashboard');

    Route::get('/employee/requests/create', [EmployeeRequestsController::class, 'create'])->name('employee.requests.create');
    Route::post('/employee/requests/store', [EmployeeRequestsController::class, 'store'])->name('employee.requests.store');
    Route::post('/employee/requests/{id}/cancel', [EmployeeRequestsController::class, 'cancel'])->name('employee.requests.cancel');

    Route::get('/employee/attendance', [AttendanceController::class, 'index'])
        ->name('employee.attendance');

    Route::post('/employee/attendance/clockin', [AttendanceController::class, 'clockIn'])
        ->name('employee.attendance.clockin');

    Route::post('/employee/attendance/clockout', [AttendanceController::class, 'clockOut'])
        ->name('employee.attendance.clockout');

    Route::get('/employee/tasks', [EmployeeTasksController::class, 'index'])
        ->name('employee.tasks');

    Route::get('/employee/tasks/{task}', [EmployeeTasksController::class, 'show'])
        ->name('employee.tasks.show');
    Route::post('/employee/tasks/{task}/feedback', [EmployeeTasksController::class, 'storeFeedback'])
        ->name('employee.tasks.feedback.store');

    Route::post('/employee/tasks/{task}/approve', [EmployeeTasksController::class, 'approve'])
        ->name('employee.tasks.approve');
    Route::post('/employee/tasks/{task}/decline', [EmployeeTasksController::class, 'decline'])
        ->name('employee.tasks.decline');

    Route::post('/employee/tasks/{task}/start', [EmployeeTasksController::class, 'start'])
        ->name('employee.tasks.start');

    Route::post('/employee/tasks/{task}/complete', [EmployeeTasksController::class, 'complete'])
        ->name('employee.tasks.complete');

    Route::post('/employee/tasks/{task}/checklist/toggle', [EmployeeTasksController::class, 'toggleChecklistItem'])
        ->name('employee.tasks.checklist.toggle');
    Route::get('/employee/tasks/{task}/checklist/status', [EmployeeTasksController::class, 'getChecklistStatus'])
        ->name('employee.tasks.checklist.status');

    Route::get('/employee/performance', [App\Http\Controllers\EmployeePerformanceController::class, 'index'])
        ->name('employee.performance');
    Route::get('/employee/development', [App\Http\Controllers\EmployeePerformanceController::class, 'development'])
        ->name('employee.development');

    Route::get('/employee/profile', [ProfileController::class, 'show'])->name('employee.profile');

    Route::get('/employee/profile/edit', [ProfileController::class, 'edit'])->name('employee.profile.edit');
    Route::post('/employee/profile/update', [ProfileController::class, 'update'])->name('employee.profile.update');
    Route::post('/employee/profile/upload-picture', [ProfileController::class, 'uploadPicture'])->name('employee.profile.upload-picture');
    Route::get('/employee/settings', [ProfileController::class, 'settings'])->name('employee.settings');
    Route::get('/employee/help-center', [ProfileController::class, 'helpcenter'])->name('employee.helpcenter');
    Route::post('/employee/settings/update-password', [ProfileController::class, 'updatePassword'])->name('employee.settings.update-password');
    Route::get('/employee/history', [App\Http\Controllers\Employee\HistoryController::class, 'index'])->name('employee.history');
    Route::post('/employee/history/feedback', [App\Http\Controllers\Employee\HistoryController::class, 'storeFeedback'])->name('employee.history.feedback');
    // Coming Soon Pages
    Route::get('/calendar', function () {
        return view('coming-soon');
    })->name('employee.schedule');

});

// --- CLIENT ROUTES ---
Route::middleware(['auth', 'client'])->group(function () {
    Route::get('/client/dashboard', [ClientAppointmentController::class, 'dashboard'])->name('client.dashboard');

    // Client Appointment/Booking Routes
    Route::get('/client/book-service', [ClientAppointmentController::class, 'create'])->name('client.appointment.create');
    Route::post('/client/book-service', [ClientAppointmentController::class, 'store'])->name('client.appointment.store');
    Route::get('/client/appointments', [ClientAppointmentController::class, 'index'])->name('client.appointments');
    Route::post('/client/appointments/{id}/cancel', [ClientAppointmentController::class, 'cancel'])->name('client.appointment.cancel');
    Route::post('/client/appointments/{id}/feedback', [ClientAppointmentController::class, 'storeFeedback'])->name('client.appointment.feedback');

    Route::get('/client/pricing', function () {
        return view('client.pricing');
    })->name('client.pricing');

    Route::get('/client/feedback', function () {
        return view('client.feedback');
    })->name('client.feedback');
    Route::post('/client/feedback', [FeedbackController::class, 'store'])->name('client.feedback.store');

    Route::get('/client/profile', [ProfileController::class, 'show'])->name('client.profile');

    Route::get('/client/history', [App\Http\Controllers\Client\HistoryController::class, 'index'])->name('client.history');
    Route::post('/client/history/feedback', [App\Http\Controllers\Client\HistoryController::class, 'storeFeedback'])->name('client.history.feedback');

    Route::get('/client/profile/edit', [ProfileController::class, 'edit'])->name('client.profile.edit');
    Route::post('/client/profile/update', [ProfileController::class, 'update'])->name('client.profile.update');
    Route::post('/client/profile/upload-picture', [ProfileController::class, 'uploadPicture'])->name('client.profile.upload-picture');
    Route::get('/client/settings', [ProfileController::class, 'settings'])->name('client.settings');
    Route::get('/client/help-center', [ProfileController::class, 'helpcenter'])->name('client.helpcenter');
    Route::post('/client/settings/update-password', [ProfileController::class, 'updatePassword'])->name('client.settings.update-password');
});

// --- MANAGER (CONTRACTED CLIENT) ROUTES ---
Route::middleware(['auth', 'manager'])->prefix('manager')->name('manager.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');

    // Schedule
    Route::get('/schedule', [ManagerScheduleController::class, 'index'])->name('schedule');
    Route::get('/schedule/tasks', [ManagerScheduleController::class, 'getTasks'])->name('schedule.tasks');
    Route::get('/schedule/locations', [ManagerScheduleController::class, 'getLocations'])->name('schedule.locations');
    Route::post('/schedule/tasks', [ManagerScheduleController::class, 'storeTask'])->name('schedule.tasks.store');

    // Employees
    Route::get('/employees', [ManagerEmployeesController::class, 'index'])->name('employees');

    // Reports
    Route::get('/reports', [ManagerReportsController::class, 'index'])->name('reports');

    // Activity
    Route::get('/activity', [ManagerActivityController::class, 'index'])->name('activity');

    // History
    Route::get('/history', [ManagerHistoryController::class, 'index'])->name('history');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-picture', [ProfileController::class, 'uploadPicture'])->name('profile.upload-picture');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::get('/help-center', [ProfileController::class, 'helpcenter'])->name('helpcenter');
    Route::post('/settings/update-password', [ProfileController::class, 'updatePassword'])->name('settings.update-password');
});

// Geofencing API endpoint (needs web session authentication)
Route::get('/api/company-settings', [App\Http\Controllers\Api\CompanySettingsController::class, 'index'])
    ->middleware('auth')
    ->name('api.company-settings');

//ALL ROUTES FOR BUTTONS
Route::get('/signup', function () {
    return view('auth.signup');
})->middleware('guest')->name('signup');

// ADD THIS NEW POST ROUTE to handle the form submission
Route::post('/signup', [ClientRegistrationController::class, 'store'])
    ->middleware('guest')
    ->name('register.client');

Route::post('/signup/send-otp', [ClientRegistrationController::class, 'sendOtp'])->middleware('guest');
Route::post('/signup/verify-otp', [ClientRegistrationController::class, 'verifyOtp'])->middleware('guest');

Route::get('/forgotpassword', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot.password');
Route::post('/forgotpassword/get-questions', [ForgotPasswordController::class, 'getSecurityQuestions'])->name('password.getQuestions');
Route::post('/forgotpassword/verify-account', [ForgotPasswordController::class, 'verifyAccountAndSendOtp'])->name('password.verifyAccount');
Route::post('/forgotpassword/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.verifyOtp');
Route::post('/forgotpassword/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset.submit');

// It was added automatically by 'php artisan breeze:install'
require __DIR__ . '/auth.php';