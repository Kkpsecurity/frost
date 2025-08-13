<?php

/**
 * Admin Services Routes
 * Routes for admin services like search, tools, etc.
 */

use App\Http\Controllers\Admin\Services\AdminSearchController;
use App\Http\Controllers\Admin\Services\StudentToolActionsController;

// Search routes
Route::prefix('services')->name('services.')->group(function () {
    Route::post('/search/{action?}', [AdminSearchController::class, 'search'])
        ->name('search');

    Route::get('/search/{action?}', [AdminSearchController::class, 'search'])
        ->name('search.get');
});

// Student Tool Routes (these were in services_routes.php)
Route::prefix('services/student_tools')->name('services.student_tools.')->group(function () {
    Route::post('/eject', [StudentToolActionsController::class, 'ejectStudent'])
        ->name('eject');

    Route::post('/reinstate', [StudentToolActionsController::class, 'reInState'])
        ->name('reinstate');

    Route::post('/ban', [StudentToolActionsController::class, 'banStudent'])
        ->name('ban');

    Route::post('/allow_access', [StudentToolActionsController::class, 'reEnterAccess'])
        ->name('allow_access');

    Route::post('/revoke_dnc', [StudentToolActionsController::class, 'revokeDNCStudent'])
        ->name('revoke_dnc');
});
