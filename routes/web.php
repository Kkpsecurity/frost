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

// Debug routes for development
if (app()->environment(['local', 'development'])) {
    require __DIR__ . '/debug.php';
}

/**
 * Games Routes for Testing
 */
Route::get('/games/speed-tictactoe', function () {
    return view('games.speed-tictactoe');
})->name('games.speed-tictactoe');

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
    // Attendance detection and auto-creation
    Route::get('/check-attendance', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'checkAttendanceRequired'])
        ->name('check-attendance');

    // Attendance marking page
    Route::get('/attendance/{studentUnit}', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'showAttendance'])
        ->name('attendance');
    Route::post('/attendance/{studentUnit}/mark', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'markAttendance'])
        ->name('attendance.mark');

    // Onboarding process
    Route::get('/onboarding/{studentUnit}', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'show'])
        ->name('onboarding');

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

    Route::post('/onboarding/{studentUnit}/agreement', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'acceptAgreement'])
        ->name('onboarding.agreement');
    Route::post('/onboarding/{studentUnit}/rules', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'acknowledgeRules'])
        ->name('onboarding.rules');
    Route::post('/onboarding/{studentUnit}/identity', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'verifyIdentity'])
        ->name('onboarding.identity');
    Route::post('/onboarding/{studentUnit}/enter', [App\Http\Controllers\Student\ClassroomOnboardingController::class, 'enterClassroom'])
        ->name('onboarding.enter');
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
    Route::post('/start', [App\Http\Controllers\Student\IdVerificationController::class, 'startVerification'])
        ->name('start');
    Route::get('/status/{studentId}', [App\Http\Controllers\Student\IdVerificationController::class, 'getVerificationStatus'])
        ->name('status');
    Route::get('/summary/{verificationId}', [App\Http\Controllers\Student\IdVerificationController::class, 'getVerificationSummary'])
        ->name('summary');
    Route::post('/retry/{verificationId}', [App\Http\Controllers\Student\IdVerificationController::class, 'retryVerificationStep'])
        ->name('retry');
    Route::post('/cancel/{verificationId}', [App\Http\Controllers\Student\IdVerificationController::class, 'cancelVerification'])
        ->name('cancel');
    Route::get('/student/{studentId}', [App\Http\Controllers\Student\IdVerificationController::class, 'getStudentVerifications'])
        ->name('student');
    Route::post('/test', [App\Http\Controllers\Student\IdVerificationController::class, 'testVerification'])
        ->name('test');
});

/**
 * Student Offline Session Tracking Routes
 */
Route::middleware('auth')->prefix('student/offline')->name('student.offline.')->group(function () {
    // Session Management
    Route::post('session/start/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'startSession'])
        ->name('session.start');
    Route::post('session/end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'endSession'])
        ->name('session.end');
    Route::get('session/status/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionStatus'])
        ->name('session.status');

    // Activity Tracking
    Route::post('track/lesson/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackLessonActivity'])
        ->name('track.lesson');
    Route::post('track/step/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'trackSessionStep'])
        ->name('track.step');

    // Analytics & Reporting
    Route::get('summary/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getSessionSummary'])
        ->name('summary');
    Route::get('activities/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'getRecentActivities'])
        ->name('activities');

    // Admin/Cleanup
    Route::post('session/force-end/{courseAuthId}', [App\Http\Controllers\Student\OfflineSessionController::class, 'forceEndSessions'])
        ->name('session.force-end');
});

/**
 * Admin Payment Configuration Routes
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Payment management routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'index'])->name('index');

        // PayPal configuration
        Route::get('/paypal', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'paypal'])->name('paypal');
        Route::put('/paypal', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'updatePayPal'])->name('update-paypal');

        // Stripe configuration
        Route::get('/stripe', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'stripe'])->name('stripe');
        Route::put('/stripe', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'updateStripe'])->name('update-stripe');

        // Connection testing
        Route::post('/test-connection', [App\Http\Controllers\Admin\AdminPaymentsController::class, 'testConnection'])->name('test-connection');
    });

    // NOTE: Communication routes moved to routes/admin/communication.php to use admin guard
});

/**
 * Clean web routes - test/debug routes moved to separate files
 */


