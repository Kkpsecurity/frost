<?php

declare(strict_types=1);

namespace App\Events\Challenge;

use App\Models\StudentLesson;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a StudentLesson is marked as Do-Not-Complete (DNC).
 *
 * Triggered from StudentLesson::MarkDNC() after dnc_at is persisted.
 * The listener will resolve the instructor via the unit relationship chain
 * and dispatch a notification.
 */
class StudentDnc
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  StudentLesson  $studentLesson  The lesson that was marked DNC.
     */
    public function __construct(
        public readonly StudentLesson $studentLesson,
    ) {}
}
