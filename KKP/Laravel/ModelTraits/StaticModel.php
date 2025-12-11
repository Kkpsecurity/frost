<?php

namespace KKP\Laravel\ModelTraits;


trait StaticModel
{

    protected static function boot()
    {

        parent::boot();

        static::creating(function ( $Model ) {
            throw new Exception( 'Static Model ' . get_class( $Model ) . ' cannot be created.' );
        });

        static::updating(function ( $Model ) {
            throw new Exception( 'Static Model ' . get_class( $Model ) . ' cannot be updated.' );
        });

        static::saving(function ( $Model ) {
            throw new Exception( 'Static Model ' . get_class( $Model ) . ' cannot be saved.' );
        });

        static::deleting(function ( $Model ) {
            throw new Exception( 'Static Model ' . get_class( $Model ) . ' cannot be deleted.' );
        });

    }

}
