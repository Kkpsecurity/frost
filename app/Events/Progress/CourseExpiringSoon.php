<?php

namespace App\Events\Progress;

use App\Models\CourseAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student's course is approaching its expiry date (30-day window).
 */
class CourseExpiringSoon
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly CourseAuth $courseAuth,
        public readonly int $daysRemaining,  // 30 or 7
    ) {}
}
