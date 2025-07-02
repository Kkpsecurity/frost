<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Collection;

use App\Models\CourseDate;
use App\Models\StudentUnit;


trait ActiveStudentUnits
{

    /**
     * Retrieves StudentUnits for $CourseDate
     *
     * @param   int|string|CourseDate  $CourseDate
     * @return  Collection             [StudentUnit]
     */
    public static function ActiveStudentUnits( int|string|CourseDate $CourseDate ) : Collection
    {

        $course_date_id = $CourseDate->id ?? (int) $CourseDate;

        return StudentUnit::where( 'course_date_id', $course_date_id )->get();

    }


}
