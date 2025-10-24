<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskStatusController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\ChatbotController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
