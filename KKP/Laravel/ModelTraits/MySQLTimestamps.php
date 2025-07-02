<?php

namespace KKP\Laravel\ModelTraits;

use DB;


trait MySQLTimestamps
{

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    /*
    public function getDateFormat() : string
    {
        return 'Y-m-d H:i:s.uO';
    }
    */

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestamp() : string
    {
        return DB::selectOne( 'SELECT CURRENT_TIMESTAMP() AS timestamp' )->timestamp;
    }

}
