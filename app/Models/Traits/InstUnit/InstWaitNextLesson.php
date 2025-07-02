<?php
declare(strict_types=1);

namespace App\Models\Traits\InstUnit;

use App;
use Illuminate\Support\Carbon;

use RCache;


trait InstWaitNextLesson
{


    public function InstCanStartLesson() : int
    {

        if ( $this->completed_at )
        {
            return 0;
        }


        $LastInstLesson = $this->InstLessons()
                               ->whereNotNull( 'completed_at' )
                               ->orderBy( 'completed_at', 'DESC' )
                               ->first();

        if ( ! $LastInstLesson )
        {
            return 0;
        }


        $next_start_time = Carbon::parse( $LastInstLesson->completed_at )
                            ->addSeconds( RCache::SiteConfig( 'instructor_next_lesson_seconds' ) );


        if ( Carbon::now() > $next_start_time )
        {
            return 0;
        }

        return Carbon::now()->diffInSeconds( $next_start_time );


    }


}
