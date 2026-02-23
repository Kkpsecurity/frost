<?php

namespace App\Notifications\Account;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'account.welcome',
            config('user_notifications.notifications.account.welcome.channels', ['database', 'mail']),
            (bool) config('user_notifications.notifications.account.welcome.user_controllable', false),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $firstName = $notifiable->fname;
        $dashboardUrl = url('/dashboard');
        $coursesUrl = url('/courses');

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->greeting('Welcome, ' . $firstName . '!')
            ->line('Thank you for joining ' . config('app.name') . '. We\'re excited to have you as part of our learning community.')
            ->line('With your new account, you can:')
            ->line('• Browse and enroll in professional training courses')
            ->line('• Track your learning progress')
            ->line('• Access course materials and resources')
            ->line('• Connect with instructors and support staff')
            ->action('Get Started', $dashboardUrl)
            ->line('**Next Steps:**')
            ->line('1. Complete your profile information')
            ->line('2. Verify your email address')
            ->line('3. Browse available courses')
            ->action('View Courses', $coursesUrl)
            ->line('If you have any questions, our support team is here to help!')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Welcome to ' . config('app.name') . '!',
            'message' => 'Your account has been created successfully. Start exploring courses and learning opportunities.',
            'icon' => 'user-check',
            'priority_color' => 'success',
            'url' => url('/dashboard'),
        ];
    }
}
