<?php
declare(strict_types=1);

namespace App\Models\Traits\StudentLesson;

use App\Classes\Challenger;


trait ClearDNC
{

    public function ClearDNC() : ?string
    {

        if ( $this->completed_at )
        {
            return 'Student Lesson already completed';
        }

        if ( $this->InstLesson->completed_at )
        {
            return 'Instructor Lesson already completed';
        }


        $this->update([ 'dnc_at' => null ]);

        Challenger::CreateClearedDNC( $this->id );

        return null;

    }

}
