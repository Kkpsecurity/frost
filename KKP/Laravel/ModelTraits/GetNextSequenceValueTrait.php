<?php
/**
 *
 * NOTE: this is postgresql-specific
 *
 * MODEL
 * -----
 *
 * use KKP\Laravel\ModelTraits\GetNextSequenceValueTrait;
 *
 * class MODEL ... {
 *
 *     use GetNextSequenceValueTrait;
 *
 * }
 *
 *
 * CONTROLLER
 * ----------
 *
 *     $next_id = MODEL::GetNextSequenceValue();
 *
 */

namespace KKP\Laravel\ModelTraits;

use DB;
use Exception;


trait GetNextSequenceValueTrait
{

    // https://stackoverflow.com/questions/37210747/how-to-get-next-id-of-autogenerated-field-in-laravel-for-specific-table

    /**
     * Increment and retrieve next sequence value
     *
     * @return  integer
     */
    public static function GetNextSequenceValue() : ?integer
    {

        $self = new static();

        if ( ! $self->getIncrementing() )
        {
            throw new Exception( sprintf( 'Model (%s) is not auto-incremented', static::class ) );
        }

        if ( ! $column_default = $self->GetPrimaryKeyDefault() )
        {
            throw new Exception( sprintf( 'Model (%s) has no sequence', static::class ) );
        }

        return DB::selectOne( "SELECT {$column_default} AS val" )->val;

    }


    /**
     * Retrieve primary key column default value (sequence) in DB format
     *
     * @return  string  column default in DB format
     */
    public static function GetPrimaryKeyDefault() : ?string
    {

        $self = new static();

        return DB::selectOne(

            'SELECT column_default FROM information_schema.columns WHERE table_name = ? AND column_name = ?',
            [ $self->getTable(), $self->getKeyName() ]

        )->column_default;

    }


    /**
     * Retrieve primary key sequence name
     *
     * @return  string  sequence name
     */
    public static function GetSequenceName() : ?string
    {

        $self = new static();

        preg_match( "/'(.*)'/", $self->GetPrimaryKeyDefault(), $matches );

        return $matches[1]; // second match

    }


}
