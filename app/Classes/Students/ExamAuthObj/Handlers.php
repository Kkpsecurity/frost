<?php

declare(strict_types=1);

namespace App\Classes\Students\ExamAuthObj;

use KKP\Laravel\PgTk;

// Exam Events
use App\Events\Exam\ExamCompleted;

// Progress Events
use App\Events\Progress\CourseCompleted;


trait Handlers
{


    public function SetExpired(): self
    {

        kkpdebug('ExamAuthObj', 'SetExpired()');

        $this->ExamAuth->forceFill([

            'completed_at'  => PgTk::now(),
            'score'         => $this->ExamAuth::EXPIRED_SCORE,
            'is_passed'     => false,

        ])->update();

        $this->ExamAuth->refresh();

        // Dispatch event (listener will handle ExamExpiredNotification)
        event(new ExamCompleted($this->ExamAuth));

        return $this->_handleFailed();
    }


    protected function _handlePassed(): self
    {

        kkpdebug('ExamAuthObj', '_handlePassed()');

        $this->CourseAuth->MarkCompleted(true);

        // Notify student: course passed + certificate ready
        event(new CourseCompleted($this->CourseAuth, true));

        return $this;
    }


    protected function _handleFailed(): self
    {

        if (! $this->_ExceededPolicyAttempts()) {
            kkpdebug('ExamAuthObj', '_handleFailed :: Inside policy_attempts');
            return $this;
        }


        //
        // student has failed course
        //

        kkpdebug('ExamAuthObj', "_handleFailed :: Marking CourseAuth Failed :: Student Failed too many Exams ({$this->ExamAuths->count()})");

        $this->CourseAuth->MarkCompleted(false);

        // Notify student: course failed
        event(new CourseCompleted($this->CourseAuth, false));

        return $this;
    }



    protected function _ExceededPolicyAttempts(): bool
    {

        if (! $this->Exam->policy_attempts) {
            return false;
        }

        $this->_LoadExamAuths(true); // reload

        return $this->ExamAuths->whereNotNull('completed_at')
            ->whereNull('hidden_at')
            ->count() >= $this->Exam->policy_attempts;
    }
}
