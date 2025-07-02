<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Classes\Payments\PayPalRESTObj;
use App\Models\Payments\PaymentModel;


class PayPalGetSaleDetails implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    // match workers: (none)
    public $timeout = 90;
    public $tries   =  3;

    protected $Payment;


    public function __construct( PaymentModel $Payment )
    {

        $this->Payment = $Payment->withoutRelations();

        $this->onQueue( 'paypal_rest' );

    }


    public function handle()
    {

        if ( $this->Payment->pp_ppref )
        {
            ( new PayPalRESTObj( $this->Payment ) )->GetSaleDetails();
        }

    }


}
