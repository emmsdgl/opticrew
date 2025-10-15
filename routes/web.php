<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController; // Import the controller
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\ClientRegistrationController;

use App\Http\Livewire\Admin\TaskDashboard;
use App\Http\Livewire\Admin\TaskList;
use App\Http\Livewire\Admin\SimulationDashboard;
use App\Http\Livewire\Admin\EmployeeAnalytics;
use App\Http\Livewire\Admin\PayrollReport;
use App\Http\Livewire\Admin\ScheduleManager; // Add this at the top
use App\Http\Livewire\Admin\SchedulingLog; // <-- Make sure this is imported
use App\Http\Livewire\Employee\Dashboard as EmployeeDashboard;
use App\Http\Controllers\AppointmentList;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

    // Route::get('/', [AppointmentList::class, 'index'])->name('client-dash');

    Route::get('/', action: function () {
        return view('login');
    })->name('login');
    
// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {
        
    // --- ADMIN ROUTES ---
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/tasks', function () {
        return view('admin-tasks');
        })->name('admin.tasks');

    // Route::get('/admin/tasks', TaskList::class)->name('admin.tasks');

    Route::get('/admin/reports', \App\Http\Livewire\Admin\Reports::class)->name('admin.reports');

    Route::get('/admin/payroll', PayrollReport::class)->name('admin.payroll');

    // The "Schedules" manager
    Route::get('/admin/schedules', ScheduleManager::class)->name('admin.schedules');

    // The "Algorithm Simulation" page
    Route::get('/admin/simulation', SimulationDashboard::class)->name('admin.simulation');

    // The "Employee Analytics" page
    Route::get('/admin/analytics/employees', EmployeeAnalytics::class)->name('admin.analytics.employees');

    // THIS IS THE CORRECTED ROUTE for the Scheduling Log
    Route::get('/admin/scheduling-log', SchedulingLog::class)->name('admin.scheduling-log');


    // --- EMPLOYEE ROUTES ---
    Route::get('/employee/dashboard', function () {

        // 1. Get the currently authenticated user.
        $user = Auth::user();

        // 2. Get the associated employee record using the relationship we just defined.
        $employee = $user->employee;

        // 3. Pass the $employee variable to the view.
        return view('employee-dash', compact('employee'));

    })->middleware(['auth'])->name('employee.dashboard');

    
    // --- CLIENT ROUTES ---
    Route::get('/client/dashboard', function() {
        // 1. Get the currently authenticated user.
        $user = Auth::user();
    
        // 2. Get the associated client record using the relationship.
        $client = $user->client;
    
        // 3. Pass the $client variable to the view.
        return view('client-dash', compact('client'));
        
    })->middleware('auth')->name('client.dashboard');
});

    //ALL ROUTES FOR BUTTONS
    Route::get('/signup', function () {
        return view('signup');
        })->middleware('guest')->name('signup');

    // ADD THIS NEW POST ROUTE to handle the form submission
    Route::post('/signup', [ClientRegistrationController::class, 'store'])
        ->middleware('guest')
        ->name('register.client');
        
    // ADD these two routes
    Route::post('/signup/send-otp', [ClientRegistrationController::class, 'sendOtp'])->middleware('guest');
    Route::post('/signup/verify-otp', [ClientRegistrationController::class, 'verifyOtp'])->middleware('guest');


// It was added automatically by 'php artisan breeze:install'
require __DIR__ . '/auth.php';