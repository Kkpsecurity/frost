<?php

function vasset( string $path, bool $is_abspath = false ) : string
{

    if ( $is_abspath )
    {
        $path = str_replace( storage_path() . '/app/public', 'storage', $path );
    }
    else
    {
        $path = config( 'define.vbasset_prepend', '' ) . $path;
    }

    //
    // get Laravel's asset()
    //

    $url = asset( $path );

    //
    // convert relative $path to full filename
    //

    $filename = public_path() . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $path );

    //
    // verify file exists
    //

    if ( ! is_file( $filename ) )
    {
        logger( __FUNCTION__ . "({$path}) Not Found: '{$filename}'" );
        return $url;
    }

    //
    // append timestamp query string
    //

    return $url . '?v=' . filemtime( $filename );

}
