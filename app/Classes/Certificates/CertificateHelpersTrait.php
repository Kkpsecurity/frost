<?php
declare(strict_types=1);

namespace App\Classes\Certificates;

use Auth;

use App\RCache;
use App\Models\StudentUnit;
use App\Models\User;


trait CertificateHelpersTrait
{


    protected function _LoadDefines() : void
    {

        //
        // convert to paths
        //
        $this->_fontpath = storage_path( $this->_fontpath );
        $this->_imgpath  = storage_path( $this->_imgpath  );

    }


    protected function _ValidCourseAuth() : ?string
    {

        if ( Auth::id() == 1 )
        {
            return null;
        }


        if ( ! $this->_CourseAuth->completed_at )
        {
            return 'Course not completed';
        }

        if ( ! $this->_CourseAuth->is_passed )
        {
            return 'Course is Failed';
        }

        if ( $this->_CourseAuth->disabled_at )
        {
            return "CourseAuth disabled; reason: {$this->CourseAuth->disabled_reason}";
        }

        if ( $this->_User->IsStudent() && $this->_CourseAuth->user_id != $this->_User->id )
        {
            return 'You do not own this CourseAuth';
        }

        return null;

    }


    //
    // find final Instructor by last StudentUnit
    //


    protected function _FinalInstructor() : User
    {

        $StudentUnit = StudentUnit::where( 'course_auth_id', $this->_CourseAuth->id )
                                ->orderBy( 'completed_at', 'DESC' )
                                   ->with( 'InstUnit' )
                                    ->get()
                                   ->last();

        abort_unless( $StudentUnit, 500, 'Could not find final Instructor' );

        return $StudentUnit->InstUnit->GetCompletedBy();

    }


    protected function _InstructorSigFile( User $Instructor ) : string
    {

        $sigfile = $this->_imgpath . "{$Instructor->id}.png";

        if ( ! file_exists( $sigfile ) )
        {
            logger( "Missing Instructor Signature File: {$sigfile}" );
            abort( 500, 'Missing Instructor Signature file' );
        }

        return $sigfile;

    }


}
