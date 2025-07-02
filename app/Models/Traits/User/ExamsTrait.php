<?php
declare(strict_types=1);

namespace App\Models\Traits\User;

use App\Models\ExamAuth;


trait ExamsTrait
{


    public function ActiveExamAuth() : ?ExamAuth
    {

        foreach ( $this->ActiveCourseAuths as $CourseAuth )
        {

            // ActiveExamAuth() handles expiration
            if ( $ExamAuth = $CourseAuth->ActiveExamAuth() )
            {
                return $ExamAuth;
            }

        }

        return null;

    }


}
