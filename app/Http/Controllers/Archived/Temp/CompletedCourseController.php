<?php

namespace App\Http\Controllers\Admin\Temp;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use App\RCache;
use App\Helpers\RangeSelect;
use App\Http\Controllers\Admin\Temp\Traits\ValidateStudentInfoTrait;
use App\Models\CourseAuth;
use KKP\Laravel\PgTk;


class CompletedCourseController extends Controller
{

    use PageMetaDataTrait;
    use ValidateStudentInfoTrait;


    public function index()
    {

        $view    = 'admin.temp.completed_courses';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $DOLCourseAuths       = $this->_GetDOLCourseAuths();
        $RangeDateCourseAuths = $this->_GetRangeDateCourseAuths();
        $RangeOpts            = RangeSelect::MakeSelectOpts( RangeSelect::UpcomingRangeDates() );

        return view( $view, compact([ 'content', 'DOLCourseAuths', 'RangeDateCourseAuths', 'RangeOpts' ]) );

    }


    public function Show( Request $Request, CourseAuth $CourseAuth )
    {

        $view    = 'admin.temp.completed_course';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));

        $User         = $CourseAuth->GetUser();
        $ExamAuth     = $CourseAuth->LatestExamAuth;
        $Instructor   = $CourseAuth->LastInstructor();
        $student_info = $this->ValidateStudentInfo( $User );

        return view( $view, compact([ 'content', 'CourseAuth', 'ExamAuth', 'User', 'Instructor', 'student_info' ]) );

    }


    public function Update( Request $Request, CourseAuth $CourseAuth )
    {

        $dol_tracking = $Request->input( 'dol_tracking' );

        if ( strlen( $dol_tracking ) < 13 )
        {
            return back()->with( 'error', 'Invalid DOL Tracking Number' );
        }

        if ( CourseAuth::firstWhere( 'dol_tracking', $dol_tracking ) )
        {
            return back()->with( 'error', 'That DOL Tracking Number has already been registered.' );
        }


        $CourseAuth->update([

            'submitted_by'  => Auth::id(),
            'submitted_at'  => PgTk::now(),
            'dol_tracking'  => $Request->input( 'dol_tracking' ),

        ]);


        if ( $NextCourseAuth = $this->_GetDOLCourseAuths()->first() )
        {
            return redirect()->route( 'admin.temp.completed_course_auths.course_auth', $NextCourseAuth );
        }

        return redirect()->route( 'admin.temp.completed_course_auths' );

    }


    public function SetRangeDate( Request $Request, CourseAuth $CourseAuth )
    {

        if ( ! $range_date_id = $Request->input( 'range_date_id' ) )
        {
            return back()->with( 'error', 'Missing range_date_id' );
        }

        $CourseAuth->update([ 'range_date_id' => $range_date_id, ]);

        return back()->with( 'success', 'CourseAuth updated' );

    }


    //
    // queries
    //


    private function _GetDOLCourseAuths() : Collection
    {

        return CourseAuth::whereNotNull( 'completed_at' )
                                ->where( 'is_passed', true )
                            ->whereNull( 'submitted_at' )
                              ->orderBy( 'completed_at' )
                                  ->get();

    }


    private function _GetRangeDateCourseAuths() : Collection
    {

        return CourseAuth::whereNotNull( 'completed_at'  )
                            ->whereNull( 'range_date_id' )
                              ->whereIn( 'course_id', RCache::Courses()->where( 'needs_range', true )->pluck( 'id' ) )
                              ->orderBy( 'completed_at'  )
                                  ->get();

    }


}
