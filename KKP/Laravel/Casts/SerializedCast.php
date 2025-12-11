<?php

namespace KKP\Laravel\Casts;

use Str;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;


class SerializedCast implements CastsAttributes
{

    protected const SERIALIZED_PREFIX = '%%SER%%';

    protected static function isSerialized( &$value ) : bool
    {

        if ( ! is_string( $value ) ) return false;

        return self::SERIALIZED_PREFIX == substr( $value, 0, strlen( self::SERIALIZED_PREFIX ) );

    }


    /**
     * Cast the given value.
     *
     * @param   Model   $model
     * @param   string  $key
     * @param   mixed   $value
     * @param   array   $attributes
     * @return  string
     */
    public function get( $model, $key, $value, $attribute ) : mixed
    {

        if ( is_string( $value ) && self::isSerialized( $value ) )
        {
            return unserialize( substr( $value, strlen( self::SERIALIZED_PREFIX ) ) );
        }

        return $value;

    }


    /**
     * Prepare the given value for storage.
     *
     * @param   Model   $model
     * @param   string  $key
     * @param   array   $value
     * @param   array   $attributes
     * @return  string
     */
    public function set( $model, $key, $value, $attributes ) : ?string
    {

        if ( ! self::isSerialized( $value ) )
        {
            logger( 'NOT SERIALIZED' );
            return self::SERIALIZED_PREFIX . serialize( $value );
        }

        logger( 'ALREADY SERIALIZED' );
        return $value;

    }


}
