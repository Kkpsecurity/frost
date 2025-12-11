<?php
declare(strict_types=1);

namespace App\Classes\Payments;

use stdClass;
use Illuminate\Support\Facades\Http;

use App\Classes\Keymaster;
use App\Classes\Payments\PayPalHelpersTrait;
use App\Models\Payments\PaymentModel;
use App\Traits\CriticalFailTrait;
use KKP\Laravel\Traits\HTTPClientTrait;


class PayPalRESTObj
{

    use PayPalHelpersTrait;
    use CriticalFailTrait, HTTPClientTrait;


    protected $_Payment;
    protected $_Response;

    protected $_http_timeout = 10;
    protected $_auth_headers = [];

    protected $_url;
    protected $_credentials;
    protected $_headers;



    public function __construct( PaymentModel $Payment )
    {
        $this->_Payment = $Payment;
    }



    public function GetSaleDetails() : void
    {

        kkpdebug( 'PayPalRESTObj', __FUNCTION__ . '(): Preparing' );


        if ( ! $this->_Payment->pp_ppref )
        {
            logger( __METHOD__ . "(): OrderID {$this->_Payment->Order->id} has no pp_ppref" );
            return;
        }


        //
        // assemble request
        //


        $this->_url = self::PayPal_REST_SaleURL_v1();
        $this->_LoadCredentials();
        $this->_BuildAuthHeaders_v1();


        //
        // send it
        //


        $this->_Response = Http::withHeaders( $this->_auth_headers )
                            ->connectTimeout( $this->_http_timeout )
                                   ->timeout( $this->_http_timeout )
                                       ->get( $this->_url );


        //
        // parse / validate response
        //


        $decoded = $this->_ValidateReponse();


        //
        // success
        //   update Payment
        //


        kkpdebug( 'PayPalRESTObj', __FUNCTION__ . "(): OrderID {$this->_Payment->Order->id} updated sale details. Fee: {$decoded->transaction_fee->value}" );


        $this->_Payment->update([
            'cc_amount'     => $decoded->amount->total,
            'cc_fee'        => $decoded->transaction_fee->value,
            'cc_transtime'  => self::Date_to_UTC( $decoded->update_time ),
        ]);

        $this->_Payment->refresh();


    }




    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected function _LoadCredentials() : void
    {

        if ( ! Keymaster::GetPayPalREST( $this->_Payment->pp_is_sandbox )::IsSuccess() )
        {
            self::CriticalFail( 'PayFlowRESTObj->Keymaster: ' . Keymaster::Message() );
        }

        $this->_credentials = Keymaster::ResponseObj();

    }


    protected function _BuildAuthHeaders_v1() : void
    {

        $this->_auth_headers = [

            'Content-Type'  => 'application/json',
            'Authorization' => "Bearer {$this->_credentials->token}",

        ];

    }


    protected function _ValidateReponse() : ?stdClass
    {

        $caller = 'PayFlowRESTObj::' . debug_backtrace()[1]['function'];

        $content = $this->_Response->getBody()->getContents();

        if ( ! $this->_Response->successful() )
        {
            self::CriticalFail( "{$caller} [1]: {$content}" );
        }

        if ( ! self::ResponseIsJSON( $this->_Response ) )
        {
            self::CriticalFail( "{$caller} [1]: Response is not JSON" );
        }

        return json_decode( $content );

    }



    /*
    protected function _BuildAuthHeaders_v2() : void
    {

        $this->_http_headers = [

            'Content-Type'  => 'application/json',
            'Authorization' => "Basic {$this->_credentials->clientid}:{$this->_credentials->token}",

        ];

    }
    */

}
