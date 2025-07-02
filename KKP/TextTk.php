<?php

namespace KKP;


class TextTk
{


    ###########################################
    ###                                     ###
    ###   flags disable unwanted behavior   ###
    ###                                     ###
    ###########################################

    const SANITIZE_NO_TRIM      = 0x1;
    const SANITIZE_NO_STRIPTAGS = 0x2;
    const SANITIZE_NO_DOS2UNIX  = 0x4;



    /**
     * Sanitize text;
     *   Strip HTML Tags
     *   Trim
     *   DOS to UNIX
     *
     * Returns null on empty string (for database compatibility)
     *
     * @param   string       $str
     * @param   int          $flags (see CONSTs)
     * @return  string|null  sanitized string
     */
    public static function Sanitize( $str, $flags = 0 ) : ?string
    {

        //
        // return null on empty string
        //
        if ( is_null( $str ) or $str === '' ) return null;


        if ( ! ( $flags & self::SANITIZE_NO_STRIPTAGS ) )
        {
            $str = strip_tags( $str );
        }


        if ( ! ( $flags & self::SANITIZE_NO_TRIM ) )
        {
            $str = trim( $str );
        }


        if ( ! ( $flags & self::SANITIZE_NO_DOS2UNIX ) )
        {
            $str = str_replace( "\r\n", "\n", $str );
        }


        //
        // always do this
        //
        $str = self::StripSmartQuotes( $str );

        //
        // return null on empty string
        //
        return ( $str ?: null );

    }

    /**
     * Replaces Microsoft "Smart Quotes" with normal characters
     *
     * @param   string  $str
     * @return  string  cleaned string
     */
    public static function StripSmartQuotes( $str ) : string
    {

        //
        // http://www.toao.net/48-replacing-smart-quotes-and-em-dashes-in-mysql
        // first, replace UTF-8 characters.
        //
        $str = str_replace(
                              [ "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6" ],
                              [            "'",            "'",            '"',            '"',            '-',            '-',          '...' ],
                            $str );

        //
        // next, replace their Windows-1252 equivalents.
        //
        $str = str_replace(
                              [ chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133) ],
                              [      "'",      "'",      '"',      '"',      '-',      '-',    '...' ],
                            $str );

        //
        // middot character
        //
        $str = str_replace( chr(183), '&#183;', $str );

        return $str;

    }


    /**
     * Convert JS to escaped string
     *
     * @param   string  $str
     * @return  string  escaped string
     */
    public static function AddJsSlashes( $str ) : string
    {

        // http://php.net/manual/en/function.addcslashes.php
        $pattern = [   "/\\\\/", "/\n/", "/\r/", "/\"/", "/\'/",    "/&/",   "/</",   "/>/" ];
        $replace = [ "\\\\\\\\",  "\\n",  "\\r", "\\\"",  "\\'",  "\\x26", "\\x3C", "\\x3E" ];

        return preg_replace( $pattern, $replace, $str );

    }


    /**
     * Format text to HTML
     *   Convert newline to <br />
     *   Convert double spaces to 2 non-breaking spaces
     *
     * @param   string  $str
     * @return  string  escaped HTML
     */
    public static function FormatBlockText( $str ) : string
    {

        $str = stripslashes( $str ); // convert legacy data
        $str = self::StripSmartQuotes( $str );
        return str_replace( [     "\n",           '  ' ],
                            [ '<br />', '&nbsp;&nbsp;' ],
                            $str );

    }


    /**
     * Format 10 digit telephone number
     *
     * @param   string  $str
     * @param   bool    $dots
     * @return  string  formatted string
     */
    public static function FormatTelephone( $str, $dots = false ) : string
    {

        return substr( $str, 0, 3 ) . ( $dots ? '.' : ' ' )
             . substr( $str, 3, 3 ) . ( $dots ? '.' : ' ' )
             . substr( $str, 6, 4 );

    }


    /**
     * Convert bytes to human-readable string
     *
     * @param   string  $str
     * @param   int     $precision
     * @return  string  formatted string
     */
    public static function BytesToString( $size, $precision = 0 ) : string
    {

        $sizes = [ 'YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'kB', 'B' ];
        $total = count( $sizes );
        while( $total-- && $size > 1024 ) $size /= 1024;
        return round( $size, $precision ) . ' ' . $sizes[$total];

    }


    /**
     * Convert seconds to hh:mm:ss
     *
     * @param   int     $seconds
     * @param   bool    $skip_empty_hours
     * @return  string  formatted time
     */
    public static function HMS( int $seconds, $skip_empty_hours = false ) : string
    {

        $timestr = sprintf( '%02d:%02d:%02d',
                            floor( $seconds / 3600    ),
                            floor( $seconds / 60 % 60 ),
                            floor( $seconds % 60      )
                          );

        if ( $skip_empty_hours )
        {
            $timestr = preg_replace( '/^00\:/', '', $timestr );
        }

        return $timestr;

    }


    /**
     * Encode Base64 URL
     *
     * @param  string $string
     * @return string
     */
    public static function Base64EncodeURL( string $string ) : string
    {
        return str_replace(
            [ '+', '/', '=' ],
            [ '-', '_', ''  ],
            base64_encode( $string )
        );
    }


    /**
     * Decode Base64 URL
     *
     * @param  string $string
     * @return string
     */
    public static function Base64DecodeURL( string $string ) : string
    {
        return base64_decode(
            str_replace(
                [ '-', '_' ],
                [ '+', '/' ],
                $string
            )
        );
    }

}
