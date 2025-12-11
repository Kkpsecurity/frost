<?php

namespace KKP\Laravel;

use KKP\TextTk;


class JSData
{


    private static $_namespaces = [];
    private static $_jqid_vals  = [];
    private static $_log_msgs   = [];


    /**
     * Sets Javascript key/value pair
     *
     * @param   string  $key
     * @param   mixed   $val
     * @param   bool    $quoted
     * @param   string  $namespace
     * @return  self
     */
    public static function SetKeyVal( $key, $val, $quoted = true, $namespace = null ) : object
    {

        $namespace = $namespace ?? 'global';

        if ( ! isset( self::$_namespaces[ $namespace ] ) )
        {
            self::$_namespaces[ $namespace ] = [];
        }

        self::$_namespaces[ $namespace ][ $key ] = ( $quoted ? "\"{$val}\"" : $val );

        return new self();

    }


    /**
     * Sets jQuery value
     *
     * @param   string  $id
     * @param   mixed   $val
     * @param   bool    $quoted
     * @return  self
     */
    public static function SetJQValue( $id, $val, $quoted = true ) : object
    {

        self::$_jqid_vals[ $id ] = ( $quoted ? "\"{$val}\"" : $val );

        return new self();

    }


    /**
     *
     * Sets Javascript console.log message
     *
     * @param   string  $msg
     * @return  self
     */
    public static function Log( $msg ) : object
    {

        array_push( self::$_log_msgs, TextTk::AddJsSlashes( $msg ) );

        return new self();

    }


    /**
     *
     * Renders JSData to HTML, including <script> tags
     *
     * @return  string
     */
    public static function ToHTML() : string
    {

        $jsRendered = '';


        //
        // global namespace
        //
        if ( isset( self::$_namespaces[ 'global' ] ) )
        {
            foreach ( self::$_namespaces[ 'global' ] as $key => $val )
            {
                $jsRendered .= "var {$key} = {$val};\n";
            }
        }


        //
        // namespace vars
        //

        foreach ( self::$_namespaces as $namespace => $kvp )
        {

            if ( $namespace == 'global' ) continue;

            foreach ( $kvp as $key => $val )
            {
                $jsRendered .= "window.{$namespace}.{$key} = {$val};\n";
            }

        }


        //
        // jQuery values
        //

        foreach ( self::$_jqid_vals as $id => $val )
        {
            $jsRendered .= "$( '#{$id}' ).val( {$val} );\n";
        }


        //
        // console log messages
        //

        if ( config( 'app.debug' ) )
        {
            foreach ( self::$_log_msgs as $msg )
            {
                $jsRendered .= "console.log( \"JSData :: {$msg}\" );\n";
            }
        }


        //
        // return HTML
        //

        return ( $jsRendered
                    ? "\n<!--  JSData  -->\n<script>\n{$jsRendered}</script>\n<!--  JSData  -->\n"
                    : '<!-- no jsdata -->'
               );

    }

}
