<?php

/**
 * Database Comparison Script
 * Purpose: Compare available databases to find the best sync source
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load .env file
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

function testDatabase($dbName) {
    $capsule = new Capsule;
    
    try {
        $capsule->addConnection([
            'driver' => 'pgsql',
            'host' => $_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com',
            'database' => $dbName,
            'username' => $_ENV['DB_USERNAME'] ?? 'frost',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]);
        
        $connection = $capsule->getConnection();
        
        // Test connection
        $connection->select('SELECT 1');
        
        // Get table count
        $tableCount = $connection->select("
            SELECT COUNT(*) as count
            FROM information_schema.tables 
            WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
        ");
        
        // Get approximate row counts from pg_stat_user_tables
        $rowStats = $connection->select("
            SELECT 
                COUNT(*) as tables_with_data,
                SUM(n_tup_ins) as total_inserts,
                SUM(n_tup_upd) as total_updates,
                SUM(n_tup_del) as total_deletes
            FROM pg_stat_user_tables 
            WHERE schemaname = 'public'
        ");
        
        // Get some key tables if they exist
        $keyTables = $connection->select("
            SELECT 
                tablename,
                n_tup_ins as approx_rows
            FROM pg_stat_user_tables 
            WHERE schemaname = 'public'
            AND tablename IN ('users', 'courses', 'course_auths', 'students', 'instructors')
            ORDER BY n_tup_ins DESC
        ");
        
        // Get database size
        $dbSize = $connection->select("
            SELECT pg_size_pretty(pg_database_size(current_database())) as size
        ");
        
        return [
            'accessible' => true,
            'table_count' => $tableCount[0]->count ?? 0,
            'stats' => $rowStats[0] ?? null,
            'key_tables' => $keyTables,
            'size' => $dbSize[0]->size ?? 'unknown'
        ];
        
    } catch (Exception $e) {
        return [
            'accessible' => false,
            'error' => $e->getMessage()
        ];
    }
}

echo "============================================\n";
echo "DATABASE COMPARISON FOR SYNC SOURCE\n";
echo "============================================\n\n";

$databases = ['frost-devel', 'frost-old', 'frost-patch'];

$results = [];

foreach ($databases as $dbName) {
    echo "Analyzing database: {$dbName}\n";
    echo str_repeat('-', 40) . "\n";
    
    $result = testDatabase($dbName);
    $results[$dbName] = $result;
    
    if ($result['accessible']) {
        echo "✅ Accessible\n";
        echo "Tables: {$result['table_count']}\n";
        echo "Size: {$result['size']}\n";
        
        if ($result['stats']) {
            $stats = $result['stats'];
            echo "Tables with data: {$stats->tables_with_data}\n";
            echo "Total operations: " . ($stats->total_inserts + $stats->total_updates + $stats->total_deletes) . "\n";
        }
        
        if (!empty($result['key_tables'])) {
            echo "Key tables:\n";
            foreach ($result['key_tables'] as $table) {
                echo "  - {$table->tablename}: ~{$table->approx_rows} rows\n";
            }
        }
        
    } else {
        echo "❌ Not accessible: {$result['error']}\n";
    }
    
    echo "\n";
}

echo "============================================\n";
echo "SYNC RECOMMENDATION\n";
echo "============================================\n";

// Find the best source database
$bestSource = null;
$maxTables = 0;
$maxActivity = 0;

foreach ($results as $dbName => $result) {
    if ($result['accessible'] && $dbName !== 'frost-devel') {  // Don't sync to itself
        $activity = 0;
        if ($result['stats']) {
            $stats = $result['stats'];
            $activity = $stats->total_inserts + $stats->total_updates + $stats->total_deletes;
        }
        
        if ($result['table_count'] > $maxTables || ($result['table_count'] == $maxTables && $activity > $maxActivity)) {
            $maxTables = $result['table_count'];
            $maxActivity = $activity;
            $bestSource = $dbName;
        }
    }
}

if ($bestSource) {
    echo "Recommended sync source: {$bestSource}\n";
    echo "Sync direction: {$bestSource} → frost-devel\n\n";
    
    echo "To update your sync scripts:\n";
    echo "1. Edit xfer-lib.sh\n";
    echo "2. Change PROD_DB from 'frost' to '{$bestSource}'\n";
    echo "3. Keep DEV_DB as 'frost-devel'\n\n";
    
    echo "Updated configuration would be:\n";
    echo "PROD_DB=\"{$bestSource}\"  # Source database\n";
    echo "DEV_DB=\"frost-devel\"     # Target database\n";
    
} else {
    echo "❌ No suitable source database found\n";
    echo "Available options require manual review\n";
}

echo "\n============================================\n";
