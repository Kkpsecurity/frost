<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseAuthSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting CourseAuth seeding...');

        // Verify database connection and table existence
        try {
            $this->command->info('Testing database connection...');
            $result = DB::select('SELECT 1 as test');
            $this->command->info('Database connection successful');

            $this->command->info('Checking table existence...');
            $tableExists = DB::select("SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = 'course_auths')")[0]->exists;

            if (!$tableExists) {
                $this->command->error('course_auths table does not exist in the database!');
                return;
            }

            $this->command->info('course_auths table confirmed to exist');

            // Test basic table access
            $this->command->info('Testing table access...');
            $count = DB::table('course_auths')->count();
            $this->command->info("Current course_auths count: {$count}");

        } catch (\Exception $e) {
            $this->command->error('Database connection or table check failed: ' . $e->getMessage());
            return;
        }

        // Check if course_auths_id_seq sequence exists and reset it
        try {
            $sequenceExists = DB::select("SELECT COUNT(*) as count FROM information_schema.sequences WHERE sequence_name = 'course_auths_id_seq'")[0]->count > 0;

            if ($sequenceExists) {
                $this->command->info('Resetting course_auths_id_seq sequence...');
                DB::statement("SELECT setval('course_auths_id_seq', 1, false)");
            } else {
                $this->command->info('course_auths_id_seq sequence does not exist, skipping reset');
            }
        } catch (\Exception $e) {
            $this->command->warn('Could not check/reset sequence: ' . $e->getMessage());
        }

        $this->executeSqlFile('database/seeders/sql/CourseAuths.sql');

        $this->command->info('CourseAuth seeding completed!');
    }

    /**
     * Execute SQL file and handle PostgreSQL sequence
     */
    private function executeSqlFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("SQL file not found: {$filePath}");
        }

        $sql = file_get_contents($filePath);
        $statements = $this->parseSqlStatements($sql);

        $insertCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        // Show a sample of statements being generated (first 3)
        $this->command->info("Sample statements being generated:");
        for ($i = 0; $i < min(3, count($statements)); $i++) {
            $this->command->info("Statement {$i}: " . substr($statements[$i], 0, 200) . "...");
        }

        // Execute each statement
        foreach ($statements as $statement) {
            $trimmedStatement = trim($statement);

            if (!empty($trimmedStatement)) {
                // Skip problematic statements that could drop the table
                if ($this->shouldSkipStatement($trimmedStatement)) {
                    $skipCount++;
                    continue;
                }

                try {
                    DB::unprepared($statement);
                    $insertCount++;
                } catch (\Exception $e) {
                    $errorMessage = $e->getMessage();

                    // Log the first few errors for debugging
                    if ($errorCount < 3) {
                        $this->command->error("Failed to execute statement: " . substr($statement, 0, 100) . "...");
                        $this->command->error("Error: " . $errorMessage);
                    }

                    // Skip errors for already existing constraints or tables, but also foreign key violations
                    if (strpos($errorMessage, 'already exists') !== false ||
                        strpos($errorMessage, 'does not exist') !== false ||
                        strpos($errorMessage, 'violates foreign key constraint') !== false ||
                        strpos($errorMessage, 'duplicate key value') !== false) {
                        $skipCount++;
                    } else {
                        $errorCount++;
                        if ($errorCount < 5) { // Only show first 5 unexpected errors
                            throw $e;
                        }
                    }
                }
            }
        }        $this->command->info("Executed {$insertCount} statements, skipped {$skipCount} statements.");

        // Check if sequence exists and reset it
        try {
            // First check if the sequence exists
            $sequenceExists = DB::select("SELECT 1 FROM pg_class WHERE relname = 'course_auths_id_seq' AND relkind = 'S'");

            if (!empty($sequenceExists)) {
                DB::unprepared("SELECT setval('course_auths_id_seq', COALESCE((SELECT MAX(id) FROM course_auths), 1), true);");
                $this->command->info('Sequence updated successfully.');
            } else {
                $this->command->info('Sequence course_auths_id_seq does not exist - skipping sequence update.');
            }
        } catch (\Exception $e) {
            $this->command->error("Failed to update sequence: " . $e->getMessage());
            // Don't throw - the data might still be inserted successfully
        }

        $this->command->info('CourseAuth data seeding completed.');
    }

    /**
     * Parse SQL file and convert COPY statements to INSERT statements
     */
    private function parseSqlStatements(string $sql): array
    {
        // Remove only line comments but preserve structure for COPY statements
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        $statements = [];
        $lines = explode("\n", $sql);
        $currentStatement = '';
        $inCopyStatement = false;
        $copyHeader = '';
        $copyData = [];

        foreach ($lines as $originalLine) {
            $trimmedLine = trim($originalLine);

            // Skip empty lines when not in COPY statement
            if (empty($trimmedLine) && !$inCopyStatement) {
                continue;
            }

            // Check if we're starting a COPY statement
            if (strpos($trimmedLine, 'COPY ') === 0) {
                if (!empty($currentStatement)) {
                    $statements[] = $currentStatement;
                    $currentStatement = '';
                }
                $copyHeader = $trimmedLine;
                $inCopyStatement = true;
                $copyData = [];
                continue;
            }

            // Check if we're ending a COPY statement
            if ($inCopyStatement && $trimmedLine === '\.') {
                // Convert COPY statement to INSERT statements
                $insertStatements = $this->convertCopyToInserts($copyHeader, $copyData);
                $statements = array_merge($statements, $insertStatements);

                $inCopyStatement = false;
                $copyData = [];
                $copyHeader = '';
                continue;
            }

            if ($inCopyStatement) {
                // Collect COPY data lines
                if (!empty($originalLine)) {
                    $copyData[] = $originalLine;
                }
            } else {
                $currentStatement .= $trimmedLine . "\n";

                // End of regular statement
                if (substr($trimmedLine, -1) === ';') {
                    $statements[] = $currentStatement;
                    $currentStatement = '';
                }
            }
        }

        // Add final statement if exists
        if (!empty($currentStatement)) {
            $statements[] = $currentStatement;
        }

        return array_filter($statements, fn($stmt) => !empty(trim($stmt)));
    }

    /**
     * Convert COPY statement and data to INSERT statements
     */
    private function convertCopyToInserts(string $copyHeader, array $copyData): array
    {
        // Extract table name and columns from COPY header
        if (!preg_match('/COPY\s+(?:public\.)?(\w+)\s*\(([^)]+)\)\s+FROM\s+stdin;?/i', $copyHeader, $matches)) {
            return [];
        }

        $tableName = $matches[1];
        $columns = array_map('trim', explode(',', $matches[2]));

        $insertStatements = [];

        foreach ($copyData as $dataLine) {
            $dataLine = rtrim($dataLine, "\r");
            if (empty($dataLine)) {
                continue;
            }

            // Split by tabs (PostgreSQL COPY format)
            $values = explode("\t", $dataLine);

            if (count($values) !== count($columns)) {
                // Skip malformed lines
                continue;
            }

            // Convert values to proper SQL format
            $formattedValues = [];
            foreach ($values as $value) {
                if ($value === '\N' || $value === '\\N') {
                    $formattedValues[] = 'NULL';
                } else if ($value === 't') {
                    $formattedValues[] = 'true';
                } else if ($value === 'f') {
                    $formattedValues[] = 'false';
                } else if (is_numeric($value) && !preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                    $formattedValues[] = $value;
                } else {
                    // Escape single quotes and wrap in quotes
                    $escapedValue = str_replace("'", "''", $value);
                    $formattedValues[] = "'{$escapedValue}'";
                }
            }

            $columnList = implode(', ', $columns);
            $valueList = implode(', ', $formattedValues);

            $insertStatements[] = "INSERT INTO {$tableName} ({$columnList}) VALUES ({$valueList});";
        }

        return $insertStatements;
    }

    /**
     * Check if a statement should be skipped (avoid problematic SQL)
     */
    private function shouldSkipStatement(string $statement): bool
    {
        $statement = trim($statement);

        // Skip empty statements
        if (empty($statement)) {
            return true;
        }

        // Skip comments
        if (strpos($statement, '--') === 0) {
            return true;
        }

        // Skip problematic SQL commands that might drop/alter the table
        $skipPatterns = [
            '/^SET\s+/i',
            '/^SELECT\s+/i',
            '/^DROP\s+/i',
            '/^ALTER\s+TABLE.*DROP\s+/i',
            '/^CREATE\s+INDEX/i',
            '/^COPY\s+/i'  // We handle COPY statements separately
        ];

        foreach ($skipPatterns as $pattern) {
            if (preg_match($pattern, $statement)) {
                return true;
            }
        }

        return false;
    }
}
