<?php

namespace KKP\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;


class ShortTimeCast implements CastsAttributes
{


    /**
     * Cast the given value.
     *
     * @param   Model    $model
     * @param   string   $key
     * @param   mixed    $value
     * @param   array    $attributes
     * @return  string
     */
    public function get( $model, $key, $value, $attribute ) : string
    {
        //
        // '08:00:00' => '08:00'
        //
        return substr( $value, 0, 5 );
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
    public function set( $model, $key, $value, $attributes ) : string
    {

        //
        // already in correct format
        //

        if ( strlen( $value ) == 8 )
        {
            return $value;
        }

        //
        // convert any time to correct format
        //

        $parts = explode( ':', $value );

        return sprintf( '%02d', $parts[0] )
                    . ':'
                    . ( isset($parts[1]) ? sprintf( '%02d', $parts[1] ) : '00' )
                    . ':'
                    . ( isset($parts[2]) ? sprintf( '%02d', $parts[2] ) : '00' );

    }


}
