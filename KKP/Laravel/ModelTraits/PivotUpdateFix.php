<?php

namespace KKP\Laravel\ModelTraits;

use DB;

#use Illuminate\Support\Traits\ForwardsCalls;
#use ForwardsCalls


trait PivotUpdateFix
{


    public function updateFix( array $attributes = [] )
    {

        if ( ! $this->exists )
        {
            return false;
        }

        $this->_updateFix( $attributes );

    }


    public function updateQuietlyFix( array $attributes = [] )
    {

        if ( ! $this->exists )
        {
            return false;
        }

        return static::withoutEvents(function () use ( $attributes ) {

            return $this->_updateFix( $attributes );

        });

    }


    private function _updateFix( array $attributes = [] )
    {

        if ( $this->fireModelEvent( 'saving' ) === false )
        {
            return false;
        }


        $saved = DB::table( $this->getTable() )
                   ->where( $this->getForeignKey(), $this->getAttribute( $this->getForeignKey() ) )
                   ->where( $this->getRelatedKey(), $this->getAttribute( $this->getRelatedKey() ) )
                  ->update( $attributes );


        if ( $saved )
        {
            $this->fireModelEvent( 'saved', false );
        }

        return $saved;

    }


}
