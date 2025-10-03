<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController; // Import the controller
use App\Http\Livewire\Admin\TaskDashboard; // <-- Make sure this is at the top of the file
use App\Http\Livewire\Admin\ScheduleManager; // Add this at the top
use App\Http\Livewire\Employee\Dashboard as EmployeeDashboard;
use App\Http\Livewire\Admin\TaskList;
use App\Http\Livewire\Admin\SimulationDashboard; // Add this at the top

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Make the login page the very first page of the site.
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Define the routes for your dashboards, protected by login middleware.
// Only logged-in users can access these pages.

    Route::get('/admin/dashboard', function () {
        return view('admin-dashboard');
    })->middleware(['auth'])->name('admin.dashboard');

    Route::get('/admin/schedules', ScheduleManager::class)
        ->middleware(['auth'])
        ->name('admin.schedules');

    Route::get('/admin/dashboard', TaskDashboard::class)
        ->middleware(['auth'])
        ->name('admin.dashboard');

            Route::get('/admin/simulation', SimulationDashboard::class)->name('admin.simulation');


    Route::get('/employee/dashboard', EmployeeDashboard::class)
        ->middleware(['auth'])
        ->name('employee.dashboard');

    Route::get('/admin/tasks', TaskList::class)->name('admin.tasks');

    // Add dashboard for external clients later
    // Route::get('/client/dashboard', ...)->name('client.dashboard');


// 3. This brings in all the necessary authentication routes like /login, /logout, /register etc.
// It was added automatically by 'php artisan breeze:install'
require __DIR__.'/auth.php';