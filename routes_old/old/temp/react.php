<?php

// use Illuminate\Support\Facades\Route;


// //
// // devel tools
// //

// Route::get( 'gen_inst_unit', function() {
//     $CourseDate = \App\Models\CourseDate::where( 'starts_at', '<=', date('r') )
//                                         ->where( 'ends_at',   '>=', date('r') )
//                                   ->firstOrFail();

//     if ( ! $InstUnit = InstUnit::where( 'course_date_id', $CourseDate->id )->first() )
//     {
//         $InstUnit = \App\Models\InstUnit::create([
//             'course_date_id' => $CourseDate->id,
//             'created_by'     => \App\RCache::Admins()->firstWhere( 'email', 'pacejf@s2institute.com' )->id
//         ])->refresh();
//     }


// });
