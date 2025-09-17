<?php

/**
 * Database Discovery Script
 * Purpose: Find available databases on the PostgreSQL server
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

$capsule = new Capsule;

// First, try connecting to postgres database (default) to list databases
$capsule->addConnection([
    'driver' => 'pgsql',
    'host' => $_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com',
    'database' => 'postgres',  // Default PostgreSQL database
    'username' => $_ENV['DB_USERNAME'] ?? 'frost',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8',
    'prefix' => '',
    'schema' => 'public',
], 'default');

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "============================================\n";
echo "DATABASE DISCOVERY\n";
echo "============================================\n";
echo "Host: " . ($_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com') . "\n";
echo "User: " . ($_ENV['DB_USERNAME'] ?? 'frost') . "\n\n";

try {
    $connection = $capsule->connection('default');
    
    // Test connection to postgres database
    $result = $connection->select('SELECT 1 as test');
    echo "✅ Connected to PostgreSQL server\n\n";
    
    // List all databases
    $databases = $connection->select("
        SELECT datname as database_name 
        FROM pg_database 
        WHERE datistemplate = false
        ORDER BY datname
    ");
    
    echo "Available databases:\n";
    foreach ($databases as $db) {
        echo "  - {$db->database_name}\n";
    }
    
    echo "\nTesting specific databases:\n";
    
    // Test common database names
    $testDatabases = ['frost', 'frost-devel', 'frost_devel', 'frostdev', 'development'];
    
    foreach ($testDatabases as $dbName) {
        echo "\nTesting database: {$dbName}\n";
        
        try {
            // Create a new connection for this database
            $testCapsule = new Capsule;
            $testCapsule->addConnection([
                'driver' => 'pgsql',
                'host' => $_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com',
                'database' => $dbName,
                'username' => $_ENV['DB_USERNAME'] ?? 'frost',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
            ]);
            
            $testConnection = $testCapsule->getConnection();
            $testResult = $testConnection->select('SELECT current_database() as db');
            
            if (!empty($testResult)) {
                echo "✅ {$dbName} - accessible\n";
                
                // Get table count
                $tableCount = $testConnection->select("
                    SELECT COUNT(*) as count
                    FROM information_schema.tables 
                    WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
                ");
                
                if (!empty($tableCount)) {
                    echo "   Tables: {$tableCount[0]->count}\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ {$dbName} - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Failed to connect to PostgreSQL server\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n============================================\n";
echo "From your .env file:\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'not set') . "\n";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'not set') . "\n";
echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'not set') . "\n";
echo "DB_PASSWORD: " . (empty($_ENV['DB_PASSWORD']) ? 'not set' : '[set]') . "\n";
echo "============================================\n";
