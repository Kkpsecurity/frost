<?php

declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file ClassroomQueries.php
 * @brief Class for handling classroom-related queries.
 * @details This class aggregates various classroom-related query functionalities.
 */

// Instructor
use App\Classes\Instructors\InitInstUnit;
use App\Classes\Instructors\InitInstLesson;
use App\Classes\Instructors\RecentInstUnits;
use App\Classes\Instructors\CompleteInstUnit;
use App\Classes\Instructors\ActiveStudentUnits;
use App\Classes\Instructors\CompletedInstLessons;
use App\Classes\Instructors\InstructorDashboardCourseDates;
// Student
use App\Classes\Students\InitStudentUnit;
use App\Classes\Students\EOLStudentLesson;
use App\Classes\Students\InitStudentLesson;
// Shared
use App\Classes\Support\ActiveInstLesson;
use App\Classes\Support\RecentChatMessages;
// Private
use App\Classes\Students\StudentCanJoinLesson;


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
