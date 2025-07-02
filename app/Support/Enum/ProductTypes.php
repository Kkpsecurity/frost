<?php namespace App\Support\Enum;

use App\BCache;

class ProductTypes
{


    /**
     * @return array
     */
    public static function options($type_id=null)
    {
        $types = BCache::ProductTypes();

        $lists = [];
        if (count($types) > 0) {
            foreach ($types as $type) {
                $lists[$type->id] = $type->name;
            }
        }
        asort($lists);
        if($type_id !== null) return $lists[$type_id];
        else return $lists;
    }
}
