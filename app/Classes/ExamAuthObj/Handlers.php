<?php
declare(strict_types=1);

namespace App\Classes\ExamAuthObj;

use KKP\Laravel\PgTk;


trait Handlers
{


    public function SetExpired() : self
    {

        kkpdebug( 'ExamAuthObj', 'SetExpired()' );

        $this->ExamAuth->forceFill([

            'completed_at'  => PgTk::now(),
            'score'         => $this->ExamAuth::EXPIRED_SCORE,
            'is_passed'     => false,

        ])->update();

        $this->ExamAuth->refresh();

        return $this->_handleFailed();

    }


    protected function _handlePassed() : self
    {

        kkpdebug( 'ExamAuthObj', '_handlePassed()' );

        $this->CourseAuth->MarkCompleted( true );

        //
        // TODO: dispatch notifications
        //

        return $this;


    }


    protected function _handleFailed() : self
    {

        if ( ! $this->_ExceededPolicyAttempts() )
        {
            kkpdebug( 'ExamAuthObj', '_handleFailed :: Inside policy_attempts' );
            return $this;
        }


        //
        // student has failed course
        //

        kkpdebug( 'ExamAuthObj', "_handleFailed :: Marking CourseAuth Failed :: Student Failed too many Exams ({$this->ExamAuths->count()})" );

        $this->CourseAuth->MarkCompleted( false );


        //
        // TODO: dispatch notifications
        //

        return $this;

    }



    protected function _ExceededPolicyAttempts() : bool
    {

        if ( ! $this->Exam->policy_attempts )
        {
            return false;
        }

        $this->_LoadExamAuths( true ); // reload

        return $this->ExamAuths->whereNotNull( 'completed_at' )
                                  ->whereNull( 'hidden_at' )
                                      ->count() >= $this->Exam->policy_attempts;

    }


}
