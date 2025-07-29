<?php

namespace App\Models\Payments;

/**
 * PaymentModel
 *
 * @property int $id
 * @property int $order_id
 * @property string $uuid
 * @property float $total_price
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon|null $refunded_at
 * @property int|null $refunded_by
 */


use Illuminate\Database\Eloquent\Model;

use stdClass;

use App\Services\RCache;

use App\Models\User;
use App\Models\Order;

use App\Traits\NoString;
use App\Traits\PgTimestamps;
use App\Presenters\PresentsTimeStamps;



class PaymentModel extends Model
{

    use PgTimestamps, PresentsTimeStamps;
    use NoString;


    protected $primaryKey   = 'id';
    public    $timestamps   = true;

    protected $guarded      = ['id', 'uuid'];


    //
    // relationships
    //


    public function Order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function RefundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }


    //
    // cache queries
    //


    public function GetUser(): User
    {
        //
        // abort_unless( Auth::id() == [Payment]->GetUser()->id, 401 );
        //
        return RCache::User($this->Order->user_id);
    }

    public function GetRefundedBy(): ?User
    {
        return RCache::Admin($this->refunded_by);
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
