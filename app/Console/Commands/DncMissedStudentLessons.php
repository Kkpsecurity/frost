<?php

namespace App\Console\Commands;

use App\Models\InstLesson;
use App\Models\StudentLesson;
use App\Models\StudentUnit;
use App\Services\RCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * DncMissedStudentLessons
 *
 * Server-side fallback for live classroom lessons:
 * - If an InstLesson was started and the join window has elapsed,
 * - and a StudentUnit exists for the InstUnit,
 * - but no StudentLesson was ever created for that student + lesson,
 * then create the StudentLesson and mark it DNC.
 *
 * This prevents the UI from showing "Pending" forever when a student
 * was not in class during lesson start (or closed the browser).
 */
class DncMissedStudentLessons extends Command
{
    protected $signature = 'classrooms:dnc-missed-lessons
                            {--lookback-hours=18 : Only process InstLessons created within this many hours}
                            {--limit=5000 : Max StudentLessons to create/DNC per run}
                            {--dry-run : Show what would change without writing}';

    protected $description = 'Backfill missing StudentLesson records for started classroom lessons and mark them DNC after the join window expires';

    public function handle(): int
    {
        $lookbackHours = (int) $this->option('lookback-hours');
        $lookbackHours = $lookbackHours > 0 ? $lookbackHours : 18;

        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 5000;

        $isDryRun = (bool) $this->option('dry-run');

        $joinSeconds = (int) RCache::SiteConfig('student_join_lesson_seconds', 300);
        $joinSeconds = $joinSeconds > 0 ? $joinSeconds : 300;

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No StudentLessons will be created/updated');
        }

        $processedInstLessons = 0;
        $processedStudentUnits = 0;
        $createdStudentLessons = 0;
        $markedDnc = 0;
        $skipped = 0;

        $instLessonStartCutoff = now()->subHours($lookbackHours);
        $joinWindowElapsedCutoff = now()->subSeconds($joinSeconds);

        $shouldStop = false;

        InstLesson::query()
            ->where('created_at', '>=', $instLessonStartCutoff)
            ->where('created_at', '<=', $joinWindowElapsedCutoff)
            ->orderBy('id')
            ->chunkById(100, function ($instLessons) use (
                &$processedInstLessons,
                &$processedStudentUnits,
                &$createdStudentLessons,
                &$markedDnc,
                &$skipped,
                &$shouldStop,
                $limit,
                $isDryRun
            ) {
                foreach ($instLessons as $instLesson) {
                    if ($markedDnc >= $limit) {
                        $shouldStop = true;
                        return false;
                    }

                    $processedInstLessons++;

                    $instUnitId = (int) $instLesson->inst_unit_id;
                    if ($instUnitId <= 0) {
                        $skipped++;
                        continue;
                    }

                    // Build a fast lookup of StudentUnit IDs that already have a StudentLesson for this InstLesson.
                    $existingStudentUnitIds = StudentLesson::query()
                        ->where('inst_lesson_id', $instLesson->id)
                        ->pluck('student_unit_id')
                        ->map(fn($id) => (int) $id)
                        ->all();

                    $existingLookup = array_fill_keys($existingStudentUnitIds, true);

                    StudentUnit::query()
                        ->where('inst_unit_id', $instUnitId)
                        ->orderBy('id')
                        ->chunkById(200, function ($studentUnits) use (
                            $instLesson,
                            &$processedStudentUnits,
                            &$createdStudentLessons,
                            &$markedDnc,
                            &$skipped,
                            &$shouldStop,
                            $limit,
                            $isDryRun,
                            $existingLookup
                        ) {
                            foreach ($studentUnits as $studentUnit) {
                                if ($markedDnc >= $limit) {
                                    $shouldStop = true;
                                    return false;
                                }

                                $processedStudentUnits++;

                                if (isset($existingLookup[(int) $studentUnit->id])) {
                                    $skipped++;
                                    continue;
                                }

                                if ($isDryRun) {
                                    $markedDnc++;
                                    continue;
                                }

                                try {
                                    $studentLesson = StudentLesson::create([
                                        'student_unit_id' => (int) $studentUnit->id,
                                        'lesson_id' => (int) $instLesson->lesson_id,
                                        'inst_lesson_id' => (int) $instLesson->id,
                                    ]);

                                    $createdStudentLessons++;

                                    try {
                                        $studentLesson->MarkDNC();
                                    } catch (\Throwable $e) {
                                        // Non-fatal: DNC side-effects (activity/event) should never break backfill.
                                        Log::warning('DncMissedStudentLessons: MarkDNC failed', [
                                            'student_lesson_id' => $studentLesson->id,
                                            'student_unit_id' => (int) $studentUnit->id,
                                            'inst_lesson_id' => (int) $instLesson->id,
                                            'lesson_id' => (int) $instLesson->lesson_id,
                                            'error' => $e->getMessage(),
                                        ]);
                                    }

                                    $markedDnc++;
                                } catch (\Throwable $e) {
                                    Log::error('DncMissedStudentLessons: failed to create StudentLesson', [
                                        'student_unit_id' => (int) $studentUnit->id,
                                        'inst_lesson_id' => (int) $instLesson->id,
                                        'lesson_id' => (int) $instLesson->lesson_id,
                                        'error' => $e->getMessage(),
                                    ]);
                                }
                            }

                            return true;
                        });

                    if ($shouldStop) {
                        return false;
                    }
                }

                return true;
            });

        $this->info(
            "Processed InstLessons: {$processedInstLessons} | " .
                "Scanned StudentUnits: {$processedStudentUnits} | " .
                "Created StudentLessons: {$createdStudentLessons} | " .
                "Marked DNC: {$markedDnc} | " .
                "Skipped: {$skipped}"
        );

        return 0;
    }
}
