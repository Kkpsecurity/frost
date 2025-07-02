<?php

namespace App\Classes;

#use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use RCache;
use App\Models\CourseAuth;
use App\Models\CourseUnit;
use App\Models\StudentUnit;


class CourseUnitObj
{


    protected $_CourseUnit;
    protected $_CourseUnitLessons;
    protected $_StudentUnits;


    public function __construct( int|CourseUnit $arg )
    {
        if ( is_int( $arg ) )
        {
            $this->_CourseUnit = CourseUnit::findOrFail( $arg );
        }
        else
        {
            $this->_CourseUnit = $arg;
        }
    }


    public function CourseUnit() : CourseUnit
    {
        return $this->_CourseUnit;
    }


    public function CourseUnitLessons() : Collection
    {

        if ( ! $this->_CourseUnitLessons )
        {
            $this->_CourseUnitLessons = $this->_CourseUnit->GetCourseUnitLessons();
        }

        return $this->_CourseUnitLessons;

    }



    //
    // called by CourseAuthObj
    //

    public function StudentUnits( CourseAuth $CourseAuth, bool $force_reload = false ) : Collection
    {

        if ( ! $this->_StudentUnits && ! $force_reload )
        {
            $this->_StudentUnits = StudentUnit::where( 'course_auth_id', $CourseAuth->id )
                                              ->where( 'course_unit_id', $this->_CourseUnit->id )
                                              ->get();
        }

        return $this->_StudentUnits;

    }



}
