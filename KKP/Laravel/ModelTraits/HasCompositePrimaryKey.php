<?php

/*
 * SOURCE: https://github.com/mopo922/LaravelTreats/blob/master/src/Model/Traits/HasCompositePrimaryKey.php
 *
 * https://stackoverflow.com/questions/36332005/laravel-model-with-two-primary-keys-update
 * https://github.com/laravel/framework/issues/34643
 * https://github.com/mopo922/LaravelTreats/blob/master/src/Model/Traits/HasCompositePrimaryKey.php
 *
 *
 *  Example for table: userprefs
 *    where primary key = [ user_id, pref_name ]
 *
 *
 *  use KKP\Laravel\ModelTraits\HasCompositePrimaryKey;
 *
 *      use HasCompositePrimaryKey;
 *
 *      protected $primaryKey  = [ 'user_id', 'pref_name' ];
 *
 */

namespace KKP\Laravel\ModelTraits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;


trait HasCompositePrimaryKey
{


    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return  bool
     */
    public function getIncrementing() : bool
    {
        return false;
    }


    /**
     * Get the value of the model's primary key.
     *
     * @return  mixed
     */
    public function getKey()
    {

        $attributes = [];

        foreach ( $this->getKeyName() as $key )
        {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;

    }


    /**
     * Set the keys for a save update query.
     *
     * @param   Builder  $query
     * @return  Builder
     */
    protected function setKeysForSaveQuery( $query ) : object
    {

        foreach ( $this->getKeyName() as $key )
        {
            if ( isset( $this->$key ) )
            {
                $query->where( $key, '=', $this->$key );
            }
            else
            {
                throw new Exception( __METHOD__ . 'Missing part of the primary key: ' . $key );
            }
        }

        return $query;

    }


    /**
     * Execute a query for a single record by ID.
     *
     * @param   array  $ids Array of keys, like [column => value].
     * @param   array  $columns
     * @return  mixed|static
     */
    public static function find( $ids, $columns = ['*'] )
    {

        $me    = new self;
        $query = $me->newQuery();

        foreach ( $me->getKeyName() as $key )
        {
            $query->where( $key, '=', $ids[$key] );
        }

        return $query->first( $columns );

    }


    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param   mixed  $ids
     * @param   array  $columns
     * @return  Model|Collection
     *
     * @throws  ModelNotFoundException
     */
    public static function findOrFail( $ids, $columns = ['*'] ) : object
    {

        $result = self::find( $ids, $columns );

        if ( ! is_null( $result ) )
        {
            return $result;
        }

        throw ( new ModelNotFoundException )->setModel(
            __CLASS__, $ids
        );

    }


    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return  $this
     */
    public function refresh() : object
    {

        if ( ! $this->exists )
        {
            return $this;
        }

        $this->setRawAttributes(
            static::findOrFail( $this->getKey() )->attributes
        );

        $this->load( collect( $this->relations )->except( 'pivot' )->keys()->toArray());

        return $this;

    }


}
