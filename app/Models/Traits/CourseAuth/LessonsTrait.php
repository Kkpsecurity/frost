<?php

declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

/**
 * This trait provides methods to manage lessons within a course.
 * It includes functionality to check if all lessons are completed,
 * retrieve completed lessons, find incomplete lessons, and cache
 * previously completed lessons.
 */

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use RCache;


trait LessonsTrait
{


    public function AllLessonsCompleted(): bool
    {
        $totalLessons = $this->GetCourse()->GetLessons()->count();

        if ($totalLessons == 0) {
            return false;
        }

        $completedLessons = $this->CompletedLessons();
        return count($completedLessons) >= $totalLessons;
    }

    public function CompletedLessons(string $carbon_format = null): array
    {
        try {
            \Log::info("CompletedLessons START for courseAuth {$this->id}");

            $lessons = [];


            foreach ($this->StudentUnits as $StudentUnit) {

                foreach ($StudentUnit->StudentLessons()->whereNotNull('completed_at')->get() as $StudentLesson) {

                    $lesson_id    = $StudentLesson->lesson_id;
                    $completed_at = Carbon::parse($StudentLesson->completed_at);

                    if (isset($lessons[$lesson_id])) {
                        if ($completed_at->gt($lessons[$lesson_id])) {
                            $lessons[$lesson_id] = $completed_at;
                        }
                    } else {
                        $lessons[$lesson_id] = $completed_at;
                    }
                }
            }


            foreach ($this->SelfStudyLessons()->whereNotNull('completed_at')->get() as $SelfStudyLesson) {

                $lesson_id    = $SelfStudyLesson->lesson_id;
                $completed_at = Carbon::parse($SelfStudyLesson->completed_at);

                if (isset($lessons[$lesson_id])) {
                    if ($completed_at->gt($lessons[$lesson_id])) {
                        $lessons[$lesson_id] = $completed_at;
                    }
                } else {
                    $lessons[$lesson_id] = $completed_at;
                }
            }


            if ($carbon_format) {

                $timezone = $this->UserTimezone($this->GetUser());

                foreach ($lessons as $lesson_id => $completed_at) {
                    $lessons[$lesson_id] = $completed_at->tz($timezone)->isoFormat($carbon_format);
                }
            }

            \Log::info("CompletedLessons END for courseAuth {$this->id}", ['count' => count($lessons)]);
            return $lessons;
        } catch (\Throwable $e) {
            \Log::error("CompletedLessons EXCEPTION for courseAuth {$this->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    public function IncompleteLessons(): Collection
    {
        $pcl_cache = $this->PCLCache();
        $cache_keys = array_keys($pcl_cache);

        \Log::info('IncompleteLessons Debug', [
            'course_auth_id' => $this->id,
            'pcl_cache_type' => gettype($pcl_cache),
            'pcl_cache_keys' => $cache_keys,
            'cache_keys_type' => gettype($cache_keys),
        ]);

        return $this->GetCourse()
            ->GetLessons()
            ->whereNotIn('id', $cache_keys);
    }


    public function PCLCache(bool $force_update = false): array
    {

        $RedisConn = Cache::store('redis')->connection();
        $redis_key = 'previous_completed_lessons:' . $this->id;

        if (! $RedisConn->exists($redis_key) or $force_update) {

            $CompletedLessons = $this->CompletedLessons();

            $RedisConn->set($redis_key, RCache::Serialize($CompletedLessons), 'EX', 3600); // 1 hour

            return $CompletedLessons;
        }

        if ($serialized = $RedisConn->get($redis_key)) {
            return RCache::Unserialize($serialized);
        }

        logger('PLCCache reached the end?');
        return [];
    }
}
