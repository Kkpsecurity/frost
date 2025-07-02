<?php
declare(strict_types=1);

namespace App\Models\Traits\CourseAuth;

use Illuminate\Support\Carbon;


trait SetStartDateTrait
{

    public function SetStartDate() : void
    {

        if ( ! $this->start_date )
        {

            $this->update([

                'start_date'  => Carbon::now()->isoFormat( 'YYYY-MM-DD' ),
                'expire_date' => $this->GetUser()->IsStudent() ? $this->GetCourse()->CalcExpire( true ) : null,

            ]);

            $this->refresh();

        }

    }

}
