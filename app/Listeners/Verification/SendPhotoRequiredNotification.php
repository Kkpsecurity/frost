<?php

namespace App\Listeners\Verification;

use App\Events\Verification\PhotoRequired;
use App\Notifications\Verification\IdVerificationRequiredNotification;
use App\Notifications\Verification\HeadshotRequiredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Handles PhotoRequired event.
 *
 * Dispatches IdVerificationRequiredNotification, HeadshotRequiredNotification,
 * or both — depending on the photoType ('id_card' | 'headshot' | 'both').
 */
class SendPhotoRequiredNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PhotoRequired $event): void
    {
        $user = $event->courseAuth->User;

        if (! $user) {
            Log::warning('SendPhotoRequiredNotification: could not resolve user', [
                'course_auth_id' => $event->courseAuth->id,
                'photo_type'     => $event->photoType,
            ]);
            return;
        }

        $photoType = $event->photoType;

        if (in_array($photoType, ['id_card', 'both'])) {
            $user->notify(new IdVerificationRequiredNotification(
                $event->courseAuth,
                $event->notes,
            ));
        }

        if (in_array($photoType, ['headshot', 'both'])) {
            $user->notify(new HeadshotRequiredNotification(
                $event->courseAuth,
                $event->notes,
            ));
        }
    }
}
