<?php

/**
 * Database Sync Validation Script (PHP Version)
 * Purpose: Validate connections and database structure before sync
 * This uses Laravel's database configuration and PHP PDO for connections
 */

// Include Laravel's autoloader and configuration
require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

class DatabaseValidator {
    
    private $config;
    private $capsule;
    
    public function __construct() {
        $this->loadConfig();
        $this->setupDatabase();
    }
    
    private function loadConfig() {
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
        
        $this->config = [
            'production' => [
                'host' => $_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com',
                'database' => 'frost',  // Production database
                'username' => $_ENV['DB_USERNAME'] ?? 'frost',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ],
            'development' => [
                'host' => $_ENV['DB_HOST'] ?? 'develc.hq.cisadmin.com',
                'database' => $_ENV['DB_DATABASE'] ?? 'frost-devel',  // Development database
                'username' => $_ENV['DB_USERNAME'] ?? 'frost',
                'password' => $_ENV['DB_PASSWORD'] ?? '',
            ]
        ];
    }
    
    private function setupDatabase() {
        $this->capsule = new Capsule;
        
        // Add production connection
        $this->capsule->addConnection([
            'driver' => 'pgsql',
            'host' => $this->config['production']['host'],
            'database' => $this->config['production']['database'],
            'username' => $this->config['production']['username'],
            'password' => $this->config['production']['password'],
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ], 'production');
        
        // Add development connection
        $this->capsule->addConnection([
            'driver' => 'pgsql',
            'host' => $this->config['development']['host'],
            'database' => $this->config['development']['database'],
            'username' => $this->config['development']['username'],
            'password' => $this->config['development']['password'],
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ], 'development');
        
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }
    
    public function validateConnection($connectionName) {
        $config = $this->config[$connectionName];
        
        echo "🔍 Testing {$connectionName} database: {$config['database']} on {$config['host']}\n";
        
        try {
            $connection = $this->capsule->connection($connectionName);
            
            // Test basic connection
            $result = $connection->select('SELECT 1 as test');
            
            if (empty($result)) {
                throw new Exception("Connection test failed");
            }
            
            echo "✅ Connection successful to {$connectionName} database\n";
            
            // Get database info
            $dbInfo = $connection->select("
                SELECT 
                    current_database() as database_name,
                    current_user as connected_user,
                    version() as postgres_version
            ");
            
            if (!empty($dbInfo)) {
                $info = $dbInfo[0];
                echo "Database: {$info->database_name}\n";
                echo "User: {$info->connected_user}\n";
                echo "PostgreSQL Version: " . substr($info->postgres_version, 0, 50) . "...\n";
            }
            
            // Get table count
            $tableCount = $connection->select("
                SELECT COUNT(*) as count
                FROM information_schema.tables 
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");
            
            if (!empty($tableCount)) {
                echo "Tables in public schema: {$tableCount[0]->count}\n";
            }
            
            // Get sample table info
            $sampleTables = $connection->select("
                SELECT 
                    tablename,
                    schemaname
                FROM pg_tables 
                WHERE schemaname = 'public'
                ORDER BY tablename
                LIMIT 10
            ");
            
            if (!empty($sampleTables)) {
                echo "Sample tables:\n";
                foreach ($sampleTables as $table) {
                    echo "  - {$table->tablename}\n";
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ Connection failed to {$connectionName} database\n";
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function compareDatabase() {
        echo "\n============================================\n";
        echo "DATABASE COMPARISON\n";
        echo "============================================\n";
        
        try {
            $prodConnection = $this->capsule->connection('production');
            $devConnection = $this->capsule->connection('development');
            
            // Get table counts
            $prodTables = $prodConnection->select("
                SELECT COUNT(*) as count
                FROM information_schema.tables 
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");
            
            $devTables = $devConnection->select("
                SELECT COUNT(*) as count
                FROM information_schema.tables 
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");
            
            $prodCount = $prodTables[0]->count ?? 0;
            $devCount = $devTables[0]->count ?? 0;
            
            echo "Production tables: {$prodCount}\n";
            echo "Development tables: {$devCount}\n";
            
            if ($prodCount == $devCount) {
                echo "✅ Table counts match\n";
            } else {
                echo "⚠️  Table counts differ (this may be normal if schemas have diverged)\n";
            }
            
            return true;
            
        } catch (Exception $e) {
            echo "❌ Database comparison failed\n";
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    public function performDryRun() {
        echo "\n============================================\n";
        echo "DRY RUN SIMULATION\n";
        echo "============================================\n";
        
        $prodConfig = $this->config['production'];
        $devConfig = $this->config['development'];
        
        echo "Simulating sync from {$prodConfig['database']} to {$devConfig['database']}...\n\n";
        
        echo "Dry run would:\n";
        echo "1. Create backup of {$devConfig['database']}\n";
        echo "2. Drop and recreate {$devConfig['database']} database\n";
        echo "3. Copy schema and data from {$prodConfig['database']}\n";
        echo "4. Update sequences and permissions\n";
        echo "5. Run post-sync cleanup\n\n";
        
        $backupFile = "backup_{$devConfig['database']}_" . date('Ymd_His') . '.sql';
        echo "Backup would be saved as: {$backupFile}\n";
        
        echo "✅ Dry run simulation complete\n";
        return true;
    }
    
    public function getConfirmation($prompt) {
        echo "\n{$prompt} (y/n): ";
        $handle = fopen("php://stdin", "r");
        $response = trim(fgets($handle));
        fclose($handle);
        
        return strtolower($response) === 'y' || strtolower($response) === 'yes';
    }
    
    public function run() {
        echo "============================================\n";
        echo "DATABASE SYNC VALIDATION\n";
        echo "============================================\n\n";
        
        echo "This script will:\n";
        echo "1. Test connections to source and destination databases\n";
        echo "2. Compare database structures\n";
        echo "3. Perform a dry run simulation\n";
        echo "4. Optionally provide sync instructions\n\n";
        
        // Step 1: Validate Production Database
        echo "============================================\n";
        echo "STEP 1: VALIDATE SOURCE DATABASE (PRODUCTION)\n";
        echo "============================================\n";
        
        if (!$this->validateConnection('production')) {
            echo "❌ Source database validation failed\n";
            return false;
        }
        
        // Step 2: Validate Development Database
        echo "\n============================================\n";
        echo "STEP 2: VALIDATE DESTINATION DATABASE (DEVELOPMENT)\n";
        echo "============================================\n";
        
        if (!$this->validateConnection('development')) {
            echo "❌ Destination database validation failed\n";
            return false;
        }
        
        // Step 3: Compare databases
        if (!$this->compareDatabase()) {
            echo "❌ Database comparison failed\n";
            return false;
        }
        
        // Step 4: Dry run
        if (!$this->performDryRun()) {
            echo "❌ Dry run failed\n";
            return false;
        }
        
        // Step 5: Next steps
        echo "\n============================================\n";
        echo "READY FOR SYNC\n";
        echo "============================================\n";
        echo "✅ All validations passed!\n\n";
        
        echo "⚠️  WARNING: The sync will REPLACE all data in {$this->config['development']['database']} with data from {$this->config['production']['database']}\n\n";
        
        if ($this->getConfirmation("Do you want to see the sync command to run?")) {
            echo "\nTo perform the actual sync, you'll need PostgreSQL client tools (pg_dump/psql).\n";
            echo "Install PostgreSQL client tools, then run:\n\n";
            echo "bash sync-db-improved.sh\n\n";
            echo "Or use the Windows batch wrapper:\n";
            echo "sync-db-improved.bat\n\n";
        } else {
            echo "You can run this validation again anytime.\n";
        }
        
        return true;
    }
}

// Run the validator
try {
    $validator = new DatabaseValidator();
    $validator->run();
} catch (Exception $e) {
    echo "❌ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
