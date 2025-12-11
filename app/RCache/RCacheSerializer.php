<?php

namespace App\RCache;

use Illuminate\Database\Eloquent\Model;


trait RCacheSerializer
{

    private static $_use_igbinary = false;


    // add to RCache::boot()
    private static function _SetSerializer() : void
    {
        self::$_use_igbinary = function_exists( 'igbinary_serialize' );
    }


    //
    //
    //


    public static function Serializer() : string
    {
        return self::$_use_igbinary ? 'igbinary_serialize' : 'serialize';
    }


    public static function Unserializer() : string
    {
        return self::$_use_igbinary ? 'igbinary_unserialize' : 'unserialize';
    }


    public static function Serialize( iterable|object $data ) : string
    {

        if ( $data instanceof Model )
        {
            $data = $data->getAttributes();
        }

        return self::$_use_igbinary ? igbinary_serialize( $data ) : serialize( $data );

    }


    public static function Unserialize( string $serialized ) : mixed
    {
        return self::$_use_igbinary ? igbinary_unserialize( $serialized ) : unserialize( $serialized );
    }


}
