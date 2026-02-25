<?php

namespace App\Events\Enrollment;

use App\Models\CourseAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseEnrolled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CourseAuth $courseAuth;

    /**
     * Create a new event instance.
     */
    public function __construct(CourseAuth $courseAuth)
    {
        $this->courseAuth = $courseAuth;
    }
}
