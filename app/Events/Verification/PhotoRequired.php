<?php

namespace App\Events\Verification;

use App\Models\CourseAuth;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when a student is asked to upload/re-upload a verification photo.
 *
 * Triggers either IdVerificationRequired or HeadshotRequired notification
 * (or both when photoType = 'both').
 */
class PhotoRequired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param CourseAuth  $courseAuth The enrollment record — used to resolve the student
     * @param string      $photoType  'id_card' | 'headshot' | 'both'
     * @param string|null $notes      Optional reason / instruction for the student
     */
    public function __construct(
        public CourseAuth $courseAuth,
        public string $photoType,
        public ?string $notes = null,
    ) {}
}
