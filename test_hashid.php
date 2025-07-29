<?php

// Bootstrap Laravel
require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use KKP\Laravel\HashIDs\HashID;

echo "Testing HashID class...\n";

try {
    $encoded = HashID::encode(123);
    echo "Encoded 123: " . $encoded . "\n";

    $decoded = HashID::decode($encoded);
    echo "Decoded back: " . $decoded . "\n";

    echo "Test successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
