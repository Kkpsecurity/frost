<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds for auth settings.
     */
    public function run(): void
    {
        $authSettings = [
            // Login Page Settings
            ['key' => 'login_page_title', 'value' => 'Sign In to Your Account', 'group' => 'auth'],
            ['key' => 'login_page_subtitle', 'value' => 'Welcome back! Please enter your credentials.', 'group' => 'auth'],
            ['key' => 'login_remember_me_enabled', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'login_forgot_password_enabled', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'login_registration_enabled', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'login_show_company_logo', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'login_company_logo_url', 'value' => '', 'group' => 'auth'],

            // Password Policy Settings
            ['key' => 'password_min_length', 'value' => '8', 'group' => 'auth'],
            ['key' => 'password_require_uppercase', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'password_require_lowercase', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'password_require_numbers', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'password_require_symbols', 'value' => 'false', 'group' => 'auth'],
            ['key' => 'password_exclude_common', 'value' => 'true', 'group' => 'auth'],

            // Session Settings
            ['key' => 'session_lifetime_minutes', 'value' => '120', 'group' => 'auth'],
            ['key' => 'session_timeout_warning_minutes', 'value' => '5', 'group' => 'auth'],
            ['key' => 'session_extend_on_activity', 'value' => 'true', 'group' => 'auth'],

            // Password Reset Settings
            ['key' => 'password_reset_expire_minutes', 'value' => '60', 'group' => 'auth'],
            ['key' => 'password_reset_throttle_seconds', 'value' => '60', 'group' => 'auth'],
            ['key' => 'password_reset_max_attempts', 'value' => '3', 'group' => 'auth'],

            // Login Security Settings
            ['key' => 'login_max_attempts', 'value' => '5', 'group' => 'auth'],
            ['key' => 'login_lockout_duration_minutes', 'value' => '15', 'group' => 'auth'],
            ['key' => 'login_track_ip_changes', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'login_require_email_verification', 'value' => 'false', 'group' => 'auth'],

            // Two-Factor Authentication
            ['key' => 'two_factor_enabled', 'value' => 'false', 'group' => 'auth'],
            ['key' => 'two_factor_mandatory', 'value' => 'false', 'group' => 'auth'],
            ['key' => 'two_factor_backup_codes', 'value' => 'true', 'group' => 'auth'],

            // Registration Settings
            ['key' => 'registration_enabled', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'registration_require_terms', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'registration_require_email_verification', 'value' => 'true', 'group' => 'auth'],
            ['key' => 'registration_auto_approve', 'value' => 'true', 'group' => 'auth'],

            // Admin Auth Settings
            ['key' => 'admin_login_page_title', 'value' => 'Admin Portal Access', 'group' => 'auth'],
            ['key' => 'admin_login_page_subtitle', 'value' => 'Administrative Access Only', 'group' => 'auth'],
            ['key' => 'admin_session_lifetime_minutes', 'value' => '240', 'group' => 'auth'],
            ['key' => 'admin_require_two_factor', 'value' => 'false', 'group' => 'auth'],
            ['key' => 'admin_login_max_attempts', 'value' => '3', 'group' => 'auth'],
            ['key' => 'admin_login_lockout_duration_minutes', 'value' => '30', 'group' => 'auth'],
        ];

        foreach ($authSettings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('Auth settings seeded successfully!');
    }
}
