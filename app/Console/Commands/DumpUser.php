<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

use App\Services\RCache;
use App\Models\User;



class DumpUser extends Command
{

    protected $signature   = 'command:dump_user {user_id}';
    protected $description = 'Dump User';


    public function handle(): int
    {

        $user_id = (int) $this->argument('user_id');

        $User = User::findOrFail($user_id);
        $this->info('Database Record');
        $this->line(print_r($User->toArray(), true));

        $User = RCache::User($user_id);
        $this->info('RCache Record');
        $this->line(print_r($User->toArray(), true));

        return 0;
    }
}
