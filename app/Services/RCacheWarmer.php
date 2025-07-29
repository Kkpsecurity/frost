<?php

namespace App\Services;


use RCache;
use Illuminate\Support\ServiceProvider;
use App\Support\RCache\RCacheWarmerTrait;

// Models
use App\Models\Exam;
use App\Models\Role;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseUnit;
use App\Models\DiscountCode;
use App\Models\ExamQuestionSpec;
use App\Models\CourseUnitLesson;


class RCacheWarmer extends ServiceProvider
{

    use RCacheWarmerTrait;


    protected static $preload_models = [

        Course::class,
        CourseUnit::class,
        CourseUnitLesson::class,
        DiscountCode::class,
        Exam::class,
        ExamQuestionSpec::class,
        Lesson::class,
        Role::class,

    ];


    public function boot(): void
    {

        foreach (self::$preload_models as $model_name) {
            if (! RCache::exists($model_name)) {
                kkpdebug('RCacheWarmer', "Loading {$model_name}");
                RCache::LoadModelCache($model_name, false);
            } else if (RCache::IsStaticModel($model_name)) {
                RCache::LoadModelCache($model_name, false);
            }
        }


        self::LoadAdmins();
        self::LoadCountries();
    }
}
