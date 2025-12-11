<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Collection;

use App\Models\CourseDate;
use App\Models\StudentUnit;


trait ActiveStudentUnits
{

    /**
     * Retrieves ACTIVE StudentUnits for $CourseDate
     * Only returns students who are currently participating in an active InstUnit session
     *
     * @param   int|string|CourseDate  $CourseDate
     * @return  Collection             [StudentUnit]
     */
    public static function ActiveStudentUnits( int|string|CourseDate $CourseDate ) : Collection
    {
        $course_date_id = $CourseDate->id ?? (int) $CourseDate;

        // Get the course date object if we only have an ID
        if (!($CourseDate instanceof CourseDate)) {
            $CourseDate = CourseDate::find($course_date_id);
        }

        // Check if there's an active InstUnit for this course date
        $instUnit = $CourseDate->instUnit()->whereNull('completed_at')->first();

        if (!$instUnit) {
            // No active InstUnit = no active students
            return collect([]);
        }

        // Return StudentUnits that are:
        // 1. For this course date
        // 2. Associated with the active InstUnit
        // 3. Not ejected
        // 4. Not completed (if we want only currently active)
        return StudentUnit::where('course_date_id', $course_date_id)
            ->where('inst_unit_id', $instUnit->id)
            ->whereNull('ejected_at')
            ->whereNull('completed_at')  // Only students still in the class
            ->get();
    }


}
