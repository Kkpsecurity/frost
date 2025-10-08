<?php
/**
 * CLEAN UP STALE STUDENTUNIT RECORDS
 * 
 * This script removes StudentUnit records that are from previous dates
 * but incorrectly associated with today's CourseDate.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CLEANING UP STALE STUDENTUNIT RECORDS ===\n\n";

try {
    // Get today's course date
    $courseDate = \App\Models\CourseDate::whereDate('starts_at', today())->first();
    
    if (!$courseDate) {
        echo "❌ No CourseDate found for today\n";
        exit(1);
    }
    
    echo "📊 COURSE DATE: {$courseDate->id}\n";
    echo "Starts At: {$courseDate->starts_at}\n";
    echo "Class Started: " . ($courseDate->InstUnit ? 'YES' : 'NO') . "\n\n";
    
    // Get all StudentUnit records for this CourseDate
    $studentUnits = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->get();
    echo "Total StudentUnit records: {$studentUnits->count()}\n\n";
    
    if ($studentUnits->count() == 0) {
        echo "✅ No StudentUnit records to clean up\n";
        exit(0);
    }
    
    // Identify stale records (created on different dates than the CourseDate)
    $courseDateDay = \Carbon\Carbon::parse($courseDate->starts_at)->format('Y-m-d');
    $staleUnits = $studentUnits->filter(function($unit) use ($courseDateDay) {
        $createdDay = \Carbon\Carbon::createFromTimestamp($unit->created_at)->format('Y-m-d');
        return $createdDay !== $courseDateDay;
    });
    
    $currentUnits = $studentUnits->filter(function($unit) use ($courseDateDay) {
        $createdDay = \Carbon\Carbon::createFromTimestamp($unit->created_at)->format('Y-m-d');
        return $createdDay === $courseDateDay;
    });
    
    echo "📅 ANALYSIS:\n";
    echo "Course Date: {$courseDateDay}\n";
    echo "StudentUnits created today: {$currentUnits->count()}\n";
    echo "Stale StudentUnits (from other dates): {$staleUnits->count()}\n\n";
    
    if ($staleUnits->count() > 0) {
        echo "🗑️  STALE RECORDS TO REMOVE:\n";
        $staleGroups = $staleUnits->groupBy(function($unit) {
            return \Carbon\Carbon::createFromTimestamp($unit->created_at)->format('Y-m-d');
        });
        
        foreach ($staleGroups as $date => $units) {
            echo "- {$date}: {$units->count()} records\n";
        }
        echo "\n";
        
        echo "❓ Do you want to remove these {$staleUnits->count()} stale StudentUnit records? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($response) === 'y') {
            $deleted = 0;
            foreach ($staleUnits as $unit) {
                $unit->delete();
                $deleted++;
            }
            
            echo "✅ Deleted {$deleted} stale StudentUnit records\n\n";
            
            // Verify cleanup
            $remainingUnits = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->count();
            echo "📊 AFTER CLEANUP:\n";
            echo "Remaining StudentUnit records: {$remainingUnits}\n";
            echo "These should only be from today's actual class session\n\n";
            
        } else {
            echo "❌ Cleanup cancelled\n\n";
        }
    } else {
        echo "✅ No stale records found\n\n";
    }
    
    // Final status
    $finalCount = \App\Models\StudentUnit::where('course_date_id', $courseDate->id)->count();
    $hasInstUnit = $courseDate->InstUnit !== null;
    
    echo "🎯 FINAL STATUS:\n";
    echo "Course Date ID: {$courseDate->id}\n";
    echo "Class Started (InstUnit exists): " . ($hasInstUnit ? 'YES' : 'NO') . "\n";
    echo "StudentUnit records: {$finalCount}\n";
    
    if ($finalCount > 0 && !$hasInstUnit) {
        echo "⚠️  WARNING: StudentUnits exist but class hasn't started (InstUnit missing)\n";
        echo "This indicates ongoing data integrity issues.\n";
    } elseif ($finalCount == 0 && !$hasInstUnit) {
        echo "✅ CORRECT: No students in class and class hasn't started\n";
    } elseif ($finalCount > 0 && $hasInstUnit) {
        echo "✅ CORRECT: Students in class and class has started\n";
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "\n=== CLEANUP COMPLETE ===\n";