<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "student_unit table columns:\n";
$columns = DB::getSchemaBuilder()->getColumnListing('student_unit');
print_r($columns);

echo "\n\nSearching for terms/rules related columns:\n";
foreach ($columns as $col) {
    if (stripos($col, 'term') !== false || stripos($col, 'rule') !== false || stripos($col, 'accept') !== false) {
        echo "  ✓ {$col}\n";
    }
}

if (!in_array('terms_accepted', $columns)) {
    echo "\n❌ 'terms_accepted' column does NOT exist\n";
}

if (!in_array('onboarding_completed', $columns)) {
    echo "❌ 'onboarding_completed' column does NOT exist\n";
}
