<?php


use App\Models\CourseDate;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Florida Online Security Training - Web Route File
|--------------------------------------------------------------------------
|
*/

// AUTH ROUTES
#Auth::routes();
Auth::routes([
    'login' => true,
    // Login Routes...
    'logout' => true,
    // Logout Routes...
    'register' => true,
    // Register Routes...
    'verify' => true,
    // Email Verification Routes...
    'reset' => true,
    // Password Reset Routes...
    'confirm' => true, // Password Confirmation Routes...
]);

/**
 * FrontEnd Non-Authorize Routes
 */
Route::redirect('/', '/pages');


/**
 * This is a react call for all data that required from laravel for the student view
 */
Route::middleware(['auth'])->get('frost/data/{course_auth}', [App\Http\Controllers\React\LaravelSharedData::class, 'getLaraData']);

/**
 * @TODO Remove this when we have found the setting
 */
Route::redirect('student', 'classroom')->name('student');

/**
 * FrontEnd Non-Authorize Routes
 */
Route::get('pages/{slug?}', [App\Http\Controllers\Web\SitePageController::class, 'render'])
    ->name('pages');

Route::post('pages/contact/send', [App\Http\Controllers\Web\Contact\ContactController::class, 'sendContactEmail'])
    ->name('pages.contact.send');

Route::get('support/{slug?}', [App\Http\Controllers\Web\Support\SupportController::class, 'render'])
    ->name('support');

/**
 * Courses Routes
 */
Route::get('courses', [App\Http\Controllers\Web\Courses\CourseController::class, 'list']);
Route::get('courses/detail/{course_id}', [App\Http\Controllers\Web\Courses\CourseController::class, 'details'])
    ->name('courses.detail');
Route::get('courses/schedules', [App\Http\Controllers\Web\Courses\CourseController::class, 'schedules']);

/**
 * Requires Email Verification
 */
//Route::middleware(['verified'])->group(function () {

Route::get('blog/{slug}', [App\Http\Controllers\Web\Blog\BlogController::class, 'details'])
    ->name('blog');


/**
 * Payment-related routes;
 * Mixed auth / no auth
 */
include 'order.php';
include 'payments.php';
include 'other.php';

//});


/**
 * Certificate Generation
 */
include 'certificates.php';

Route::post('services/error/log', [App\Http\Controllers\Web\Services\WebLogController::class, 'log'])
    ->name('services.error.log');


/**
 * Requires Auth and Email Verification
 */
Route::middleware(['auth'])->group(function () {
    Route::namespace('App\Http\Controllers\Services')->prefix('services')->group(function () {
        require base_path('routes/services_routes.php');
    });

    Route::namespace('App\Http\Controllers\Web')->group(function () {
        require base_path('routes/frontend/account_routes.php');
        require base_path('routes/frontend/frost_classroom_routes.php');
        require base_path('routes/frontend/frost_exam_routes.php');
    });

    /**
     * Zoom OAuth Routes
     */
    Route::get('zoom/authorize', [ZoomController::class, 'redirectToZoom'])->name('zoom.authorize');
    Route::get('zoom/callback', [ZoomController::class, 'handleZoomCallback'])->name('zoom.callback');


    // select RangeDate
    include 'frontend/range_date.php';

});

/**
 * Push to Admin Dashboard
 */
Route::redirect('admin', 'admin/dashboard')->name('admin');




Route::get('impersonate/account/leave', [App\Http\Controllers\HomeController::class, 'impersonateLeave'])
    ->name('impersonate.account.leave');

/**
 * Admin Routes
 * System Admin and below
 * Validate Admin Guards and Email is verified
 */
Route::middleware(['isinstructor'])->namespace('App\Http\Controllers\Admin')
    ->prefix('admin')->group(function () {

        Route::get('impersonate/account/user/{id}', [App\Http\Controllers\Admin\AdminCenter\CenterController::class, 'impersonate'])
            ->name('admin.impersonate.account.user');

        Route::get('/frost/data/', [App\Http\Controllers\React\LaravelSharedData::class, 'getLaraAdminData'])->name('admin.frost.data');
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

        require base_path('routes/admin/account_routes.php');
        require base_path('routes/admin/ac_routes.php');

        require base_path('routes/admin/instructor_routes.php');
        #Route::withoutMiddleware( 'throttle:api' )->group(function () { require 'admin/instructor_routes.php'; });
    
        require base_path('routes/admin/admin_user_routes.php');
        require base_path('routes/admin/student_routes.php');
        require base_path('routes/admin/course_routes.php');
        require base_path('routes/admin/order_routes.php');
        require base_path('routes/admin/report_routes.php');

        require base_path('routes/admin/support_routes.php');
        require base_path('routes/admin/student_status_routes.php');

        Route::post('services/search', [App\Http\Controllers\Admin\Services\AdminSearchController::class, 'search'])->name('admin.services.search');

        // jonesy temp routes
        require 'admin/temp.php';

    });


/**
 * DevOnly
 */
if (app()->isLocal()) {

    Route::get('reset_course_date/{course_date}', function (CourseDate $CourseDate) {
        echo "Resetting CourseDate:<br /><br />\n";
        App\Classes\ResetRecords::ResetCourseDate($CourseDate);
        return redirect()->back();
    });

    /*  jonesy testing  */
    include 'sattest.php';
}
