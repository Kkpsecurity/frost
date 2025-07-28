<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Frontend Routes for Frost Theme
Route::get('/', function () {
    return view('frontend.home');
})->name('home');

Route::get('/about', function () {
    return view('frontend.about');
})->name('about');

Route::get('/contact', function () {
    return view('frontend.contact');
})->name('contact');

Route::get('/team', function () {
    return view('frontend.team');
})->name('team');

Route::get('/faq', function () {
    return view('frontend.faq');
})->name('faq');

Route::get('/pricing', function () {
    return view('frontend.pricing');
})->name('pricing');

Route::get('/reviews', function () {
    return view('frontend.reviews');
})->name('reviews');

Route::get('/terms', function () {
    return view('frontend.terms');
})->name('terms');

Route::get('/blog', function () {
    return view('frontend.blog.index');
})->name('blog');

Route::get('/blog/sidebar', function () {
    return view('frontend.blog.sidebar');
})->name('blog.sidebar');

// User Dashboard and Profile Routes (using Blade for consistency)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('frontend.user.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include Laravel Breeze authentication routes
require __DIR__.'/auth.php';

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest:admin')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login']);

        // Password Reset Routes
        Route::get('password/reset', [App\Http\Controllers\Admin\AdminPasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [App\Http\Controllers\Admin\AdminPasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [App\Http\Controllers\Admin\AdminPasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [App\Http\Controllers\Admin\AdminPasswordResetController::class, 'reset'])->name('password.update');
    });

    Route::post('logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])
        ->middleware('auth:admin')
        ->name('logout');
});

// AdminLTE Admin routes - use auth:admin guard specifically
Route::prefix('admin')->name('admin.')->middleware(['auth:admin', 'admin'])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard.alt');

    // Admin Profile/Password Management
    Route::get('/profile/change-password', [App\Http\Controllers\Admin\AdminProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::post('/profile/update-password', [App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('profile.update-password');
});
