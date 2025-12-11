<?php
declare(strict_types=1);

namespace App\Classes\DOLRecords;

use Illuminate\Support\Collection;

use App\Models\StudentLesson;
use App\Models\StudentUnit;


trait LessonsTrait
{


    protected function CourseUnitLessons() : self
    {

        $this->_curr_y += 4;


        $AllStudentLessons = $this->GetStudentLessons();

        foreach ( $this->_Course->GetCourseUnits() as $CourseUnit )
        {

            foreach ( $CourseUnit->GetLessons() as $Lesson )
            {

                $this->_PDF->SetY( $this->_curr_y );
                $this->_PDF->SetFont( 'Helvetica', 'B', 10 );
                $this->_PDF->SetX( $this->_left_x_start );
                $this->_PDF->Cell( 0, 5, "{$CourseUnit->title} :: {$Lesson->title}", $this->_showborders, 0 );
                $this->_curr_y += 2;

                //
                //
                //

                $StudentLessons = $AllStudentLessons->where( 'StudentUnit.course_unit_id', $CourseUnit->id )
                                                    ->where( 'lesson_id', $Lesson->id );

                if ( $StudentLessons->count() )
                {

                    // add header?

                    foreach ( $StudentLessons as $StudentLesson )
                    {

                        $this->_curr_y += 4;

                        $this->_PDF->SetY( $this->_curr_y );
                        $this->_PDF->SetFont( 'Helvetica', '', 10 );

                        $this->_PDF->SetX( 15 );
                        $this->_PDF->Cell( 45, 4, 'Started: ' . $this->FormatTimestamp( $StudentLesson->created_at ), $this->_showborders, 0 );

                        if ( $StudentLesson->completed_at )
                        {

                            $this->_PDF->SetX( 60 );
                            $this->_PDF->Cell( 50, 4, 'Completed: ' . $this->FormatTimestamp( $StudentLesson->completed_at ), $this->_showborders, 0 );

                            $this->_PDF->SetX( 110 );
                            $this->_PDF->Cell( 0, 4, 'Instructor: ' . $StudentLesson->InstLesson->GetInstructor(), $this->_showborders, 0 );

                        }
                        else if ( $StudentLesson->dnc_at )
                        {

                            $this->_PDF->SetX( 60 );
                            $this->_PDF->SetFont( 'Helvetica', 'I', 10 );
                            $this->_PDF->Cell( 0, 4, 'This Lesson was marked Did Not Complete by the system.' );
                            $this->_PDF->SetFont( 'Helvetica', '', 10 );

                        }
                        else
                        {

                            $this->_PDF->SetX( 60 );
                            $this->_PDF->SetFont( 'Helvetica', 'I', 10 );
                            $this->_PDF->Cell( 0, 4, 'Lesson Incomplete' );
                            $this->_PDF->SetFont( 'Helvetica', '', 10 );

                        }

                    }

                }

                // spacer
                $this->_curr_y += 5;

            }

        }

        return $this;

    }



    //
    //
    //


    protected function GetStudentLessons() : Collection
    {

        return StudentLesson::whereIn( 'student_unit_id',
                   StudentUnit::where( 'course_auth_id', $this->_CourseAuth->id )->pluck( 'id' )
               )
               ->orderBy( 'created_at' )
               ->with( 'StudentUnit' )
               ->get();

    }


}
