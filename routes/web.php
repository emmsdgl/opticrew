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

use App\Http\Livewire\Admin\EmployeeAnalytics;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Route::get('/', function () {
//     if (Auth::check()) {
//         $role = Auth::user()->role;

//         if ($role === 'admin') {
//             return redirect()->route('admin.dashboard');
//         } elseif ($role === 'employee') {
//             return redirect()->route('employee.dashboard');
//         } elseif ($role === 'external_client') {
//             return redirect()->route('client.dashboard');
//         }
//     }

//     return redirect()->route('login');
// });

Route::get('/', action: function () {
    return view('client-appointments');
})->name('client-appointments');


Route::get('/employee-tasks', [EmployeeTasksController::class, 'index'])
    ->middleware('auth')
    ->name('employee-tasks');

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    Route::get('/admin-dash', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Display task calendar and kanban board
    Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');

    // Create new task from calendar
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

    // Update task status (Kanban board drag & drop)
    Route::patch('/tasks/{taskId}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');

    // Get all clients (for refreshing dropdown)
    Route::get('/tasks/clients', [TaskController::class, 'getClients'])->name('tasks.clients');

    // Add external client from order
    Route::post('/tasks/add-external-client', [TaskController::class, 'addExternalClientFromOrder'])
        ->name('tasks.add-external-client');

    Route::get('/admin/optimization/{optimizationRunId}/results', [TaskController::class, 'getOptimizationResults']);
    Route::post('/admin/optimization/reoptimize', [TaskController::class, 'reoptimize']);

    // ✅ NEW: Check for unsaved schedules
    Route::get('/admin/optimization/check-unsaved', [TaskController::class, 'checkUnsavedSchedule'])
        ->name('admin.optimization.check-unsaved');

    // ✅ NEW: Save schedule (mark as saved)
    Route::post('/admin/optimization/save-schedule', [TaskController::class, 'saveSchedule'])
        ->name('admin.optimization.save');

    // Analytics dashboard for optimization metrics
    Route::get('/analytics', EmployeeAnalytics::class)->name('admin.analytics');

    Route::prefix('schedules')->group(function () {
        Route::post('/optimize', [ScheduleController::class, 'optimize']);
        Route::get('/', [ScheduleController::class, 'getSchedule']);
        Route::get('/statistics', [ScheduleController::class, 'getStatistics']);
    });

    // What-If Scenario Routes
    Route::prefix('scenarios')->group(function () {
        Route::get('/types', [ScenarioController::class, 'getScenarioTypes']);
        Route::post('/analyze', [ScenarioController::class, 'analyze']);
        Route::post('/compare', [ScenarioController::class, 'compareScenarios']);
    });
});


// --- EMPLOYEE ROUTES ---

Route::middleware(['auth'])->group(function () {
    Route::get('/employee-dash', [EmployeeDashboardController::class, 'index'])
        ->name('employee.dashboard');

    Route::get('/employee/attendance', [AttendanceController::class, 'index'])
        ->name('employee.attendance');

    Route::post('/employee/attendance/clockin', [AttendanceController::class, 'clockIn'])
        ->name('employee.attendance.clockin');

    Route::post('/employee/attendance/clockout', [AttendanceController::class, 'clockOut'])
        ->name('employee.attendance.clockout');
});

// --- CLIENT ROUTES ---
Route::get('/client/dashboard', function () {
    // 1. Get the currently authenticated user.
    $user = Auth::user();

    // 2. Get the associated client record using the relationship.
    $client = $user->client;

    // 3. Pass the $client variable to the view.
    return view('client-dash', compact('client'));

})->middleware('auth')->name('client.dashboard');

// Client Appointment/Booking Routes
Route::get('/client/book-service', [ClientAppointmentController::class, 'create'])->name('client.appointment.create');
Route::post('/client/book-service', [ClientAppointmentController::class, 'store'])->name('client.appointment.store');

Route::get('/client/appointments', function () {
    $user = Auth::user();

    // Fetch all appointments for the current client
    $appointments = $user->client->appointments()->orderBy('scheduled_date', 'asc')->get();

    // Pass the fetched data to the new view file
    return view('client-appointments', compact('appointments'));

})->middleware('auth')->name('client.appointments');


//ALL ROUTES FOR BUTTONS
Route::get('/signup', function () {
    return view('signup');
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