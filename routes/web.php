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
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\ClientAppointmentController;
use App\Http\Controllers\LanguageController;

use App\Http\Livewire\Admin\EmployeeAnalytics;

Route::get('/', action: function () {
    return view('client.appointments');
})->name('client-appointments');


/*
|-------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- LANGUAGE SWITCHING ROUTES ---
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');
Route::post('/api/language/switch', [LanguageController::class, 'switchApi'])->name('language.switch.api');

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
        }
    }

    // Show landing page for guests
    return view('landing.home');
})->name('home');

Route::get('/about', function () {
    return response()->file(resource_path('views/landing/about.blade.php'));
})->name('about');

Route::get('/services', function () {
    return response()->file(resource_path('views/landing/service.blade.php'));
})->name('services');

Route::get('/pricing', function () {
    return response()->file(resource_path('views/landing/guest-pricing.blade.php'));
})->name('pricing');


// --- AUTHENTICATED ROUTES ---

// --- ADMIN ROUTES ---
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Display task calendar and kanban board
    Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');

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

    // ✅ NEW: Save schedule (mark as saved)
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

    // --- ADMIN HOLIDAY ROUTES ---
    Route::prefix('admin/holidays')->name('admin.holidays.')->group(function () {
        Route::post('/', [\App\Http\Controllers\Admin\HolidayController::class, 'store'])->name('store');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\HolidayController::class, 'destroy'])->name('destroy');
        Route::get('/by-date', [\App\Http\Controllers\Admin\HolidayController::class, 'getByDate'])->name('by-date');
    });

    Route::get('/analytics', action: function () {
        return view('admin.analytics');
    })->name('admin.analytics');


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

    Route::get('/employee/attendance', [AttendanceController::class, 'index'])
        ->name('employee.attendance');

    Route::post('/employee/attendance/clockin', [AttendanceController::class, 'clockIn'])
        ->name('employee.attendance.clockin');

    Route::post('/employee/attendance/clockout', [AttendanceController::class, 'clockOut'])
        ->name('employee.attendance.clockout');

    Route::get('/employee/tasks', [EmployeeTasksController::class, 'index'])
    ->name('employee.tasks');

    Route::get('/employee/performance', action: function () {
    return view('employee.performance');
    })->name('employee.performance');

});

// --- CLIENT ROUTES ---
Route::middleware(['auth', 'client'])->group(function () {
    Route::get('/client/dashboard', function () {
        // 1. Get the currently authenticated user.
        $user = Auth::user();

        // 2. Get the associated client record using the relationship.
        $client = $user->client;

        // 3. Fetch holidays
        $holidays = App\Models\Holiday::all()->map(function ($holiday) {
            return [
                'date' => $holiday->date->format('Y-m-d'),
                'name' => $holiday->name,
            ];
        });

        // 4. Pass the $client and holidays to the view.
        return view('client.dashboard', compact('client', 'holidays'));
    })->name('client.dashboard');

    // Client Appointment/Booking Routes
    Route::get('/client/book-service', [ClientAppointmentController::class, 'create'])->name('client.appointment.create');
    Route::post('/client/book-service', [ClientAppointmentController::class, 'store'])->name('client.appointment.store');

    Route::get('/client/appointments', function () {
        $user = Auth::user();

        // Fetch all appointments for the current client
        $appointments = $user->client->appointments()->orderBy('service_date', 'asc')->get();

        // Pass the fetched data to the new view file
        return view('client.appointments', compact('appointments'));
    })->name('client.appointments');
});


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