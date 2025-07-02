<?php

namespace App\Models\Payments;

use stdClass;
use Illuminate\Database\Eloquent\Model;

use RCache;
use App\Models\Order;
use App\Models\User;
use App\Presenters\PresentsTimeStamps;
use KKP\Laravel\ModelTraits\NoString;
use KKP\Laravel\ModelTraits\PgTimestamps;


class PaymentModel extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString;


    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $guarded      = [ 'id', 'uuid' ];


    //
    // relationships
    //


    public function Order()
    {
        return $this->belongsTo( Order::class, 'order_id' );
    }

    public function RefundedBy()
    {
        return $this->belongsTo( User::class, 'refunded_by' );
    }


    //
    // cache queries
    //


    public function GetUser() : User
    {
        //
        // abort_unless( Auth::id() == [Payment]->GetUser()->id, 401 );
        //
        return RCache::User( $this->Order->user_id );
    }

    public function GetRefundedBy() : ?User
    {
        return RCache::Admin( $this->refunded_by );
    }


    //
    // helpers
    //


    /*
    public function Status() : stdClass
    {

        if ( $this->refunded_at )
        {
            return (object) [
                'completed' => true,
                'name'      => 'Refunded',
                'timestamp' => $this->RefundedAt(),
            ];
        }

        if ( $this->completed_at )
        {
            return (object) [
                'completed' => true,
                'name'      => 'Payment Completed',
                'timestamp' => $this->CompletedAt(),
            ];
        }

        return (object) [
            'completed' => false,
            'name'      => 'Incomplete',
            'timestamp' => null,
        ];

    }
    */

}
