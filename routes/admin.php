<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\Frost\CourseDateController;
use App\Http\Controllers\Admin\AdminCenter\AdminUserController;

/**
 * Ensure Dashboard Route
 */
Route::redirect('/admin', '/admin/dashboard', 301);

Route::middleware(['auth:admin', 'admin', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Admin Dashboard
        Route::get('/', [
            AdminDashboardController::class,
            'dashboard'
        ])->name('dashboard');

        // Admin Center Routes
        Route::prefix('admin-center')
            ->name('admin-center.')
            ->group(function () {
            // Admin Users Management
            Route::prefix('admin-users')
                ->name('admin-users.')
                ->group(function () {

                // Admin Users Index
                Route::get('/', [
                    AdminUserController::class,
                    'index'
                ])->name('index');

                // Admin Users DataTables Data (AJAX endpoint)
                Route::get('/data', [
                    AdminUserController::class,
                    'getData'
                ])->name('data');

                // Admin Users Create Form
                Route::get('/create', [
                    AdminUserController::class,
                    'create'
                ])->name('create');

                // Admin Users Store
                Route::post('/', [
                    AdminUserController::class,
                    'store'
                ])->name('store');

                // Admin Users Show
                Route::get('/{user}', [
                    AdminUserController::class,
                    'show'
                ])->name('show');

                // Admin Users Edit Form
                Route::get('/{user}/edit', [
                    AdminUserController::class,
                    'edit'
                ])->name('edit');

                // Admin Users Update
                Route::put('/{user}', [
                    AdminUserController::class,
                    'update'
                ])->name('update');

                // Admin Users Delete
                Route::delete('/{user}', [
                    AdminUserController::class,
                    'destroy'
                ])->name('destroy');

                // Admin Users Deactivate
                Route::post('/{user}/deactivate', [
                    AdminUserController::class,
                    'deactivate'
                ])->name('deactivate');

                // Admin Users Activate
                Route::post('/{user}/activate', [
                    AdminUserController::class,
                    'activate'
                ])->name('activate');

                // Admin Users Password Update
                Route::post('/{user}/password', [
                    AdminUserController::class,
                    'updatePassword'
                ])->name('password');

                // Admin Users Avatar Update
                Route::post('/{user}/avatar', [
                    AdminUserController::class,
                    'updateAvatar'
                ])->name('avatar');

            });
        });

        // Impersonate Routes (outside admin-center but within admin)
        Route::prefix('impersonate')
            ->name('impersonate.')
            ->group(function () {

            // Start impersonating a user
            Route::get('/{user}', [
                AdminUserController::class,
                'impersonate'
            ])->name('start');

            // Stop impersonating and return to original user
            Route::get('/stop', [
                AdminUserController::class,
                'stopImpersonating'
            ])->name('stop');

        });

        // Add a simple route for backwards compatibility
        Route::get('/impersonate/{user}', [
            AdminUserController::class,
            'impersonate'
        ])->name('impersonate');

        // Students Management Routes
        Route::prefix('students')
            ->name('students.')
            ->group(function () {

            // Students Index
            Route::get('/', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'index'
            ])->name('index');

            // Students DataTables Data (AJAX endpoint)
            Route::get('/data', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'getData'
            ])->name('data');

            // Students Create Form
            Route::get('/create', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'create'
            ])->name('create');

            // Students Store
            Route::post('/', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'store'
            ])->name('store');

            // Students Show
            Route::get('/{student}', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'show'
            ])->name('show');

            // Students Edit Form
            Route::get('/{student}/edit', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'edit'
            ])->name('edit');

            // Students Update
            Route::put('/{student}', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'update'
            ])->name('update');

            // Students Delete
            Route::delete('/{student}', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'destroy'
            ])->name('destroy');

            // Student Details API endpoint
            Route::get('/details/{studentId}', [
                \App\Http\Controllers\Admin\Students\StudentController::class,
                'getStudentDetails'
            ])->name('details');

        });

        // Settings Routes
        require __DIR__ . '/admin/settings.php';

        // Media Manager Routes
        require __DIR__ . '/admin/media.php';

        // Messaging Routes
        require __DIR__ . '/admin/messaging.php';

        // Courses Routes
        require __DIR__ . '/admin/courses.php';

        // Lessons Routes
        require __DIR__ . '/admin/lessons.php';

        // Course Dates Routes
        require __DIR__ . '/admin/course-dates.php';

        // Orders Routes
        require __DIR__ . '/admin/orders.php';

        // Support Routes
        require __DIR__ . '/admin/support.php';

        // Reports Routes
        require __DIR__ . '/admin/reports.php';

        // Instructor Routes
        require __DIR__ . '/instrcutors/instructor.route.php';

        // Admin Center Dashboard
        Route::get('/courses/course_dates', [
            CourseDateController::class,
            'index'
        ])->name('courses.courses_dates');

        Route::prefix('notifications')
            ->name('notifications.')
            ->group(function () {
                // Student Dashboard
                require __DIR__ . '/frontend/classroom.routes.php';
            });
    });
