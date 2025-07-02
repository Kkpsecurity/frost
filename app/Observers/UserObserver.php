<?php

namespace App\Observers;

use Exception;

use RCache;
use App\RCacheWarmer;
use App\Models\User;


class UserObserver
{


    public function created( User $User )
    {
        $User->SetPref( 'timezone', config( 'define.timezone.default' ) );
    }


    public function saved( User $User )
    {

        kkpdebug( 'Observer', __METHOD__ );

        RCache::StoreUser( $User );

        if ( $User->IsAnyAdmin() )
        {
            RCacheWarmer::LoadAdmins( true );
        }

    }


    public function deleting( User $User )
    {
        throw new Exception( 'Cannot delete users' );
    }


}
