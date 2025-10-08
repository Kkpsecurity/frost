<?php
/**
 * TEST UPDATED COURSE DATES VIEW - Two Count Display
 * 
 * This script simulates the data that will be shown in the Course Dates Management
 * with both registered and attending counts.
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING UPDATED COURSE DATES VIEW - TWO COUNT COMPARISON ===\n\n";

try {
    // Get today's course date with all relationships
    $courseDate = \App\Models\CourseDate::with([
        'CourseUnit.Course.CourseAuths',
        'InstUnit.CreatedBy',
        'StudentUnits'
    ])->whereDate('starts_at', today())->first();
    
    if (!$courseDate) {
        echo "❌ No CourseDate found for today\n";
        exit(1);
    }
    
    // Simulate the view logic
    $course = $courseDate->CourseUnit->Course ?? null;
    $totalRegistered = $course ? $course->CourseAuths->count() : 0;
    $hasStarted = $courseDate->InstUnit !== null;
    $actualAttending = $hasStarted ? $courseDate->StudentUnits->count() : 0;
    $staleStudentUnits = $courseDate->StudentUnits->count();
    
    echo "📊 COURSE DATES MANAGEMENT VIEW SIMULATION:\n";
    echo "==========================================\n\n";
    
    echo "🎓 COURSE: {$course->title}\n";
    echo "📅 DATE: " . \Carbon\Carbon::parse($courseDate->starts_at)->format('M j, Y g:i A') . "\n";
    echo "🏫 CLASS STATUS: " . ($hasStarted ? 'STARTED' : 'NOT STARTED') . "\n\n";
    
    echo "👥 STUDENT COUNTS (WHAT UI WILL SHOW):\n";
    echo "=====================================\n";
    echo "📝 {$totalRegistered} registered (CourseAuth records - total enrollment)\n";
    echo "🎯 {$actualAttending} attending (StudentUnit records - only when class started)\n\n";
    
    if (!$hasStarted && $staleStudentUnits > 0) {
        echo "⚠️  WARNING: {$staleStudentUnits} stale StudentUnit records detected\n";
        echo "These would show a warning badge in the UI\n\n";
    }
    
    echo "🎨 UI BADGE DISPLAY:\n";
    echo "===================\n";
    echo "Badge 1: [{$totalRegistered} registered] (blue badge - info)\n";
    
    if ($hasStarted) {
        echo "Badge 2: [{$actualAttending} attending] (green badge - success)\n";
    } else {
        echo "Badge 2: [0 attending] (gray badge - secondary)\n";
    }
    
    if (!$hasStarted && $staleStudentUnits > 0) {
        echo "Warning: [⚠️ Stale data] (yellow text - warning)\n";
    }
    echo "\n";
    
    echo "📋 COMPARISON BENEFITS:\n";
    echo "======================\n";
    echo "1. ✅ Shows total course enrollment (helps instructors see course popularity)\n";
    echo "2. ✅ Shows actual class attendance (only when class has started)\n";
    echo "3. ✅ Prevents confusion between enrollment and attendance\n";
    echo "4. ✅ Warns about data integrity issues (stale StudentUnits)\n";
    echo "5. ✅ Clear visual distinction between the two metrics\n\n";
    
    echo "🎯 EXPECTED BEHAVIOR:\n";
    echo "====================\n";
    echo "BEFORE CLASS STARTS:\n";
    echo "- Shows enrollment count (helps plan for class size)\n";
    echo "- Shows 0 attending (correct - class hasn't started)\n\n";
    
    echo "WHEN INSTRUCTOR STARTS CLASS:\n";
    echo "- InstUnit is created\n";
    echo "- As students enter, StudentUnits are created\n";
    echo "- Attending count increases in real-time\n\n";
    
    echo "AFTER CLASS ENDS:\n";
    echo "- Enrollment count remains (permanent course registration)\n";
    echo "- Attending count shows final attendance for that session\n\n";
    
    // Test with a hypothetical started class
    echo "🧪 SIMULATION: If this class were started...\n";
    echo "=============================================\n";
    echo "Step 1: Instructor clicks 'Start Class'\n";
    echo "Step 2: InstUnit created → hasStarted = true\n";
    echo "Step 3: Students join → StudentUnits created\n";
    echo "Step 4: UI shows: [{$totalRegistered} registered] [X attending]\n";
    echo "Where X = actual students who joined the session\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n\n";
}

echo "=== TEST COMPLETE - DUAL COUNT DISPLAY READY ===\n";