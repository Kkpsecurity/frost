<?php
declare(strict_types=1);

namespace App\Classes\ClassroomQueries;

use DB;
use Illuminate\Support\Collection;

use App\Models\InstUnit;
use App\Models\User;
use KKP\Laravel\PgTk;


trait RecentInstUnits
{

    /**
     * Retrieves recent InstUnits for User
     *
     * @param   int|string|User  $User
     * @param   int|null         $days = 30
     * @return  Collection       [InstUnit]
     */
    public static function RecentInstructorUnits( int|string|User $User, int $days = 30 ) : Collection
    {

        $user_id = $User->id ?? (int) $User;

        //
        // stored procedure is *much* faster
        //

        return PgTk::toModels(
                    InstUnit::class,
                    DB::select( 'SELECT * FROM sp_recent_instunits( :user_id, :days )', [
                        'user_id' => $user_id,
                        'days'    => $days,
                    ])
               );

    }

}
