<?php

namespace App\Http\Controllers\Admin\Temp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use RCache;
use App\Models\CourseAuth;
use App\Models\DiscountCode;
use App\Models\Order;


class DiscountCodeController extends Controller
{

    use PageMetaDataTrait;



    public function index()
    {
    }


    public function Clients()
    {

        $view = 'admin.temp.discount_codes_clients';
        $content = array_merge([

        ], self::renderPageMeta($view));


        $DiscountCodes = DiscountCode::whereNotNull( 'client' )
                                     ->where( 'set_price', '0.00' )
                                     ->orderBy( 'client' )
                                     ->orderBy( 'course_id' )
                                     ->with( 'Orders' )
                                     ->get();

        foreach ( $DiscountCodes as $DiscountCode )
        {

            $DiscountCode->CourseAuths =
                CourseAuth::whereIn( 'id', $DiscountCode->Orders->pluck( 'course_auth_id' ) )
                             ->with( 'User' )
                              ->get()
                           ->sortBy( 'user.fname', SORT_NATURAL | SORT_FLAG_CASE )
                           ->sortBy( 'user.lname', SORT_NATURAL | SORT_FLAG_CASE );

        }

        $course_lessons_counts = RCache::CourseLessonCounts();

        return view($view, compact([ 'content', 'DiscountCodes', 'course_lessons_counts' ]));

    }


}
