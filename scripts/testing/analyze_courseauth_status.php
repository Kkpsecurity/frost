<?php
/**
 * ANALYZE COURSEAUTH STATUS FIELD
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ANALYZING COURSEAUTH STATUS FIELD ===\n\n";

try {
    $courseDate = \App\Models\CourseDate::with(['CourseUnit.Course.CourseAuths'])->whereDate('starts_at', today())->first();
    $course = $courseDate->CourseUnit->Course;
    
    // Get first 10 CourseAuths to examine their structure
    $sampleCourseAuths = $course->CourseAuths->take(10);
    
    echo "📊 SAMPLE COURSEAUTH RECORDS:\n";
    echo "=============================\n";
    
    foreach ($sampleCourseAuths as $i => $auth) {
        echo "CourseAuth #" . ($i + 1) . ":\n";
        echo "  ID: {$auth->id}\n";
        echo "  User ID: {$auth->user_id}\n";
        echo "  Status: '" . ($auth->status ?? 'NULL') . "'\n";
        echo "  Created: {$auth->created_at}\n";
        echo "  Updated: {$auth->updated_at}\n";
        
        // Check for other possible status fields
        $attributes = $auth->getAttributes();
        foreach ($attributes as $key => $value) {
            if (str_contains(strtolower($key), 'status') || str_contains(strtolower($key), 'active') || str_contains(strtolower($key), 'complete')) {
                echo "  {$key}: " . ($value ?? 'NULL') . "\n";
            }
        }
        echo "\n";
    }
    
    // Check what fields are available
    echo "🔍 AVAILABLE COURSEAUTH FIELDS:\n";
    echo "===============================\n";
    $firstAuth = $sampleCourseAuths->first();
    if ($firstAuth) {
        $fields = array_keys($firstAuth->getAttributes());
        foreach ($fields as $field) {
            echo "- {$field}\n";
        }
    }
    echo "\n";
    
    // Check if there's a different way to determine active CourseAuths
    echo "💡 ACTIVE COURSEAUTH DETERMINATION:\n";
    echo "===================================\n";
    
    // Option 1: All CourseAuths might be considered active if they exist
    $totalCourseAuths = $course->CourseAuths->count();
    echo "Option 1 - All existing CourseAuths: {$totalCourseAuths}\n";
    
    // Option 2: Check if there's a deleted_at field (soft deletes)
    $nonDeletedAuths = $course->CourseAuths->whereNull('deleted_at')->count();
    echo "Option 2 - Non-deleted CourseAuths: {$nonDeletedAuths}\n";
    
    // Option 3: Check if there's an 'active' field specifically
    $hasActiveField = $course->CourseAuths->first() && isset($course->CourseAuths->first()->active);
    if ($hasActiveField) {
        $activeBooleanAuths = $course->CourseAuths->where('active', true)->count();
        echo "Option 3 - Active=true CourseAuths: {$activeBooleanAuths}\n";
    }
    
    // Option 4: Recent CourseAuths (not too old)
    $recentAuths = $course->CourseAuths->where('created_at', '>=', now()->subMonths(6))->count();
    echo "Option 4 - Recent CourseAuths (last 6 months): {$recentAuths}\n";
    
    echo "\n🎯 RECOMMENDATION:\n";
    echo "Since status field is empty/null for all records, we should use:\n";
    echo "✅ All existing CourseAuths: {$totalCourseAuths}\n";
    echo "✅ OR Non-deleted CourseAuths: {$nonDeletedAuths}\n";
    echo "This represents students who have purchased/enrolled in the course.\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "=== ANALYSIS COMPLETE ===\n";