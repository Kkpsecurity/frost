<?php

namespace App\Http\Controllers\Web;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Traits\PageMetaDataTrait;

use App\Classes\ExamAuthObj;
use App\Models\CourseAuth;
use App\Models\ExamAuth;


class ExamAuthController extends Controller
{

    use PageMetaDataTrait;


    private $_display_route = 'classroom.exam';


    /**
     * Show Exam or the Result
     */
    public function index( ExamAuth $ExamAuth )
    {

        $ExamAuthObj = new ExamAuthObj( $ExamAuth );
        $content = array_merge([], self::renderPageMeta(('Exam Room')));

        if ( ! $ExamAuthObj->ValidateCanScore( false ) ) // false if completed / expired
        {
            return view( 'frontend.students.exam.result', compact([ 'content', 'ExamAuth' ]) );
        }

        return view( 'frontend.students.exam.view', compact([ 'content', 'ExamAuthObj' ]) );

    }


    /**
     * Show Exam or the Result
     */
    public function AuthorizeExam( CourseAuth $CourseAuth, $acknowledged = false ) : View|RedirectResponse
    {

        if ( ! $CourseAuth->IsActive() )
        {
            return redirect()->route( 'classroom.dashboard' );
        }


        //
        // already an active ExamAuth
        //

        if ( $ExamAuth = $CourseAuth->ActiveExamAuth() )
        {

            ( new ExamAuthObj( $ExamAuth ) )->ValidateCanScore( true ); // handle expired

            return redirect()->route( $this->_display_route, $ExamAuth );

        }


        //
        // student is not ready for next exam
        //

        if ( ! $CourseAuth->ExamReady() )
        {
            return redirect()->route( 'classroom.dashboard' );
        }


        //
        // student acknowledgement
        //

        if ( ! $acknowledged )
        {

            $content = array_merge([], self::renderPageMeta(('Exam Room')));
            $Exam = $CourseAuth->GetCourse()->GetExam();

            return view( 'frontend.students.exam.acknowledgement', compact([ 'content', 'CourseAuth', 'Exam' ]) );

        }


        //
        // do it
        //

        $ExamAuth = ExamAuth::create([
            'course_auth_id' => $CourseAuth->id,
        ])->refresh();


        return redirect()->route( $this->_display_route, $ExamAuth );

    }


    public function ScoreExam( Request $Request, ExamAuth $ExamAuth ): RedirectResponse
    {

        $ExamAuthObj = new ExamAuthObj( $ExamAuth );

        if ( $ExamAuthObj->ValidateCanScore( true ) ) // false if completed / expired
        {
            $ExamAuthObj->Score( $Request );
        }

        return redirect()->route( $this->_display_route, $ExamAuth );

    }


}
