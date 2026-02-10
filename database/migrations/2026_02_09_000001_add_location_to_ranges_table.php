<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add latitude and longitude columns to the ranges table to support
     * Google Maps integration for displaying range locations.
     */
    public function up(): void
    {
        Schema::table('ranges', function (Blueprint $table) {
            // Add location coordinates for Google Maps integration
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Add index for location-based queries
            $table->index(['latitude', 'longitude'], 'ranges_location_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranges', function (Blueprint $table) {
            $table->dropIndex('ranges_location_index');
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
