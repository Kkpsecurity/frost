<?php

use App\Http\Controllers\Web\SitePageController;
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
 * Redirect the root URL to the pages route.
 */
Route::redirect('/', '/pages', 302);

Route::match(['GET', 'POST'], '/pages/{page?}', [SitePageController::class, 'render']);
