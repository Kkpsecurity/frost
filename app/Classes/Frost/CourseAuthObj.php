<?php

namespace App\Classes\Frost;

/**
 * @file CourseAuthObj.php
 * @brief Class for handling course authorization objects.
 * @details This class provides methods to interact with course authorization data, including user and course details.
 */

#use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

###use App\Classes\Extensions\CAOInitRecords;
###use App\Classes\Extensions\CAOStudent;
#use App\Models\CourseUnit;
#use App\Models\InstLesson;
#use App\Models\InstUnit;
#use App\Models\Lesson;
#use App\Models\StudentLesson;
#use App\Models\StudentUnit;

use App\Services\RCache;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseAuth;

use App\Classes\CourseUnitObj;

class CourseAuthObj
{


    ###use CAOInitRecords;
    ###use CAOStudent;


    protected $_CourseAuth;
    protected $_Course;
    protected $_User;

    protected $_CourseUnitObjs;
    protected $_ExamAuths;


    public function __construct(int|CourseAuth $CourseAuth)
    {

        if (is_int($CourseAuth)) {
            $this->_CourseAuth = CourseAuth::findOrFail($CourseAuth);
        } else {
            $this->_CourseAuth = $CourseAuth;
        }
    }


    public function CourseAuth(): CourseAuth
    {
        return $this->_CourseAuth;
    }


    //
    // only load models on demand
    //


    public function Course(): Course
    {

        if (! $this->_Course) {
            $this->_Course = $this->_CourseAuth->GetCourse();
        }

        return $this->_Course;
    }


    public function User(): User
    {

        if (! $this->_User) {
            $this->_User = $this->_CourseAuth->GetUser();
        }

        return $this->_User;
    }


    #public function CourseUnits() : Collection
    #{
    #    return $this->CourseUnitObjs();
    #}


    public function CourseUnitObjs(): Collection
    {

        if (! $this->_CourseUnitObjs) {

            $this->_CourseUnitObjs = new Collection;

            foreach ($this->Course()->GetCourseUnits() as $CourseUnit) {
                $this->_CourseUnitObjs->put($CourseUnit->id, new CourseUnitObj($CourseUnit));
            }
        }

        return $this->_CourseUnitObjs;
    }


    public function ExamAuths(): Collection
    {

        if (! $this->_ExamAuths) {
            $this->_ExamAuths = $this->_CourseAuth->ExamAuths;
        }

        return $this->_ExamAuths;
    }
}
