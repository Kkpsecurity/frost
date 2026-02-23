<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ProfileUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected array $updatedFields;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $updatedFields = [])
    {
        $this->updatedFields = $updatedFields;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'account.profile_updated',
            config('user_notifications.notifications.account.profile_updated.channels', ['database']),
            (bool) config('user_notifications.notifications.account.profile_updated.user_controllable', true),
        );
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Profile Updated Successfully',
            'message' => 'Your profile information has been updated.',
            'icon' => 'check-circle',
            'priority_color' => 'success',
            'url' => url('/account?section=profile'),
            'updated_fields' => $this->updatedFields,
        ];
    }
}
