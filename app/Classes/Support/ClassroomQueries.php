<?php

declare(strict_types=1);

namespace App\Classes\Support;

// Instructor
use App\Classes\Instructors\ActiveStudentUnits;
use App\Classes\Instructors\CompleteInstUnit;
use App\Classes\Instructors\CompletedInstLessons;
use App\Classes\Instructors\InitInstLesson;
use App\Classes\Instructors\InitInstUnit;
use App\Classes\Instructors\InstructorDashboardCourseDates;
use App\Classes\Instructors\RecentInstUnits;
// Student
use App\Classes\Students\EOLStudentLesson;
use App\Classes\Students\InitStudentLesson;
use App\Classes\Students\InitStudentUnit;
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
    use RecentChatMessages; // @deprecated - Use App\Classes\MiscQueries::RecentChatMessages() instead

    // Private methods / internal use only
    use StudentCanJoinLesson;
}
