<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing orders if requested
        if ($this->command->confirm('Clear existing orders before seeding?', false)) {
            DB::table('orders')->delete();
            $this->command->info('Existing orders cleared.');
        }

        $sqlFile = database_path('seeders/sql/orders.sql');

        if (!file_exists($sqlFile)) {
            $this->command->error('SQL file not found: ' . $sqlFile);
            return;
        }

        $this->command->info('Reading file: ' . $sqlFile);

        // Read the file
        $content = file_get_contents($sqlFile);

        // Find the start of data (after "FROM stdin;")
        $dataStart = strpos($content, "FROM stdin;\n");
        if ($dataStart === false) {
            $this->command->error('Could not find "FROM stdin;" in file');
            return;
        }

        // Get everything after "FROM stdin;"
        $dataSection = substr($content, $dataStart + strlen("FROM stdin;\n"));

        // Split into lines and process
        $lines = explode("\n", $dataSection);
        $orders = [];
        $totalProcessed = 0;
        $totalInserted = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and end markers
            if (empty($line) || $line === '\.' || strpos($line, '--') === 0) {
                continue;
            }

            // Stop if we hit the end of dump
            if (strpos($line, 'PostgreSQL database dump') !== false) {
                break;
            }

            // Split by tabs
            $parts = explode("\t", $line);

            // We need exactly 13 parts for our orders table
            if (count($parts) === 13) {
                $orders[] = [
                    'id' => $this->parseValue($parts[0]),
                    'user_id' => $this->parseValue($parts[1]),
                    'course_id' => $this->parseValue($parts[2]),
                    'payment_type_id' => $this->parseValue($parts[3]),
                    'course_price' => $this->parseValue($parts[4]),
                    'discount_code_id' => $this->parseValue($parts[5]),
                    'total_price' => $this->parseValue($parts[6]),
                    'created_at' => $this->parseValue($parts[7]),
                    'updated_at' => $this->parseValue($parts[8]),
                    'completed_at' => $this->parseValue($parts[9]),
                    'course_auth_id' => $this->parseValue($parts[10]),
                    'refunded_at' => $this->parseValue($parts[11]),
                    'refunded_by' => $this->parseValue($parts[12]),
                ];

                $totalProcessed++;

                // Insert in chunks of 100
                if (count($orders) >= 100) {
                    try {
                        DB::table('orders')->insert($orders);
                        $totalInserted += count($orders);
                        $this->command->info("Inserted {$totalInserted} orders...");
                    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                        // Try inserting one by one to skip duplicates
                        $inserted = 0;
                        foreach ($orders as $order) {
                            try {
                                DB::table('orders')->insert([$order]);
                                $inserted++;
                            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                                // Skip duplicate
                            }
                        }
                        $totalInserted += $inserted;
                        $this->command->info("Inserted {$inserted} new orders (skipped duplicates)...");
                    }
                    $orders = [];
                }
            }
        }

        // Insert remaining orders
        if (!empty($orders)) {
            try {
                DB::table('orders')->insert($orders);
                $totalInserted += count($orders);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Try inserting one by one to skip duplicates
                $inserted = 0;
                foreach ($orders as $order) {
                    try {
                        DB::table('orders')->insert([$order]);
                        $inserted++;
                    } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                        // Skip duplicate
                    }
                }
                $totalInserted += $inserted;
                $this->command->info("Inserted {$inserted} remaining orders (skipped duplicates)");
            }
        }

        $this->command->info("Successfully imported {$totalInserted} orders from {$totalProcessed} processed lines.");

        // Update PostgreSQL sequence
        if (DB::getDriverName() === 'pgsql' && $totalInserted > 0) {
            $maxId = DB::table('orders')->max('id');
            DB::statement("SELECT setval('orders_id_seq', {$maxId}, true)");
            $this->command->info("Updated sequence to {$maxId}.");
        }
    }

    /**
     * Parse a value from the COPY format
     */
    private function parseValue($value)
    {
        $value = trim($value);

        // Handle PostgreSQL NULL
        if ($value === '\\N') {
            return null;
        }

        // Convert numbers
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? (float)$value : (int)$value;
        }

        return $value;
    }
}
