<?php

namespace App\Http\Controllers\Admin\Temp;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use App\RCache;
use App\Http\Controllers\Admin\Temp\Traits\ValidateStudentInfoTrait;
use App\Models\CourseAuth;
use App\Models\StudentLesson;
use App\Models\StudentUnit;
use KKP\Laravel\PgTk;


class CourseAuthController extends Controller
{

    use PageMetaDataTrait;
    use ValidateStudentInfoTrait;


    public function index()
    {


        $view    = 'admin.temp.course_auths';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $ActiveCourseAuths    = $this->ActiveCourseAuths();
        $CompletedCourseAuths = $this->CompletedCourseAuths();

        return view( $view, compact([ 'content', 'ActiveCourseAuths', 'CompletedCourseAuths' ]) );

    }


    public function Show( CourseAuth $CourseAuth )
    {

        $view    = 'admin.temp.course_auth';
        $widgets = scandir(resource_path('views/admin/plugins/widgets/dashboard'));
        $content = array_merge([
            'widgets' => $widgets,
        ], self::renderPageMeta( $view ));


        $StudentLessons = $this->StudentLessons( $CourseAuth );
        $student_info   = $this->ValidateStudentInfo( $CourseAuth->GetUser() );

        return view( $view, compact([ 'content', 'CourseAuth', 'StudentLessons', 'student_info' ]) );

    }


    //
    // update record(s)
    //


    public function UpdateStudentInfo( Request $Request, CourseAuth $CourseAuth )
    {

        $CourseAuth->User->student_info = [
            'fname'     => $Request->input( 'fname'   ),
            'initial'   => $Request->input( 'initial' ),
            'lname'     => $Request->input( 'lname'   ),
            'suffix'    => $Request->input( 'suffix'  ),
            'dob'       => $Request->input( 'dob'     ),
            'phone'     => $Request->input( 'phone'   ),
        ];

        $CourseAuth->User->save();

        return back()->with( 'success', 'Updated Student DOL Info' );

    }


    public function MarkAdminExamAuth( Request $Request, CourseAuth $CourseAuth )
    {

        if ( ! $admin_user_id = $Request->input( 'admin_user_id' ) )
        {
            abort( 500, 'Missing admin_user_id' );
        }

        if ( ! RCache::Admins()->where( 'id', $admin_user_id ) )
        {
            abort( 500, 'Invalid admin_user_id' );
        }

        $CourseAuth->update([ 'exam_admin_id' => $admin_user_id ]);

        return back()->with( 'success', 'Marked Exam Authorized By Admin' );

    }


    public function MarkLessonCompleted( CourseAuth $CourseAuth, StudentLesson $StudentLesson )
    {

        $StudentLesson->update([
            'dnc_at'        => null,
            'completed_at'  => Carbon::parse( $StudentLesson->InstLesson->completed_at )
        ]);

        return back()->with( 'success', 'Lesson Marked Completed' );

    }


    //
    // queries
    //


    public function ActiveCourseAuths() : Collection
    {
        return CourseAuth::whereNull( 'completed_at' )
                      ->whereNotNull( 'start_date' )
                         ->whereNull( 'disabled_at' )
                              ->with( 'User' )
                               ->get()
                             ->where( 'User.role_id', RCache::RoleID( 'Student' ) )
                            ->sortBy( 'User.fname', SORT_NATURAL | SORT_FLAG_CASE  )
                            ->sortBy( 'User.lname', SORT_NATURAL | SORT_FLAG_CASE  );
    }


    public function CompletedCourseAuths() : Collection
    {
        return CourseAuth::whereNotNull( 'completed_at' )
                                 ->with( 'User' )
                                  ->get()
                                ->where( 'User.role_id', RCache::RoleID( 'Student' ) )
                               ->sortBy( 'User.fname', SORT_NATURAL | SORT_FLAG_CASE  )
                               ->sortBy( 'User.lname', SORT_NATURAL | SORT_FLAG_CASE  );
    }


    public function StudentLessons( CourseAuth $CourseAuth ) : Collection
    {

        return StudentLesson::whereIn( 'student_unit_id',
                   StudentUnit::where( 'course_auth_id', $CourseAuth->id )->pluck( 'id' )
               )
               ->orderBy( 'created_at' )
               ->get();

    }


}
