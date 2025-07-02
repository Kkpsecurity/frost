<?php

namespace KKP\Laravel\ServiceProviders;

use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;
use Illuminate\Support\ServiceProvider;


class CollectionMacroServiceProvider extends ServiceProvider
{

    public function boot()
    {


        /**
         * Add findOrFail to collection
         *
         * @param   mixed  $id
         * @param   mixed  $value
         * @return  mixed
         */
        Collection::macro( 'findOrFail', function ( $id, $value ) {

            return $this->where( $id, $value ) ?: abort( 404 );

            #if ( $result = $this->where( $id, $value ) )
            #{
            #    return $result;
            #}
            #throw new ItemNotFoundException;

        });


        /**
         * Add firstOrFail to collection
         *
         * @param   mixed  $id
         * @param   mixed  $value
         * @return  mixed
         */
        /**  added in Laravel 9
        Collection::macro( 'firstOrFail', function ( $id, $value ) {

            return $this->firstWhere( $id, $value ) ?: abort( 404 );

        });
        */


        /**
         * Add sortByIDArray to collection
         *
         * @param   array  $ids
         * @return  array  (Collection)
         */
        Collection::macro( 'sortByIDArray', function ( array $ids ) {

            return $this->sortBy( function( $Item ) use ( $ids ) {

                return array_search( $Item->id, $ids );

            });

        });


        /**
         * Convert Laravel Collection to KVP array
         *
         * @return  array
         * @throws  Exception  (if count( values ) != 2)
         */
        Collection::macro( 'toKVP', function() {

            $kvp = [];

            foreach ( $this->values() as $item )
            {

                //
                // convert $item to array, regardless of current type
                //

                $arr = array_values( (array) $item );

                if ( count( $arr ) != 2 )
                {
                    throw new Exception( 'Collection->toKVP() received Collection with ' .  count( $arr ) . ' values' );
                    return null;
                }

                $kvp[ $arr[0] ] = $arr[1];

            }

            return $kvp;

        });



        //
        // https://gist.github.com/iamsajidjaved/4bd59517e4364ecec98436debdc51ecc
        //

        /**
         * Paginate a standard Laravel Collection.
         *
         * @param   int     $perPage
         * @param   int     $total
         * @param   int     $page
         * @param   string  $pageName
         * @return  array
         */
        Collection::macro( 'paginate', function( $perPage, $total = null, $page = null, $pageName = 'page' ) {

            $page = $page ?: LengthAwarePaginator::resolveCurrentPage( $pageName );

            return new LengthAwarePaginator(

                $this->forPage( $page, $perPage ),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path'     => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]

            );

        });


    }

}
