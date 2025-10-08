<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Services\CronManagerController;

/**
 * Admin Services Routes
 * Location: Admin Center > Services
 */

Route::middleware(['admin'])->prefix('services')->name('services.')->group(function () {

    // Cron Manager Routes
    Route::prefix('cron-manager')->name('cron-manager.')->group(function () {
        Route::get('/', [CronManagerController::class, 'index'])->name('index');
        Route::post('/run-task', [CronManagerController::class, 'runTask'])->name('run-task');
        Route::post('/run-schedule', [CronManagerController::class, 'runSchedule'])->name('run-schedule');
        Route::get('/logs', [CronManagerController::class, 'getLogs'])->name('logs');
        Route::post('/test', [CronManagerController::class, 'testCron'])->name('test');
    });

    // Future services can be added here:
    // - Log Manager
    // - Cache Manager
    // - Queue Manager
    // - System Monitor
    // etc.

});