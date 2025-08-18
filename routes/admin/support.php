<?php

/**
 * Admin Support Routes
 * Routes for support center functionality and site documentation
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SupportDashboardController;

// Support Routes
Route::prefix('support')
    ->name('support.')
    ->group(function () {
        
        // Support Dashboard/Index
        Route::get('/', [
            SupportDashboardController::class,
            'index'
        ])->name('index');
        
        // Support Dashboard (alternative route)
        Route::get('/dashboard', [
            SupportDashboardController::class,
            'index'
        ])->name('dashboard');
        
        // Support Statistics API
        Route::get('/stats', [
            SupportDashboardController::class,
            'getStats'
        ])->name('stats');
        
        // Recent Tickets API
        Route::get('/recent-tickets', [
            SupportDashboardController::class,
            'getRecentTickets'
        ])->name('recent-tickets');
        
    });
