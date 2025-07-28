<?php declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file ExamAuthObj.php
 * @brief Class for handling exam authentication objects.
 * @details Provides methods for managing exam authentication, scoring, and related functionalities.
 */

use Illuminate\Support\Facades\Auth;

use App\Services\RCache;

use App\Models\ExamAuth;

use App\Classes\ExamAuthObj\Scoring;
use App\Classes\ExamAuthObj\Handlers;
use App\Classes\ExamAuthObj\Internals;


class ExamAuthObj
{

    use Handlers, Internals, Scoring;


    public $Course;
    public $CourseAuth;
    public $Exam;
    public $ExamAuth;
    public $ExamAuths;
    public $ExamQuestions;


    public function __construct(int|ExamAuth $ExamAuth)
    {

        if ($ExamAuth instanceof ExamAuth) {
            $this->ExamAuth = $ExamAuth;
        } else {
            $this->ExamAuth = ExamAuth::findOrFail($ExamAuth);
        }

        $this->_LoadAll();

        abort_if(($this->CourseAuth->user_id != Auth::id() && ! Auth::user()->IsAnyAdmin()),
            403,
            'ExamAuthObj :: Not owner of CourseAuth'
        );
    }


    public function ValidateCanScore(bool $require_active_course_auth = true): bool
    {

        if ($require_active_course_auth && ! $this->CourseAuth->IsActive()) {
            abortToRoute(route('classroom.dashboard'), 'Course is no longer active');
        }

        if ($this->ExamAuth->completed_at or $this->ExamAuth->hidden_at) {
            return false;
        }

        if ($this->ExamAuth->IsExpired()) {
            $this->SetExpired();
            return false;
        }

        return true;
    }
}
