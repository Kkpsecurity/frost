<?php

namespace App\Console\Commands;

use App\Classes\Students\Challenger;
use App\Models\Challenge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * ExpirePendingChallenges
 *
 * Marks pending challenges as failed when their expires_at time has passed,
 * even if the student stops polling (e.g., they leave the site/tab).
 *
 * This intentionally uses Challenger::MarkFailed() (not Challenge::MarkFailed())
 * so final/EOL challenges still trigger the expected StudentLesson DNC behavior.
 */
class ExpirePendingChallenges extends Command
{
    protected $signature = 'challenges:expire-pending
                            {--limit=500 : Max challenges to process per run}
                            {--dry-run : Display what would be expired without writing changes}';

    protected $description = 'Expire pending classroom participation challenges whose expires_at has passed';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 500;

        $isDryRun = (bool) $this->option('dry-run');

        $processed = 0;
        $markedFailed = 0;
        $skipped = 0;

        $now = now();

        if ($isDryRun) {
            $this->warn('DRY RUN MODE — No challenges will be updated');
        }

        Challenge::query()
            ->whereNull('completed_at')
            ->whereNull('failed_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->with(['StudentLesson.InstLesson'])
            ->orderBy('id')
            ->chunkById(100, function ($challenges) use (&$processed, &$markedFailed, &$skipped, $limit, $isDryRun) {
                foreach ($challenges as $challenge) {
                    if ($processed >= $limit) {
                        return false; // stop chunking
                    }

                    // Re-check (avoid races)
                    if ($challenge->completed_at || $challenge->failed_at) {
                        $skipped++;
                        $processed++;
                        continue;
                    }

                    if (! $challenge->expires_at || now()->lt($challenge->expires_at)) {
                        $skipped++;
                        $processed++;
                        continue;
                    }

                    if ($isDryRun) {
                        $processed++;
                        continue;
                    }

                    try {
                        Challenger::MarkFailed($challenge);
                        $markedFailed++;
                    } catch (\Throwable $e) {
                        Log::error('ExpirePendingChallenges: failed to mark challenge failed', [
                            'challenge_id' => $challenge->id,
                            'student_lesson_id' => $challenge->student_lesson_id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    $processed++;
                }

                return true;
            });

        $this->info("Processed: {$processed} | Marked failed: {$markedFailed} | Skipped: {$skipped}");

        return 0;
    }
}
