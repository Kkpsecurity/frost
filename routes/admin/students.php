<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Students\StudentDashboardController;

/**
 * Student account management routes
 * @author Admin Dashboard
 */

Route::prefix('students')->name('students.')->middleware(['admin'])->group(function () {

    // =====================================================
    // STUDENT ADMIN ROOT - Dashboard & Main Interface
    // =====================================================

    // Main student management dashboard
    Route::get('/', [StudentDashboardController::class, 'dashboard'])
        ->name('dashboard');

    // =====================================================
    // STUDENT MANAGEMENT - Account & Order Management
    // =====================================================

    Route::prefix('manage')->name('manage.')->group(function () {
        // Individual student account view
        Route::get('/{student}', [StudentDashboardController::class, 'viewStudent'])
            ->name('view');

        // Student account editing
        Route::get('/{student}/edit', [StudentDashboardController::class, 'editStudent'])
            ->name('edit');

        Route::put('/{student}', [StudentDashboardController::class, 'updateStudent'])
            ->name('update');

        // Student order management
        Route::get('/{student}/orders', [StudentDashboardController::class, 'viewOrders'])
            ->name('orders');

        // Student payment management
        Route::get('/{student}/payments', [StudentDashboardController::class, 'viewPayments'])
            ->name('payments');

        // Account status management
        Route::post('/{student}/activate', [StudentDashboardController::class, 'activateStudent'])
            ->name('activate');

        Route::post('/{student}/deactivate', [StudentDashboardController::class, 'deactivateStudent'])
            ->name('deactivate');
    });

    // =====================================================
    // DATA ENDPOINTS - Student Data for Dashboard
    // =====================================================

    Route::prefix('data')->name('data.')->group(function () {
        // All students list with pagination
        Route::get('/students/list', [StudentDashboardController::class, 'getStudentsList'])
            ->name('students.list');

        // Recent student registrations
        Route::get('/students/recent', [StudentDashboardController::class, 'getRecentStudents'])
            ->name('students.recent');

        // Student search functionality
        Route::get('/students/search', [StudentDashboardController::class, 'searchStudents'])
            ->name('students.search');

        // Student statistics overview
        Route::get('/stats/overview', [StudentDashboardController::class, 'getStats'])
            ->name('stats.overview');

        // Active orders data
        Route::get('/orders/active', [StudentDashboardController::class, 'getActiveOrders'])
            ->name('orders.active');

        // Recent payments data
        Route::get('/payments/recent', [StudentDashboardController::class, 'getRecentPayments'])
            ->name('payments.recent');

        // Account status summary
        Route::get('/accounts/status', [StudentDashboardController::class, 'getAccountStatus'])
            ->name('accounts.status');

        // Course enrollment overview
        Route::get('/enrollments/overview', [StudentDashboardController::class, 'getEnrollmentOverview'])
            ->name('enrollments.overview');

        // DEBUG: Student data structure
        Route::get('/debug/students', [StudentDashboardController::class, 'debugStudentData'])
            ->name('debug.students');
    });

    // =====================================================
    // BULK OPERATIONS - Mass Student Management
    // =====================================================

    Route::prefix('bulk')->name('bulk.')->group(function () {
        // Export student data
        Route::get('/export', [StudentDashboardController::class, 'exportStudents'])
            ->name('export');

        // Bulk status updates
        Route::post('/activate', [StudentDashboardController::class, 'bulkActivate'])
            ->name('activate');

        Route::post('/deactivate', [StudentDashboardController::class, 'bulkDeactivate'])
            ->name('deactivate');

        // Bulk email functionality
        Route::post('/email', [StudentDashboardController::class, 'bulkEmail'])
            ->name('email');
    });
});
