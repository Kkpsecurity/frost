<?php

namespace App\Observers;

use App\Models\ZoomCreds;


class ZoomCredsObserver
{

    public function saved(ZoomCreds $ZoomCreds)
    {

        #kkpdebug( 'Observer', __METHOD__ );

        kkpdebug('Observer', "ZoomCreds Status: {$ZoomCreds->zoom_status} Email: {$ZoomCreds->zoom_email}");
    }
}
