<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/**
 * Include Frontend Routes
 * All frontend routes are organized in the frontend.php file
 */
require __DIR__ . '/frontend.php';

/**
 * Games Routes for Testing
 */
Route::get('/games/speed-tictactoe', function () {
    return view('games.speed-tictactoe');
})->name('games.speed-tictactoe');

/**
 * API Testing Routes
 */
Route::middleware('auth')->get('/test-lesson-session', function () {
    return view('test-lesson-session');
})->name('test.lesson-session');

/**
 * Account Profile Routes
 */
Route::middleware('auth')->group(function () {
    Route::get('/account', [App\Http\Controllers\Student\ProfileController::class, 'index'])->name('account.index');
    Route::post('/account/profile', [App\Http\Controllers\Student\ProfileController::class, 'updateProfile'])->name('account.profile.update');
    Route::post('/account/settings', [App\Http\Controllers\Student\ProfileController::class, 'updateSettings'])->name('account.settings.update');
    Route::get('/account/invoice/{order}', [App\Http\Controllers\Student\ProfileController::class, 'downloadInvoice'])->name('student.invoice');

    // Payment method management routes
    Route::prefix('account/payments')->name('account.payments.')->group(function () {
        Route::post('/add-stripe-method', [App\Http\Controllers\Student\ProfileController::class, 'addStripePaymentMethod'])->name('add-stripe');
        Route::get('/connect-paypal', [App\Http\Controllers\Student\ProfileController::class, 'connectPayPal'])->name('connect-paypal');
        Route::post('/paypal-callback', [App\Http\Controllers\Student\ProfileController::class, 'paypalCallback'])->name('paypal-callback');
        Route::post('/set-default', [App\Http\Controllers\Student\ProfileController::class, 'setDefaultPaymentMethod'])->name('set-default');
        Route::delete('/delete-method', [App\Http\Controllers\Student\ProfileController::class, 'deletePaymentMethod'])->name('delete-method');
    });
});

/**
 * Student Classroom Onboarding Routes
 */
Route::middleware('auth')->prefix('classroom')->name('classroom.')->group(function () {
    // TODO: Create ClassroomOnboardingController or remove these routes
    // Attendance detection and auto-creation
    // Route::get('/check-attendance', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'checkAttendanceRequired'])
    //     ->name('check-attendance');

    // Attendance marking page
    // Route::get('/attendance/{studentUnit}', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'showAttendance'])
    //     ->name('attendance');
    // Route::post('/attendance/{studentUnit}/mark', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'markAttendance'])
    //     ->name('attendance.mark');

    // Onboarding process
    // Route::get('/onboarding/{studentUnit}', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'show'])
    //     ->name('onboarding');

    // Terms and conditions acceptance
    Route::post('/student/onboarding/accept-terms', [App\Http\Controllers\Student\StudentDashboardController::class, 'acceptTerms'])
        ->name('student.onboarding.accept-terms');
    Route::get('/student/onboarding/check-agreement/{courseAuthId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'checkAgreementStatus'])
        ->name('student.onboarding.check-agreement');

    // Classroom rules acceptance (tracked daily)
    Route::post('/student/onboarding/accept-rules', [App\Http\Controllers\Student\StudentDashboardController::class, 'acceptRules'])
        ->name('student.onboarding.accept-rules');
    Route::get('/student/onboarding/check-rules/{courseAuthId}/{courseDateId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'checkRulesStatus'])
        ->name('student.onboarding.check-rules');

    // ID Verification Routes (permanent ID card + daily headshot)
    Route::post('/id-verification/start', [App\Http\Controllers\Student\StudentDashboardController::class, 'startIdVerification'])
        ->name('id-verification.start');
    Route::get('/id-verification/status/{studentId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'getIdVerificationStatus'])
        ->name('id-verification.status');
    Route::get('/id-verification/summary/{verificationId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'getIdVerificationSummary'])
        ->name('id-verification.summary');
    Route::post('/id-verification/upload-headshot', [App\Http\Controllers\Student\StudentDashboardController::class, 'uploadHeadshot'])
        ->name('id-verification.upload-headshot');
    Route::get('/student/onboarding/check-headshot', [App\Http\Controllers\Student\StudentDashboardController::class, 'checkHeadshotStatus'])
        ->name('student.onboarding.check-headshot');
    Route::get('/student/onboarding/check-id-card/{courseAuthId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'checkIdCardStatus'])
        ->name('student.onboarding.check-id-card');
    Route::get('/student/onboarding/course-dates-headshots/{courseAuthId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'getCourseDatesWithHeadshots'])
        ->name('student.onboarding.course-dates-headshots');

    // Complete onboarding
    Route::post('/student/onboarding/complete', [App\Http\Controllers\Student\StudentDashboardController::class, 'completeOnboarding'])
        ->name('student.onboarding.complete');

    // Offline onboarding for FSTB (Fast Study Training Base)
    Route::post('/student/offline-onboarding', [App\Http\Controllers\Student\StudentDashboardController::class, 'completeOfflineOnboarding'])
        ->name('student.offline-onboarding');

    // Route::post('/onboarding/{studentUnit}/agreement', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'acceptAgreement'])
    //     ->name('onboarding.agreement');
    // Route::post('/onboarding/{studentUnit}/rules', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'acknowledgeRules'])
    //     ->name('onboarding.rules');
    // Route::post('/onboarding/{studentUnit}/identity', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'verifyIdentity'])
    //     ->name('onboarding.identity');
    // Route::post('/onboarding/{studentUnit}/enter', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'enterClassroom'])
    //     ->name('onboarding.enter');

    // SESSION MANAGEMENT ROUTES
    Route::post('/session/heartbeat', [App\Http\Controllers\Student\ClassroomController::class, 'heartbeat'])
        ->name('session.heartbeat');
    Route::get('/session/status/{studentUnitId}', [App\Http\Controllers\Student\ClassroomController::class, 'sessionStatus'])
        ->name('session.status');
    Route::post('/session/leave', [App\Http\Controllers\Student\ClassroomController::class, 'leaveClassroom'])
        ->name('session.leave');
    Route::post('/session/check-or-create', [App\Http\Controllers\Student\ClassroomController::class, 'checkOrCreateSession'])
        ->name('session.check-or-create');

    // VIDEO QUOTA MANAGEMENT ROUTES
    Route::get('/video-quota', [App\Http\Controllers\Student\StudentDashboardController::class, 'getVideoQuota'])
        ->name('video-quota');
});

/**
 * Student Course Management Routes
 */
Route::middleware('auth')->prefix('student/course')->name('student.course.')->group(function () {
    // Start class - sets start_date and expire_date (1 year from start)
    Route::post('/start', [App\Http\Controllers\Student\StudentDashboardController::class, 'startClass'])
        ->name('start');
});

/**
 * Student ID Verification Routes
 */
Route::middleware('auth')->prefix('student/id-verification')->name('student.id-verification.')->group(function () {
    // NOTE: IdVerificationController does not exist in this repo.
    // These routes are kept as aliases to the existing StudentDashboardController endpoints.
    Route::post('/start', [App\Http\Controllers\Student\StudentDashboardController::class, 'startIdVerification'])
        ->name('start');
    Route::get('/status/{studentId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'getIdVerificationStatus'])
        ->name('status');
    Route::get('/summary/{verificationId}', [App\Http\Controllers\Student\StudentDashboardController::class, 'getIdVerificationSummary'])
        ->name('summary');
});

/**
 * Student Offline Session Tracking Routes
 */
if (class_exists('App\\Http\\Controllers\\Student\\OfflineSessionController')) {
    Route::middleware('auth')->prefix('student/offline')->name('student.offline.')->group(function () {
        // Session Management
        Route::post('session/start/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@startSession')
            ->name('session.start');
        Route::post('session/end/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@endSession')
            ->name('session.end');
        Route::get('session/status/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@getSessionStatus')
            ->name('session.status');

        // Activity Tracking
        Route::post('track/lesson/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@trackLessonActivity')
            ->name('track.lesson');
        Route::post('track/step/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@trackSessionStep')
            ->name('track.step');

        // Analytics & Reporting
        Route::get('summary/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@getSessionSummary')
            ->name('summary');
        Route::get('activities/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@getRecentActivities')
            ->name('activities');

        // Admin/Cleanup
        Route::post('session/force-end/{courseAuthId}', 'App\\Http\\Controllers\\Student\\OfflineSessionController@forceEndSessions')
            ->name('session.force-end');
    });
}

/**
 * Admin Payment Configuration Routes
 */
if (class_exists('App\\Http\\Controllers\\Admin\\AdminPaymentsController')) {
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        // Payment management routes
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@index')->name('index');

            // PayPal configuration
            Route::get('/paypal', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@paypal')->name('paypal');
            Route::put('/paypal', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@updatePayPal')->name('update-paypal');

            // Stripe configuration
            Route::get('/stripe', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@stripe')->name('stripe');
            Route::put('/stripe', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@updateStripe')->name('update-stripe');

            // Connection testing
            Route::post('/test-connection', 'App\\Http\\Controllers\\Admin\\AdminPaymentsController@testConnection')->name('test-connection');
        });

        // NOTE: Communication routes moved to routes/admin/communication.php to use admin guard
    });
}

/**
 * Clean web routes - test/debug routes moved to separate files
 */


