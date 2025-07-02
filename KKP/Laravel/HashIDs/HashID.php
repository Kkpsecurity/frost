<?php

namespace KKP\Laravel\HashIDs;

use Exception;
use Jenssegers\Optimus\Optimus;


class HashID
{


    private static $_Encoder;


    public static function Encoder() : object
    {

        if ( ! self::$_Encoder )
        {
            self::$_Encoder = new Optimus(
                config( 'optimus.prime'     ),
                config( 'optimus.inverse'   ),
                config( 'optimus.random'    ),
                config( 'optimus.bitlength' )
            );
        }

        return self::$_Encoder;

    }


	public static function encode( int $id ) : string
	{
        return self::Encoder()->encode( $id );
	}


	public static function decode( string $hash_id ) : string
	{
        return self::Encoder()->decode( $hash_id );
	}


    public static function Validate_Encode( string $value, string $model_field ) : ?string
    {

        if ( ! filter_var( $value, FILTER_VALIDATE_INT ) )
        {
            throw new Exception( get_called_class() . " '{$model_field}' value '{$value}' is not an integer" );
            return null;
        }

        return self::encode( $value );

    }


}
