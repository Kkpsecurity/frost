<?php

namespace KKP;

use Exception;


class ValidatorLib
{


    public static function AbsClassName( $class_name ) : void
    {

        if ( strpos( $class_name, '\\' ) !== 0 )
        {
            throw new Exception( "Invalid Class Name ( must begin with backslash ) '{$class_name}'" );
        }

        return self::ClassName( $class_name );

    }


    public static function ClassName( $class_name ) : void
    {

        foreach ( explode( '\\', preg_replace( '/^\\\/', '', $class_name ) ) as $part )
        {
            if ( ! preg_match( '/^[a-z0-9]*$/i', $part ) )
            {
                throw new Exception( "Invalid Class Name ( invalid part '{$part}' ) '{$class_name}'" );
            }
        }

        if ( ! class_exists( $class_name, true ) )
        {
            throw new Exception( "Invalid Class Name ( ! class_exists ) '{$class_name}'" );
        }

    }


}
