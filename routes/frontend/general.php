<?php

use App\Http\Controllers\Web\SitePageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| General Frontend Routes
|--------------------------------------------------------------------------
|
| General site routes, pages, contact forms, and other frontend functionality
|
*/

/**
 * Redirect the root URL to the pages route.
 */
Route::redirect('/', '/pages', 302);

Route::match(['GET', 'POST'], '/pages/{page?}', [SitePageController::class, 'render'])
    ->name('pages');

// Contact form submission route
Route::post('/contact/send', [SitePageController::class, 'sendContactEmail'])
    ->name('contact.send');
