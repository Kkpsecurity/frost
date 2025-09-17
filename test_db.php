<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Database Connection Test\n";
echo "========================\n";
echo "Database: " . config('database.connections.pgsql.database') . "\n";
echo "Host: " . config('database.connections.pgsql.host') . "\n\n";

// Test basic connection
try {
    $result = DB::select('SELECT version()');
    echo "✓ Database connection successful\n";
    echo "PostgreSQL Version: " . $result[0]->version . "\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if course_auths table exists
try {
    $count = DB::selectOne('SELECT COUNT(*) as count FROM course_auths');
    echo "✓ course_auths table exists\n";
    echo "Records in course_auths: " . $count->count . "\n\n";
} catch (Exception $e) {
    echo "✗ course_auths table issue: " . $e->getMessage() . "\n\n";
}

// Check if sequence exists
try {
    $sequence = DB::selectOne("SELECT last_value FROM course_auths_id_seq");
    echo "✓ course_auths_id_seq sequence exists\n";
    echo "Current sequence value: " . $sequence->last_value . "\n\n";
} catch (Exception $e) {
    echo "✗ course_auths_id_seq sequence issue: " . $e->getMessage() . "\n\n";
}

// List all tables
try {
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    echo "Available tables:\n";
    foreach ($tables as $table) {
        echo "  - " . $table->table_name . "\n";
    }
} catch (Exception $e) {
    echo "✗ Could not list tables: " . $e->getMessage() . "\n";
}
