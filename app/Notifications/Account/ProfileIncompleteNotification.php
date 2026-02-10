<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileIncompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $missingFields;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $missingFields = [])
    {
        $this->missingFields = $missingFields;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences
        $userPrefs = $notifiable->UserPrefs->pluck('pref_value', 'pref_name')->toArray();

        // Check if user has enabled this notification type
        if (($userPrefs['notification_account.profile_incomplete'] ?? '1') === '1') {
            // Add mail channel if enabled
            if (($userPrefs['notification_channel_mail'] ?? '1') === '1') {
                $channels[] = 'mail';
            }
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $firstName = $notifiable->fname;
        $profileUrl = url('/account?section=profile');

        $message = (new MailMessage)
            ->subject('Complete Your Profile - ' . config('app.name'))
            ->greeting('Hi ' . $firstName . ',')
            ->line('Your profile is incomplete. To get the most out of ' . config('app.name') . ', please complete the following information:');

        foreach ($this->missingFields as $field) {
            $message->line('• ' . $field);
        }

        return $message
            ->action('Complete Profile', $profileUrl)
            ->line('A complete profile helps us provide you with better service and ensures smooth enrollment in courses.')
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $fieldsText = count($this->missingFields) > 0
            ? implode(', ', $this->missingFields)
            : 'some required information';

        return [
            'title' => 'Complete Your Profile',
            'message' => 'Your profile is missing: ' . $fieldsText . '. Please update your information.',
            'icon' => 'user-edit',
            'priority_color' => 'warning',
            'url' => url('/account?section=profile'),
            'missing_fields' => $this->missingFields,
        ];
    }
}
