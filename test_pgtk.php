<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing PgTk::now(): " . App\Helpers\PgTk::now() . "\n";
echo "Testing PgTk::UUID_v4(): " . App\Helpers\PgTk::UUID_v4() . "\n";
echo "All tests successful!\n";
