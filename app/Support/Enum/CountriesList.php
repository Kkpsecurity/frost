<?php namespace App\Support\Enum;


use App\BCache;

class CountriesList
{


    /**
     * @return array
     */
    public static function options()
    {
        return BCache::Countries(true);
    }
}
