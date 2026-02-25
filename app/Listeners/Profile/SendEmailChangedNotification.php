<?php

namespace App\Listeners\Profile;

use App\Events\Profile\EmailChanged;
use App\Notifications\Profile\EmailChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailChangedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * The notification is sent to the user record (new email) so the student
     * receives it at their new address.  The old email is embedded in the
     * notification body for reference.
     */
    public function handle(EmailChanged $event): void
    {
        $event->user->notify(
            new EmailChangedNotification($event->oldEmail, $event->newEmail)
        );
    }
}
