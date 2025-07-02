<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::namespace( '\App\Http\Controllers\Admin\Temp' )
     ->prefix( 'temp')->name( 'admin.temp' )->group(function () {


    Route::controller( CourseAuthController::class )
         ->prefix( '/course_auths' )->name( '.course_auths' )->group(function () {

        Route::get(  '/',              'index' )->name( '' );
        Route::get(  '/{course_auth}', 'Show'  )->name( '.course_auth' );

        Route::post( '/{course_auth}/update_student_info', 'UpdateStudentInfo' )->name( '.update_student_info' );

        Route::post( '/{course_auth}/mark_admin_exam_auth',                   'MarkAdminExamAuth'   )->name( '.mark_admin_exam_auth'  );
        Route::post( '/{course_auth}/mark_lesson_completed/{student_lesson}', 'MarkLessonCompleted' )->name( '.mark_lesson_completed' );

    });


    Route::controller( CourseDateController::class )
         ->prefix( '/course_dates' )->name( '.course_dates' )->group(function () {

        Route::get(  '/{course?}', 'index' )->name( '' );

        Route::post( '/{course_date}/toggleactive', 'ToggleActive' )->name( '.toggleactive' );


    });


    Route::controller( CompletedCourseController::class )
         ->prefix( '/completed_course_auths' )->name( '.completed_course_auths' )->group(function () {

        Route::get(  '/',                           'index'         )->name( '' );
        Route::get(  '/{course_auth}',              'Show'          )->name( '.course_auth' );
        Route::post( '/{course_auth}/update',       'Update'        )->name( '.update' );
        Route::post( '/{course_auth}/setrangedate', 'SetRangeDate'  )->name( '.setrangedate' );

    });


    Route::controller( DiscountCodeController::class )
         ->prefix( '/discount_codes' )->name( '.discount_codes' )->group(function () {

        Route::get(  '/',       'index'   )->name( '' );
        Route::get( '/clients', 'Clients' )->name( '.clients' );

    });


    Route::controller( OrderController::class )
         ->prefix( '/orders' )->name( '.orders' )->group(function () {

        Route::get(  '/',               'index'  )->name( '' );

        Route::post( '/search_id',      'SearchID'   )->name( '.search_id' );
        Route::post( '/search_name',    'SearchName' )->name( '.search_name' );

        Route::get(  '/{order}/view',   'Show'   )->name( '.show' );
        Route::post( '/{order}/refund', 'Refund' )->name( '.refund' );

        Route::get(  '/{order}/getsaledetails', 'GetSaleDetails'         )->name( '.getsaledetails' );
        Route::get(  '/getmissingorderdetails', 'GetMissingOrderDetails' )->name( '.getmissingorderdetails' );

        Route::post( '/{order}/reactivatecourseauth', 'ReactivateCourseAuth' )->name( '.reactivatecourseauth' );

        Route::get(  '/csv/query', 'CSVQuery' )->name( '.csv.query' );
        Route::post( '/csv/dump',  'CSVDump'  )->name( '.csv.dump'  );

    });


    Route::controller( RangeController::class )
         ->prefix( '/ranges' )->name( '.ranges' )->group(function () {

        Route::get(  '/',               'index'  )->name( '' );
        Route::get(  '/{range}/view',   'Show'   )->name( '.show' );
        Route::post( '/{range}/update', 'Update' )->name( '.update' );

        Route::post( '/{range}/update/times', 'UpdateTimes' )->name( '.update.times' );
        Route::post( '/{range}/update/price', 'UpdatePrice' )->name( '.update.price' );

        Route::get(  '/{range}/dates',    'ShowDates' )->name( '.showdates' );
        Route::post( '/{range}/adddates', 'AddDates'  )->name( '.adddates'  );

        Route::post( '/range/{range}/toggleactive',          'ToggleRangeActive'     )->name( '.range.toggleactive' );
        Route::post( '/rangedate/{range_date}/toggleactive', 'ToggleRangeDateActive' )->name( '.rangedate.toggleactive' );


        Route::middleware([ 'issysadmin' ])->group(function () {

            Route::get(  '/create', 'Create' )->name( '.create' );
            Route::post( '/store',  'Store'  )->name( '.store'  );

            Route::post( '/{range}/updaterangehtml', 'UpdateRangeHTML' )->name( '.updaterangehtml' );

        });


    });


    //
    // sysadmin only
    //


    Route::middleware([ 'issysadmin' ])->group(function () {

        Route::controller( SiteConfigController::class )
             ->prefix( '/site_configs' )->name( '.site_configs' )->group(function () {

            Route::get(  '/',                     'index'  )->name( '' );
            Route::post( '/{site_config}/update', 'Update' )->name( '.update' );

        });

    });


    /*
    Route::controller( ReportController::class )
        ->prefix( '/reports' )->name( '.reports' )->group(function () {

        Route::get( '/exams', 'ExamIndex' )->name( '.exams' );

    });
    */


});
