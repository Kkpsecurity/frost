<?php

namespace App\RCache;

use stdClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

use KKP\TextTk;


trait RCacheRedis
{


    protected static $_report_debugbar = false;
    protected static $_redis_exists    = 0;
    protected static $_redis_reads     = 0;
    protected static $_redis_writes    = 0;


    public static function Redis() : object
    {
        return Redis::Connection( 'rcache' );
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
        kkpdebug( 'RCacheRedis', "EXISTS( '{$key}' )" );
        return self::Redis()->exists( $key );
    }


    public static function get( string $key ) : ?string
    {
        self::$_redis_reads++;
        kkpdebug( 'RCacheRedis', "GET( '{$key}' )" );
        return self::Redis()->get( $key );
    }


    public static function set( string $key, $val, int $expire_seconds = null ) : void
    {
        self::$_redis_writes++;
        if ( is_null( $expire_seconds ) )
        {
            kkpdebug( 'RCacheRedis', "SET( '{$key}', [value] )" );
            self::Redis()->set( $key, $val );
        }
        else
        {
            kkpdebug( 'RCacheRedis', "SET( '{$key}', [value], 'EX', {$expire_seconds} )" );
            self::Redis()->set( $key, $val, 'EX', $expire_seconds );
        }
    }


    public static function setexp( string $key, int $expire_seconds ) : void
    {
        self::$_redis_writes++;
        kkpdebug( 'RCacheRedis', "EXPIRE( '{$key}', {$expire_seconds} )" );
        self::Redis()->expire( $key, $expire_seconds );
    }


    public static function delete( string $key ) : void
    {
        self::$_redis_writes++;
        kkpdebug( 'RCacheRedis', "DEL( '{$key}' )" );
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
        kkpdebug( 'RCacheRedis', "HEXISTS( '{$hkey}', '{$id}' )" );
        return self::Redis()->hexists( $hkey, $id );
    }


    public static function hget( string $hkey, $id ) : ?string
    {
        self::$_redis_reads++;
        kkpdebug( 'RCacheRedis', "HGET( '{$hkey}', '{$id}' )" );
        return self::Redis()->hget( $hkey, $id );
    }


    public static function hgetall( string $hkey ) : ?array
    {
        self::$_redis_reads++;
        kkpdebug( 'RCacheRedis', "HGETALL( '{$hkey}' )" );
        return self::Redis()->hgetall( $hkey );
    }


    public static function hset( string $hkey, $id, $val ) : void
    {
        self::$_redis_writes++;
        kkpdebug( 'RCacheRedis', "HSET( '{$hkey}', '{$id}', [value] )" );
        self::Redis()->hset( $hkey, $id, $val );
    }


    public static function hdel( string $hkey, $id ) : void
    {
        self::$_redis_writes++;
        kkpdebug( 'RCacheRedis', "HDEL( '{$hkey}', '{$id}' )" );
        self::Redis()->hdel( $hkey, $id );
    }


}
