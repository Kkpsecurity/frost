<?php

/**
 * Simple Database Comparison
 * Purpose: Get basic info about each database to determine sync source
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

function analyzeDatabase($dbName) {
    try {
        $capsule = new Capsule;
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
        
        // Basic connection test
        $connection->select('SELECT 1');
        
        // Get table count
        $tableCount = $connection->select("
            SELECT COUNT(*) as count
            FROM information_schema.tables 
            WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
        ");
        
        // Get database size
        $dbSize = $connection->select("
            SELECT pg_size_pretty(pg_database_size(current_database())) as size
        ");
        
        // Get list of tables
        $tables = $connection->select("
            SELECT table_name
            FROM information_schema.tables 
            WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ORDER BY table_name
            LIMIT 10
        ");
        
        // Try to get some row counts from common tables
        $commonTables = ['users', 'courses', 'course_auths'];
        $tableCounts = [];
        
        foreach ($commonTables as $tableName) {
            try {
                $count = $connection->select("SELECT COUNT(*) as count FROM {$tableName}");
                $tableCounts[$tableName] = $count[0]->count ?? 0;
            } catch (Exception $e) {
                // Table doesn't exist, skip
            }
        }
        
        return [
            'accessible' => true,
            'table_count' => $tableCount[0]->count ?? 0,
            'size' => $dbSize[0]->size ?? 'unknown',
            'sample_tables' => $tables,
            'row_counts' => $tableCounts
        ];
        
    } catch (Exception $e) {
        return [
            'accessible' => false,
            'error' => $e->getMessage()
        ];
    }
}

echo "============================================\n";
echo "SIMPLE DATABASE ANALYSIS\n";
echo "============================================\n\n";

$databases = ['frost-devel', 'frost-old', 'frost-patch'];
$results = [];

foreach ($databases as $dbName) {
    echo "Database: {$dbName}\n";
    echo str_repeat('-', 30) . "\n";
    
    $result = analyzeDatabase($dbName);
    $results[$dbName] = $result;
    
    if ($result['accessible']) {
        echo "✅ Status: Accessible\n";
        echo "📊 Tables: {$result['table_count']}\n";
        echo "💾 Size: {$result['size']}\n";
        
        if (!empty($result['row_counts'])) {
            echo "📈 Row counts:\n";
            foreach ($result['row_counts'] as $table => $count) {
                echo "   {$table}: {$count} rows\n";
            }
        }
        
        if (!empty($result['sample_tables'])) {
            echo "📋 Sample tables:\n";
            $count = 0;
            foreach ($result['sample_tables'] as $table) {
                echo "   - {$table->table_name}\n";
                $count++;
                if ($count >= 5) {
                    echo "   ... and " . (count($result['sample_tables']) - 5) . " more\n";
                    break;
                }
            }
        }
        
    } else {
        echo "❌ Status: Not accessible\n";
        echo "Error: " . substr($result['error'], 0, 100) . "...\n";
    }
    
    echo "\n";
}

// Make recommendation
echo "============================================\n";
echo "SYNC RECOMMENDATIONS\n";
echo "============================================\n";

$currentDev = $results['frost-devel'] ?? null;
if ($currentDev && $currentDev['accessible']) {
    echo "Current development database (frost-devel):\n";
    echo "- Tables: {$currentDev['table_count']}\n";
    echo "- Size: {$currentDev['size']}\n";
    
    if (!empty($currentDev['row_counts'])) {
        $totalRows = array_sum($currentDev['row_counts']);
        echo "- Sample data: {$totalRows} rows in key tables\n";
    }
}

echo "\nPossible sync sources:\n";

foreach (['frost-old', 'frost-patch'] as $dbName) {
    $result = $results[$dbName] ?? null;
    if ($result && $result['accessible']) {
        $totalRows = !empty($result['row_counts']) ? array_sum($result['row_counts']) : 0;
        echo "- {$dbName}: {$result['table_count']} tables, {$result['size']}, {$totalRows} sample rows\n";
    }
}

echo "\n📋 NEXT STEPS:\n";
echo "1. Choose which database contains your 'production' data\n";
echo "2. Update the sync configuration in xfer-lib.sh\n";
echo "3. Run the validation again\n";
echo "4. Perform the sync\n";

echo "\n💡 CONFIGURATION UPDATE NEEDED:\n";
echo "Edit scripts/xfer-lib.sh and change:\n";
echo "PROD_DB=\"frost\"          # ❌ This database doesn't exist\n";
echo "PROD_DB=\"frost-old\"      # ✅ or frost-patch (you decide)\n";

echo "\n============================================\n";
