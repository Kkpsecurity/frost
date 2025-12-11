<?php

namespace KKP\Laravel\ModelTraits;

use DB;
use Exception;
use Illuminate\Support\Carbon;

use KKP\Laravel\PgTk;


trait PgTimestamps
{

    // vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasAttributes.php

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat() : string
    {
        return 'Y-m-d H:i:s.uO';
    }


    // vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasTimestamps.php

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function freshTimestamp() : Carbon
    {
        return Carbon::parse( PgTk::now() );
    }


    /**
     * Set timestamp field.
     *
     * @param   string|null  $column_name
     * @return  $this
     */
    public function pgtouch( string $column_name = 'updated_at' ) : self
    {

        //
        // skip all other checks
        //

        if ( $column_name == 'updated_at' )
        {
            DB::statement( DB::raw( "UPDATE {$this->getTable()} SET updated_at = CURRENT_TIMESTAMP WHERE id = {$this->id}" ) );
            return $this->fresh();
        }


        //
        // validate field
        //

        $casts = [ 'date', 'datetime', 'timestamp' ];

        if ( ! $this->hasCast( $column_name, $casts ) )
        {
            throw new Exception( get_class( $this ) . ' needs cast [ ' . join( ' | ', $casts ) . " ] for field {$column_name}" );
            return $this;
        }


        //
        // NOTE: don't use $this->update([])
        //       won't update guarded/hidden fields
        //

        //
        // no timestamps; quick update
        //

        if ( ! $this->timestamps )
        {
            $this->{$column_name} = PgTk::now();
            $this->save();
            return $this->fresh();
        }


        //
        // make $column_name and updated_at match
        //

        $timestamp = PgTk::now();

        $this->timestamps = false;

        $this->{$column_name} = $timestamp;
        $this->{$this->getUpdatedAtColumn()} = $timestamp;
        $this->save();

        $this->timestamps = true;

        return $this->fresh();

    }


}
