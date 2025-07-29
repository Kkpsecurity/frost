<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use KKP\Laravel\HashIDs\HashID;

class TestHashID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:hashid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the HashID functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing HashID class...');

        try {
            $testId = 123;
            $encoded = HashID::encode($testId);
            $this->info("Encoded {$testId}: {$encoded}");

            $decoded = HashID::decode($encoded);
            $this->info("Decoded back: {$decoded}");

            if ($decoded == $testId) {
                $this->info('✅ Test successful! HashID is working correctly.');
            } else {
                $this->error('❌ Test failed! Decoded value does not match original.');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
