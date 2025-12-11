<?php

namespace KKP\Laravel\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;


class JSONCast implements CastsAttributes
{


    /**
     * Cast the given value.
     *
     * @param   Model   $model
     * @param   string  $key
     * @param   mixed   $value
     * @param   array   $attributes
     * @return  string
     */
    public function get( $model, $key, $value, $attribute ) : ?array
    {
        return ( is_array( $value ) ? $value : json_decode( (string) $value, true ) );
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
        return ( is_array( $value ) ? json_encode( $value ) : $value );
    }


}
