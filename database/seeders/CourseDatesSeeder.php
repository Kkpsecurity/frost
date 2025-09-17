<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseDatesSeeder extends Seeder
{
    /**
     * Run the course dates seeder.
     *
     * Seeds historical course date data from July 2023 - September 2026
     * Reads data from SQL file: database/seeders/sql/course_dates.sql
     *
     * @return void
     */
    public function run()
    {
        // Clear all existing course dates - complete refresh
        DB::table('course_dates')->delete();

        // Load and execute SQL file
        $this->executeSqlFile('course_dates.sql');

        // Reset the sequence to the highest ID + 1 (PostgreSQL only)
        if (DB::getDriverName() === 'pgsql') {
            $maxId = DB::table('course_dates')->max('id') ?? 0;
            DB::statement("SELECT pg_catalog.setval('course_dates_id_seq', " . ($maxId + 1) . ", false)");
        }

        $totalRecords = DB::table('course_dates')->count();
        $this->command->info("Course dates seeded successfully! Total records: {$totalRecords}");
    }

    /**
     * Execute SQL file from the sql directory
     *
     * @param string $filename
     * @return void
     */
    private function executeSqlFile(string $filename): void
    {
        $sqlPath = database_path('seeders/sql/' . $filename);

        if (!file_exists($sqlPath)) {
            throw new \Exception("SQL file not found: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);

        // Split SQL into individual statements and execute them
        $statements = $this->parseSqlStatements($sql);

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                DB::statement($statement);
            }
        }

        $this->command->info("Executed SQL file: {$filename}");
    }

    /**
     * Parse SQL file into individual statements
     *
     * @param string $sql
     * @return array
     */
    private function parseSqlStatements(string $sql): array
    {
        // Remove comments and split by semicolons
        $sql = preg_replace('/--.*$/m', '', $sql);  // Remove single line comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);  // Remove multi-line comments

        // Split by semicolons and filter out empty statements
        $statements = explode(';', $sql);

        return array_filter(array_map('trim', $statements), function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*$/', $stmt);
        });
    }
}
