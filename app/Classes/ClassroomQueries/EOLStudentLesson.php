<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use App\Models\StudentLesson;
use App\Models\StudentUnit;


trait EOLStudentLesson
{

    /**
     * Retrieves StudentLesson ready for EOL Challenge
     *
     * @param   int|string|StudentUnit  $StudentUnit
     * @return  StudentLesson|null
     */
    public static function EOLStudentLesson( int|string|StudentUnit $StudentUnit ) : ?StudentLesson
    {

        $student_unit_id = $StudentUnit->id ?? (int) $StudentUnit;

        return StudentLesson::where( 'student_unit_id', $student_unit_id )
                        ->whereNull( 'dnc_at' )
                        ->whereNull( 'completed_at' )
                              ->get()
                             ->last();

    }

}
