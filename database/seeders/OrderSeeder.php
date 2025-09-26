<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if we should clear existing orders
        if ($this->command->confirm('Clear existing orders before seeding?', false)) {
            DB::table('orders')->delete();
            $this->command->info('Existing orders cleared.');
        }

        // Try to read SQL dump file first
        $sqlFile = database_path('seeders/sql/orders.sql');
        if (file_exists($sqlFile)) {
            $this->importFromSqlFile($sqlFile);
        } else {
            $this->importFromArray();
        }
    }

    /**
     * Import orders from SQL dump file
     */
    private function importFromSqlFile(string $filePath): void
    {
        $this->command->info('Reading SQL dump from: ' . $filePath);

        $content = file_get_contents($filePath);

        // Try PostgreSQL COPY format first
        if ($this->importFromCopyFormat($content)) {
            return;
        }

        // Fallback to INSERT format
        if (preg_match_all('/INSERT INTO orders.*?VALUES\s*\((.*?)\);/s', $content, $matches)) {
            $totalInserted = 0;

            foreach ($matches[1] as $valuesString) {
                // Parse the VALUES part
                if (preg_match_all('/\((.*?)\)/', $valuesString, $valueMatches)) {
                    $orders = [];

                    foreach ($valueMatches[1] as $values) {
                        $order = $this->parseOrderValues($values);
                        if ($order) {
                            $orders[] = $order;
                        }
                    }

                    if (!empty($orders)) {
                        // Insert in chunks
                        foreach (array_chunk($orders, 100) as $chunk) {
                            DB::table('orders')->insert($chunk);
                            $totalInserted += count($chunk);
                        }
                    }
                }
            }

            $this->command->info("Successfully imported {$totalInserted} orders from SQL dump.");

            // Update PostgreSQL sequence
            if (DB::getDriverName() === 'pgsql' && $totalInserted > 0) {
                $maxId = DB::table('orders')->max('id');
                DB::statement("SELECT setval('orders_id_seq', {$maxId}, true)");
                $this->command->info("Updated sequence to {$maxId}.");
            }
        } else {
            $this->command->error('No valid INSERT or COPY statements found in SQL file.');
            $this->importFromArray();
        }
    }

    /**
     * Import from PostgreSQL COPY format
     */
    private function importFromCopyFormat(string $content): bool
    {
        // Look for COPY statement and data - more flexible pattern
        if (!preg_match('/COPY public\.orders.*?FROM stdin;\s*(.*?)(?:^\s*--|\\\.\s*$|\Z)/ms', $content, $matches)) {
            $this->command->info('Could not find COPY statement. Checking for simpler pattern...');

            // Try simpler pattern - just find the data after COPY ... FROM stdin;
            $lines = explode("\n", $content);
            $dataStarted = false;
            $dataLines = [];

            $this->command->info('Total lines in file: ' . count($lines));

            foreach ($lines as $lineNum => $line) {
                $line = trim($line);

                if (strpos($line, 'FROM stdin;') !== false) {
                    $dataStarted = true;
                    $this->command->info('Found COPY FROM stdin; statement at line ' . ($lineNum + 1));
                    continue;
                }

                if ($dataStarted) {
                    // Stop when we hit comments or end markers
                    if (strpos($line, '--') === 0 ||
                        $line === '\.' ||
                        strpos($line, 'PostgreSQL database dump') !== false) {
                        $this->command->info('Found end marker at line ' . ($lineNum + 1) . ': ' . substr($line, 0, 50));
                        break;
                    }

                    // Skip empty lines
                    if (empty($line)) {
                        continue;
                    }

                    $dataLines[] = $line;

                    // Show progress for first few lines
                    if (count($dataLines) <= 5) {
                        $this->command->info('Data line ' . count($dataLines) . ': ' . substr($line, 0, 100));
                    }
                }
            }

            if (empty($dataLines)) {
                $this->command->error('No data lines found after parsing.');
                return false;
            }

            $this->command->info('Found ' . count($dataLines) . ' data lines to process.');
            return $this->processCopyLines($dataLines);
        }

        $dataSection = trim($matches[1]);
        $lines = array_filter(explode("\n", $dataSection), function($line) {
            return !empty(trim($line)) && trim($line) !== '\.';
        });

        if (empty($lines)) {
            $this->command->error('No data lines found in COPY format.');
            return false;
        }

        return $this->processCopyLines($lines);
    }

    /**
     * Process COPY format lines
     */
    private function processCopyLines(array $lines): bool
    {
        $orders = [];
        $totalInserted = 0;

        $this->command->info('Processing ' . count($lines) . ' lines...');

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Split by tab (COPY format uses tabs)
            $parts = explode("\t", $line);

            $this->command->info("Line " . ($lineNum + 1) . ": Found " . count($parts) . " parts");

            if (count($parts) >= 13) {
                $order = [
                    'id' => $this->cleanCopyValue($parts[0]),
                    'user_id' => $this->cleanCopyValue($parts[1]),
                    'course_id' => $this->cleanCopyValue($parts[2]),
                    'payment_type_id' => $this->cleanCopyValue($parts[3]),
                    'course_price' => $this->cleanCopyValue($parts[4]),
                    'discount_code_id' => $this->cleanCopyValue($parts[5]),
                    'total_price' => $this->cleanCopyValue($parts[6]),
                    'created_at' => $this->cleanCopyValue($parts[7]),
                    'updated_at' => $this->cleanCopyValue($parts[8]),
                    'completed_at' => $this->cleanCopyValue($parts[9]),
                    'course_auth_id' => $this->cleanCopyValue($parts[10]),
                    'refunded_at' => $this->cleanCopyValue($parts[11]),
                    'refunded_by' => $this->cleanCopyValue($parts[12]),
                ];

                $orders[] = $order;

                // Insert in chunks of 100
                if (count($orders) >= 100) {
                    DB::table('orders')->insert($orders);
                    $totalInserted += count($orders);
                    $this->command->info("Inserted {$totalInserted} orders...");
                    $orders = [];
                }
            }
        }

        // Insert remaining orders
        if (!empty($orders)) {
            DB::table('orders')->insert($orders);
            $totalInserted += count($orders);
        }

        $this->command->info("Successfully imported {$totalInserted} orders from COPY format.");

        // Update PostgreSQL sequence
        if (DB::getDriverName() === 'pgsql' && $totalInserted > 0) {
            $maxId = DB::table('orders')->max('id');
            DB::statement("SELECT setval('orders_id_seq', {$maxId}, true)");
            $this->command->info("Updated sequence to {$maxId}.");
        }

        return true;
    }    /**
     * Import orders from hardcoded array (fallback)
     */
    private function importFromArray(): void
    {
        $this->command->info('Using fallback array method...');

        // Sample orders - replace with your actual data
        $orders = [
            // Add your order data here in this format:
            /*
            [
                'id' => 1,
                'user_id' => 123,
                'course_id' => 1,
                'payment_type_id' => 1,
                'course_price' => 100.00,
                'discount_code_id' => null,
                'total_price' => 100.00,
                'created_at' => '2024-01-01 10:00:00',
                'updated_at' => '2024-01-01 10:00:00',
                'completed_at' => '2024-01-01 10:30:00',
                'course_auth_id' => null,
                'refunded_at' => null,
                'refunded_by' => null,
            ],
            */
        ];

        if (empty($orders)) {
            $this->command->warn('No order data provided. Please add data to the $orders array or provide SQL dump file.');
            return;
        }

        // Insert orders in chunks
        foreach (array_chunk($orders, 100) as $chunk) {
            DB::table('orders')->insert($chunk);
        }

        $this->command->info('Successfully seeded ' . count($orders) . ' orders from array.');

        // Update PostgreSQL sequence
        if (DB::getDriverName() === 'pgsql') {
            $maxId = max(array_column($orders, 'id'));
            DB::statement("SELECT setval('orders_id_seq', {$maxId}, true)");
            $this->command->info("Updated sequence to {$maxId}.");
        }
    }

    /**
     * Parse order values from SQL string
     */
    private function parseOrderValues(string $values): ?array
    {
        // Split by comma, handling quoted values
        $parts = str_getcsv($values, ',', "'");

        if (count($parts) < 13) { // Adjust based on your table structure
            return null;
        }

        return [
            'id' => $this->cleanValue($parts[0]),
            'user_id' => $this->cleanValue($parts[1]),
            'course_id' => $this->cleanValue($parts[2]),
            'payment_type_id' => $this->cleanValue($parts[3]),
            'course_price' => $this->cleanValue($parts[4]),
            'discount_code_id' => $this->cleanValue($parts[5]),
            'total_price' => $this->cleanValue($parts[6]),
            'created_at' => $this->cleanValue($parts[7]),
            'updated_at' => $this->cleanValue($parts[8]),
            'completed_at' => $this->cleanValue($parts[9]),
            'course_auth_id' => $this->cleanValue($parts[10]),
            'refunded_at' => $this->cleanValue($parts[11]),
            'refunded_by' => $this->cleanValue($parts[12]),
        ];
    }

    /**
     * Clean and convert SQL value to proper PHP type
     */
    private function cleanValue($value): mixed
    {
        $value = trim($value);

        // Handle NULL
        if (strtoupper($value) === 'NULL' || $value === '') {
            return null;
        }

        // Remove quotes
        $value = trim($value, "'\"");

        // Convert to proper types
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        return $value;
    }

    /**
     * Clean and convert PostgreSQL COPY format value to proper PHP type
     */
    private function cleanCopyValue($value): mixed
    {
        $value = trim($value);

        // Handle PostgreSQL NULL representation
        if ($value === '\\N' || $value === '' || strtoupper($value) === 'NULL') {
            return null;
        }

        // Convert to proper types
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        return $value;
    }
}
