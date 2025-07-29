<?php

namespace App\Observers;

use Exception;
use App\Models\CourseUnit;


class CourseUnitObserver
{

    public function deleting(CourseUnit $CourseUnit): bool
    {

        kkpdebug('Observer', __METHOD__);

        if ($CourseUnit->CourseUnitLessons) {
            throw new Exception("CourseUnit '{$CourseUnit}' has CourseUnitLessons and cannot be deleted");
            return false;
        }

        return true;
    }
}
