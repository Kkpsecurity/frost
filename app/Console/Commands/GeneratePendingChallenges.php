<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Classes\Students\Challenger;
use App\Models\StudentLesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * GeneratePendingChallenges
 *
 * Server-side fallback for the Challenge engine when student polling stops
 * (inactive tab, browser closed, network issues, etc.).
 *
 * - Runs Challenger::Ready() for active (in-progress) InstLessons so challenges
 *   are created on schedule even without the frontend.
 * - Runs Challenger::EOLReady() for instructor-completed InstLessons so EOL
 *   challenges are still issued (and will fail/DNC when unanswered).
 *
 * This command does not mark challenges failed directly; that is handled by
 * the existing challenges:expire-pending scheduler (and Challenger validation).
 */
class GeneratePendingChallenges extends Command
{
    protected $signature = 'challenges:generate-pending
                            {--lookback-hours=18 : Only process InstLessons started within this many hours}
                            {--limit=2000 : Max StudentLessons to process per run}
                            {--dry-run : Show what would run without writing changes}';

    protected $description = 'Generate/advance participation challenges server-side when student polling is inactive';

    public function handle(): int
    {
        $lookbackHours = (int) $this->option('lookback-hours');
        $lookbackHours = $lookbackHours > 0 ? $lookbackHours : 18;

        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 2000;

        $isDryRun = (bool) $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No challenges will be created/updated');
        }

        $processed = 0;
        $readyChecks = 0;
        $eolChecks = 0;
        $responses = 0;
        $skipped = 0;
        $errors = 0;

        $cutoff = now()->subHours($lookbackHours);

        // Some environments may not yet have all optional columns migrated.
        // Guard any query filters that reference them.
        $hasStudentLessonFailedAt = false;
        $hasStudentUnitAttendanceType = false;

        try {
            $hasStudentLessonFailedAt = Schema::hasColumn('student_lesson', 'failed_at');
            $hasStudentUnitAttendanceType = Schema::hasColumn('student_unit', 'attendance_type');
        } catch (\Throwable $e) {
            // Non-fatal: if schema introspection fails for any reason, fall back to safest behavior.
            $hasStudentLessonFailedAt = false;
            $hasStudentUnitAttendanceType = false;
        }

        // Cache completed lesson IDs per StudentUnit so we don't re-query per row.
        $completedLessonIdsByStudentUnitId = [];

        $query = StudentLesson::query()
            ->whereNull('completed_at')
            ->whereNull('dnc_at')
            ->whereNotNull('inst_lesson_id')
            ->whereHas('StudentUnit', function ($q) use ($hasStudentUnitAttendanceType) {
                if ($hasStudentUnitAttendanceType) {
                    $q->where('attendance_type', 'online');
                }

                $q->whereNull('completed_at')
                    ->whereNull('ejected_at');
            })
            ->whereHas('InstLesson', function ($q) use ($cutoff) {
                $q->where('created_at', '>=', $cutoff);
            })
            ->with(['StudentUnit', 'InstLesson', 'LatestChallenge'])
            ->orderBy('id');

        if ($hasStudentLessonFailedAt) {
            $query->whereNull('failed_at');
        }

        $query->chunkById(100, function ($studentLessons) use (
            &$processed,
            &$readyChecks,
            &$eolChecks,
            &$responses,
            &$skipped,
            &$errors,
            $limit,
            $isDryRun,
            &$completedLessonIdsByStudentUnitId
        ) {
            $now = now();

            foreach ($studentLessons as $studentLesson) {
                if ($processed >= $limit) {
                    return false; // stop chunking
                }

                $processed++;

                $instLesson = $studentLesson->InstLesson;
                $studentUnit = $studentLesson->StudentUnit;

                if (! $instLesson || ! $studentUnit) {
                    $skipped++;
                    continue;
                }

                $studentUnitId = (int) $studentLesson->student_unit_id;

                if (! array_key_exists($studentUnitId, $completedLessonIdsByStudentUnitId)) {
                    $completedLessonIdsByStudentUnitId[$studentUnitId] = StudentLesson::query()
                        ->where('student_unit_id', $studentUnitId)
                        ->whereNotNull('completed_at')
                        ->pluck('lesson_id')
                        ->toArray();
                }

                $completedLessonIds = $completedLessonIdsByStudentUnitId[$studentUnitId];

                try {
                    // Instructor-completed lesson: ensure EOL challenge is issued.
                    if ($instLesson->completed_at) {
                        $eolChecks++;

                        if ($isDryRun) {
                            continue;
                        }

                        $resp = Challenger::EOLReady($studentLesson, $completedLessonIds);

                        if ($resp && $resp->challenge_id) {
                            $responses++;
                        }

                        continue;
                    }

                    // Active lesson: do not generate new challenges during pause/break.
                    if ($instLesson->is_paused) {
                        $skipped++;
                        continue;
                    }

                    // If there is already an active (pending) challenge that hasn't expired,
                    // don't run Ready() — it would only re-send the current challenge.
                    $pending = $studentLesson->LatestChallenge;

                    if ($pending && $pending->expires_at && $now->lt($pending->expires_at)) {
                        $skipped++;
                        continue;
                    }

                    $readyChecks++;

                    if ($isDryRun) {
                        continue;
                    }

                    $resp = Challenger::Ready($studentLesson, $completedLessonIds);

                    if ($resp && $resp->challenge_id) {
                        $responses++;
                    }
                } catch (\Throwable $e) {
                    $errors++;

                    Log::error('GeneratePendingChallenges: challenger tick failed', [
                        'student_lesson_id' => (int) $studentLesson->id,
                        'student_unit_id' => (int) $studentLesson->student_unit_id,
                        'inst_lesson_id' => (int) $studentLesson->inst_lesson_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return true;
        });

        $this->info(
            "Processed: {$processed} | " .
                "Ready checks: {$readyChecks} | " .
                "EOL checks: {$eolChecks} | " .
                "Responses: {$responses} | " .
                "Skipped: {$skipped} | " .
                "Errors: {$errors}"
        );

        return 0;
    }
}
