<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add the group column to settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->after('id')->nullable();
        });

        // Step 2: Backfill the group column from existing key values
        DB::table('settings')->get()->each(function ($setting) {
            if (str_contains($setting->key, '.')) {
                [$group, $newKey] = explode('.', $setting->key, 2);
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update([
                        'group' => $group,
                        'key' => $newKey
                    ]);
            } else {
                // For keys without dots, set group to 'general'
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['group' => 'general']);
            }
        });

        // Step 3: Make group column not nullable and add index
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->nullable(false)->change();
            $table->index(['group', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Reconstruct the original dot-notation keys
        DB::table('settings')->get()->each(function ($setting) {
            if ($setting->group !== 'general') {
                $originalKey = $setting->group . '.' . $setting->key;
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['key' => $originalKey]);
            }
        });

        // Step 2: Remove the group column and index
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group', 'key']);
            $table->dropColumn('group');
        });
    }
};
