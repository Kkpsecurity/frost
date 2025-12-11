<?php namespace App\Support\Enum;

use App\BCache;

class SecLevelList
{


    /**
     * @return array
     */
    public static function options($level=null)
    {
        $secLevel = [
            '1' => 'Security Level 1',
            '2' => 'Security Level 2',
            '3' => 'Security Level 3',
            '4' => 'Security Level 4'
        ];

        if($level === null) {
            return $secLevel;
        } else {
            return $secLevel[$level];
        }
    }
}
