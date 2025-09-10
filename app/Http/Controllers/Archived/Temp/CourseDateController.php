<?php

namespace App\Http\Controllers\Admin\Temp;

use DB;
#use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use RCache;
use App\Models\Course;
use App\Models\CourseDate;
#use KKP\Laravel\PgTk;


class CourseDateController extends Controller
{

    use PageMetaDataTrait;


    public function index( ?Course $Course = null )
    {

        if ( ! $Course )
        {
            $Course = RCache::Courses( 1 );
        }

        $view    = 'admin.temp.course_dates';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $CourseDates = $this->_CourseDates( $Course );
        $dates       = $this->_ParseDates( $CourseDates );
        $first_date  = Carbon::parse( $CourseDates->first()->starts_at )->startOfWeek()->subDays( 1 ); // make it Sunday
        $last_date   = Carbon::parse( $CourseDates->last()->starts_at  )->endOfWeek()->subDays( 1 );   // make it Saturday

        return view( $view, compact([ 'content', 'Course', 'dates', 'first_date', 'last_date' ]) );

    }


    public function ToggleActive( CourseDate $CourseDate )
    {

        $CourseDate->update([
            'is_active' => ! $CourseDate->is_active
        ]);

        return response()->json([
            'is_active' => $CourseDate->is_active
        ]);

    }


    //
    //
    //


    protected function _CourseDates( Course $Course ) : Collection
    {

        return CourseDate::whereIn( 'course_unit_id', RCache::Courses( $Course->id )->GetCourseUnits()->pluck( 'id' ) )
                           ->where( 'starts_at', '>=', date( 'Y-m-01' ) )
                         ->orderBy( 'starts_at' )
                             ->get();

    }


    protected function _ParseDates( Collection $CourseDates ) : Collection
    {

        $dates = collect([]);

        foreach ( $CourseDates as $CourseDate )
        {

            $starts_at = Carbon::parse( $CourseDate->starts_at );

            $dates->put( $starts_at->isoFormat( 'YYYY-MM-DD' ), (object) [
                'is_active' => $CourseDate->is_active,
                'month'     => $starts_at->month,
                'route'     => route( 'admin.temp.course_dates.toggleactive', $CourseDate->id ),
            ]);

        }

        return $dates;

    }

}
