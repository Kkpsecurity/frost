<?php

namespace App\Events\Preparation;

use App\Models\CourseAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student is enrolled in a course that requires a range date
 * selection but has not yet assigned one.
 *
 * Triggers a RangeDateRequiredNotification so the student knows to
 * complete this step before their class.
 */
class RangeDateRequired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly CourseAuth $courseAuth,
    ) {}
}
