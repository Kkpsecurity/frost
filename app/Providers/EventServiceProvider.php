<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            \App\Listeners\SendEmailVerifiedNotification::class,
        ],

        // Payment Events
        \App\Events\Payment\PaymentCompleted::class => [
            \App\Listeners\Payment\SendPaymentSuccessNotifications::class,
        ],
        \App\Events\Payment\PaymentFailed::class => [
            \App\Listeners\Payment\SendPaymentFailedNotification::class,
        ],
        \App\Events\Payment\PaymentPending::class => [
            \App\Listeners\Payment\SendPaymentPendingNotification::class,
        ],
        \App\Events\Payment\PaymentMethodAdded::class => [
            \App\Listeners\Payment\SendPaymentMethodAddedNotification::class,
        ],
        \App\Events\Payment\PaymentMethodRemoved::class => [
            \App\Listeners\Payment\SendPaymentMethodRemovedNotification::class,
        ],
        \App\Events\Payment\RefundProcessed::class => [
            \App\Listeners\Payment\SendRefundProcessedNotification::class,
        ],

        // Enrollment Events
        \App\Events\Enrollment\CourseEnrolled::class => [
            \App\Listeners\Enrollment\SendCourseEnrolledNotification::class,
            \App\Listeners\Preparation\SendTermsRequiredOnEnrollment::class,
        ],

        // Preparation Events (Phase 9a — event-driven)
        \App\Events\Preparation\TermsAccepted::class => [
            \App\Listeners\Preparation\SendRulesRequiredNotification::class,
        ],
        \App\Events\Preparation\RangeDateRequired::class => [
            \App\Listeners\Preparation\SendRangeDateRequiredNotification::class,
        ],

        // Identity Verification Events
        \App\Events\Verification\ValidationApproved::class => [
            \App\Listeners\Verification\SendValidationApprovedNotifications::class,
        ],
        \App\Events\Verification\ValidationRejected::class => [
            \App\Listeners\Verification\SendValidationRejectedNotification::class,
        ],
        \App\Events\Verification\PhotoRequired::class => [
            \App\Listeners\Verification\SendPhotoRequiredNotification::class,
        ],

        // Exam Events
        \App\Events\Exam\ExamAuthorized::class => [
            \App\Listeners\Exam\SendExamAuthorizedNotification::class,
        ],
        \App\Events\Exam\ExamStarted::class => [
            \App\Listeners\Exam\SendExamStartedNotification::class,
        ],
        \App\Events\Exam\ExamCompleted::class => [
            \App\Listeners\Exam\SendExamCompletedNotifications::class,
        ],
        \App\Events\Exam\ExamTimeWarning::class => [
            \App\Listeners\Exam\SendExamTimeWarningNotification::class,
        ],
        \App\Events\Exam\RetakeAvailable::class => [
            \App\Listeners\Exam\SendRetakeAvailableNotification::class,
        ],
        \App\Events\Exam\ExamOverridden::class => [
            \App\Listeners\Exam\SendExamOverriddenNotification::class,
        ],

        // Classroom Experience Events
        \App\Events\Classroom\ClassSessionStarted::class => [
            \App\Listeners\Classroom\SendClassSessionStartedNotification::class,
        ],
        \App\Events\Classroom\LessonStarted::class => [
            \App\Listeners\Classroom\SendLessonStartedNotification::class,
        ],
        \App\Events\Classroom\LessonCompleted::class => [
            \App\Listeners\Classroom\SendLessonCompletedNotification::class,
        ],
        \App\Events\Classroom\LessonPaused::class => [
            \App\Listeners\Classroom\SendLessonPausedNotification::class,
        ],
        \App\Events\Classroom\LessonResumed::class => [
            \App\Listeners\Classroom\SendLessonResumedNotification::class,
        ],
        \App\Events\Classroom\InstructorMessageSent::class => [
            \App\Listeners\Classroom\SendInstructorMessageNotification::class,
        ],
        \App\Events\Classroom\StudentEjectedFromClassroom::class => [
            \App\Listeners\Classroom\SendStudentKickedNotification::class,
        ],

        // Course Progress & Completion Events (Phase 11)
        \App\Events\Progress\LessonMilestoneReached::class => [
            \App\Listeners\Progress\SendLessonMilestoneNotification::class,
        ],
        \App\Events\Progress\AllLessonsCompleted::class => [
            \App\Listeners\Progress\SendAllLessonsCompletedNotification::class,
        ],
        \App\Events\Progress\CourseCompleted::class => [
            \App\Listeners\Progress\SendCourseCompletedNotifications::class,
        ],
        \App\Events\Progress\CourseExpiringSoon::class => [
            \App\Listeners\Progress\SendCourseExpiringSoonNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
