<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

use App\Services\RCache;
use App\Traits\RCacheModelTrait;

use App\Models\CourseUnit;
use App\Models\ExamQuestion;
use App\Models\CourseUnitLesson;

use App\Helpers\TextTk;
use App\Traits\Observable;


class Lesson extends Model
{

    use Observable, RCacheModelTrait;

    protected $table        = 'lessons';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'title'             => 'string',  // 64
        'credit_minutes'    => 'integer',
        'video_seconds'     => 'integer',
        'video_url'         => 'string',

    ];

    protected $guarded      = ['id'];

    public function __toString()
    {
        return $this->title;
    }


    //
    // relationships
    //


    public function CourseUnits()
    {
        return $this->belongsToMany(CourseUnit::class, 'course_unit_lessons', 'lesson_id', 'course_unit_id')
            ->using(CourseUnitLesson::class);
    }

    public function CourseUnitLessons()
    {
        return $this->hasMany(CourseUnitLesson::class, 'lesson_id');
    }

    public function ExamQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'lesson_id');
    }

    // Offline Play relationship
    public function offlineSessions()
    {
        return $this->hasMany(OfflineSession::class);
    }


    //
    // incoming data filters
    //


    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = TextTk::Sanitize($value);
    }


    //
    // cache queries
    //


    public function GetCourses()
    {
        return RCache::Lesson_Courses($this);
    }

    public function GetCourseUnits()
    {
        return RCache::Lesson_CourseUnits($this);
    }

    public function GetCourseUnitLessons()
    {
        return RCache::Lesson_CourseUnitLessons($this);
    }


    //
    // misc
    //


    public function CreditHours(): string
    {
        return sprintf('%0.1f', $this->credit_minutes / 60);
    }


    public function SelfStudyMinSeconds(): int
    {
        return $this->video_seconds - 300;  // 5 minutes
    }


    //
    // S3 Video Methods
    //


    /**
     * Check if lesson has a video URL assigned
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    /**
     * Get the full S3 URL for the video
     * Note: Using s3_media disk which doesn't have a 'root' prefix
     */
    public function getVideoUrl(): ?string
    {
        if (!$this->hasVideo()) {
            return null;
        }

        // Build direct S3 URL without root prefix
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$this->video_url}";
    }

    /**
     * Get a temporary signed URL for secure video access
     *
     * @param int $expiresInMinutes Default 60 minutes
     * Note: Using s3_media disk which doesn't have a 'root' prefix
     */
    public function getSignedVideoUrl(int $expiresInMinutes = 60): ?string
    {
        if (!$this->hasVideo()) {
            return null;
        }

        // Use AWS SDK directly to avoid the 'root' prefix issue
        $s3Client = \Storage::disk('s3')->getClient();
        $bucket = config('filesystems.disks.s3.bucket');

        try {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $this->video_url, // Direct key without root prefix
            ]);

            $request = $s3Client->createPresignedRequest($cmd, "+{$expiresInMinutes} minutes");
            return (string) $request->getUri();
        } catch (\Exception $e) {
            \Log::error("Failed to generate signed URL for video: {$this->video_url}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
