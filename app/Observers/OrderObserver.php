<?php

namespace App\Observers;

use RCache;
use App\Models\Order;


class OrderObserver
{

    public function creating( Order $Order )
    {

        kkpdebug( 'Observer', __METHOD__ );

        if ( ! $Order->total_price )
        {
            kkpdebug( 'Observer', 'Order :: Setting total_price' );
            $Order->total_price = RCache::Courses( $Order->course_id )->price;
        }

    }

}
