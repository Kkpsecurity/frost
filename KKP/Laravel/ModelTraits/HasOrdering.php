<?php
/*
 *
 * only attach to Pivot model
 *
 *
 */

namespace KKP\Laravel\ModelTraits;

use DB;
use Illuminate\Database\Eloquent\Relations\Pivot;


trait HasOrdering
{


    public function refresh_ordering( array $attributes = [] ) : void
    {

        if ( $this instanceof Pivot )
        {
            $Items = get_class( $this )::where(
                        $this->getForeignKey(),
                        $this->getAttribute( $this->getForeignKey() )
                     )->orderBy( 'ordering' )->get();
        }
        else
        {
            if ( $attributes )
            {
                $Items = get_class( $this )::where( $attributes )->orderBy( 'ordering' )->get();
            }
            else
            {
                $Items = get_class( $this )::orderBy( 'ordering' )->get();
            }
        }


        $ordering = 0;

        foreach ( $Items as $Item )
        {

            $ordering++;

            if ( $Item->ordering != $ordering )
            {
                $Item->updateQuietly([ 'ordering' => $ordering ]);
            }

        }

    }

}
