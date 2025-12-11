<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use App\Models\InstLesson;
use App\Models\InstUnit;


trait ActiveInstLesson
{

    /**
     * Retrieves -latest- active InstLesson
     *
     * @param   int|string|InstUnit  $InstUnit
     * @return  InstLesson|null
     */
    public static function ActiveInstLesson( int|string|InstUnit $InstUnit ) : ?InstLesson
    {

        $inst_unit_id = $InstUnit->id ?? (int) $InstUnit;

        return InstLesson::where( 'inst_unit_id', $InstUnit->id )
                     ->whereNull( 'completed_at' )
                        ->latest()
                         ->first();

    }

}
