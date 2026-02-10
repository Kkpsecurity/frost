<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Range;
use App\Helpers\GeocodingHelper;

/**
 * Geocode all ranges that don't have coordinates
 *
 * This command will process all active ranges and geocode their addresses
 * to populate latitude and longitude fields for Google Maps integration.
 */
class GeocodeRanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranges:geocode
                            {--force : Force geocoding even if coordinates exist}
                            {--id= : Geocode only a specific range by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode range addresses to latitude/longitude coordinates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting range geocoding process...');
        $this->newLine();

        // Check if Google Maps API key is configured
        if (empty(config('services.google_maps.api_key'))) {
            $this->error('Google Maps API key is not configured!');
            $this->line('Please set GOOGLE_MAPS_API_KEY in your .env file');
            return 1;
        }

        // Get ranges to geocode
        $query = Range::query();

        // If specific ID provided
        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        // If not forcing, only get ranges without coordinates
        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('latitude')
                    ->orWhereNull('longitude');
            });
        }

        $ranges = $query->get();

        if ($ranges->isEmpty()) {
            $this->info('No ranges found to geocode.');
            return 0;
        }

        $this->info("Found {$ranges->count()} range(s) to geocode");
        $this->newLine();

        $successCount = 0;
        $failureCount = 0;

        // Process each range
        foreach ($ranges as $range) {
            $this->line("Processing: {$range->name} ({$range->city})");
            $this->line("  Address: {$range->full_address}");

            // Geocode the address
            if ($range->geocodeAddress()) {
                // Save the coordinates
                $range->save();

                $this->info("  ✓ Success: {$range->latitude}, {$range->longitude}");
                $successCount++;
            } else {
                $this->error("  ✗ Failed to geocode");
                $failureCount++;
            }

            $this->newLine();

            // Small delay to respect API rate limits
            if ($ranges->count() > 1) {
                usleep(200000); // 200ms delay between requests
            }
        }

        // Summary
        $this->newLine();
        $this->info('=== Geocoding Summary ===');
        $this->line("Total processed: {$ranges->count()}");
        $this->info("Successful: {$successCount}");
        if ($failureCount > 0) {
            $this->error("Failed: {$failureCount}");
        }

        return $failureCount > 0 ? 1 : 0;
    }
}
