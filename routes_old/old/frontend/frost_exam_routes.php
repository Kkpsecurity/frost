<?php
use App\Helpers\DevHelpers;


// Route::controller( ExamAuthController::class )
//     ->prefix( '/classroom/exam' )->name( 'classroom.exam' )->group(function () {

//         Route::get(  '/{exam_auth}',             'index'         )->name( '' );
//         Route::post( '/score/{exam_auth}',       'ScoreExam'     )->name( '.score' );
//         Route::get(  '/authorize/{course_auth}', 'AuthorizeExam' )->name( '.authorize' );

// });


/*
 *
 * NOTE:
 *   Don't change 'classroom.exam' names without updating \App\Classes\ExamRedirector
 *
 */


Route::get('/classroom/exam/{exam_auth:uuid}',
[App\Http\Controllers\Web\ExamAuthController::class, 'index'])
    ->name('classroom.exam');

Route::get('/classroom/exam/authorize/{course_auth}/{acknowledged?}',
[App\Http\Controllers\Web\ExamAuthController::class, 'AuthorizeExam'])
    ->name('classroom.exam.authorize');

Route::post('/classroom/exam/score/{exam_auth:uuid}',
[App\Http\Controllers\Web\ExamAuthController::class, 'ScoreExam'])
    ->name('classroom.exam.score');

Route::get('/classroom/exam/reset', function() {
    DevHelpers::CourseAuthReset();
});
