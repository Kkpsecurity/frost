<?php

namespace App\Traits;

use DB;


trait ResetsSequence
{

    /**
     * Resets table's sequence
     *
     * @return void
     */
    public static function ResetSequence() : void
    {
        DB::statement( 'SELECT sp_resetseq( :table_name )', [
            'table_name' => ( self::$table ?? ( new static )->getTable() )
        ]);
    }

}
