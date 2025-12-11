<?php
/*
 *  apache :: should place in <Directory>
 *
 *  <Directory <path>/public>
 *      RewriteEngine On
 *      RewriteRule ^(.*)(\-\d{10})(\.[0-9a-zA-Z]{1,6})$ $1$3 [L]
 *  </Directory>
 *
 *
 *  nginx :: can place under 'location /' or before it:
 *
 *    location /assets {
 *		  # cachebuster
 *        "^(.*)(\-\d{10})(\.[0-9a-zA-Z]{1,6})$" $1$3 break;
 *    }
 *
 */


function cbasset( $path )
{

    //
    // probably 'assets/'
    //

    $path = config( 'define.cbasset_prepend', '' ) . $path;

    //
    // ensure this regex matches server config
    //

    $pattern = '/(\.[0-9a-zA-Z]{1,6})$/';

    //
    // get Laravel's asset()
    //

    $url = asset( $path );

    //
    // convert relative $path to full filename
    //

    $filename = public_path() . DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $path );

    if ( ! is_file( $filename ) )
    {
        logger( __FUNCTION__ . "({$path}) Not Found: {$filename}" );
        return $url;
    }

    //
    // verify filename matches pattern, get extension
    //

    if ( ! preg_match( $pattern, $filename, $matches ) )
    {
        logger( __FUNCTION__ . "({$path}) Pattern Match Failed" );
        return $url;
    }

    $ext   = $matches[0];
    $mtime = filemtime( $filename );  logger( $mtime );

    return preg_replace( "/{$ext}$/", "-{$mtime}{$ext}", $url );

}
