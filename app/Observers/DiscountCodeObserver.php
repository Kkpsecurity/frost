<?php

namespace App\Observers;

use Exception;

use RCache;
use App\Models\DiscountCode;


class DiscountCodeObserver
{

    public function creating(DiscountCode $DiscountCode)
    {

        kkpdebug('Observer', __METHOD__);

        if (RCache::DiscountCodes()->firstWhere('code', $DiscountCode->code)) {
            throw new Exception("Code '{$DiscountCode->code}' already in use");
            return false;
        }
    }
}
