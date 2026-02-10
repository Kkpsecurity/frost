<?php

namespace App\Notifications\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences
        $userPrefs = $notifiable->UserPrefs->pluck('pref_value', 'pref_name')->toArray();

        // Add mail channel if enabled (for email verified confirmation)
        if (($userPrefs['notification_channel_mail'] ?? '1') === '1') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $firstName = $notifiable->fname;
        $dashboardUrl = url('/dashboard');

        return (new MailMessage)
            ->subject('Email Verified - ' . config('app.name'))
            ->greeting('Great news, ' . $firstName . '!')
            ->line('Your email address has been successfully verified.')
            ->line('You now have full access to all features of ' . config('app.name') . '.')
            ->line('You can now:')
            ->line('• Enroll in courses')
            ->line('• Receive important notifications')
            ->line('• Access all learning materials')
            ->action('Go to Dashboard', $dashboardUrl)
            ->line('Thank you for verifying your email!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Email Verified Successfully',
            'message' => 'Your email address has been verified. You now have full access to all features.',
            'icon' => 'envelope-circle-check',
            'priority_color' => 'success',
            'url' => url('/dashboard'),
        ];
    }
}
