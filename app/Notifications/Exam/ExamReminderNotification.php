<?php

namespace App\Notifications\Exam;

use App\Models\CourseAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected CourseAuth $courseAuth;
    protected int $daysWaiting;

    /**
     * Create a new notification instance.
     *
     * Accepts a CourseAuth (not ExamAuth) because reminders are sent BEFORE
     * the student has started the exam — no ExamAuth record exists yet.
     */
    public function __construct(CourseAuth $courseAuth, int $daysWaiting = 3)
    {
        $this->courseAuth = $courseAuth;
        $this->daysWaiting = $daysWaiting;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_reminder',
            config('user_notifications.notifications.exams.exam_reminder.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_reminder.user_controllable', true),
        );
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $courseName  = $this->courseAuth->Course->title ?? 'your course';
        $maxAttempts = $this->courseAuth->GetCourse()->GetExam()->policy_attempts ?? 2;

        return (new MailMessage)
            ->subject('⏰ Exam Reminder - ' . $courseName)
            ->greeting('Don\'t Forget Your Exam!')
            ->line('Your exam has been ready for ' . $this->daysWaiting . ' days but you haven\'t started it yet.')
            ->line('**Course:** ' . $courseName)
            ->line('**Attempts Available:** ' . $maxAttempts)
            ->action('Start Exam Now', route('classroom.course', $this->courseAuth->id))
            ->line('Complete your certification by taking the exam today!');
    }

    /**
     * Get the array representation of the notification.
     *
     * NOTE: course_auth_id and days_waiting are used by SendExamReminders
     * for deduplication — do not rename these keys.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'          => 'Exam Reminder',
            'message'        => 'Your exam has been ready for ' . $this->daysWaiting . ' days. Don\'t forget to complete it!',
            'course_auth_id' => $this->courseAuth->id,
            'course_name'    => $this->courseAuth->Course->title ?? null,
            'days_waiting'   => $this->daysWaiting,
            'icon'           => 'clock',
            'color'          => 'warning',
            'priority'       => 'medium',
            'url'            => route('classroom.course', $this->courseAuth->id),
        ];
    }
}
