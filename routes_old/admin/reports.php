<?php

/**
 * Admin Reports Routes
 * Routes for reports and analytics functionality
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Reports\ReportController;

// Reports Routes
Route::prefix('reports')
    ->name('reports.')
    ->group(function () {
        
        // Reports Dashboard/Index
        Route::get('/', [
            ReportController::class,
            'index'
        ])->name('index');
        
        // Reports Dashboard (alternative route)
        Route::get('/dashboard', [
            ReportController::class,
            'index'
        ])->name('dashboard');
        
        // Analytics API Endpoints
        Route::prefix('api')
            ->name('api.')
            ->group(function () {
                
                // Base Analytics & Traffic
                Route::get('/analytics/overview', [
                    ReportController::class,
                    'getAnalyticsOverview'
                ])->name('analytics.overview');
                
                Route::get('/analytics/traffic', [
                    ReportController::class,
                    'getTrafficData'
                ])->name('analytics.traffic');
                
                // Financial Reports
                Route::get('/finance/overview', [
                    ReportController::class,
                    'getFinanceOverview'
                ])->name('finance.overview');
                
                Route::get('/finance/revenue', [
                    ReportController::class,
                    'getRevenueData'
                ])->name('finance.revenue');
                
                // Classroom Reports
                Route::get('/classroom/overview', [
                    ReportController::class,
                    'getClassroomOverview'
                ])->name('classroom.overview');
                
                Route::get('/classroom/performance', [
                    ReportController::class,
                    'getPerformanceData'
                ])->name('classroom.performance');
                
            });
        
    });
