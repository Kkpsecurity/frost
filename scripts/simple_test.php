<?php

// Simple test script
echo "Testing Logo Setting:\n";

require 'bootstrap/app.php';
$app = require 'bootstrap/app.php';

try {
    // Bootstrap the application
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "Logo from config: " . config('adminlte.logo', 'not found') . "\n";
    echo "Title from config: " . config('adminlte.title', 'not found') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
