<?php
declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

use stdClass;
use Illuminate\Support\Carbon;

use App\Classes\ExamAuthObj;
use App\Models\ExamAuth;


trait ExamsTrait
{


    public function LatestExamAuth()
    {

        return $this->hasOne( ExamAuth::class, 'course_auth_id' )
                 ->whereNull( 'hidden_at' )
                   ->orderBy( 'completed_at', 'DESC' );

    }


    public function ActiveExamAuth() : ?ExamAuth
    {

        if ( ! $ExamAuth = $this->LatestExamAuth )
        {
            return null;
        }

        // ValidateCanScore() handles expiration
        return ( new ExamAuthObj( $ExamAuth ) )->ValidateCanScore( true ) ? $ExamAuth : null;

    }


    public function ExamReady() : bool
    {

        if ( ! $this->IsActive() )
        {
            return false;
        }


        //
        // check last Exam
        //

        if ( $ExamAuth = $this->LatestExamAuth )
        {

            if ( $ExamAuth->is_passed )
            {
                // shouldn't get here
                return false;
            }

            return Carbon::now()->gt( Carbon::parse( $ExamAuth->next_attempt_at ) );

        }

        //
        // check Admin override
        //

        if ( $this->exam_admin_id )
        {
            return true;
        }

        //
        // check all lessons completed
        //

        if ( ! $this->AllLessonsCompleted() )
        {
            return false;
        }

        return true;

    }


    // TODO: remove this
    /*
    public function NextAttempt( string $fmt = null ) : ?string
    {

        if ( ! $this->IsActive() )
        {
            return null;
        }

        if ( ! $ExamAuth = $this->LatestExamAuth )
        {
            return null;
        }

        if ( $ExamAuth->is_passed )
        {
            return null;
        }

        return $ExamAuth->NextAttemptAt( $fmt );

    }
    */


    public function ClassroomExam( string $fmt = null ) : stdClass
    {

        $res = (object) [
            'is_ready'        => false,
            'next_attempt_at' => null,
            'missing_id_file' => false,
        ];



        //
        // don't do this if an Admin authorized an exam
        //

        //
        // TODO: replace hard-coded path
        //
        #if ( ! $this->exam_admin_id && ! $this->id_override && ! file_exists( storage_path( 'app/public/validations/idcards/' ) . $this->id . '.png' ) )



        /*** TODO: FIXME
        if (
               ! $this->exam_admin_id
            && ! $this->id_override
            && ! ( $this->Validation->IsValid() ?? false )
        )
        {
            $res->missing_id_file = true;
            return $res;
        }
        ***/


        //
        // this handles all initial checks
        //

        if ( $this->ExamReady() )
        {
            $res->is_ready = true;
            return $res;
        }


        //
        // verify there is a previous exam
        //

        if ( ! $ExamAuth = $this->LatestExamAuth )
        {
            return $res;
        }


        //
        // previous exam was passed; don't send next attempt
        //

        if ( ! $ExamAuth->is_passed )
        {
            $res->next_attempt_at = $ExamAuth->NextAttemptAt( $fmt );
        }


        return $res;

    }


}
