<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use Auth;

use App\Models\InstUnit;
use KKP\Laravel\PgTk;


trait CompleteInstUnit
{

    /**
     * Marks InstUnit complete
     * Marks StudentUnits complete (as appropriate)
     *
     * @param  InstUnit  $InstUnit
     */
    public static function CompleteInstUnit( InstUnit $InstUnit ) : void
    {

        if ( $InstUnit->completed_at )
        {
            kkpdebug( 'ClassroomQueries', "CompleteInstUnit({$InstUnit->id}) :: InstUnit already completed" );
            return;
        }


        $InstUnit->update([
            'completed_at' => PgTk::now(),
            'completed_by' => ( Auth::id() ?? $InstUnit->created_by )
        ]);


        foreach ( $InstUnit->StudentUnits as $StudentUnit )
        {
            if ( ! $StudentUnit->completed_at && ! $StudentUnit->dnc_at )
            {
                $StudentUnit->pgtouch( 'completed_at' );
            }
        }

    }

}
