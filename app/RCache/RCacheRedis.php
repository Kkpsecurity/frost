<?php

namespace App\RCache;

use stdClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

use KKP\TextTk;


trait RCacheRedis
{


    protected static $_report_debugbar = false;
    protected static $_redis_exists    = 0;
    protected static $_redis_reads     = 0;
    protected static $_redis_writes    = 0;


    private static $redis_available = null;

    private static function isRedisAvailable() : bool
    {
        if (self::$redis_available === null) {
            try {
                // Check if Redis is configured and available
                if (!config('database.redis.rcache')) {
                    self::$redis_available = false;
                    return false;
                }

                $redis = \Illuminate\Support\Facades\Redis::connection('rcache');
                $redis->ping();
                self::$redis_available = true;
            } catch (\Exception $e) {
                Log::warning('Redis connection failed, falling back to database: ' . $e->getMessage());
                self::$redis_available = false;
            }
        }

        return self::$redis_available;
    }

    public static function Redis() : object|null
    {
        if (!self::isRedisAvailable()) {
            return null;
        }

        return \Illuminate\Support\Facades\Redis::connection( 'rcache' );
    }


    public static function RedisDebugBar() : void
    {

        if ( ! self::$_report_debugbar ) return;

        if ( class_exists( '\Debugbar' ) && \Debugbar::isEnabled() )
        {
            $meminfo = self::RedisMemory();
            \Debugbar::info( 'Redis Memory Total: ' . $meminfo->total_human  );
            \Debugbar::info( 'Redis Memory Data:  ' . $meminfo->data_human   );
            \Debugbar::info( 'Redis Laravel Keys: ' . $meminfo->laravel_keys );
            \Debugbar::info( 'Redis RCache Keys:  ' . $meminfo->rcache_keys  );

            \Debugbar::info( 'Redis Exists: ' . self::$_redis_exists );
            \Debugbar::info( 'Redis Reads:  ' . self::$_redis_reads  );
            \Debugbar::info( 'Redis Writes: ' . self::$_redis_writes );
        }

    }


    public static function RedisMemory() : stdClass
    {

        $default_db = Cache::store( 'redis' )->connection()->client()->getConnection()->getParameters()->database;
        $rcache_db  = self::Redis()->getConnection()->getParameters()->database;
        $redis_info = self::Redis()->Info();

        return (object) [
            'total'        => $redis_info['Memory']['used_memory'],
            'total_human'  => TextTk::BytesToString( $redis_info['Memory']['used_memory'] ),
            'data'         => $redis_info['Memory']['used_memory_dataset'],
            'data_human'   => TextTk::BytesToString( $redis_info['Memory']['used_memory_dataset'] ),
            'laravel_keys' => $redis_info['Keyspace']["db{$default_db}"]['keys'] ?? 0,
            'rcache_keys'  => $redis_info['Keyspace']["db{$rcache_db}"]['keys']  ?? 0,
        ];

    }


    ###########################
    ###                     ###
    ###   strings ( kvp )   ###
    ###                     ###
    ###########################


    public static function exists( string $key ) : bool
    {
        self::$_redis_exists++;
        \kkpdebug("EXISTS( '{$key}' )", 'RCacheRedis');
        $redis = self::Redis();
        return $redis ? $redis->exists( $key ) : false;
    }


    public static function get( string $key ) : ?string
    {
        self::$_redis_reads++;
        \kkpdebug("GET( '{$key}' )", 'RCacheRedis');
        return self::Redis()->get( $key );
    }


    public static function set( string $key, $val, int $expire_seconds = null ) : void
    {
        self::$_redis_writes++;
        if ( is_null( $expire_seconds ) )
        {
            \kkpdebug("SET( '{$key}', [value] )", 'RCacheRedis');
            self::Redis()->set( $key, $val );
        }
        else
        {
            \kkpdebug("SET( '{$key}', [value], 'EX', {$expire_seconds} )", 'RCacheRedis');
            self::Redis()->set( $key, $val, 'EX', $expire_seconds );
        }
    }


    public static function setexp( string $key, int $expire_seconds ) : void
    {
        self::$_redis_writes++;
        \kkpdebug("EXPIRE( '{$key}', {$expire_seconds} )", 'RCacheRedis');
        self::Redis()->expire( $key, $expire_seconds );
    }


    public static function delete( string $key ) : void
    {
        self::$_redis_writes++;
        \kkpdebug("DEL( '{$key}' )", 'RCacheRedis');
        self::Redis()->del( $key );
    }



    ##################
    ###            ###
    ###   hashes   ###
    ###            ###
    ##################


    public static function hexists( string $hkey, $id ) : bool
    {
        self::$_redis_exists++;
        \kkpdebug("HEXISTS( '{$hkey}', '{$id}' )", 'RCacheRedis');
        $redis = self::Redis();
        return $redis ? $redis->hexists( $hkey, $id ) : false;
    }


    public static function hget( string $hkey, $id ) : ?string
    {
        self::$_redis_reads++;
        \kkpdebug("HGET( '{$hkey}', '{$id}' )", 'RCacheRedis');
        $redis = self::Redis();
        return $redis ? $redis->hget( $hkey, $id ) : null;
    }


    public static function hgetall( string $hkey ) : ?array
    {
        self::$_redis_reads++;
        \kkpdebug("HGETALL( '{$hkey}' )", 'RCacheRedis');
        $redis = self::Redis();
        return $redis ? $redis->hgetall( $hkey ) : [];
    }


    public static function hset( string $hkey, $id, $val ) : void
    {
        self::$_redis_writes++;
        \kkpdebug("HSET( '{$hkey}', '{$id}', [value] )", 'RCacheRedis');
        $redis = self::Redis();
        if ($redis) {
            $redis->hset( $hkey, $id, $val );
        }
    }


    public static function hdel( string $hkey, $id ) : void
    {
        self::$_redis_writes++;
        \kkpdebug("HDEL( '{$hkey}', '{$id}' )", 'RCacheRedis');
        self::Redis()->hdel( $hkey, $id );
    }


}
