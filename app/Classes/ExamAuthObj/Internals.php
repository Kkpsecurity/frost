<?php
declare(strict_types=1);

namespace App\Classes\ExamAuthObj;

use App\Models\CourseAuth;
use App\Models\Exam;
use App\Models\ExamAuth;


trait Internals
{


    protected function _LoadAll() : self
    {

        return $this->_LoadCourseAuth()
                    ->_LoadCourse()
                    ->_LoadExam()
                    ->_LoadExamAuths()
                    ->_LoadExamQuestions();

    }


    //
    // in order
    //


    protected function _LoadCourseAuth() : self
    {

        if ( ! $this->CourseAuth )
        {
            $this->CourseAuth = $this->ExamAuth->CourseAuth;
        }

        return $this;

    }

    protected function _LoadCourse() : self
    {

        if ( ! $this->Course )
        {
            $this->Course = $this->CourseAuth->Course;
        }

        return $this;

    }

    protected function _LoadExam() : self
    {

        if ( ! $this->Exam )
        {
            $this->Exam = $this->Course->Exam;
        }

        return $this;

    }

	protected function _LoadExamAuths( bool $force_reload = false ) : self
	{

        if ( $force_reload )
        {
            kkpdebug( 'ExamAuthObj', 'Reloading ExamAuths' );
            $this->CourseAuth->refresh();
            $this->ExamAuths = $this->CourseAuth->ExamAuths;
            return $this;
        }

        if ( ! $this->ExamAuths )
        {
            $this->ExamAuths = $this->CourseAuth->ExamAuths;
        }

        return $this;

	}

    protected function _LoadExamQuestions() : self
    {

        if ( ! $this->ExamQuestions )
        {
            $this->ExamQuestions = $this->ExamAuth->ExamQuestions();
        }

        return $this;

    }

}
