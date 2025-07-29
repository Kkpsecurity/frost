<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file TestCmd.php
 * @brief Command for testing purposes.
 * @details This command is used to perform various test operations.
 */

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

use App\Servcies\RCache;



class TestCmd extends Command
{

    protected $signature   = 'command:test_cmd';
    protected $description = 'TestCmd';


    public function handle(): int
    {


        //
        // DNR
        //
        $this->info('TestCmd completed successfully');
        return 0;
    }
}
