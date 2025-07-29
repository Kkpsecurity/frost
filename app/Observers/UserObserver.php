<?php

namespace App\Observers;

use Exception;

use App\Models\User;
use App\Services\RCache;
use App\Services\RCacheWarmer;


class UserObserver
{


    public function created(User $User)
    {
        $User->SetPref('timezone', config('define.timezone.default'));
    }


    public function saved(User $User)
    {

        kkpdebug('Observer', __METHOD__);

        RCache::StoreUser($User);

        if ($User->IsAnyAdmin()) {
            RCacheWarmer::LoadAdmins(true);
        }
    }


    public function deleting(User $User)
    {
        throw new Exception('Cannot delete users');
    }
}
