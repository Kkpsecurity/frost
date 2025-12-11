<?php
declare(strict_types=1);

namespace App\Classes\Payments;

use Illuminate\Support\Carbon;


trait PayPalHelpersTrait
{


    public static function Date_to_UTC( ?string $timestamp ) : ?string
    {

        if ( ! $timestamp ) return null;

        return Carbon::parse( $timestamp, 'America/Los_Angeles' )
                     ->setTimezone( 'UTC' )
                     ->isoFormat( 'YYYY-MM-DD HH:mm:ss' );

    }


    ################
    ###          ###
    ###   URLs   ###
    ###          ###
    ################


    public function PayPal_REST_URL() : string
    {
        return 'https://' . ( $this->_Payment->pp_is_sandbox ? 'api.sandbox' : 'api' ) . '.paypal.com';
    }

    public function PayPal_REST_SaleURL_v1() : string
    {
        return self::PayPal_REST_URL() . '/v1/payments/sale/' . $this->_Payment->pp_ppref;
    }

    /*
    public function PayPal_REST_SaleURL_v2() : string
    {
        return self::PayPal_REST_URL() . '/v2/checkout/orders/' . $this->_Payment->pp_pnref;
    }
    */


}
