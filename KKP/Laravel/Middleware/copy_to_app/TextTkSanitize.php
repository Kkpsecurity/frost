<?php
/**
 *
 * This replaces TrimStrings
 *

App\Http\Kernel.php
-------------------

protected $middleware = [

    #\App\Http\Middleware\TrimStrings::class,
    #\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\TextTkSanitize::class,

];

 *
 *
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

use KKP\TextTk;


class TextTkSanitize extends TransformsRequest
{

    /**
     * The names of the attributes that should not be filtered.
     *
     * @var array
     */
    protected $except = [

        '_token',

    ];


    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $no_trim = [

        'current_password',
        'password',
        'password_confirmation',

    ];


    /**
     * The names of the attributes that should not have HTML tags stripped.
     *
     * @var array
     */
    protected $no_strip_tags = [

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

        if ( in_array( $key, $this->except, true ) )
        {
            return $value;
        }

        if ( ! is_string( $value ) or is_numeric( $value ) )
        {
            return $value;
        }

        //
        // TextTk::Sanitize flags
        //

        $flags = 0;

        if ( in_array( $key, $this->no_trim, true ) )
        {
            $flags = $flags + TextTk::SANITIZE_NO_TRIM;
        }

        if ( in_array( $key, $this->no_strip_tags, true ) )
        {
            $flags = $flags + TextTk::SANITIZE_NO_STRIPTAGS;
        }

        return TextTk::Sanitize( $value, $flags );

    }

}
