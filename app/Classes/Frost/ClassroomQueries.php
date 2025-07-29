<?php

declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file ClassroomQueries.php
 * @brief Class for handling classroom-related queries.
 * @details This class aggregates various classroom-related query functionalities.
 */

// Instructor
use App\Classes\ClassroomQueries\InitInstUnit;
use App\Classes\ClassroomQueries\InitInstLesson;
use App\Classes\ClassroomQueries\RecentInstUnits;
use App\Classes\ClassroomQueries\CompleteInstUnit;
use App\Classes\ClassroomQueries\ActiveStudentUnits;
use App\Classes\ClassroomQueries\CompletedInstLessons;
use App\Classes\ClassroomQueries\InstructorDashboardCourseDates;
// Student
use App\Classes\ClassroomQueries\InitStudentUnit;
use App\Classes\ClassroomQueries\EOLStudentLesson;
use App\Classes\ClassroomQueries\InitStudentLesson;
// Shared
use App\Classes\ClassroomQueries\ActiveInstLesson;
use App\Classes\ClassroomQueries\RecentChatMessages;
// Private
use App\Classes\ClassroomQueries\StudentCanJoinLesson;


class ClassroomQueries
{

    //
    // Instructor
    //
    use ActiveStudentUnits;
    use CompleteInstUnit;
    use CompletedInstLessons;
    use InitInstLesson;
    use InitInstUnit;
    use InstructorDashboardCourseDates;
    use RecentInstUnits;

    //
    // Student
    //
    use EOLStudentLesson;
    use InitStudentLesson;
    use InitStudentUnit;

    //
    // Shared
    //
    use ActiveInstLesson;
    use RecentChatMessages;

    // Private methods / internal use only
    use StudentCanJoinLesson;
}
