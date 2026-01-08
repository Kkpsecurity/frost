<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== InstUnit Table Structure ===\n\n";

$columns = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns
    WHERE table_name = 'inst_unit'
    ORDER BY ordinal_position
");

foreach ($columns as $col) {
    echo "{$col->column_name}: {$col->data_type} " . ($col->is_nullable === 'YES' ? '(nullable)' : '(not null)') . "\n";
}

echo "\n=== Sample InstUnit Record (ID: 41) ===\n";
$sample = DB::select("SELECT * FROM inst_unit WHERE id = 41")[0];
foreach ($sample as $key => $value) {
    echo "{$key}: {$value}\n";
}
