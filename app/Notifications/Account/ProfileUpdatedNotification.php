<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

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
        $channels = [];

        // Check user preferences
        $userPrefs = $notifiable->UserPrefs->pluck('pref_value', 'pref_name')->toArray();

        // Check if user has enabled this notification type
        if (($userPrefs['notification_account.profile_updated'] ?? '1') === '1') {
            // Always send to database
            $channels[] = 'database';
        }

        return $channels;
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
