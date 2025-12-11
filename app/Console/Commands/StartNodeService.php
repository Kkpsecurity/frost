<?php

namespace App\Console\Commands;

/**
 * @file StartNodeService.php
 * @brief Command to start the Node.js service.
 * @details This command starts the Node.js service using pm2, with an option to run npm install first.
 */

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartNodeService extends Command
{
    protected $signature = 'node:start {--i : Run npm install before starting the service}';
    protected $description = 'Start the Node.js service';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Check if the install flag (-i) was passed
        if ($this->option('i')) {
            $this->info('Running npm install...');

            $installProcess = new Process(['npm', 'install'], base_path('services'));
            $installProcess->run();

            if (!$installProcess->isSuccessful()) {
                $this->error('Failed to run npm install');
                return 1;
            }

            $this->info('npm install completed successfully');
        }

        // Start the Node.js service using pm2
        $process = new Process(['pm2', 'start', base_path('services/server.js'), '--name', 'zoom-node-server']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('Failed to start the Node.js service');
            return 1;
        }

        $this->info('Node.js service started successfully');
        return 0;
    }
}
