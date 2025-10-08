<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Investigating CourseDate 10566 InstUnit Relationship\n";
echo "=====================================================\n\n";

try {
    $courseDate = \App\Models\CourseDate::find(10566);

    if (!$courseDate) {
        echo "❌ CourseDate 10566 not found\n";
        exit(1);
    }

    echo "📋 CourseDate 10566 Details:\n";
    echo "   ID: {$courseDate->id}\n";
    echo "   starts_at: " . ($courseDate->starts_at ?? 'NULL') . "\n";
    echo "   ends_at: " . ($courseDate->ends_at ?? 'NULL') . "\n";
    echo "   course_unit_id: {$courseDate->course_unit_id}\n";
    echo "   is_active: " . ($courseDate->is_active ? 'true' : 'false') . "\n";

    // Check raw InstUnit query without date condition
    echo "\n🔍 InstUnit Investigation:\n";
    $allInstUnits = \App\Models\InstUnit::where('course_date_id', 10566)->get();
    echo "   Total InstUnits with course_date_id=10566: " . $allInstUnits->count() . "\n";

    if ($allInstUnits->count() > 0) {
        foreach ($allInstUnits as $instUnit) {
            echo "     - InstUnit ID: {$instUnit->id}, created_at: {$instUnit->created_at}\n";
        }
    }

    // Check InstUnit relationship with date condition
    echo "\n🔍 Relationship Query:\n";
    $relationshipQuery = $courseDate->InstUnit();
    echo "   Relationship SQL: " . $relationshipQuery->toSql() . "\n";
    echo "   Relationship bindings: " . json_encode($relationshipQuery->getBindings()) . "\n";

    $instUnitViaRelationship = $courseDate->InstUnit;
    echo "   InstUnit via relationship: " . ($instUnitViaRelationship ? 'EXISTS (ID: ' . $instUnitViaRelationship->id . ')' : 'NULL') . "\n";

    // Check exists() vs get()
    echo "\n🔍 Different Query Methods:\n";
    echo "   exists(): " . ($courseDate->InstUnit()->exists() ? 'true' : 'false') . "\n";
    echo "   count(): " . $courseDate->InstUnit()->count() . "\n";
    echo "   first(): " . ($courseDate->InstUnit()->first() ? 'found' : 'null') . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Investigation completed.\n";
