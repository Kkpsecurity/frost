<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;


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
        $Request = Request::create( $this->argument( 'uri' ), 'GET' );
        $this->info( app()['Illuminate\Contracts\Http\Kernel']->handle( $Request ) );
    }

}
