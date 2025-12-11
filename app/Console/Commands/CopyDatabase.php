<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopyDatabase extends Command
{
    protected $signature = 'db:copy {source} {target} {--dry-run}';
    protected $description = 'Copy data from source database to target database';

    private $batchSize = 1000;

    public function handle()
    {
        $sourceDb = $this->argument('source');
        $targetDb = $this->argument('target');
        $dryRun = $this->option('dry-run');

        $this->info("Database Copy Command");
        $this->info("Source: $sourceDb");
        $this->info("Target: $targetDb");

        if ($dryRun) {
            $this->warn("DRY RUN MODE: No actual changes will be made");
        }

        if (!$this->confirm('Continue with database copy?')) {
            $this->info('Operation cancelled');
            return;
        }

        try {
            $this->validateConnections($sourceDb, $targetDb);
            $tables = $this->getTableList($sourceDb);
            $this->prepareTargetDatabase($targetDb, $dryRun);
            $this->copyTableData($sourceDb, $targetDb, $tables, $dryRun);

            $this->info('Database copy completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function validateConnections($sourceDb, $targetDb)
    {
        $this->info('Validating database connections...');

        // Test source connection
        $sourceConnection = $this->createConnection($sourceDb);
        $sourceConnection->select('SELECT 1 as test');
        $this->info("✓ Source database ($sourceDb) connected");

        // Test target connection
        $targetConnection = $this->createConnection($targetDb);
        $targetConnection->select('SELECT 1 as test');
        $this->info("✓ Target database ($targetDb) connected");
    }

    private function getTableList($sourceDb)
    {
        $sourceConnection = $this->createConnection($sourceDb);

        $tables = $sourceConnection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ");

        $tableNames = array_map(fn($table) => $table->table_name, $tables);

        $this->info("Found " . count($tableNames) . " tables to copy");

        return $tableNames;
    }

    private function prepareTargetDatabase($targetDb, $dryRun)
    {
        if ($dryRun) {
            $this->info("Would truncate all tables in target database");
            return;
        }

        $this->info('Preparing target database...');
        $targetConnection = $this->createConnection($targetDb);

        // Get all tables in target database
        $tables = $targetConnection->select("
            SELECT table_name
            FROM information_schema.tables
            WHERE table_schema = 'public'
            AND table_type = 'BASE TABLE'
        ");

        // Truncate all tables except migrations
        foreach ($tables as $table) {
            $tableName = $table->table_name;
            if ($tableName !== 'migrations') {
                $targetConnection->statement("TRUNCATE TABLE \"$tableName\" RESTART IDENTITY CASCADE");
            }
        }

        $this->info('✓ Target database prepared');
    }

    private function copyTableData($sourceDb, $targetDb, $tables, $dryRun)
    {
        $sourceConnection = $this->createConnection($sourceDb);
        $targetConnection = $this->createConnection($targetDb);

        foreach ($tables as $table) {
            if ($table === 'migrations') {
                $this->warn("Skipping migrations table");
                continue;
            }

            $this->info("Copying table: $table");

            if ($dryRun) {
                $count = $sourceConnection->table($table)->count();
                $this->info("  Would copy $count records");
                continue;
            }

            try {
                $totalCount = $sourceConnection->table($table)->count();

                if ($totalCount === 0) {
                    $this->info("  Table is empty");
                    continue;
                }

                $copiedCount = 0;
                $progressBar = $this->output->createProgressBar($totalCount);

                // Copy data in batches
                $sourceConnection->table($table)->orderBy('id')->chunk($this->batchSize, function ($records) use ($targetConnection, $table, &$copiedCount, $progressBar) {
                    $data = [];
                    foreach ($records as $record) {
                        $data[] = (array) $record;
                    }

                    if (!empty($data)) {
                        $targetConnection->table($table)->insert($data);
                        $copiedCount += count($data);
                        $progressBar->advance(count($data));
                    }
                });

                $progressBar->finish();
                $this->newLine();
                $this->info("  ✓ Copied $totalCount records");

            } catch (\Exception $e) {
                $this->error("  Error copying $table: " . $e->getMessage());
            }
        }
    }

    private function createConnection($database)
    {
        $connectionName = "temp_$database";

        config(["database.connections.$connectionName" => [
            'driver' => 'pgsql',
            'host' => config('database.connections.pgsql.host'),
            'port' => config('database.connections.pgsql.port'),
            'database' => $database,
            'username' => config('database.connections.pgsql.username'),
            'password' => config('database.connections.pgsql.password'),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
        ]]);

        return DB::connection($connectionName);
    }
}
