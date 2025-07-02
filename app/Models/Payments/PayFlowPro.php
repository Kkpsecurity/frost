<?php

namespace App\Models\Payments;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Models\Payments\PaymentModel;

use App\RCache;
use App\Classes\Payments\PayFlowProTrait;
use App\Classes\Payments\PayPalHelpersTrait;
use App\Jobs\PayPalGetSaleDetails;


class PayFlowPro extends PaymentModel
{

    use Notifiable;
    use PayFlowProTrait, PayPalHelpersTrait;

    const REFUND_LIMIT_DAYS = 180;


    protected $table        = 'payment_payflowpro';

    protected $casts        = [

        'id'                => 'integer',
        'order_id'          => 'integer',
        'uuid'              => 'string',

        'total_price'       => 'decimal:2',

        'created_at'        => 'timestamp',
        'updated_at'        => 'timestamp',
        'completed_at'      => 'timestamp',

        'refunded_at'       => 'timestamp',
        'refunded_by'       => 'integer',

        /* -- cc response -- */

        'cc_last_at'        => 'timestamp',
        'cc_last_code'      => 'integer',
        'cc_last_respmsg'   => 'string',    // 255

        'cc_amount'         => 'decimal:2',
        'cc_fee'            => 'decimal:2',
        'cc_transtime'      => 'timestamp',

        /* -- paypal data -- */

        'pp_is_sandbox'     => 'boolean',

        'pp_token_id'       => 'string',    // 36  https://developer.paypal.com/api/nvp-soap/payflow/integration-guide/secure-token/
        'pp_token'          => 'string',    // 32
        'pp_token_exp'      => 'integer',
        'pp_token_count'    => 'integer',

        'pp_pnref'          => 'string',    // 64
        'pp_ppref'          => 'string',    // 64

        /* -- cc complete response -- */

        'cc_last_data'      => 'json',
        'cc_refund_data'    => 'json',

    ];



    //
    // helpers
    //


    public function PaymentTypeID() : int
    {
        return RCache::PaymentTypes( 'PayFlowPro', 'name' )->id;
    }


    public function InvoiceID() : string
    {
        return $this->id
            . ( $this->pp_is_sandbox
                ? config( 'define.payflowpro.sandbox_ext' )
                : config( 'define.payflowpro.invoice_ext' )
            );
    }


    public function IsCompleted() : bool
    {

        /*
        if (
               ! $this->completed_at
            or ! $this->pp_ppref
            or   $this->cc_last_result !== 0
        )
        {
            return false;
        }
        */

        if ( ! $this->completed_at or ! $this->pp_ppref )
        {
            return false;
        }

        return true;

    }


    public function CanRefund() : bool
    {

        if ( ! $this->IsCompleted() or $this->refunded_at )
        {
            return false;
        }

        if ( Carbon::now()->gt( Carbon::parse( $this->cc_transtime )->addDays( self::REFUND_LIMIT_DAYS ) ) )
        {
            return false;
        }

        return true;

    }


    public function CanGetSaleDetails() : bool
    {

        if ( ! $this->IsCompleted() or $this->pp_is_sandbox )
        {
            return false;
        }

        return true;

    }


    //
    //
    //


    public function TransTime( string $fmt = null ) : ?string
    {
        // convert PayPal transtime to PST to match their reports
        return $this->cc_transtime
                        ? Carbon::parse( $this->cc_transtime )
                                   ->tz( 'America/Los_Angeles' )
                            ->isoFormat( $fmt ?? config( 'define.carbon_format.default' ) )
                        : null;
    }


    public function ResetTokenVars() : void
    {
        $this->pp_token_id    = null;
        $this->pp_token       = null;
        $this->pp_token_exp   = null;
        $this->pp_token_count = 0;
        // don't save
    }


    public function DispatchGetSaleDetails() : void
    {

        if ( $this->pp_is_sandbox )
        {
            logger( 'DispatchGetSaleDetails(): skipping sandbox payment' );
            return;
        }

        PayPalGetSaleDetails::dispatch( $this );

    }


}
