<?php

namespace App\Observers;

use Exception;

use App\Models\Course;


class CourseObserver
{

    public function deleting(Course $Course): bool
    {

        kkpdebug('Observer', __METHOD__);

        if ($Course->CourseAuths) {
            throw new Exception("Course '{$Course}' has CourseAuths and cannot be deleted");
            return false;
        }

        if ($Course->CourseUnits) {
            throw new Exception("Course '{$Course}' has CourseUnits and cannot be deleted");
            return false;
        }

        return true;
    }
}
