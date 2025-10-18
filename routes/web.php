<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\Auth\ClientRegistrationController;
use App\Http\Controllers\Auth\ForgotPasswordController; 
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AppointmentList;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
    Route::get('/', action: function () {
        return view('client-appointment-form');
    })->name('client-appointment-form');
    
// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {
        
    // --- ADMIN ROUTES ---
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Display task calendar and kanban board
    Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks');
    
    // Create new task from calendar
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    
    // Get all clients (for refreshing dropdown)
    Route::get('/tasks/clients', [TaskController::class, 'getClients'])->name('tasks.clients');
    
    // Add external client from order
    Route::post('/tasks/add-external-client', [TaskController::class, 'addExternalClientFromOrder'])
        ->name('tasks.add-external-client');
});


    // --- EMPLOYEE ROUTES ---
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->middleware('auth')->name('employee.dashboard');


    // --- CLIENT ROUTES ---
    Route::get('/client/dashboard', function() {
        // 1. Get the currently authenticated user.
        $user = Auth::user();
    
        // 2. Get the associated client record using the relationship.
        $client = $user->client;
    
        // 3. Pass the $client variable to the view.
        return view('client-dash', compact('client'));
        
    })->middleware('auth')->name('client.dashboard');
    
    Route::get('/client/appointments', function () {
        $user = Auth::user(); 
        
        // Fetch all appointments for the current client
        $appointments = $user->client->appointments() ->orderBy('scheduled_date', 'asc') ->get();
    
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