<?php
declare(strict_types=1);

namespace App\Models\Traits\InstLesson;

use DB;

use App\Models\CourseUnitLesson;
use KKP\Laravel\PgTk;


trait GetCourseUnitLesson
{


    protected $_CourseUnitLesson;


    public function GetCourseUnitLesson() : CourseUnitLesson
    {

        if ( ! $this->_CourseUnitLesson )
        {

            $this->_CourseUnitLesson = PgTk::toModels(
                CourseUnitLesson::class,
                DB::select(<<<SQL
SELECT course_unit_lessons.*
FROM   course_unit_lessons
JOIN   course_units ON course_units.id             = course_unit_lessons.course_unit_id
JOIN   course_dates ON course_dates.course_unit_id = course_units.id
JOIN   inst_unit    ON inst_unit.course_date_id    = course_dates.id
WHERE  inst_unit.id                  = {$this->inst_unit_id}
AND    course_unit_lessons.lesson_id = {$this->lesson_id}
SQL
                )
            )->first();

        }

        return $this->_CourseUnitLesson;

    }


}
