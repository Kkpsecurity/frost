<?php
declare(strict_types=1);

namespace App\Classes;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

use RCache;


class VideoCallRequest
{

    const EXPIRE_SECONDS = 43200; // 12H


    public static function Redis() : object
    {
        return Redis::Connection( 'cache' );
    }


    public static function RedisKey( int|string $course_date_id ) : string
    {
        return "vcr:{$course_date_id}";
    }


    public static function Create( int|string $course_date_id, int|string $user_id ) : void
    {

        kkpdebug( 'VideoCallRequest', "Create() CourseDateID: {$course_date_id} UserID: {$user_id}" );

        $User   = RCache::User( (int) $user_id );
        $record = RCache::Serialize([
            'created_at'    => time(),
            'user_id'       => $User->id,
            'fname'         => $User->fname,
            'lname'         => $User->lname,
            'email'         => $User->email,
        ]);

    	self::Redis()->hsetnx( self::RedisKey( $course_date_id ), $user_id, $record );
    	self::Redis()->expire( self::RedisKey( $course_date_id ), self::EXPIRE_SECONDS );

    }


    public static function Delete( int|string $course_date_id, int|string $user_id ) : void
    {

        kkpdebug( 'VideoCallRequest', "Delete() CourseDateID: {$course_date_id} UserID {$user_id}" );

        self::Redis()->hdel( self::RedisKey( $course_date_id ), $user_id );

    }


    public static function Queue( int|string $course_date_id = null ) : Collection
    {

        kkpdebug( 'VideoCallRequest', 'Queue() CourseDateID: ' . ( $course_date_id ?? 'null' ) );

        if ( ! $course_date_id )
        {
            return collect([]);
        }

        return collect( self::Redis()->hvals( self::RedisKey( $course_date_id ) ) )
                 ->map( function( $record ) { return RCache::Unserialize( $record ); })
              ->sortBy( 'created_at' );

    }


    //
    //
    //


    public static function RedisInstCallKey( int|string $course_date_id ) : string
    {
        return "vcr_call_inst:{$course_date_id}";
    }

    public static function RedisStudentCallKey( int|string $course_date_id ) : string
    {
        return "vcr_call_student:{$course_date_id}";
    }


    public static function CallCancel( int|string $course_date_id, int|string $user_id = null ) : void
    {

        if ( $user_id )
        {

            //
            // student is cancelling
            //

            kkpdebug( 'VideoCallRequest', "CallCancel() CourseDateID: {$course_date_id} -- by UserID: {$user_id}" );

            if ( (string) $user_id === (string) self::Redis()->get( self::RedisInstCallKey( $course_date_id ) ) )
            {
                kkpdebug( 'VideoCallRequest', '  CallCancel() cancelling Inst Accept' );
                self::Redis()->del( self::RedisInstCallKey( $course_date_id ) );
            }

            if ( (string) $user_id === (string) self::Redis()->get( self::RedisStudentCallKey( $course_date_id ) ) )
            {
                kkpdebug( 'VideoCallRequest', '  CallCancel() cancelling Student Accept' );
                self::Redis()->del( self::RedisStudentCallKey( $course_date_id ) );
            }

        }
        else
        {

            //
            // instructor is cancelling
            //

            kkpdebug( 'VideoCallRequest', "CallCancel() CourseDateID: {$course_date_id} -- by Instructor" );
            self::Redis()->del( self::RedisInstCallKey( $course_date_id ) );
            self::Redis()->del( self::RedisStudentCallKey( $course_date_id ) );

        }

    }


    public static function CallDeleteAll( int|string $course_date_id, int|string $user_id ) : void
    {

        self::CallCancel( $course_date_id, $user_id );

        self::Delete( $course_date_id, $user_id );

        #self::Redis()->hdel( self::RedisKey( $course_date_id ), $user_id );

    }


    //
    // instructor
    //


    public static function InstCallSetReady( int|string $course_date_id, int|string $user_id ) : void
    {

        self::Redis()->set( self::RedisInstCallKey( $course_date_id ), $user_id, 'EX', self::EXPIRE_SECONDS );

    }


    public static function InstCallGetReady( int|string $course_date_id ) : ?int
    {

        if ( $user_id = self::Redis()->get( self::RedisInstCallKey( $course_date_id ) ) )
        {
            return (int) $user_id;
        }

        return null;

    }


    /*
    public static function InstCallReset( int|string $course_date_id ) : void
    {

        kkpdebug( 'VideoCallRequest', "InstCallReset() CourseDateID {$course_date_id}" );

        self::Redis()->del( self::RedisInstCallKey( $course_date_id ) );

    }
    */


    public static function StudentCallIsReady( int|string $course_date_id ) : ?int
    {

        #return self::Redis()->exists( self::RedisStudentCallKey( $course_date_id ) );

        if ( $user_id = self::Redis()->get( self::RedisStudentCallKey( $course_date_id ) ) )
        {
            return (int) $user_id;
        }

        return null;

    }


    //
    // student
    //


    public static function InstCallIsReady( int|string $course_date_id, int|string $user_id ) : bool
    {

        return (string) $user_id === (string) self::Redis()->get( self::RedisInstCallKey( $course_date_id ) );

    }


    public static function StudentAcceptCall( int|string $course_date_id, int|string $user_id ) : void
    {

        kkpdebug( 'VideoCallRequest', "StudentAcceptCall() CourseDateID: {$course_date_id} UserID: {$user_id}" );

        self::Redis()->set( self::RedisStudentCallKey( $course_date_id ), $user_id, 'EX', self::EXPIRE_SECONDS );

    }


    /*
    public static function StudentAcceptReset( int|string $course_date_id, int|string $user_id ) : void
    {

        kkpdebug( 'VideoCallRequest', "StudentAcceptReset() CourseDateID {$course_date_id} UserID {$user_id}" );

        if ( (string) $user_id === (string) self::Redis()->get( self::RedisInstCallKey( $course_date_id ) ) )
        {
            self::Redis()->del( self::RedisStudentCallKey( $course_date_id ) );
        }

    }
    */


    //
    // devel tool
    //


    public static function DevData( int|string $course_date_id )
    {

        return (object) [

            'inst_redis_key'    => self::RedisInstCallKey( $course_date_id ),
            'inst_user_id'      => self::Redis()->get( self::RedisInstCallKey( $course_date_id ) )
                                ?: '-',

            'student_redis_key' => self::RedisStudentCallKey( $course_date_id ),
            'student_user_id'   => self::Redis()->get( self::RedisStudentCallKey( $course_date_id ) )
                                ?: '-',

        ];

    }


}
