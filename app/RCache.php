<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;

use App\RCache\RCacheTraitLoader;

use App\Models\Course;
use App\Models\CourseUnit;
use App\Models\CourseUnitLesson;
use App\Models\DiscountCode;
use App\Models\Exam;
use App\Models\ExamQuestionSpec;
use App\Models\Lesson;
use App\Models\PaymentType;
use App\Models\Role;
use App\Models\SiteConfig;
use App\Models\User;


class RCache
{

    use RCacheTraitLoader;


    protected static $_cache_models = false;
    protected static $_StaticCaches;
    protected static $_ModelCaches;


    protected static $_static_models = [
        //
        // ensure these records cannot be edited
        //   use KKP\Laravel\ModelTraits\StaticModel;
        //
        ExamQuestionSpec::class,
        PaymentType::class,
        Role::class,
    ];



    #########################
    ###                   ###
    ###   object caches   ###
    ###                   ###
    #########################


    public static function Courses( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( Course::class, $value, $key );
    }

    public static function CourseUnits( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( CourseUnit::class, $value, $key );
    }

    public static function CourseUnitLessons( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( CourseUnitLesson::class, $value, $key );
    }

    public static function DiscountCodes( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( DiscountCode::class, $value, $key );
    }

    public static function Exams( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( Exam::class, $value, $key );
    }

    public static function ExamQuestionSpecs( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( ExamQuestionSpec::class, $value, $key );
    }

    public static function Lessons( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( Lesson::class, $value, $key );
    }

    public static function PaymentTypes( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( PaymentType::class, $value, $key );
    }

    public static function Roles( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( Role::class, $value, $key );
    }

    public static function SiteConfigs( $value = null, string $key = 'id' ) : object
    {
        return self::_getModelCache( SiteConfig::class, $value, $key );
    }



    ########################
    ###                  ###
    ###   query caches   ###
    ###                  ###
    ########################


    public static function ActiveCourses() : Collection
    {
        return self::Courses()->where( 'is_active', true )->sortBy( 'id' );
    }



    ################
    ###          ###
    ###   misc   ###
    ###          ###
    ################


    public static function Countries( $as_hash = false ) : array
    {
        $countries = self::Unserialize( self::get( 'countries' ) );
        return ( $as_hash ? idx2hash( $countries ) : $countries );
    }

}
