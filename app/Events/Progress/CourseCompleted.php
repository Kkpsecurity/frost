<?php

namespace App\Events\Progress;

use App\Models\CourseAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student's CourseAuth is marked completed (passed or failed).
 * Triggers course_completed and certificate_ready notifications.
 */
class CourseCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly CourseAuth $courseAuth,
        public readonly bool $isPassed,
    ) {}
}
