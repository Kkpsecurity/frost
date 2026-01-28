<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PauseTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Checking inst_lesson records ===\n";

        // Get all inst_lesson records
        $lessons = DB::table('inst_lesson')
            ->select('id', 'inst_unit_id', 'lesson_id', 'is_paused', 'created_at', 'completed_at')
            ->get();

        echo "Total inst_lesson records: " . count($lessons) . "\n";

        foreach ($lessons as $lesson) {
            $paused = $lesson->is_paused ? 'YES ✓' : 'NO';
            echo "  ID: {$lesson->id} | Lesson: {$lesson->lesson_id} | Paused: {$paused} | Created: {$lesson->created_at}\n";
        }

        echo "\n=== Setting first non-completed lesson to paused ===\n";

        // Find first non-completed lesson
        $nonCompletedLesson = DB::table('inst_lesson')
            ->whereNull('completed_at')
            ->orderBy('id', 'asc')
            ->first();

        if ($nonCompletedLesson) {
            echo "Found non-completed lesson: ID {$nonCompletedLesson->id}, Lesson {$nonCompletedLesson->lesson_id}\n";

            // Update to paused
            DB::table('inst_lesson')
                ->where('id', $nonCompletedLesson->id)
                ->update(['is_paused' => true]);

            echo "✓ Lesson ID {$nonCompletedLesson->id} is now PAUSED\n";

            // Verify
            $updated = DB::table('inst_lesson')
                ->where('id', $nonCompletedLesson->id)
                ->first();

            echo "Verification: is_paused = " . ($updated->is_paused ? 'true ✓' : 'false') . "\n";
        } else {
            echo "No non-completed lessons found\n";
        }

        echo "\n=== Final state ===\n";

        $allLessons = DB::table('inst_lesson')
            ->select('id', 'lesson_id', 'is_paused')
            ->get();

        foreach ($allLessons as $lesson) {
            $paused = $lesson->is_paused ? 'YES ✓' : 'NO';
            echo "  ID: {$lesson->id} | Lesson: {$lesson->lesson_id} | Paused: {$paused}\n";
        }
    }
}
