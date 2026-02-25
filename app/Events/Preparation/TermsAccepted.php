<?php

namespace App\Events\Preparation;

use App\Models\CourseAuth;
use App\Models\StudentUnit;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student successfully accepts the course terms and conditions
 * for a specific class day.
 *
 * Triggers the next onboarding step reminder: classroom rules acceptance.
 */
class TermsAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly CourseAuth $courseAuth,
        public readonly StudentUnit $studentUnit,
    ) {}
}
