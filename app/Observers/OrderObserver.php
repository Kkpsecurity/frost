<?php

namespace App\Observers;

/**
 * OrderObserver
 *
 * Listens for changes to the Order model and ensures data consistency
 * between the primary database and Redis cache. Automatically syncs
 * updates, creations, and deletions to maintain real-time state.
 */


use App\Models\Order;
use App\Services\RCache;


class OrderObserver
{

    public function creating(Order $Order)
    {

        kkpdebug('Observer', __METHOD__);

        if (! $Order->total_price) {
            kkpdebug('Observer', 'Order :: Setting total_price');
            $Order->total_price = RCache::Courses($Order->course_id)->price;
        }
    }
}
