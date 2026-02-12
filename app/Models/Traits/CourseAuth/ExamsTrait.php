<?php

declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

/**
 * @file ExamsTrait.php
 * @brief Trait for managing exam-related functionality in course auth.
 * @details This trait provides methods to handle exam readiness, latest exam authorization,
 * and classroom exam checks for users enrolled in courses.
 */

use stdClass;
use Illuminate\Support\Carbon;

use App\Models\ExamAuth;
use App\Classes\ExamAuthObj;
use Illuminate\Support\Facades\Log;

trait ExamsTrait
{


    public function LatestExamAuth(): ?ExamAuth
    {
        return ExamAuth::where('course_auth_id', $this->id)
            ->whereNull('hidden_at')
            ->orderBy('completed_at', 'DESC')
            ->first();
    }


    public function ActiveExamAuth(): ?ExamAuth
    {
        $ExamAuth = $this->LatestExamAuth();

        if (! $ExamAuth) {
            return null;
        }

        // ValidateCanScore() handles expiration
        return (new ExamAuthObj($ExamAuth))->ValidateCanScore(true) ? $ExamAuth : null;
    }


    public function ExamReady(): bool
    {
        return $this->ExamReadinessFailureReason() === null;
    }

    public function ExamReadinessFailureReason(): ?array
    {
        if (! $this->IsActive()) {
            \Log::debug('ExamReadinessFailureReason: Course is not active', [
                'course_auth_id' => $this->id,
            ]);
            return ['reason' => 'inactive'];
        }

        $ExamAuth = $this->LatestExamAuth();

        Log::debug('ExamReadinessFailureReason: Checking latest exam', [
            'course_auth_id' => $this->id,
            'has_exam_auth' => $ExamAuth !== null,
            'exam_auth_id' => $ExamAuth?->id,
            'is_passed' => $ExamAuth?->is_passed,
            'next_attempt_at' => $ExamAuth?->next_attempt_at,
            'completed_at' => $ExamAuth?->completed_at,
        ]);

        if ($ExamAuth) {

            if ($ExamAuth->is_passed) {
                Log::debug('ExamReadinessFailureReason: Already passed', [
                    'course_auth_id' => $this->id,
                    'exam_auth_id' => $ExamAuth->id,
                ]);
                return ['reason' => 'already_passed'];
            }

            $nextAttempt = $ExamAuth->next_attempt_at
                ? Carbon::parse($ExamAuth->next_attempt_at)
                : null;

            if ($nextAttempt && ! Carbon::now()->gt($nextAttempt)) {
                Log::debug('ExamReadinessFailureReason: In cooldown period', [
                    'course_auth_id' => $this->id,
                    'exam_auth_id' => $ExamAuth->id,
                    'next_attempt_at' => $nextAttempt->toIso8601String(),
                    'now' => Carbon::now()->toIso8601String(),
                ]);
                return [
                    'reason' => 'cooldown',
                    'next_attempt_at' => $nextAttempt->toIso8601String(),
                ];
            }
        }

        if ($this->exam_admin_id) {
            Log::debug('ExamReadinessFailureReason: Admin override - ready', [
                'course_auth_id' => $this->id,
                'exam_admin_id' => $this->exam_admin_id,
            ]);
            return null;
        }

        if (! $this->AllLessonsCompleted()) {
            Log::debug('ExamReadinessFailureReason: Lessons not completed', [
                'course_auth_id' => $this->id,
            ]);
            return ['reason' => 'lessons'];
        }

        Log::debug('ExamReadinessFailureReason: Ready for exam', [
            'course_auth_id' => $this->id,
        ]);

        return null;
    }


    public function ClassroomExam(string &$fmt = null): stdClass
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

        if ($this->ExamReady()) {
            $res->is_ready = true;
            return $res;
        }


        //
        // verify there is a previous exam
        //

        $ExamAuth = $this->LatestExamAuth();

        if (! $ExamAuth) {
            return $res;
        }


        //
        // previous exam was passed; don't send next attempt
        //

        if (! $ExamAuth->is_passed) {
            $res->next_attempt_at = $ExamAuth->NextAttemptAt($fmt);
        }


        return $res;
    }
}
