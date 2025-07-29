<?php

declare(strict_types=1);

namespace App\Classes\Frost;

/**
 * @file ChatLogCache.php
 * @brief Cache class for chat logs.
 * @details This class handles caching of chat logs using Redis, including saving, deleting, and querying logs.
 */

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

use App\Services\RCache;

use App\Models\ChatLog;


class ChatLogCache
{

    const EXPIRE_SECONDS = 86400; // 24H


    public static function Redis(): object
    {
        return Redis::Connection('cache');
    }

    public static function RedisKey(int|ChatLog $ChatLog): string
    {
        return is_int($ChatLog) ? "chat_log:{$ChatLog}" : "chat_log:{$ChatLog->course_date_id}";
    }


    //
    // observer
    //


    public static function observer_saved(ChatLog $ChatLog): void
    {
        self::Redis()->hset(self::RedisKey($ChatLog), $ChatLog->id, RCache::Serialize($ChatLog->toArray()));
        self::Redis()->expire(self::RedisKey($ChatLog), self::EXPIRE_SECONDS);
    }


    public static function observer_deleted(ChatLog $ChatLog): void
    {
        self::Redis()->hdel(self::RedisKey($ChatLog), $ChatLog->id);
    }


    //
    // query
    //


    public static function Query(int $course_date_id): Collection
    {

        //
        // get last N keys
        //

        $keys = self::Redis()->hkeys(self::RedisKey($course_date_id));
        usort($keys, function ($a, $b) {
            return intval($a) < intval($b) ? 1 : -1;
        }); // reverse natsort
        $keys = array_splice($keys, 0, RCache::SiteConfig('chat_log_last'));

        //
        // hydrate to Collection
        //

        return ChatLog::hydrate(array_map(RCache::Unserializer(), self::Redis()->hmget(self::RedisKey($course_date_id), $keys)))
            ->filter(function ($ChatLog) {
                return $ChatLog->hidden_at == null;
            });
    }


    //
    // enable / disable
    //


    public static function RedisEnabledKey(int $course_date_id): string
    {
        return "chat_log_enabled:{$course_date_id}";
    }


    public static function Enable(int $course_date_id): void
    {
        self::Redis()->set(self::RedisEnabledKey($course_date_id), '1', 'EX', self::EXPIRE_SECONDS);
    }


    public static function Disable(int $course_date_id): void
    {
        self::Redis()->del(self::RedisEnabledKey($course_date_id));
    }


    public static function IsEnabled(int $course_date_id): bool
    {
        return (bool) self::Redis()->exists(self::RedisEnabledKey($course_date_id));
    }


    //
    //
    //


    public static function Devel_Purge(int $course_date_id): void
    {
        self::Redis()->del(self::RedisKey($course_date_id));
    }
}
