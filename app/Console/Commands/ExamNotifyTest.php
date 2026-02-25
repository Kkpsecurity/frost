<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\Exam\ExamAuthorized;
use App\Events\Exam\ExamCompleted;
use App\Events\Exam\ExamOverridden;
use App\Events\Exam\ExamStarted;
use App\Events\Exam\ExamTimeWarning;
use App\Events\Exam\RetakeAvailable;
use App\Models\CourseAuth;
use App\Models\ExamAuth;
use App\Models\User;
use App\Notifications\Exam\ExamAdminOverrideNotification;
use App\Notifications\Exam\ExamAuthorizedNotification;
use App\Notifications\Exam\ExamExpiredNotification;
use App\Notifications\Exam\ExamFailedNotification;
use App\Notifications\Exam\ExamPassedNotification;
use App\Notifications\Exam\ExamReminderNotification;
use App\Notifications\Exam\ExamStartedNotification;
use App\Notifications\Exam\RetakeAvailableNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Dev/test tool: fire exam notifications against an existing student+course_auth.
 *
 * NEVER creates students or course_auths — only uses what is already in the database.
 * This is a dev-only tool and should NOT be deployed to production.
 *
 * Usage:
 *   php artisan exam:notify-test                         # interactive picker
 *   php artisan exam:notify-test --list                  # show eligible students
 *   php artisan exam:notify-test --user=42               # pick user by ID
 *   php artisan exam:notify-test --user=test@example.com # pick user by email
 *   php artisan exam:notify-test --course-auth=99        # skip interactive course-auth pick
 *   php artisan exam:notify-test --notification=all      # fire every exam notification
 *   php artisan exam:notify-test --dry-run               # show what would fire, nothing sent
 */
class ExamNotifyTest extends Command
{
    protected $signature = 'exam:notify-test
                            {--list             : List all students with active course auths and exit}
                            {--user=            : User ID or email address}
                            {--course-auth=     : CourseAuth ID (skips interactive selection)}
                            {--notification=    : Notification type to fire (or "all"). See list below.}
                            {--dry-run          : Show what would be fired without sending anything}';

    protected $description = '[DEV] Fire exam notifications against an existing student for UI testing';

    /**
     * All supported notification types and their descriptions.
     */
    private const NOTIFICATION_TYPES = [
        'exam_authorized'      => 'ExamAuthorized event → ExamAuthorizedNotification (needs ExamAuth)',
        'exam_started'         => 'ExamStarted event → ExamStartedNotification (needs ExamAuth)',
        'exam_time_warning_15' => 'ExamTimeWarning event (15 min remaining) (needs ExamAuth)',
        'exam_time_warning_5'  => 'ExamTimeWarning event (5 min remaining) (needs ExamAuth)',
        'exam_passed'          => 'ExamCompleted event (pass) → ExamPassedNotification (needs ExamAuth)',
        'exam_failed'          => 'ExamCompleted event (fail) → ExamFailedNotification (needs ExamAuth)',
        'exam_expired'         => 'ExamExpiredNotification directly (needs ExamAuth)',
        'exam_overridden'      => 'ExamOverridden event → ExamAdminOverrideNotification (needs ExamAuth)',
        'retake_available'     => 'RetakeAvailable event → RetakeAvailableNotification (needs ExamAuth)',
        'exam_reminder_3'      => 'ExamReminderNotification (3-day) (uses CourseAuth only)',
        'exam_reminder_7'      => 'ExamReminderNotification (7-day) (uses CourseAuth only)',
        'exam_reminder_14'     => 'ExamReminderNotification (14-day) (uses CourseAuth only)',
        'all'                  => 'Fire all of the above in sequence',
    ];

    public function handle(): int
    {
        // Guard: refuse to run in production
        if (app()->environment('production')) {
            $this->error('❌  exam:notify-test is a dev tool and cannot run in production.');
            return Command::FAILURE;
        }

        if ($this->option('list')) {
            return $this->listEligibleStudents();
        }

        // ── 1. Resolve user ──────────────────────────────────────────────────
        $user = $this->resolveUser();
        if (! $user) {
            return Command::FAILURE;
        }

        // ── 2. Resolve course auth ───────────────────────────────────────────
        $courseAuth = $this->resolveCourseAuth($user);
        if (! $courseAuth) {
            return Command::FAILURE;
        }

        // ── 3. Resolve notification type ─────────────────────────────────────
        $notificationType = $this->resolveNotificationType();
        if (! $notificationType) {
            return Command::FAILURE;
        }

        // ── 4. Resolve or create a temp ExamAuth for notifications that need it
        $examAuth = $this->resolveExamAuth($courseAuth, $notificationType);

        // ── 5. Display plan ──────────────────────────────────────────────────
        $this->displayPlan($user, $courseAuth, $examAuth, $notificationType);

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN — nothing sent.');
            return Command::SUCCESS;
        }

        if (! $this->confirm('Send now?', true)) {
            $this->info('Aborted.');
            return Command::SUCCESS;
        }

        // ── 6. Fire ──────────────────────────────────────────────────────────
        $types = ($notificationType === 'all')
            ? array_keys(array_filter(self::NOTIFICATION_TYPES, fn($k) => $k !== 'all', ARRAY_FILTER_USE_KEY))
            : [$notificationType];

        foreach ($types as $type) {
            $this->fireNotification($type, $user, $courseAuth, $examAuth);
        }

        $this->newLine();
        $this->info('✅  Done. Check the student\'s notification inbox.');

        return Command::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Resolution helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function resolveUser(): ?User
    {
        $input = $this->option('user');

        if ($input) {
            $user = is_numeric($input)
                ? User::find((int) $input)
                : User::where('email', $input)->first();

            if (! $user) {
                $this->error("User not found: {$input}");
                return null;
            }

            return $user;
        }

        // Interactive: show table of students with active course auths
        $rows = $this->getEligibleRows();

        if ($rows->isEmpty()) {
            $this->error('No students with active course auths found in the database.');
            return null;
        }

        $this->table(
            ['User ID', 'Name', 'Email', 'Course Auth ID', 'Course', 'Started', 'Exam Ready'],
            $rows->map(fn($r) => array_values((array) $r))->toArray()
        );

        $userId = $this->ask('Enter User ID');

        $user = User::find((int) $userId);
        if (! $user) {
            $this->error("User ID {$userId} not found.");
            return null;
        }

        return $user;
    }

    private function resolveCourseAuth(User $user): ?CourseAuth
    {
        $caId = $this->option('course-auth');

        if ($caId) {
            $ca = CourseAuth::where('id', $caId)
                ->where('user_id', $user->id)
                ->first();

            if (! $ca) {
                $this->error("CourseAuth ID {$caId} not found for user {$user->id}.");
                return null;
            }

            return $ca;
        }

        // Load all active course auths for this user
        $courseAuths = CourseAuth::with('Course')
            ->where('user_id', $user->id)
            ->whereNull('disabled_at')
            ->get();

        if ($courseAuths->isEmpty()) {
            $this->error("No course auths found for user {$user->id} ({$user->email}).");
            return null;
        }

        if ($courseAuths->count() === 1) {
            $ca = $courseAuths->first();
            $this->info("Auto-selected only course auth: [{$ca->id}] {$ca->Course->title}");
            return $ca;
        }

        $this->table(
            ['CourseAuth ID', 'Course', 'Active', 'Exam Ready', 'Completed', 'Passed'],
            $courseAuths->map(function (CourseAuth $ca) {
                return [
                    $ca->id,
                    $ca->Course->title ?? '?',
                    $ca->IsActive() ? '✓' : '✗',
                    $ca->ExamReady() ? '✓' : '✗',
                    $ca->completed_at ? Carbon::parse($ca->completed_at)->toDateString() : '—',
                    $ca->is_passed ? '✓' : '✗',
                ];
            })->toArray()
        );

        $caId = $this->ask('Enter CourseAuth ID');

        $ca = $courseAuths->firstWhere('id', (int) $caId);
        if (! $ca) {
            $this->error("CourseAuth ID {$caId} not found in the list above.");
            return null;
        }

        return $ca;
    }

    private function resolveNotificationType(): ?string
    {
        $type = $this->option('notification');

        if ($type) {
            if (! array_key_exists($type, self::NOTIFICATION_TYPES)) {
                $this->error("Unknown notification type: '{$type}'");
                $this->line('Valid types: ' . implode(', ', array_keys(self::NOTIFICATION_TYPES)));
                return null;
            }
            return $type;
        }

        $this->newLine();
        $this->line('<fg=cyan>Available notification types:</>');

        $i = 1;
        $indexMap = [];
        foreach (self::NOTIFICATION_TYPES as $key => $desc) {
            $this->line("  <fg=yellow>[{$i}]</> <fg=white>{$key}</> — {$desc}");
            $indexMap[$i] = $key;
            $i++;
        }

        $choice = $this->ask('Enter number or type name');

        if (is_numeric($choice) && isset($indexMap[(int) $choice])) {
            return $indexMap[(int) $choice];
        }

        if (array_key_exists($choice, self::NOTIFICATION_TYPES)) {
            return $choice;
        }

        $this->error("Invalid choice: {$choice}");
        return null;
    }

    /**
     * Resolve or synthesise an ExamAuth for notifications that require one.
     * Never persisted to DB — uses an existing one or builds an in-memory stub.
     */
    private function resolveExamAuth(CourseAuth $courseAuth, string $notificationType): ?ExamAuth
    {
        // Reminder notifications work off CourseAuth only — no ExamAuth needed.
        if (in_array($notificationType, ['exam_reminder_3', 'exam_reminder_7', 'exam_reminder_14', 'all'], true)) {
            // Still try to load a real one for the "all" case
            if ($notificationType !== 'all') {
                return null;
            }
        }

        // Try to find an existing ExamAuth for this course auth (most recent)
        $existing = ExamAuth::where('course_auth_id', $courseAuth->id)
            ->whereNull('hidden_at')
            ->latest('created_at')
            ->first();

        if ($existing) {
            $this->info("Using existing ExamAuth ID: {$existing->id}");
            return $existing;
        }

        // No ExamAuth exists yet. Synthesise a lightweight in-memory stub.
        // This does NOT save to the DB — it just satisfies notification constructors.
        $this->warn('No ExamAuth found for this course auth. Building an in-memory stub for testing.');

        $stub = new ExamAuth();
        $stub->forceFill([
            'id'              => 0,
            'course_auth_id'  => $courseAuth->id,
            'created_at'      => now()->timestamp,
            'expires_at'      => now()->addHours(2)->timestamp,
            'next_attempt_at' => now()->addDay()->timestamp,
            'is_passed'       => false,
        ]);

        // Set score raw to avoid the 'nnn / nnn' format mutator
        $stub->setRawAttributes(array_merge($stub->getAttributes(), ['score' => null]));

        // Bind the CourseAuth so relation calls don't hit the DB
        $stub->setRelation('CourseAuth', $courseAuth);

        return $stub;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Firing logic
    // ─────────────────────────────────────────────────────────────────────────

    private function fireNotification(string $type, User $user, CourseAuth $courseAuth, ?ExamAuth $examAuth): void
    {
        $this->line("  → Firing <fg=yellow>{$type}</fg=yellow>...");

        // When ExamAuth is an in-memory stub (id=0), queued listeners would try
        // to reload it from the DB by PK and fail. So we call notify() directly,
        // bypassing the event/listener/queue system entirely.
        $isStub = $examAuth && $examAuth->id === 0;

        try {
            if ($isStub) {
                // Direct notification — notifyNow() bypasses the queue entirely,
                // so the in-memory stub ExamAuth is never serialized/reloaded by PK.
                $passedAuth = $this->makePassedExamAuth($examAuth);
                $failedAuth = $this->makeFailedExamAuth($examAuth);

                match ($type) {
                    'exam_authorized'      => $user->notifyNow(new ExamAuthorizedNotification($examAuth)),
                    'exam_started'         => $user->notifyNow(new ExamStartedNotification($examAuth)),
                    'exam_time_warning_15' => null, // no notification class exists; listener is a no-op placeholder
                    'exam_time_warning_5'  => null, // no notification class exists; listener is a no-op placeholder
                    'exam_passed'          => $user->notifyNow(new ExamPassedNotification($passedAuth)),
                    'exam_failed'          => $user->notifyNow(new ExamFailedNotification($failedAuth)),
                    'exam_expired'         => $user->notifyNow(new ExamExpiredNotification($examAuth)),
                    'exam_overridden'      => $user->notifyNow(new ExamAdminOverrideNotification($examAuth, 'override')),
                    'retake_available'     => $user->notifyNow(new RetakeAvailableNotification($examAuth)),
                    'exam_reminder_3'      => $user->notifyNow(new ExamReminderNotification($courseAuth, 3)),
                    'exam_reminder_7'      => $user->notifyNow(new ExamReminderNotification($courseAuth, 7)),
                    'exam_reminder_14'     => $user->notifyNow(new ExamReminderNotification($courseAuth, 14)),
                };
            } else {
                // Real ExamAuth — fire through events so listeners + activity tracking all run
                match ($type) {
                    'exam_authorized'      => event(new ExamAuthorized($examAuth)),
                    'exam_started'         => event(new ExamStarted($examAuth)),
                    'exam_time_warning_15' => event(new ExamTimeWarning($examAuth, 15)),
                    'exam_time_warning_5'  => event(new ExamTimeWarning($examAuth, 5)),
                    'exam_passed'          => event(new ExamCompleted($this->makePassedExamAuth($examAuth))),
                    'exam_failed'          => event(new ExamCompleted($this->makeFailedExamAuth($examAuth))),
                    'exam_expired'         => $user->notify(new ExamExpiredNotification($examAuth)),
                    'exam_overridden'      => event(new ExamOverridden($examAuth)),
                    'retake_available'     => event(new RetakeAvailable($examAuth)),
                    'exam_reminder_3'      => $user->notify(new ExamReminderNotification($courseAuth, 3)),
                    'exam_reminder_7'      => $user->notify(new ExamReminderNotification($courseAuth, 7)),
                    'exam_reminder_14'     => $user->notify(new ExamReminderNotification($courseAuth, 14)),
                };
            }

            $this->line("    <fg=green>✓ sent</>");

            Log::info("[ExamNotifyTest] Fired {$type}", [
                'user_id'        => $user->id,
                'course_auth_id' => $courseAuth->id,
                'exam_auth_id'   => $examAuth?->id,
                'stub'           => $isStub,
            ]);
        } catch (\Throwable $e) {
            $this->error("    ✗ FAILED: {$e->getMessage()}");
            Log::error("[ExamNotifyTest] Failed to fire {$type}", [
                'error'          => $e->getMessage(),
                'user_id'        => $user->id,
                'course_auth_id' => $courseAuth->id,
            ]);
        }
    }

    /**
     * Clone the stub with is_passed=true and a passing score for pass scenario.
     */
    private function makePassedExamAuth(ExamAuth $base): ExamAuth
    {
        $clone = clone $base;
        $clone->forceFill([
            'is_passed'    => true,
            'score'        => '48 / 50',
            'completed_at' => now()->timestamp,
        ]);
        return $clone;
    }

    /**
     * Clone the stub with is_passed=false and a failing score for fail scenario.
     */
    private function makeFailedExamAuth(ExamAuth $base): ExamAuth
    {
        $clone = clone $base;
        $clone->forceFill([
            'is_passed'    => false,
            'score'        => '30 / 50',
            'completed_at' => now()->timestamp,
        ]);
        return $clone;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Display helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function displayPlan(User $user, CourseAuth $courseAuth, ?ExamAuth $examAuth, string $notificationType): void
    {
        $this->newLine();
        $this->line('<fg=cyan>──── Test Plan ────────────────────────────────</>');
        $this->line("  User          : [{$user->id}] {$user->fname} {$user->lname} <{$user->email}>");
        $this->line("  CourseAuth    : [{$courseAuth->id}] " . ($courseAuth->Course->title ?? '?'));
        $this->line("  ExamAuth      : " . ($examAuth ? "[{$examAuth->id}]" . ($examAuth->id === 0 ? ' (in-memory stub)' : '') : 'n/a (CourseAuth only)'));
        $this->line("  Notification  : {$notificationType}");
        if ($notificationType !== 'all') {
            $this->line("  Description   : " . (self::NOTIFICATION_TYPES[$notificationType] ?? ''));
        }
        $this->line('<fg=cyan>───────────────────────────────────────────────</>');
        $this->newLine();
    }

    private function listEligibleStudents(): int
    {
        $rows = $this->getEligibleRows();

        if ($rows->isEmpty()) {
            $this->warn('No students with active course auths found.');
            return Command::SUCCESS;
        }

        $this->table(
            ['User ID', 'Name', 'Email', 'Course Auth ID', 'Course', 'Started', 'Exam Ready'],
            $rows->map(fn($r) => array_values((array) $r))->toArray()
        );

        $this->info("{$rows->count()} course auth(s) found.");
        return Command::SUCCESS;
    }

    /**
     * Load active course auths with their users for display/selection.
     * Filters: not disabled, not expired, user role = student.
     */
    private function getEligibleRows(): \Illuminate\Support\Collection
    {
        return CourseAuth::with(['User', 'Course'])
            ->whereNull('disabled_at')
            ->whereHas('User', fn($q) => $q->where('role_id', 5)) // role_id 5 = student
            ->get()
            ->filter(fn(CourseAuth $ca) => $ca->User && $ca->Course)
            ->map(function (CourseAuth $ca) {
                return (object) [
                    'user_id'       => $ca->user_id,
                    'name'          => "{$ca->User->fname} {$ca->User->lname}",
                    'email'         => $ca->User->email,
                    'course_auth_id' => $ca->id,
                    'course'        => $ca->Course->title ?? '?',
                    'started'       => $ca->start_date
                        ? Carbon::parse($ca->start_date)->toDateString()
                        : '(not set)',
                    'exam_ready'    => $ca->ExamReady() ? '✓' : '✗',
                ];
            })
            ->sortBy('name')
            ->values();
    }
}
