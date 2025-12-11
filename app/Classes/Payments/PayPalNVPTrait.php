<?php
declare(strict_types=1);

namespace App\Classes\Payments;

use stdClass;


trait PayPalNVPTrait
{


    protected $_nvp_mimetype = 'text/namevalue'; // PHP8.2 make this a const


    //
    // 2025-04-28
    //  Paypal returning 'text/namevalue;charset=UTF-8'
    //

    public static function IsNVPMimetype( string $content_type ) : bool
    {
        #return false !== strpos( $content_type, self::MIMETYPE );
        return false !== strpos( $content_type, 'text/namevalue' );
    }


    public static function EncodeNVP( array $nvp_arr ) : string
    {

        $nvp = [];

        foreach ( $nvp_arr as $key => $val )
        {
            array_push( $nvp, "{$key}[" . strlen($val) . "]={$val}" );
        }

        return join( '&', $nvp );

    }


    public static function DecodeNVP( string $nvp_str ) : stdClass
    {

        //
        // https://www.manongdao.com/q-762728.html
        //   updated to stdClass
        //

        $nvp_obj = new stdClass;
        $initial = 0;

        while( strlen( $nvp_str ) )
        {

            $keypos = strpos( $nvp_str, '=' );
            $valpos = strpos( $nvp_str, '&' ) ? strpos( $nvp_str, '&' ) : strlen( $nvp_str );

            $keyval = substr( $nvp_str, $initial, $keypos );
            $vallen = $valpos - $keypos - 1;

            if ( $bracketpos = strpos( $keyval, '[' ) )
            {
                $vallen = substr( $keyval, $bracketpos + 1, strlen( $keyval ) - $bracketpos - 2 );
                $keyval = substr( $keyval, 0, $bracketpos );
            }
            $valval = substr( $nvp_str, $keypos + 1, $vallen );


            $key_decoded = urldecode( $keyval );
            $val_decoded = urldecode( $valval );
            $nvp_obj->{$key_decoded} = $val_decoded;


            $nvp_str = substr( $nvp_str, $keypos + $vallen + 2, strlen( $nvp_str ) );

        }

        return $nvp_obj;

    }

}
