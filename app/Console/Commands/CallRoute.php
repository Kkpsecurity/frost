<?php

namespace App\Console\Commands;

/**
 * @file CallRoute.php
 * @brief Command to call a route from the CLI.
 * @details This command allows you to call a specific route by providing its URI as an argument.
 */

use Illuminate\Http\Request;
use Illuminate\Console\Command;


class CallRoute extends Command
{

    protected $signature   = 'command:callroute {uri}';
    protected $description = 'Call route from CLI';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $Request = Request::create($this->argument('uri'), 'GET');
        $this->info(app()['Illuminate\Contracts\Http\Kernel']->handle($Request));
    }
}
