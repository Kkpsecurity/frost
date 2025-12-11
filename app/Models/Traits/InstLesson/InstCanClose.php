<?php
declare(strict_types=1);

namespace App\Models\Traits\InstLesson;

use App\Services\RCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;


trait InstCanClose
{


    public function InstCanClose() : bool
    {

        if ( $this->completed_at )
        {
            return false;
        }

        $minutes = App::environment( 'production' )
                    ? RCache::SiteConfig( 'instructor_close_lesson_minutes' )
                    : 5;

        return Carbon::now()->gt( Carbon::parse( $this->created_at )->addMinutes( $minutes ) );

    }


}
