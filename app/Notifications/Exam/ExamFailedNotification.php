<?php

namespace App\Notifications\Exam;

use App\Models\ExamAuth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Concerns\UsesUserNotificationPreferences;

class ExamFailedNotification extends Notification implements ShouldQueue
{
    use Queueable, UsesUserNotificationPreferences;

    protected ExamAuth $examAuth;

    public function __construct(ExamAuth $examAuth)
    {
        $this->examAuth = $examAuth;
    }

    public function via(object $notifiable): array
    {
        return $this->preferredChannels(
            $notifiable,
            'notification_exam.exam_failed',
            config('user_notifications.notifications.exams.exam_failed.channels', ['database']),
            (bool) config('user_notifications.notifications.exams.exam_failed.user_controllable', true),
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->examAuth->CourseAuth->Course->title ?? 'your course';
        $score = $this->examAuth->score ?? 0;
        $passingScore = $this->examAuth->PassingScore ?? 80;
        $attemptsRemaining = $this->examAuth->AttemptsRemaining();

        $message = (new MailMessage)
            ->subject('📊 Exam Result - ' . $courseName)
            ->greeting('Exam Not Passed')
            ->line('Your exam score did not meet the passing threshold.')
            ->line('**Course:** ' . $courseName)
            ->line('**Your Score:** ' . $score . '%')
            ->line('**Passing Score:** ' . $passingScore . '%')
            ->line('**Attempts Remaining:** ' . $attemptsRemaining);

        if ($attemptsRemaining > 0) {
            $message->action('Review & Retry', route('classroom.course', $this->examAuth->course_auth_id))
                ->line('Review the course material and try again when ready.');
        } else {
            $message->line('No attempts remaining. Contact support for assistance.');
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Exam Not Passed',
            'message' => 'Score: ' . ($this->examAuth->score ?? 0) . '%. ' . $this->examAuth->AttemptsRemaining() . ' attempts remaining.',
            'exam_auth_id' => $this->examAuth->id,
            'course_auth_id' => $this->examAuth->course_auth_id,
            'course_name' => $this->examAuth->CourseAuth->Course->title ?? null,
            'score' => $this->examAuth->score,
            'passing_score' => $this->examAuth->PassingScore,
            'attempts_remaining' => $this->examAuth->AttemptsRemaining(),
            'icon' => 'times-circle',
            'color' => 'warning',
            'priority' => 'high',
            'url' => route('classroom.course', $this->examAuth->course_auth_id),
        ];
    }
}
