<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;

//
// this is include()d in RouteServiceProvider.php
//


//
// patterns
//

Route::pattern('id',     '^\d*$'); // only digits
#Route::pattern( 'base64', config( 'define.regex.base64' ) );
Route::pattern('uuid',   config('define.regex.uuidv4'));


//
// auto resolve
//

#Route::bind( 'base64', function( $base64 ) { return base64_decode( $base64 ); });



//
// RCache binding overrides
//

$rcache_bindings = [

    'course'             => 'RCache::Courses',
    'course_unit'        => 'RCache::CourseUnits',
    'course_unit_lesson' => 'RCache::CourseUnitLessons',
    #'discount_code'      => 'RCache::DiscountCodes',  // can't use UUIDs
    'exam'               => 'RCache::Exams',
    'exam_question_spec' => 'RCache::ExamQuestionSpecs',
    'lesson'             => 'RCache::Lessons',
    'role'               => 'RCache::Roles',
    'site_config'        => 'RCache::SiteConfigs',
    'user'               => 'RCache::User',

];


foreach ($rcache_bindings as $id => $method) {
    Route::bind($id, function ($id) use ($method) {
        return call_user_func($method, $id);
    });
}
