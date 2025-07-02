<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Collection;

use App\Models\InstLesson;
use App\Models\InstUnit;


trait CompletedInstLessons
{

    /**
     * Retrieves completed InstLessons for InstUnit
     *
     * @param   int|string|InstUnit  $InstUnit
     * @return  Collection           [InstLesson]
     */
    public static function CompletedInstLessons( int|string|InstUnit $InstUnit ) : Collection
    {

        $inst_unit_id = $InstUnit->id ?? (int) $InstUnit;

        return InstLesson::where( 'inst_unit_id', $inst_unit_id )
                  ->whereNotNull( 'completed_at' )
                       ->orderBy( 'completed_at' )
                           ->get();

    }

}
