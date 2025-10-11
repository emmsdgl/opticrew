<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController; // Import the controller
use App\Http\Livewire\Admin\TaskDashboard;
use App\Http\Livewire\Admin\TaskList;
use App\Http\Livewire\Admin\SimulationDashboard;
use App\Http\Livewire\Admin\EmployeeAnalytics;
use App\Http\Livewire\Admin\PayrollReport;
use App\Http\Livewire\Admin\ScheduleManager; // Add this at the top
use App\Http\Livewire\Admin\SchedulingLog; // <-- Make sure this is imported
use App\Http\Livewire\Employee\Dashboard as EmployeeDashboard;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//FOR ADMIN DASHBOARD SHOWING
Route::get('/', action: function () {
    return view('login');
})->name('login');

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    // Route::get('/admin/dashboard', function () {
    //     return view('admin-dash');
    // })->middleware(['auth'])->name(name: 'admin.dashboard');
    
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Route::get('/admin/dashboard', TaskDashboard::class)
    //     ->middleware(['auth'])
    //     ->name('admin.dashboard');
    
    Route::get('/admin/tasks', TaskList::class)->name('admin.tasks');

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
    
    Route::get('/admin/reports', \App\Http\Livewire\Admin\Reports::class)->name('admin.reports');
    
    Route::get('/admin/payroll', PayrollReport::class)->name('admin.payroll');
});

    // Add dashboard for external clients later
    // Route::get('/client/dashboard', ...)->name('client.dashboard');

    //ALL ROUTES FOR BUTTONS
    Route::get('/signup', function () {
    return view('signup');
    })->name('signup');


// 3. This brings in all the necessary authentication routes like /login, /logout, /register etc.
// It was added automatically by 'php artisan breeze:install'
require __DIR__.'/auth.php';