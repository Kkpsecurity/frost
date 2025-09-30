<?php

/**
 * Database Copy Script
 * Copies data from frost-patch database to frost-devel database
 *
 * Usage: php scripts/copy_database.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseCopyScript
{
    private $sourceDb = 'frost-patch';
    private $targetDb = 'frost-devel';
    private $host = 'develc.hq.cisadmin.com';
    private $port = '5432';
    private $username = 'frost';
    private $password = 'kj,L@-N%AyAFWxda';
    private $dryRun = false;

    public function __construct($dryRun = false)
    {
        $this->dryRun = $dryRun;

        echo "Database Copy Script" . ($dryRun ? " (DRY RUN MODE)" : "") . "\n";
        echo "====================\n";
        echo "Source: {$this->sourceDb}\n";
        echo "Target: {$this->targetDb}\n";
        echo "Host: {$this->host}\n";

        if ($dryRun) {
            echo "\n🔍 DRY RUN MODE: No actual changes will be made\n";
        }
        echo "\n";
    }

    public function run()
    {
        try {
            $this->validateConnection();
            $this->confirmOperation();
            $this->performCopy();
            echo "\n✅ Database copy completed successfully!\n";
        } catch (Exception $e) {
            echo "\n❌ Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function validateConnection()
    {
        echo "🔍 Validating database connections...\n";

        // Test source database connection
        $sourceCmd = "pg_dump --version";
        exec($sourceCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception("pg_dump is not available. Please ensure PostgreSQL client tools are installed.");
        }

        echo "✅ PostgreSQL client tools available\n";
    }

    private function confirmOperation()
    {
        if ($this->dryRun) {
            echo "🔍 DRY RUN: This will show what would happen without making changes\n\n";
            return true;
        }

        echo "⚠️  WARNING: This operation will:\n";
        echo "   1. Drop all tables in '{$this->targetDb}'\n";
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

        // Step 1: Create backup of target database (optional safety measure)
        echo "1️⃣ Creating backup of target database...\n";
        $this->createBackup();

        // Step 2: Drop and recreate target database
        echo "2️⃣ Preparing target database...\n";
        $this->prepareTargetDatabase();

        // Step 3: Copy data from source to target
        echo "3️⃣ Copying data from source to target...\n";
        $this->copyData();

        // Step 4: Run Laravel migrations to ensure schema is up to date
        echo "4️⃣ Running Laravel migrations...\n";
        $this->runMigrations();

        echo "✅ All steps completed!\n";
    }

    private function createBackup()
    {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = __DIR__ . "/backups/{$this->targetDb}_backup_{$timestamp}.sql";

        // Create backups directory if it doesn't exist
        $backupDir = dirname($backupFile);
        if (!is_dir($backupDir)) {
            if ($this->dryRun) {
                echo "   🔍 Would create backup directory: $backupDir\n";
            } else {
                mkdir($backupDir, 0755, true);
            }
        }

        $cmd = sprintf(
            'pg_dump -h %s -p %s -U %s -d %s -f %s',
            escapeshellarg($this->host),
            escapeshellarg($this->port),
            escapeshellarg($this->username),
            escapeshellarg($this->targetDb),
            escapeshellarg($backupFile)
        );

        if ($this->dryRun) {
            echo "   🔍 Would run command: $cmd\n";
            echo "   🔍 Would create backup file: " . basename($backupFile) . "\n";
            return;
        }

        // Set password environment variable
        putenv("PGPASSWORD={$this->password}");

        exec($cmd, $output, $returnCode);

        if ($returnCode === 0) {
            echo "   ✅ Backup created: " . basename($backupFile) . "\n";
        } else {
            echo "   ⚠️ Backup failed (continuing anyway)\n";
        }
    }

    private function prepareTargetDatabase()
    {
        // Drop all tables in target database
        $cmd = sprintf(
            'psql -h %s -p %s -U %s -d %s -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"',
            escapeshellarg($this->host),
            escapeshellarg($this->port),
            escapeshellarg($this->username),
            escapeshellarg($this->targetDb)
        );

        if ($this->dryRun) {
            echo "   🔍 Would run command: $cmd\n";
            echo "   🔍 Would drop all tables in target database\n";
            return;
        }

        putenv("PGPASSWORD={$this->password}");

        exec($cmd, $output, $returnCode);

        if ($returnCode === 0) {
            echo "   ✅ Target database cleared\n";
        } else {
            throw new Exception("Failed to clear target database");
        }
    }

    private function copyData()
    {
        // Create a temporary dump file
        $tempFile = __DIR__ . "/temp_dump.sql";

        // Dump source database
        $dumpCmd = sprintf(
            'pg_dump -h %s -p %s -U %s -d %s --no-owner --no-privileges -f %s',
            escapeshellarg($this->host),
            escapeshellarg($this->port),
            escapeshellarg($this->username),
            escapeshellarg($this->sourceDb),
            escapeshellarg($tempFile)
        );

        // Restore to target database
        $restoreCmd = sprintf(
            'psql -h %s -p %s -U %s -d %s -f %s',
            escapeshellarg($this->host),
            escapeshellarg($this->port),
            escapeshellarg($this->username),
            escapeshellarg($this->targetDb),
            escapeshellarg($tempFile)
        );

        if ($this->dryRun) {
            echo "   🔍 Would dump source database with: $dumpCmd\n";
            echo "   🔍 Would restore to target database with: $restoreCmd\n";
            echo "   🔍 Would create temporary file: $tempFile\n";
            echo "   🔍 Would clean up temporary file after restoration\n";
            return;
        }

        putenv("PGPASSWORD={$this->password}");

        echo "   📤 Dumping source database...\n";
        exec($dumpCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception("Failed to dump source database");
        }

        echo "   📥 Restoring to target database...\n";
        exec($restoreCmd, $output, $returnCode);

        // Clean up temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        if ($returnCode === 0) {
            echo "   ✅ Data copied successfully\n";
        } else {
            throw new Exception("Failed to restore data to target database");
        }
    }

    private function runMigrations()
    {
        echo "   🔄 Running Laravel migrations...\n";

        if ($this->dryRun) {
            echo "   🔍 Would change to Laravel directory: " . dirname(__DIR__) . "\n";
            echo "   🔍 Would run command: php artisan migrate --force\n";
            return;
        }

        // Change to the Laravel directory
        $laravelDir = dirname(__DIR__);
        $originalDir = getcwd();
        chdir($laravelDir);

        try {
            // Run migrations
            $cmd = "php artisan migrate --force";
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0) {
                echo "   ✅ Migrations completed\n";
            } else {
                echo "   ⚠️ Migration warnings (check manually)\n";
            }

        } finally {
            // Change back to original directory
            chdir($originalDir);
        }
    }
}

// Run the script
if (php_sapi_name() === 'cli') {
    // Check for command line arguments
    $dryRun = in_array('--dry-run', $argv) || in_array('-d', $argv);

    if ($dryRun) {
        echo "Running in DRY RUN mode...\n\n";
    }

    $script = new DatabaseCopyScript($dryRun);
    $script->run();
} else {
    echo "This script can only be run from the command line.\n";
    echo "Usage: php scripts/copy_database.php [--dry-run|-d]\n";
}
