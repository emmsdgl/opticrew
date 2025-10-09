<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

use App\Http\Controllers\Auth\AuthenticatedSessionController; // Import the controller
use App\Http\Livewire\Admin\TaskDashboard;
use App\Http\Livewire\Admin\TaskList;
use App\Http\Livewire\Admin\SimulationDashboard;
use App\Http\Livewire\Admin\EmployeeAnalytics;
use App\Http\Livewire\Admin\ScheduleManager; // Add this at the top
use App\Http\Livewire\Admin\SchedulingLog; // <-- Make sure this is imported
use App\Http\Livewire\Employee\Dashboard as EmployeeDashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', action: function () {
    return view('admin-dash');
})->name('admin-dash');


// 2. Define the routes for your dashboards, protected by login middleware.
// Only logged-in users can access these pages.

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {

    // --- ADMIN ROUTES ---
    // The "Job Creation" dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin-dashboard');
    })->middleware(['auth'])->name(name: 'admin.dashboard');

    // The "Tasks" list page (we can create this later, for now it points to Job Creation)
    Route::get('/admin/dashboard', TaskDashboard::class)
        ->middleware(['auth'])
        ->name('admin.dashboard');
    
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
    Route::get('/employee/dashboard', EmployeeDashboard::class)
        ->middleware(['auth'])
        ->name('employee.dashboard');

});

    // Add dashboard for external clients later
    // Route::get('/client/dashboard', ...)->name('client.dashboard');


// 3. This brings in all the necessary authentication routes like /login, /logout, /register etc.
// It was added automatically by 'php artisan breeze:install'
require __DIR__.'/auth.php';