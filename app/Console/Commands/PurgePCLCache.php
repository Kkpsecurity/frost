<?php

declare(strict_types=1);

namespace App\Console\Commands;

/**
 * @file PurgePCLCache.php
 * @brief Command to purge Previous Completed Lessons cache.
 * @details This command removes all keys related to previous completed lessons from the Redis cache.
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


class PurgePCLCache extends Command
{

    protected $signature   = 'command:purge_pcl_cache';
    protected $description = 'Purge PCL Cache';


    public function handle(): int
    {

        $RedisConn = Redis::connection();

        $count = 0;

        foreach ($RedisConn->keys('previous_completed_lessons:*') as $key) {
            $RedisConn->del($key);
            $count++;
        }

        print "{$count} keys removed.\n";

        return 0;
    }
}
