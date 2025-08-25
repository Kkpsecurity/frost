<?php

use App\Http\Controllers\Web\SitePageController;
use App\Http\Controllers\Web\CoursesController;
use App\Http\Controllers\Web\ScheduleController;
use App\Http\Controllers\Web\EnrollmentController;
use App\Http\Controllers\BlogController;
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
 * Frontend Authentication Routes
 */
require __DIR__ . '/auth.routes.php';

/**
 * Redirect the root URL to the pages route.
 */
Route::redirect('/', '/pages', 302);

Route::match(['GET', 'POST'], '/pages/{page?}', [SitePageController::class, 'render'])
    ->name('pages');

/**
 * Student Classroom / React mount
 */
Route::prefix('classroom')->name('classroom.')->group(function () {
    // Student dashboard (will mount React student app)
    Route::get('/', [App\Http\Controllers\React\StudentPortalController::class, 'dashboard'])
        ->name('dashboard');

    // Legacy/portal routes used by the React app
    Route::get('/portal/class/{course_auth_id}', [App\Http\Controllers\React\StudentPortalController::class, 'RunPortal'])
        ->name('portal.class');
});

// Courses routes
Route::get('/courses', [CoursesController::class, 'index'])
    ->name('courses.index');
Route::get('/courses/schedules', [ScheduleController::class, 'index'])
    ->name('courses.schedules');
Route::get('/courses/{slug}', [CoursesController::class, 'show'])
    ->name('courses.show');

// Course enrollment route
Route::post('/enroll/{course}', [App\Http\Controllers\Web\EnrollmentController::class, 'AutoPayFlowPro'])
    ->name('enroll')
    ->middleware('auth');

// Blog routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/list', [BlogController::class, 'list'])->name('list');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/category/{category}', [BlogController::class, 'category'])->name('category');
    Route::get('/tag/{tag}', [BlogController::class, 'tag'])->name('tag');
    Route::get('/archive/{year}/{month?}', [BlogController::class, 'archive'])->name('archive');
    Route::post('/subscribe', [BlogController::class, 'subscribe'])->name('subscribe');
    Route::get('/rss', [BlogController::class, 'rss'])->name('rss');
    Route::get('/sitemap', [BlogController::class, 'sitemap'])->name('sitemap');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// Alternative blog routes for menu compatibility
Route::get('/blogs/list', [BlogController::class, 'list'])->name('blogs.list');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
