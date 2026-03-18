<?php

namespace App\Notifications\Progress;

use App\Models\CourseAuth;
use App\Notifications\Concerns\UsesUserNotificationPreferences;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent at key renewal milestones for completed (passed) course licenses.
 *
 * $daysRemaining values:
 *   180 — renewal window opens (6 months out)
 *    90 — 3 months until expiry
 *    30 — 30 days until expiry
 *    15 — 15 days until expiry
 *     0 — license expired (yesterday)
 */
class LicenseRenewalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use UsesUserNotificationPreferences;

    public function __construct(
        protected readonly CourseAuth $courseAuth,
        protected readonly int $daysRemaining,   // 180 | 90 | 30 | 15 | 0
    ) {}

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private function notificationType(): string
    {
        return match ($this->daysRemaining) {
            180 => 'progress.license_renewal_180days',
            90  => 'progress.license_renewal_90days',
            30  => 'progress.license_renewal_30days',
            15  => 'progress.license_renewal_15days',
            default => 'progress.license_expired',
        };
    }

    private function configKey(): string
    {
        return match ($this->daysRemaining) {
            180 => 'user_notifications.notifications.progress.license_renewal_180days',
            90  => 'user_notifications.notifications.progress.license_renewal_90days',
            30  => 'user_notifications.notifications.progress.license_renewal_30days',
            15  => 'user_notifications.notifications.progress.license_renewal_15days',
            default => 'user_notifications.notifications.progress.license_expired',
        };
    }

    private function subjectLine(string $courseName): string
    {
        return match ($this->daysRemaining) {
            180 => 'License Renewal Open - ' . $courseName,
            90  => 'License Renewal Reminder - 3 Months Remaining - ' . $courseName,
            30  => 'License Expiring in 30 Days - ' . $courseName,
            15  => 'License Expiring in 15 Days - ' . $courseName,
            default => 'License Expired - ' . $courseName,
        };
    }

    private function expireDateFormatted(): string
    {
        return $this->courseAuth->expire_date
            ? Carbon::parse($this->courseAuth->expire_date)->format('F j, Y')
            : 'N/A';
    }

    private function priority(): string
    {
        return match ($this->daysRemaining) {
            180, 90 => 'medium',
            default  => 'high',
        };
    }

    // -------------------------------------------------------------------------
    // Notification channels
    // -------------------------------------------------------------------------

    public function via(object $notifiable): array
    {
        $defaults = config($this->configKey() . '.channels', ['database', 'mail']);
        $userControllable = (bool) config($this->configKey() . '.user_controllable', true);

        return $this->preferredChannels(
            $notifiable,
            $this->notificationType(),
            $defaults,
            $userControllable,
        );
    }

    // -------------------------------------------------------------------------
    // Mail
    // -------------------------------------------------------------------------

    public function toMail(object $notifiable): MailMessage
    {
        $courseName  = $this->courseAuth->Course?->title ?? $this->courseAuth->Course?->title_long ?? 'your course';
        $expireDate  = $this->expireDateFormatted();
        $firstName   = $notifiable->fname ?? 'Student';

        $message = (new MailMessage)
            ->subject($this->subjectLine($courseName))
            ->greeting('Hello, ' . $firstName . '.');

        switch ($this->daysRemaining) {
            case 180:
                $message
                    ->line('Your license for **' . $courseName . '** expires on **' . $expireDate . '**.')
                    ->line('You are now eligible to renew your license — you have 6 months remaining.')
                    ->line('Renewing early ensures uninterrupted coverage.');
                break;

            case 90:
                $message
                    ->line('Your license for **' . $courseName . '** expires on **' . $expireDate . '** — 3 months from today.')
                    ->line('Please schedule your renewal course now to avoid a lapse in your license.');
                break;

            case 30:
                $message
                    ->line('Your license for **' . $courseName . '** expires in **30 days** on **' . $expireDate . '**.')
                    ->line('Enroll in a renewal course immediately to keep your license current.');
                break;

            case 15:
                $message
                    ->line('⚠️ Your license for **' . $courseName . '** expires in **15 days** on **' . $expireDate . '**.')
                    ->line('Please contact us or enroll in a renewal course as soon as possible.');
                break;

            default: // 0 — expired
                $message
                    ->line('Your license for **' . $courseName . '** expired on **' . $expireDate . '**.')
                    ->line('You must renew your license to continue operating under it. Please contact us to enroll in a renewal course.');
                break;
        }

        return $message
            ->action('View My Licenses', route('classroom.dashboard'))
            ->line('If you have any questions, please contact our support team.');
    }

    // -------------------------------------------------------------------------
    // Database
    // -------------------------------------------------------------------------

    public function toDatabase(object $notifiable): array
    {
        $courseName  = $this->courseAuth->Course?->title ?? $this->courseAuth->Course?->title_long ?? 'your course';
        $expireDate  = $this->courseAuth->expire_date
            ? Carbon::parse($this->courseAuth->expire_date)->toDateString()
            : null;

        $title = match ($this->daysRemaining) {
            180 => 'License Renewal Open',
            90  => 'License Renewal — 3 Months',
            30  => 'License Expiring in 30 Days',
            15  => 'License Expiring in 15 Days',
            default => 'License Expired',
        };

        $message = match ($this->daysRemaining) {
            180 => $courseName . ' license can be renewed now (expires ' . ($expireDate ?? 'N/A') . ').',
            90  => $courseName . ' license expires in 3 months (' . ($expireDate ?? 'N/A') . ').',
            30  => $courseName . ' license expires in 30 days (' . ($expireDate ?? 'N/A') . ').',
            15  => $courseName . ' license expires in 15 days (' . ($expireDate ?? 'N/A') . ').',
            default => $courseName . ' license expired on ' . ($expireDate ?? 'N/A') . '.',
        };

        return [
            'type'           => $this->notificationType(),
            'title'          => $title,
            'message'        => $message,
            'course_auth_id' => $this->courseAuth->id,
            'course_id'      => $this->courseAuth->course_id,
            'course_name'    => $courseName,
            'expire_date'    => $expireDate,
            'days_remaining' => $this->daysRemaining,
            'url'            => route('classroom.dashboard'),
            'icon'           => $this->daysRemaining === 0 ? 'times-circle' : 'id-card',
            'priority'       => $this->priority(),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    // -------------------------------------------------------------------------
    // Deduplication — one notification per threshold per CourseAuth per year
    // -------------------------------------------------------------------------

    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'database') {
            return true;
        }

        return ! $notifiable->notifications()
            ->whereRaw("(data::jsonb)->>'type' = ?", [$this->notificationType()])
            ->whereRaw("(data::jsonb)->>'course_auth_id' = ?", [(string) $this->courseAuth->id])
            ->where('created_at', '>=', now()->startOfYear())
            ->exists();
    }
}
