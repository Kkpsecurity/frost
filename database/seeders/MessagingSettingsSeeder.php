<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Support\Settings;

class MessagingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed default messaging settings
        Settings::put('messaging', [
            'allow_new_threads_roles' => ['admin', 'instructor', 'support'],
            'notify_on' => ['new_thread', 'new_message'],
            'quiet_hours' => ['start' => '22:00', 'end' => '07:00'],
            'webpush_enabled' => true,
            'realtime_enabled' => true,
            'badge_poll_ms' => 15000,
        ]);

        $this->command->info('Messaging settings seeded successfully');
    }
}
