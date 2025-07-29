<?php

namespace App\Services;

/**
 * @file RCache.php
 * @brief Service for managing Redis caching.
 * @details This service provides methods to cache and retrieve various models and queries, as well as utility functions for serialization and debugging.
 */

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use App\Support\RCache\RCacheTraitLoader;

// Models
use App\Models\Exam;
use App\Models\Role;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\SiteConfig;
use App\Models\CourseUnit;
use App\Models\PaymentType;
use App\Models\DiscountCode;
use App\Models\CourseUnitLesson;
use App\Models\ExamQuestionSpec;


class RCache
{

    use RCacheTraitLoader;


    protected static $_ModelCaches;
    protected static $_StaticCaches;
    protected static $_cache_models = false;


    protected static $_static_models = [
        //
        // ensure these records cannot be edited
        //   use KKP\Laravel\ModelTraits\StaticModel;
        //
        ExamQuestionSpec::class,
        PaymentType::class,
        Role::class,
    ];

    /**
     * Deprecated method for handling Redis DebugBar.
     * This method is now a no-op and should not be used.
     * It is retained for backward compatibility.
     */
    public function RedisDebugBar(): void
    {
        if (config('app.debug') && class_exists('Debugbar')) {
            log('RCache::RedisDebugBar() called');
        }
    }

    #########################
    ###                   ###
    ###   object caches   ###
    ###                   ###
    #########################


    public static function Courses($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(Course::class, $value, $key);
    }

    public static function CourseUnits($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(CourseUnit::class, $value, $key);
    }

    public static function CourseUnitLessons($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(CourseUnitLesson::class, $value, $key);
    }

    public static function DiscountCodes($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(DiscountCode::class, $value, $key);
    }

    public static function Exams($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(Exam::class, $value, $key);
    }

    public static function ExamQuestionSpecs($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(ExamQuestionSpec::class, $value, $key);
    }

    public static function Lessons($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(Lesson::class, $value, $key);
    }

    public static function PaymentTypes($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(PaymentType::class, $value, $key);
    }

    public static function Roles($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(Role::class, $value, $key);
    }

    public static function SiteConfigs($value = null, string $key = 'id'): object
    {
        return self::_getModelCache(SiteConfig::class, $value, $key);
    }

    public static function User(int $user_id): User
    {
        return self::_getModelCache(User::class, $user_id, 'id');
    }

    public static function Course_CourseUnits(int $course_id): Collection
    {
        return self::CourseUnits()
            ->where('course_id', $course_id)
            ->sortBy('id')
            ->pluck('id');
    }


    ########################
    ###                  ###
    ###   query caches   ###
    ###                  ###
    ########################


    public static function ActiveCourses(): Collection
    {
        return self::Courses()->where('is_active', true)->sortBy('id');
    }



    ################
    ###          ###
    ###   misc   ###
    ###          ###
    ################


    public static function Countries($as_hash = false): array
    {
        $countries = self::Unserialize(self::get('countries'));
        return ($as_hash ? idx2hash($countries) : $countries);
    }

    /**
     * Get lessons for a specific course
     *
     * @param int $course_id
     * @return Collection
     */
    public static function Course_Lessons(int $course_id): Collection
    {
        $course = Course::find($course_id);
        if (!$course) {
            return collect();
        }

        // Get all lessons for this course through course units
        $lessons = collect();
        $courseUnits = self::Course_CourseUnits($course_id);

        foreach ($courseUnits as $unit) {
            $unitLessons = CourseUnitLesson::where('course_unit_id', $unit->id)
                ->with('lesson')
                ->get()
                ->pluck('lesson')
                ->filter();
            $lessons = $lessons->merge($unitLessons);
        }

        return $lessons;
    }
}
