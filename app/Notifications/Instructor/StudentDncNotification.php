<?php

namespace App\Notifications\Instructor;

use App\Models\StudentLesson;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies the instructor when one of their students is marked Do-Not-Complete (DNC).
 *
 * Non-controllable — always sent regardless of any preference setting.
 * Channels: database + mail.
 */
class StudentDncNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  StudentLesson  $studentLesson   The lesson that was marked DNC.
     * @param  User           $student         The student who received the DNC.
     * @param  string         $studentName     Display name for the student.
     * @param  string         $lessonName      The lesson title / identifier.
     * @param  string         $courseName      The course the lesson belongs to.
     */
    public function __construct(
        protected StudentLesson $studentLesson,
        protected User          $student,
        protected string        $studentName,
        protected string        $lessonName,
        protected string        $courseName,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * Non-controllable — always database + mail.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dncAt = $this->studentLesson->dnc_at?->format('M j, Y g:i A T') ?? 'unknown time';

        return (new MailMessage)
            ->subject('DNC Alert: ' . $this->studentName . ' — ' . $this->lessonName)
            ->greeting('Hello ' . ($notifiable->fname ?? 'Instructor') . ',')
            ->line('A student in your class has been marked **Do Not Complete (DNC)**.')
            ->line('**Student:** ' . $this->studentName)
            ->line('**Course:** ' . $this->courseName)
            ->line('**Lesson:** ' . $this->lessonName)
            ->line('**Marked at:** ' . $dncAt)
            ->line('Please review this student\'s progress and contact support if a makeup is required.')
            ->action('Open Instructor Dashboard', route('admin.instructors.dashboard'))
            ->salutation('The ' . config('app.name') . ' System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'               => 'instructor.student_dnc',
            'title'              => 'DNC: ' . $this->studentName,
            'message'            => $this->studentName . ' was marked Do Not Complete for ' . $this->lessonName . ' (' . $this->courseName . ').',
            'icon'               => 'user-slash',
            'priority_color'     => 'warning',
            'url'                => route('admin.instructors.dashboard'),
            'student_id'         => $this->student->id,
            'student_name'       => $this->studentName,
            'student_lesson_id'  => $this->studentLesson->id,
            'lesson_name'        => $this->lessonName,
            'course_name'        => $this->courseName,
            'dnc_at'             => $this->studentLesson->dnc_at?->toISOString(),
        ];
    }
}
