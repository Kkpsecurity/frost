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
        // First, let's create the proper site_configs structure to match the dump
        Schema::dropIfExists('site_configs');
        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->string('cast_to', 50);
            $table->string('config_name', 255);
            $table->text('config_value');
            $table->timestamps();
        });

        // Insert the data from your dump
        $siteConfigData = [
            ['id' => 1, 'cast_to' => 'text', 'config_name' => 'site_company_name', 'config_value' => '%%SER%%s:32:"Florida Online Security Training";'],
            ['id' => 2, 'cast_to' => 'longtext', 'config_name' => 'site_company_address', 'config_value' => '%%SER%%s:52:"3200 S Congress Ave Ste 203,\nBoynton Beach, FL 33426";'],
            ['id' => 3, 'cast_to' => 'text', 'config_name' => 'site_contact_email', 'config_value' => '%%SER%%s:41:"support@floridaonlinesecuritytraining.com";'],
            ['id' => 4, 'cast_to' => 'text', 'config_name' => 'site_support_email', 'config_value' => '%%SER%%s:41:"support@floridaonlinesecuritytraining.com";'],
            ['id' => 5, 'cast_to' => 'text', 'config_name' => 'site_support_phone', 'config_value' => '%%SER%%s:12:"866-540-0817";'],
            ['id' => 6, 'cast_to' => 'text', 'config_name' => 'site_support_phone_hours', 'config_value' => '%%SER%%s:11:"Call Center";'],
            ['id' => 7, 'cast_to' => 'longtext', 'config_name' => 'site_google_map_url', 'config_value' => '%%SER%%s:326:"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d10395.906563817327!2d-80.09215252055921!3d26.492890375324926!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88d8df82fc39ac8f%3A0x3629a6db4e846043!2s3200%20S%20Congress%20Ave%20%23203%2C%20Boynton%20Beach%2C%20FL%2033426!5e0!3m2!1sen!2sus!4v1654109394052!5m2!1sen!2sus";'],
            ['id' => 8, 'cast_to' => 'int', 'config_name' => 'classtimes_starts_soon_seconds', 'config_value' => '%%SER%%i:14400;'],
            ['id' => 9, 'cast_to' => 'int', 'config_name' => 'classtimes_is_ended_seconds', 'config_value' => '%%SER%%i:600;'],
            ['id' => 10, 'cast_to' => 'int', 'config_name' => 'classtimes_zoom_duration_minutes', 'config_value' => '%%SER%%i:15;'],
            ['id' => 11, 'cast_to' => 'int', 'config_name' => 'student_lesson_complete_seconds', 'config_value' => '%%SER%%i:120;'],
            ['id' => 12, 'cast_to' => 'int', 'config_name' => 'student_poll_seconds', 'config_value' => '%%SER%%i:30;'],
            ['id' => 13, 'cast_to' => 'int', 'config_name' => 'chat_log_last', 'config_value' => '%%SER%%i:50;'],
            ['id' => 14, 'cast_to' => 'int', 'config_name' => 'instructor_close_lesson_minutes', 'config_value' => '%%SER%%i:30;'],
            ['id' => 15, 'cast_to' => 'int', 'config_name' => 'instructor_pre_start_minutes', 'config_value' => '%%SER%%i:120;'],
            ['id' => 16, 'cast_to' => 'int', 'config_name' => 'instructor_post_end_minutes', 'config_value' => '%%SER%%i:30;'],
            ['id' => 17, 'cast_to' => 'int', 'config_name' => 'student_join_lesson_seconds', 'config_value' => '%%SER%%i:300;'],
            ['id' => 18, 'cast_to' => 'int', 'config_name' => 'instructor_next_lesson_seconds', 'config_value' => '%%SER%%i:300;'],
            ['id' => 19, 'cast_to' => 'int', 'config_name' => 'instructor_close_unit_seconds', 'config_value' => '%%SER%%i:300;'],
        ];

        DB::table('site_configs')->insert($siteConfigData);

        // Now migrate data from site_configs to settings table
        $this->migrateSiteConfigsToSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the migrated settings from the settings table
        $configNames = [
            'site.company_name',
            'site.company_address',
            'site.contact_email',
            'site.support_email',
            'site.support_phone',
            'site.support_phone_hours',
            'site.google_map_url',
            'class.starts_soon_seconds',
            'class.is_ended_seconds',
            'class.zoom_duration_minutes',
            'student.lesson_complete_seconds',
            'student.poll_seconds',
            'student.join_lesson_seconds',
            'chat.log_last',
            'instructor.close_lesson_minutes',
            'instructor.pre_start_minutes',
            'instructor.post_end_minutes',
            'instructor.next_lesson_seconds',
            'instructor.close_unit_seconds',
        ];

        DB::table('settings')->whereIn('key', $configNames)->delete();

        // Restore original site_configs structure
        Schema::dropIfExists('site_configs');
        Schema::create('site_configs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Migrate data from site_configs to settings table
     */
    private function migrateSiteConfigsToSettings(): void
    {
        $siteConfigs = DB::table('site_configs')->get();

        foreach ($siteConfigs as $config) {
            // Parse the serialized value
            $value = $this->parseSerializedValue($config->config_value);

            // Convert old config names to new dot notation
            $newKey = $this->convertConfigNameToSettingKey($config->config_name);

            // Insert into settings table
            DB::table('settings')->insert([
                'key' => $newKey,
                'value' => $value,
            ]);
        }
    }

    /**
     * Parse serialized values from the old format
     */
    private function parseSerializedValue(string $serializedValue): string
    {
        // Remove the %%SER%% prefix
        $cleaned = str_replace('%%SER%%', '', $serializedValue);

        // Parse different types
        if (preg_match('/^s:(\d+):"(.*)";$/', $cleaned, $matches)) {
            // String: s:32:"Florida Online Security Training";
            return $matches[2];
        } elseif (preg_match('/^i:(\d+);$/', $cleaned, $matches)) {
            // Integer: i:14400;
            return $matches[1];
        }

        // Fallback: return as-is
        return $cleaned;
    }

    /**
     * Convert old config names to new dot notation keys
     */
    private function convertConfigNameToSettingKey(string $configName): string
    {
        $mapping = [
            'site_company_name' => 'site.company_name',
            'site_company_address' => 'site.company_address',
            'site_contact_email' => 'site.contact_email',
            'site_support_email' => 'site.support_email',
            'site_support_phone' => 'site.support_phone',
            'site_support_phone_hours' => 'site.support_phone_hours',
            'site_google_map_url' => 'site.google_map_url',
            'classtimes_starts_soon_seconds' => 'class.starts_soon_seconds',
            'classtimes_is_ended_seconds' => 'class.is_ended_seconds',
            'classtimes_zoom_duration_minutes' => 'class.zoom_duration_minutes',
            'student_lesson_complete_seconds' => 'student.lesson_complete_seconds',
            'student_poll_seconds' => 'student.poll_seconds',
            'student_join_lesson_seconds' => 'student.join_lesson_seconds',
            'chat_log_last' => 'chat.log_last',
            'instructor_close_lesson_minutes' => 'instructor.close_lesson_minutes',
            'instructor_pre_start_minutes' => 'instructor.pre_start_minutes',
            'instructor_post_end_minutes' => 'instructor.post_end_minutes',
            'instructor_next_lesson_seconds' => 'instructor.next_lesson_seconds',
            'instructor_close_unit_seconds' => 'instructor.close_unit_seconds',
        ];

        return $mapping[$configName] ?? $configName;
    }
};
