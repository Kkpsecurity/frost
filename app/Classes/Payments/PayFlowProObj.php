<?php
declare(strict_types=1);

namespace App\Classes\Payments;

use Auth;
use stdClass;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;


use App\Classes\Keymaster;
use App\Classes\Payments\PayFlowProTrait;
use App\Classes\Payments\PayPalNVPTrait;
use App\Models\Payments\PayFlowPro as Payment;
use App\Traits\CriticalFailTrait;
#use KKP\Laravel\Traits\HTTPClientTrait;


class PayFlowProObj
{

    use PayFlowProTrait, PayPalNVPTrait;
    use CriticalFailTrait;
    #use CriticalFailTrait, HTTPClientTrait;


    protected $_Payment;
    protected $_Response;

    protected $_http_timeout = 10;

    protected $_url;
    protected $_credentials;
    protected $_headers;
    protected $_nvp_str;


    public function __construct( Payment $Payment )
    {

        $this->_Payment = $Payment;
        $this->_url     = $this->PayFlowProURL();

    }


    public function TokenIsValid() : bool
    {

        if ( $this->_Payment->pp_token_count == 0 or $this->_Payment->pp_token_count >= 3 )
        {
            kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): false' );
            return false;
        }

        if ( $this->_Payment->pp_token_exp <= time() )
        {
            kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): false' );
            return false;
        }

        kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): true' );
        return true;

    }


    public function SetToken() : void
    {

        kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): Preparing' );

        //
        // assemble request
        //


        $this->_LoadCredentials();
        $this->_BuildTokenRequest();


        //
        // send it
        //


        $this->_Response = Http::timeout( $this->_http_timeout )
                        ->connectTimeout( $this->_http_timeout )
                              ->withBody( $this->_nvp_str, $this->_nvp_mimetype )
                                  ->post( $this->_url );


        //
        // parse / validate response
        //


        $decoded = $this->_ValidateReponse();


        //
        // success
        //   update/reset Payment
        //


        kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): Setting Token' );

        $this->_Payment->update([

            'pp_token_id'    => $decoded->SECURETOKENID,
            'pp_token'       => $decoded->SECURETOKEN,
            'pp_token_exp'   => time() + 1800, // 30 minutes
            'pp_token_count' => 1,

        ]);

        $this->_Payment->refresh();

    }



    public function IssueRefund()
    {

        kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): Preparing' );


        if ( ! $this->_Payment->completed_at )
        {
            self::CriticalFail( __METHOD__ . '(): Payment not complete' );
        }

        if ( $this->_Payment->refunded_at )
        {
            self::CriticalFail( __METHOD__ . '(): Payment already refunded' );
        }


        //
        // assemble request
        //


        $this->_LoadCredentials();
        $this->_BuildRefundRequest();


        //
        // send it
        //


        $this->_Response = Http::timeout( $this->_http_timeout )
                        ->connectTimeout( $this->_http_timeout )
                              ->withBody( $this->_nvp_str, $this->_nvp_mimetype )
                                  ->post( $this->_url );


        //
        // parse / validate response
        //


        $decoded = $this->_ValidateReponse( 'cc_refund_data' );


        //
        // success
        //   update Payment and Order
        //


        kkpdebug( 'PayFlowProObj', __FUNCTION__ . '(): Refund Issued' );


        //
        // update Payment, Order, CourseAuth
        //


        $timestamp = Carbon::now(); // use the same for all records


        $this->_Payment->update([
            'refunded_at' => $timestamp,
            'refunded_by' => Auth::id(),
        ]);
        $this->_Payment->refresh();


        $this->_Payment->Order->update([
            'refunded_at' => $timestamp,
            'refunded_by' => Auth::id(),
        ]);
        $this->_Payment->Order->refresh();


        if ( $this->_Payment->Order->CourseAuth )
        {
            $this->_Payment->Order->CourseAuth->update([
                'disabled_at'     => $timestamp,
                'disabled_reason' => 'Order refunded',
            ]);
            $this->_Payment->Order->CourseAuth->refresh();
        }
        else
        {
            logger( "Order has no CourseAuth -- PayFlowProID: {$this->_Payment->id} -- OrderID: {$this->_Payment->Order->id}" );
        }


    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected function _LoadCredentials() : void
    {

        if ( ! Keymaster::GetPayPalPayFlow( $this->_Payment->pp_is_sandbox )::IsSuccess() )
        {
            self::CriticalFail( 'PayFlowProObj->Keymaster: ' . Keymaster::Message() );
        }

        $this->_credentials = Keymaster::ResponseObj();

    }


    protected function _BuildTokenRequest() : void
    {

        //
        // note: PayFlow response includes SECURETOKENID
        //       this is $Payment->pp_token_id
        //       so we don't need to save it here
        //

        $return_route = route( 'payments.payflowpro.payment_return', $this->_Payment );

        $nvp_arr = [

            'CREATESECURETOKEN' => 'Y',
            'SECURETOKENID'     => self::GenTokenID(),
            'SILENTTRAN'        => 'TRUE',
            'VERBOSITY'         => ( $this->_Payment->pp_is_sandbox ? 'HIGH' : 'LOW' ),

            'TRXTYPE'           => 'S',
            'CURRENCY'          => 'USD',
            'AMT'               => $this->_Payment->total_price,
            'INVNUM'            => $this->_Payment->InvoiceID(),

            'RETURNURL'         => $return_route,
            'ERRORURL'          => $return_route,
            'CANCELURL'         => $return_route,

        ];

        $this->_nvp_str = self::EncodeNVP( array_merge( $nvp_arr, (array) $this->_credentials ) );

    }


    protected function _BuildRefundRequest()
    {

        $nvp_arr = [

            'TRXTYPE'   => 'C', // credit (refund)
            'ORIGID'    => $this->_Payment->pp_pnref,
            'VERBOSITY' => 'HIGH',

        ];

        $this->_nvp_str = self::EncodeNVP( array_merge( $nvp_arr, (array) $this->_credentials ) );

    }



    protected function _ValidateReponse( string $save_column = null ) : stdClass
    {

        $caller = 'PayFlowProObj::' . debug_backtrace()[1]['function'];


        $content = $this->_Response->getBody()->getContents();


        if ( ! $this->_Response->successful() )
        {
            #self::CriticalFail( "{$caller} [1]: {$content}" );
            self::CriticalFail( "{$caller} [1] - OrderID {$this->_Payment->order_id} - Not Successful - {$content}" );
        }

        /*
        if ( $this->_Response->header( 'Content-Type' ) != $this->_nvp_mimetype )
        {
            self::CriticalFail( "{$caller} [1]: Response is not {$this->_nvp_mimetype}" );
        }
        */
        // note: cast to string
        #if ( ! $this->IsNVPMimetype( (string) $this->_Response->header( 'Content-Type' ) ) )
        if ( ! self::IsNVPMimetype( (string) $this->_Response->header( 'Content-Type' ) ) )
        {
            self::CriticalFail( "{$caller} [1] - OrderID {$this->_Payment->order_id} - Response Content-Type '{$this->_Response->header( 'Content-Type' )}'" );
        }


        $decoded = self::DecodeNVP( $content );


        if ( $save_column )
        {
            $this->_Payment->update([ $save_column => $decoded ]);
        }


        if ( $decoded->RESULT !== '0' )
        {
            #self::CriticalFail( "{$caller} [2]: ({$decoded->RESULT}) {$decoded->RESPMSG}" );
            self::CriticalFail( "{$caller} [2] - OrderID {$this->_Payment->order_id} - RESULT not 0 - ({$decoded->RESULT}) {$decoded->RESPMSG}" );
        }


        return $decoded;

    }


}
