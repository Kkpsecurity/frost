<?php
/**
 * FROST Database Validation and Sync Script
 * 
 * Purpose: Validate database connections and perform sync validation
 * Author: GitHub Copilot
 * Date: September 15, 2025
 */

require_once '../vendor/autoload.php';

// Load Laravel environment
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DatabaseValidator {
    
    private $prodConfig;
    private $devConfig;
    
    public function __construct() {
        // Production database configuration (source)
        $this->prodConfig = [
            'host' => 'develc.hq.cisadmin.com',
            'database' => 'frost',
            'username' => 'frost',
            'password' => 'kj,L@-N%AyAFWxda'
        ];
        
        // Development database configuration (target - from .env)
        $this->devConfig = [
            'host' => config('database.connections.pgsql.host'),
            'database' => config('database.connections.pgsql.database'),
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password')
        ];
    }
    
    public function validateConnections() {
        echo "🔍 FROST Database Connection Validation\n";
        echo "=====================================\n\n";
        
        // Show configuration
        echo "📋 Configuration:\n";
        echo "  Production (Source): {$this->prodConfig['database']} on {$this->prodConfig['host']}\n";
        echo "  Development (Target): {$this->devConfig['database']} on {$this->devConfig['host']}\n\n";
        
        // Test production database
        echo "🧪 Testing Production Database Connection...\n";
        $prodExists = $this->testConnection($this->prodConfig, 'PRODUCTION');
        
        // Test development database
        echo "\n🧪 Testing Development Database Connection...\n";
        $devExists = $this->testConnection($this->devConfig, 'DEVELOPMENT');
        
        // If production doesn't exist, suggest alternatives
        if (!$prodExists) {
            echo "\n⚠️  ISSUE: Production database '{$this->prodConfig['database']}' not found!\n";
            echo "🔍 Discovering available databases...\n\n";
            $this->discoverAlternatives();
        }
        
        return ['prod' => $prodExists, 'dev' => $devExists];
    }
    
    private function testConnection($config, $label) {
        try {
            // Create temporary connection
            Config::set('database.connections.temp', [
                'driver' => 'pgsql',
                'host' => $config['host'],
                'port' => 5432,
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
            ]);
            
            $connection = DB::connection('temp');
            $result = $connection->select('SELECT 1 as test');
            
            if ($result) {
                echo "  ✅ {$label}: Connected successfully to '{$config['database']}'\n";
                
                // Get basic stats
                $tables = $connection->select("
                    SELECT COUNT(*) as table_count 
                    FROM information_schema.tables 
                    WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
                ");
                
                $size = $connection->select("
                    SELECT pg_size_pretty(pg_database_size(current_database())) as size
                ");
                
                echo "     📊 Tables: {$tables[0]->table_count}\n";
                echo "     💾 Size: {$size[0]->size}\n";
                
                return true;
            }
        } catch (Exception $e) {
            echo "  ❌ {$label}: Connection failed\n";
            echo "     Error: " . $e->getMessage() . "\n";
            return false;
        }
        
        return false;
    }
    
    private function discoverAlternatives() {
        try {
            // Connect to default postgres database to list all databases
            Config::set('database.connections.discovery', [
                'driver' => 'pgsql',
                'host' => $this->prodConfig['host'],
                'port' => 5432,
                'database' => 'postgres', // Connect to default database
                'username' => $this->prodConfig['username'],
                'password' => $this->prodConfig['password'],
                'charset' => 'utf8',
                'prefix' => '',
                'schema' => 'public',
            ]);
            
            $connection = DB::connection('discovery');
            
            $databases = $connection->select("
                SELECT datname, pg_size_pretty(pg_database_size(datname)) as size
                FROM pg_database 
                WHERE datistemplate = false 
                AND datname NOT IN ('postgres')
                ORDER BY pg_database_size(datname) DESC
            ");
            
            echo "📋 Available Databases:\n";
            foreach ($databases as $db) {
                echo "  • {$db->datname} ({$db->size})\n";
            }
            
            echo "\n🎯 Recommendations:\n";
            echo "  1. 'frost-patch' - Likely most recent production data\n";
            echo "  2. 'frost-old' - Backup/older production data\n";
            echo "  3. Create the 'frost' database if it should exist\n";
            
        } catch (Exception $e) {
            echo "❌ Could not discover databases: " . $e->getMessage() . "\n";
        }
    }
    
    public function performDryRun() {
        echo "\n🧪 DRY RUN SIMULATION\n";
        echo "===================\n";
        echo "This would perform the following actions:\n\n";
        echo "1. 📋 Backup current development database\n";
        echo "2. 🔄 Sync data from production to development\n";
        echo "3. 🧹 Clear cache and sessions\n";
        echo "4. ✅ Verify sync completion\n\n";
        echo "⚠️  Your current database '{$this->devConfig['database']}' would be overwritten!\n";
        echo "💾 A backup would be created first for safety.\n\n";
    }
    
    public function promptForSync() {
        echo "🤔 Ready to proceed with sync? (y/N): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        return trim(strtolower($line)) === 'y';
    }
}

// Run the validation
$validator = new DatabaseValidator();
$connections = $validator->validateConnections();

if ($connections['prod'] && $connections['dev']) {
    echo "\n✅ ALL CONNECTIONS SUCCESSFUL!\n";
    $validator->performDryRun();
    
    if ($validator->promptForSync()) {
        echo "\n🚀 Starting sync process...\n";
        echo "💡 Run: bash scripts/sync-db-improved.sh\n";
    } else {
        echo "\n👍 Sync cancelled. You can run it later with:\n";
        echo "   bash scripts/sync-db-improved.sh\n";
    }
} else {
    echo "\n❌ CONNECTION ISSUES DETECTED\n";
    echo "Please resolve connection issues before proceeding with sync.\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Fix any connection issues\n";
echo "2. Choose correct source database if 'frost' doesn't exist\n";
echo "3. Run sync with: bash scripts/sync-db-improved.sh\n";
