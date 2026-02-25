<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Notifications Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all notification definitions for user-facing alerts
    | and notifications throughout the platform. Each notification includes:
    | - Unique key/identifier
    | - Category for organization
    | - Priority level (critical, high, medium, low)
    | - Default channels (database, mail, browser, sms)
    | - User preference control (can user disable?)
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    */
    'channels' => [
        'database' => true,    // In-app notifications
        'mail' => true,        // Email notifications
        'browser' => true,     // Browser push notifications
        'sms' => false,        // SMS notifications (future)
    ],

    /*
    |--------------------------------------------------------------------------
    | Default User Preferences
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'email_alerts' => true,
        'browser_notifications' => true,
        'sms_alerts' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Exam Reminder Intervals (in days)
    |--------------------------------------------------------------------------
    | Defines how many days after exam becomes available to send reminders
    | Command: php artisan exams:send-reminders
    */
    'exam_reminder_days' => [3, 7, 14],

    /*
    |--------------------------------------------------------------------------
    | Notification Definitions by Category
    |--------------------------------------------------------------------------
    */

    'notifications' => [

        /*
        |--------------------------------------------------------------------------
        | 1. ACCOUNT & REGISTRATION
        |--------------------------------------------------------------------------
        */
        'account' => [
            'welcome' => [
                'key' => 'account.welcome',
                'name' => 'Welcome Email',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'email_verification_required' => [
                'key' => 'account.email_verification_required',
                'name' => 'Email Verification Required',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'email_verified' => [
                'key' => 'account.email_verified',
                'name' => 'Email Verified Successfully',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'profile_incomplete' => [
                'key' => 'account.profile_incomplete',
                'name' => 'Profile Incomplete',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'profile_updated' => [
                'key' => 'account.profile_updated',
                'name' => 'Profile Updated Successfully',
                'priority' => 'low',
                'channels' => ['database'],
                'user_controllable' => true,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 2. COURSE ENROLLMENT & PURCHASE
        |--------------------------------------------------------------------------
        */
        'enrollment' => [
            'order_created' => [
                'key' => 'enrollment.order_created',
                'name' => 'Order Created',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'payment_processing' => [
                'key' => 'enrollment.payment_processing',
                'name' => 'Payment Processing',
                'priority' => 'medium',
                'channels' => ['database'],
                'user_controllable' => false,
            ],
            'payment_successful' => [
                'key' => 'enrollment.payment_successful',
                'name' => 'Payment Successful',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'payment_failed' => [
                'key' => 'enrollment.payment_failed',
                'name' => 'Payment Failed',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'course_enrollment_confirmed' => [
                'key' => 'enrollment.confirmed',
                'name' => 'Course Enrollment Confirmed',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'course_materials_available' => [
                'key' => 'enrollment.materials_available',
                'name' => 'Course Materials Available',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 3. PRE-CLASSROOM PREPARATION
        |--------------------------------------------------------------------------
        */
        'preparation' => [
            'terms_required' => [
                'key' => 'preparation.terms_required',
                'name' => 'Terms Agreement Required',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'classroom_rules_required' => [
                'key' => 'preparation.rules_required',
                'name' => 'Classroom Rules Required',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'class_approaching' => [
                'key' => 'preparation.class_approaching',
                'name' => 'Class Date Approaching',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => true,
            ],
            'class_tomorrow' => [
                'key' => 'preparation.class_tomorrow',
                'name' => 'Class Starts Tomorrow',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser', 'webpush'],
                'user_controllable' => false,
            ],
            'class_starting_soon' => [
                'key' => 'preparation.class_starting_soon',
                'name' => 'Class Starting Soon (1 hour)',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'range_date_required' => [
                'key' => 'preparation.range_date_required',
                'name' => 'Range Date Required',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 4. CLASSROOM EXPERIENCE
        |--------------------------------------------------------------------------
        */
        'classroom' => [
            'session_started' => [
                'key' => 'classroom.session_started',
                'name' => 'Class Session Started',
                'priority' => 'critical',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'lesson_started' => [
                'key' => 'classroom.lesson_started',
                'name' => 'Lesson Started',
                'priority' => 'high',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'lesson_paused' => [
                'key' => 'classroom.lesson_paused',
                'name' => 'Lesson On Break',
                'priority' => 'medium',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'lesson_resumed' => [
                'key' => 'classroom.lesson_resumed',
                'name' => 'Lesson Resumed',
                'priority' => 'medium',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'lesson_completed' => [
                'key' => 'classroom.lesson_completed',
                'name' => 'Lesson Completed',
                'priority' => 'low',
                'channels' => ['database'],
                'user_controllable' => true,
            ],
            'instructor_message' => [
                'key' => 'classroom.instructor_message',
                'name' => 'Instructor Message',
                'priority' => 'high',
                'channels' => ['database', 'browser'],
                'user_controllable' => true,
            ],
            'attention_required' => [
                'key' => 'classroom.attention_required',
                'name' => 'Attention Required',
                'priority' => 'critical',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'kicked_from_classroom' => [
                'key' => 'classroom.kicked',
                'name' => 'Kicked from Classroom',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 5. IDENTITY VERIFICATION
        |--------------------------------------------------------------------------
        */
        'verification' => [
            'id_required' => [
                'key' => 'verification.id_required',
                'name' => 'ID Verification Required',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'headshot_required' => [
                'key' => 'verification.headshot_required',
                'name' => 'Daily Headshot Required',
                'priority' => 'high',
                'channels' => ['database', 'browser'],
                'user_controllable' => false,
            ],
            'id_approved' => [
                'key' => 'verification.id_approved',
                'name' => 'ID Approved',
                'priority' => 'medium',
                'channels' => ['database'],
                'user_controllable' => true,
            ],
            'verification_rejected' => [
                'key' => 'verification.rejected',
                'name' => 'Verification Rejected',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'verification_complete' => [
                'key' => 'verification.complete',
                'name' => 'Verification Complete',
                'priority' => 'medium',
                'channels' => ['database', 'browser'],
                'user_controllable' => true,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 6. COURSE PROGRESS & COMPLETION
        |--------------------------------------------------------------------------
        */
        'progress' => [
            'milestone_25' => [
                'key' => 'progress.milestone_25',
                'name' => '25% Complete',
                'priority' => 'low',
                'channels' => ['database', 'browser'],
                'user_controllable' => true,
            ],
            'milestone_50' => [
                'key' => 'progress.milestone_50',
                'name' => '50% Complete',
                'priority' => 'low',
                'channels' => ['database', 'browser'],
                'user_controllable' => true,
            ],
            'milestone_75' => [
                'key' => 'progress.milestone_75',
                'name' => '75% Complete',
                'priority' => 'low',
                'channels' => ['database', 'browser'],
                'user_controllable' => true,
            ],
            'all_lessons_complete' => [
                'key' => 'progress.all_lessons_complete',
                'name' => 'All Lessons Completed',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'course_completed' => [
                'key' => 'progress.course_completed',
                'name' => 'Course Completed',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'certificate_ready' => [
                'key' => 'progress.certificate_ready',
                'name' => 'Certificate Ready',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
            'course_expiring_30days' => [
                'key' => 'progress.expiring_30days',
                'name' => 'Course Expiring in 30 Days',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'course_expiring_7days' => [
                'key' => 'progress.expiring_7days',
                'name' => 'Course Expiring in 7 Days',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 7. EXAMS & ASSESSMENTS
        |--------------------------------------------------------------------------
        */
        'exams' => [
            'exam_ready' => [
                'key' => 'notification_exam.exam_ready',
                'name' => 'Exam Ready',
                'description' => 'Notified when exam becomes available',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_authorized' => [
                'key' => 'notification_exam.exam_authorized',
                'name' => 'Exam Authorized',
                'description' => 'Notified when exam is authorized to start',
                'priority' => 'high',
                'channels' => ['database', 'mail', 'webpush'],
                'user_controllable' => true,
            ],
            'exam_started' => [
                'key' => 'notification_exam.exam_started',
                'name' => 'Exam Started',
                'description' => 'Confirmation when exam timer begins',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_submitted' => [
                'key' => 'notification_exam.exam_submitted',
                'name' => 'Exam Submitted',
                'description' => 'Confirmation when exam is submitted',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_passed' => [
                'key' => 'notification_exam.exam_passed',
                'name' => 'Exam Passed',
                'description' => 'Notified when exam is passed successfully',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_failed' => [
                'key' => 'notification_exam.exam_failed',
                'name' => 'Exam Failed',
                'description' => 'Notified when exam is not passed',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_expired' => [
                'key' => 'notification_exam.exam_expired',
                'name' => 'Exam Time Expired',
                'description' => 'CRITICAL: Time limit exceeded (always sent)',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'retake_available' => [
                'key' => 'notification_exam.retake_available',
                'name' => 'Retake Available',
                'description' => 'Notified when cooldown expires and retake is available',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'final_attempt_warning' => [
                'key' => 'notification_exam.final_attempt',
                'name' => 'Final Attempt Warning',
                'description' => 'CRITICAL: Last attempt remaining (always sent)',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'no_attempts_remaining' => [
                'key' => 'notification_exam.no_attempts',
                'name' => 'No Attempts Remaining',
                'description' => 'CRITICAL: All attempts exhausted (always sent)',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'exam_reminder' => [
                'key' => 'notification_exam.exam_reminder',
                'name' => 'Exam Reminder',
                'description' => 'Reminder for waiting exams (configurable days)',
                'priority' => 'low',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_review' => [
                'key' => 'notification_exam.exam_review',
                'name' => 'Exam Review Available',
                'description' => 'Notified when exam results are available for review',
                'priority' => 'low',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'admin_override' => [
                'key' => 'notification_exam.admin_override',
                'name' => 'Exam Admin Override',
                'description' => 'Notified when admin changes exam status',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'exam_not_ready' => [
                'key' => 'notification_exam.exam_not_ready',
                'name' => 'Exam Not Ready',
                'description' => 'Notified when exam requirements are not met',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 8. PAYMENT & BILLING
        |--------------------------------------------------------------------------
        */
        'payment' => [
            'payment_success' => [
                'key' => 'payment.payment_success',
                'name' => 'Payment Successful',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'payment_failed' => [
                'key' => 'payment.payment_failed',
                'name' => 'Payment Failed',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false, // Always notify
            ],
            'payment_pending' => [
                'key' => 'payment.payment_pending',
                'name' => 'Payment Processing',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'payment_method_added' => [
                'key' => 'payment.payment_method_added',
                'name' => 'Payment Method Added',
                'priority' => 'low',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'payment_method_removed' => [
                'key' => 'payment.payment_method_removed',
                'name' => 'Payment Method Removed',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'payment_method_expiring' => [
                'key' => 'payment.payment_method_expiring',
                'name' => 'Payment Method Expiring',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'default_payment_updated' => [
                'key' => 'payment.default_payment_updated',
                'name' => 'Default Payment Method Updated',
                'priority' => 'low',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'refund_initiated' => [
                'key' => 'payment.refund_initiated',
                'name' => 'Refund Initiated',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false, // Always notify
            ],
            'refund_processed' => [
                'key' => 'payment.refund_processed',
                'name' => 'Refund Processed',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false, // Always notify
            ],
            'invoice_generated' => [
                'key' => 'payment.invoice_generated',
                'name' => 'Invoice Generated',
                'priority' => 'low',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'receipt_emailed' => [
                'key' => 'payment.receipt_emailed',
                'name' => 'Receipt Emailed',
                'priority' => 'low',
                'channels' => ['database'],
                'user_controllable' => true,
            ],
            'balance_due' => [
                'key' => 'payment.balance_due',
                'name' => 'Balance Due',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false, // Always notify
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 9. PROFILE & ACCOUNT MANAGEMENT
        |--------------------------------------------------------------------------
        */
        'profile' => [
            'password_changed' => [
                'key' => 'profile.password_changed',
                'name' => 'Password Changed',
                'priority' => 'high',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'email_changed' => [
                'key' => 'profile.email_changed',
                'name' => 'Email Address Changed',
                'priority' => 'critical',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'suspicious_login' => [
                'key' => 'profile.suspicious_login',
                'name' => 'Suspicious Login Detected',
                'priority' => 'critical',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => false,
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | 10. SYSTEM & ADMINISTRATIVE
        |--------------------------------------------------------------------------
        */
        // Instructor-facing notifications (not student-preference controlled)
        'instructor' => [
            'student_dnc' => [
                'key'              => 'instructor.student_dnc',
                'name'             => 'Student DNC Alert',
                'priority'         => 'high',
                'channels'         => ['database', 'mail'],
                'user_controllable' => false,
            ],
        ],

        'system' => [
            'maintenance_scheduled' => [
                'key' => 'system.maintenance_scheduled',
                'name' => 'Maintenance Scheduled',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => true,
            ],
            'policy_updated' => [
                'key' => 'system.policy_updated',
                'name' => 'Policy Updated',
                'priority' => 'medium',
                'channels' => ['database', 'mail'],
                'user_controllable' => false,
            ],
            'support_response' => [
                'key' => 'system.support_response',
                'name' => 'Support Response',
                'priority' => 'medium',
                'channels' => ['database', 'mail', 'browser'],
                'user_controllable' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Levels
    |--------------------------------------------------------------------------
    */
    'priorities' => [
        'critical' => [
            'color' => 'danger',
            'icon' => 'fa-exclamation-circle',
            'sound' => true,
        ],
        'high' => [
            'color' => 'warning',
            'icon' => 'fa-exclamation-triangle',
            'sound' => false,
        ],
        'medium' => [
            'color' => 'info',
            'icon' => 'fa-info-circle',
            'sound' => false,
        ],
        'low' => [
            'color' => 'secondary',
            'icon' => 'fa-bell',
            'sound' => false,
        ],
    ],

];
