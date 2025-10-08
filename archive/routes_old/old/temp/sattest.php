<?php

use Illuminate\Routing\Middleware\ThrottleRequests;

use App\Classes\VideoCallRequest;
use App\Models\Course;
use App\Models\CourseDate;
use App\Models\ExamAuth;
use App\Models\User;



Route::middleware( 'issysadmin' )->withoutMiddleware( ThrottleRequests::class )
    ->name( 'sattest' )->prefix( '/sattest' )->group( function() {


    Route::get( '/', function() {

        require base_path( '/sat/_sat_helpers.php' );
        require base_path( '/sat/SATTest.php' );
        return ( new SATTest )->Display();

    });

});
