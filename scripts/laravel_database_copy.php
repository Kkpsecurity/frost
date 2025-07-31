<?php

/**
 * Laravel Database Copy Script
 * Copies data from frost-patch to frost-devel using Laravel's database connections
 *
 * Usage: php scripts/laravel_database_copy.php [--dry-run]
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

class LaravelDatabaseCopy
{
    private $sourceDb = 'frost-patch';
    private $targetDb = 'frost-devel';
    private $dryRun = false;
    private $batchSize = 1000;

    public function __construct($dryRun = false)
    {
        $this->dryRun = $dryRun;

        echo "Laravel Database Copy Script" . ($dryRun ? " (DRY RUN MODE)" : "") . "\n";
        echo "============================\n";
        echo "Source: {$this->sourceDb}\n";
        echo "Target: {$this->targetDb}\n";
        echo "Method: Laravel Database Connections\n";

        if ($dryRun) {
            echo "\n🔍 DRY RUN MODE: No actual changes will be made\n";
        }
        echo "\n";
    }

    public function run()
    {
        try {
            $this->validateConnections();
            $this->confirmOperation();
            $this->performCopy();
            echo "\n✅ Database copy completed successfully!\n";
        } catch (Exception $e) {
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function validateConnections()
    {
        echo "🔍 Validating database connections...\n";

        try {
            // Test source connection
            $sourceConnection = $this->getSourceConnection();
            $sourceTest = $sourceConnection->select('SELECT 1 as test');
            echo "   ✅ Source database ({$this->sourceDb}) connected\n";

            // Test target connection
            $targetConnection = $this->getTargetConnection();
            $targetTest = $targetConnection->select('SELECT 1 as test');
            echo "   ✅ Target database ({$this->targetDb}) connected\n";

        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    private function confirmOperation()
    {
        if ($this->dryRun) {
            echo "🔍 DRY RUN: This will show what would happen without making changes\n\n";
            return true;
        }

        echo "⚠️  WARNING: This operation will:\n";
        echo "   1. Clear all data in '{$this->targetDb}'\n";
        echo "   2. Copy all data from '{$this->sourceDb}' to '{$this->targetDb}'\n";
        echo "   3. This action cannot be undone!\n\n";

        echo "Do you want to continue? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);

        if (trim(strtolower($line)) !== 'yes') {
            echo "Operation cancelled.\n";
            exit(0);
        }
    }

    private function performCopy()
    {
        echo "\n🚀 Starting database copy process...\n\n";

        echo "1️⃣ Getting table list from source database...\n";
        $tables = $this->getTableList();

        echo "2️⃣ Preparing target database...\n";
        $this->prepareTargetDatabase();

        echo "3️⃣ Copying data from source to target...\n";
        $this->copyTableData($tables);

        echo "4️⃣ Running Laravel migrations...\n";
        $this->runMigrations();

        echo "✅ All steps completed!\n";
    }

    private function getTableList()
    {
        $sourceConnection = $this->getSourceConnection();

        $query = "
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ";

        $tables = $sourceConnection->select($query);
        $tableNames = array_map(function($table) {
            return $table->table_name;
        }, $tables);

        if ($this->dryRun) {
            echo "   🔍 Found " . count($tableNames) . " tables to copy:\n";
            foreach ($tableNames as $table) {
                echo "       - $table\n";
            }
        } else {
            echo "   ✅ Found " . count($tableNames) . " tables to copy\n";
        }

        return $tableNames;
    }

    private function prepareTargetDatabase()
    {
        if ($this->dryRun) {
            echo "   🔍 Would truncate all tables in target database\n";
            return;
        }

        $targetConnection = $this->getTargetConnection();

        // Disable foreign key checks
        $targetConnection->statement('SET foreign_key_checks = 0');

        // Get all tables in target database
        $tables = $targetConnection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
        ");

        // Truncate all tables
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            if ($tableName !== 'migrations') { // Don't truncate migrations table
                $targetConnection->statement("TRUNCATE TABLE \"$tableName\" RESTART IDENTITY CASCADE");
            }
        }

        // Re-enable foreign key checks
        $targetConnection->statement('SET foreign_key_checks = 1');

        echo "   ✅ Target database prepared\n";
    }

    private function copyTableData($tables)
    {
        $sourceConnection = $this->getSourceConnection();
        $targetConnection = $this->getTargetConnection();

        foreach ($tables as $table) {
            if ($table === 'migrations') {
                echo "   ⏭️  Skipping migrations table\n";
                continue;
            }

            echo "   📋 Copying table: $table\n";

            if ($this->dryRun) {
                $count = $sourceConnection->table($table)->count();
                echo "       🔍 Would copy $count records\n";
                continue;
            }

            try {
                // Get total count for progress
                $totalCount = $sourceConnection->table($table)->count();

                if ($totalCount === 0) {
                    echo "       ✅ Table is empty\n";
                    continue;
                }

                $copiedCount = 0;

                // Copy data in batches
                $sourceConnection->table($table)->orderBy('id')->chunk($this->batchSize, function ($records) use ($targetConnection, $table, &$copiedCount, $totalCount) {
                    $data = [];
                    foreach ($records as $record) {
                        $data[] = (array) $record;
                    }

                    if (!empty($data)) {
                        $targetConnection->table($table)->insert($data);
                        $copiedCount += count($data);
                        echo "       📊 Progress: $copiedCount/$totalCount records\n";
                    }
                });

                echo "       ✅ Copied $totalCount records\n";

            } catch (Exception $e) {
                echo "       ⚠️  Error copying $table: " . $e->getMessage() . "\n";
            }
        }
    }

    private function runMigrations()
    {
        if ($this->dryRun) {
            echo "   🔍 Would run Laravel migrations\n";
            return;
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            echo "   ✅ Migrations completed\n";
        } catch (Exception $e) {
            echo "   ⚠️  Migration warnings: " . $e->getMessage() . "\n";
        }
    }

    private function getSourceConnection()
    {
        // Create dynamic connection for source database
        config(['database.connections.source_temp' => [
            'driver' => 'pgsql',
            'host' => config('database.connections.pgsql.host'),
            'port' => config('database.connections.pgsql.port'),
            'database' => $this->sourceDb,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]]);

        return DB::connection('source_temp');
    }

    private function getTargetConnection()
    {
        // Create dynamic connection for target database
        config(['database.connections.target_temp' => [
            'driver' => 'pgsql',
            'host' => config('database.connections.pgsql.host'),
            'port' => config('database.connections.pgsql.port'),
            'database' => $this->targetDb,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]]);

        return DB::connection('target_temp');
    }
}

// Run the script
if (php_sapi_name() === 'cli') {
    $dryRun = in_array('--dry-run', $argv) || in_array('-d', $argv);

    if ($dryRun) {
        echo "Running in DRY RUN mode...\n\n";
    }

    $script = new LaravelDatabaseCopy($dryRun);
    $script->run();
} else {
    echo "This script can only be run from the command line.\n";
    echo "Usage: php scripts/laravel_database_copy.php [--dry-run|-d]\n";
}
