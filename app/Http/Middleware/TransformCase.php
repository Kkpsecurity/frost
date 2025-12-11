<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest as Middleware;

class TransformCase extends Middleware
{

    /**
     * The names of the attributes that should be converted to lowercase.
     *
     * @var array
     */
    protected $to_lowercase = [

        'email',
        'inst_email', // Range

    ];


    /**
     * The names of the attributes that should be converted to uppercase.
     *
     * @var array
     */
    protected $to_uppercase = [

        //

    ];


    /**
     * Transform the given value.
     *
     * @param   string  $key
     * @param   mixed   $value
     * @return  mixed
     */
    protected function transform( $key, $value )
    {

        //
        // don't log warning
        //
        if ( ! is_string( $value ) )
        {
            return $value;
        }

        if ( in_array( $key, $this->to_lowercase, true ) )
        {
            return strtolower( $value );
        }

        if ( in_array( $key, $this->to_uppercase, true ) )
        {
            return strtoupper( $value );
        }

        return $value;

    }


}
