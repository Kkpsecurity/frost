<?php

namespace App\Observers;

use Exception;
use Illuminate\Support\Carbon;

use App\Models\ExamAuth;


class ExamAuthObserver
{


    public function creating(ExamAuth $ExamAuth)
    {

        if (! $ExamAuth->created_at) {
            $ExamAuth->created_at = Carbon::now();
        }

        // Don't set expires_at yet - timer starts when user clicks "Begin Exam" on acknowledgement screen
        // Set next_attempt_at to now (no cooldown for new exams)
        // $ExamAuth->expires_at      = $ExamAuth->MakeExpiresAt();
        $ExamAuth->next_attempt_at = Carbon::now();

        if (! $ExamAuth->question_ids) {
            kkpdebug('Observer', 'Adding question_ids to ExamAuth');
            $ExamAuth->question_ids = $ExamAuth->RandomQuestionIDs();
        }
    }
}
