<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
|
| Here are the routes that handle the frontend application sections.
| Each section is organized in its own file for better maintainability.
|
*/

/**
 * Frontend Authentication Routes
 */
require __DIR__ . '/auth.routes.php';

/**
 * Student Section Routes
 */
require __DIR__ . '/frontend/student.php';

/**
 * Courses Section Routes  
 */
require __DIR__ . '/frontend/courses.php';

/**
 * Blog Section Routes
 */
require __DIR__ . '/frontend/blog.php';

/**
 * General Frontend Routes
 */
require __DIR__ . '/frontend/general.php';