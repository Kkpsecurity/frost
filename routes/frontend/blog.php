<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
|
| Routes for blog functionality, posts, categories, and RSS feeds
|
*/

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
    Route::get('/{blogPost:slug}', [BlogController::class, 'show'])->name('show');
    Route::post('/{blogPost:slug}', [BlogController::class, 'show']); // For AJAX view increments
});

// Alternative blog routes for menu compatibility
Route::get('/blogs/list', [BlogController::class, 'list'])->name('blogs.list');
Route::get('/blogs/{blogPost:slug}', [BlogController::class, 'show'])->name('blogs.show');
