<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Illuminate\Support\Carbon;

use RCache;
use App\Models\InstLesson;
use App\Models\StudentUnit;


trait StudentCanJoinLesson
{

    /**
     * Determines if Student can join Lesson based on time InstLesson was created
     *   Admins can always join
     *   uses SiteConfig: student_join_lesson_seconds
     *
     * @param   StudentUnit  $StudentUnit
     * @param   InstLesson   $InstLesson
     * @return  bool
     */
    protected static function StudentCanJoinLesson( StudentUnit $StudentUnit, InstLesson $InstLesson ) : bool
    {

        if ( $StudentUnit->GetUser()->IsAnyAdmin() )
        {
            return true;
        }

        return Carbon::now()->lt(
                    Carbon::parse( $InstLesson->created_at )
                     ->addSeconds( RCache::SiteConfig( 'student_join_lesson_seconds' ) )
               );

    }

}
