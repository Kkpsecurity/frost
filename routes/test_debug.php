<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Temporary test routes without authentication
 */
Route::get('/test/debug', [StudentDashboardController::class, 'debug'])
    ->name('test.debug');

Route::get('/test/kkpdebug', function() {
    // Test if kkpdebug function is available
    try {
        \kkpdebug("Testing kkpdebug function", "TEST");
        return response()->json([
            'status' => 'success',
            'message' => 'kkpdebug function is working'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'kkpdebug function error: ' . $e->getMessage()
        ]);
    }
});
