<?php
declare(strict_types=1);

namespace App\Classes\DOLRecords;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;


trait HelpersTrait
{


    protected function FormatTimestamp( int|string|Carbon $timestamp, string $fmt = 'YYYY-MM-DD HH:mm' ) : string
    {

        return Carbon::parse( $timestamp )
                        ->tz( 'America/New_York' )
                 ->isoFormat( $fmt );

    }


    protected function CourseStatus() : string
    {

        if ( $this->_CourseAuth->disabled_reason )
        {
            return $this->_CourseAuth->disabled_reason;
        }

        if ( $this->_CourseAuth->is_passed )
        {
            return 'Course Completed Succesfully on ' . $this->FormatTimestamp( $this->_CourseAuth->completed_at );
        }

        if ( $this->_CourseAuth->completed_at && ! $this->_CourseAuth->is_passed )
        {
            return 'Course Failed';
        }

        if ( $this->_CourseAuth->IsExpired() )
        {
            return 'This course authorization has expired.';
        }

        return 'Course Incomplete. Started: ' . $this->FormatTimestamp( $this->_CourseAuth->start_date, 'YYYY-MM-DD' );

    }


    protected function ExamAuths() : ?array
    {

        $exam_auths = [];

        if ( ! $ExamAuths = $this->_CourseAuth->ExamAuths->sortBy( 'created_at' ) )
        {
            return null;
        }

        foreach ( $ExamAuths as $ExamAuth )
        {

            if ( ! $ExamAuth->completed_at )
            {
                $exam_auths[] = $this->FormatTimestamp( $ExamAuth->created_at ) . ' Failed (Not completed)';
            }
            else
            {
                $exam_auths[] = $this->FormatTimestamp( $ExamAuth->completed_at )
                              . ' - ' . ( $ExamAuth->is_passed ? 'Passed' : 'Failed' )
                              . " - {$ExamAuth->score}";
            }

        }

        return $exam_auths;

    }


}
