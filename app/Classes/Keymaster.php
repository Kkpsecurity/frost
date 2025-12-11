<?php
declare(strict_types=1);

namespace App\Classes;

use stdClass;
use Exception;

use KKP\Laravel\SignedRequestTk;
use KKP\Laravel\Traits\AssertConfigTrait;


class Keymaster
{

    use AssertConfigTrait;


    protected static $_config;
    protected static $_payload;
    protected static $_response;
    protected static $_SignedRequest;


    public static function boot() : self
    {

        if ( is_object( self::$_config ) )
        {
            // already booted
            return new self();
        }


        self::_ResetResponse();


        //
        // load config
        //

        self::$_config = self::AssertConfig( 'keymaster', [ 'url', 'timeout', 'account', 'clientid' ] );


        //
        // initialize payload
        //

        self::$_payload = (object) [
            'account'   => self::$_config->account,
            'sandbox'   => false,
        ];


        //
        // initialize SignedRequest
        //

        try {

            self::$_SignedRequest = new SignedRequestTk( self::$_config->url );

            self::$_SignedRequest
                ->SetHTTPTimeout( self::$_config->timeout )
                ->AddHTTPHeaders([ 'X-Auth-ID' => self::$_config->clientid ])
                ->LoadPrivKeyFile( self::_CertFile() );

        } catch ( Exception $e ) {

            kkpdebug( 'Keymaster', "SignedRequest init failed: {$e->getMessage()}" );

            self::$_response->is_success = false;
            self::$_response->content    = $e->getMessage();

        }

        return new self();

    }


    public static function SetAccount( string $account ) : self
    {
        self::boot();
        kkpdebug( 'Keymaster', "Setting account '{$account}'" );
        self::$_payload->account = $account;
        return new self();
    }


    public static function SetSandbox( bool $is_sandbox ) : self
    {
        self::boot();
        kkpdebug( 'Keymaster', 'Setting sandbox ' . ( $is_sandbox ? 'TRUE' : 'FALSE' ) );
        self::$_payload->sandbox = $is_sandbox;
        return new self();
    }


    public static function GetPayPalREST( bool $is_sandbox ) : self
    {
        return self::SetSandbox( $is_sandbox )::_Send( 'Get PayPal REST' );
    }


    public static function GetPayPalPayFlow( bool $is_sandbox ) : self
    {
        return self::SetSandbox( $is_sandbox )::_Send( 'Get PayPal PayFlow' );
    }



    ###########################
    ###                     ###
    ###   retrieve result   ###
    ###                     ###
    ###########################


    public static function Response() : stdClass
    {
        return clone self::$_response;
    }

    public static function IsSuccess() : bool
    {
        return (bool) self::$_response->is_success;
    }

    public static function Message() : ?string
    {
        return self::$_response->content;
    }

    public static function ResponseObj() : ?stdClass
    {
        return clone self::$_response->content_obj;
    }



    ###################
    ###             ###
    ###   send it   ###
    ###             ###
    ###################


    protected static function _Send( string $action ) : self
    {

        kkpdebug( 'Keymaster', "Preparing {$action}" );


        if ( ! self::$_response->is_success )
        {
            // SignedRequestTk failed
            return new self();
        }


        self::$_payload->action  = $action;
        self::$_payload->entropy = SignedRequestTk::GenEntropy();

        self::_ResetResponse();


        //
        // send request and parse response
        //

        $time_start = microtime(true);

        $payload_json = json_encode( self::$_payload );


        $Response = self::$_SignedRequest->SendRequest( $payload_json )
                                         ->GetResponse();

        self::$_response->is_success = $Response->is_success;
        self::$_response->http_code  = $Response->http_code;
        self::$_response->content    = $Response->content;
        self::$_response->is_json    = $Response->is_json;


        if ( $Response->is_json )
        {
            self::$_response->content_obj = json_decode( $Response->content );
        }

        if ( ! $Response->is_success )
        {
            kkpdebug( 'Keymaster', "  SignedRequest->SendRequest() FAILURE '{$Response->content}'"  );
        }
        else
        {
            kkpdebug( 'Keymaster', '  SignedRequest->SendRequest() SUCCESS' );
        }



        $response_msec = sprintf( '%0.3f', ( microtime(true) - $time_start ) );

        kkpdebug( 'Keymaster', 'Completed ' . self::$_payload->action . " in {$response_msec}ms" );

        return new self();

    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected static function _ResetResponse() : void
    {
        self::$_response = (object) [

            'is_success'  => true,
            'http_code'   => 0,
            'content'     => '',
            'content_obj' => null,
            'is_json'     => false,

        ];
    }


    protected static function _CertFile() : string
    {
        return storage_path( 'certs/' . self::$_config->clientid . '.privkey' );
    }



    #####################
    ###               ###
    ###   debugging   ###
    ###               ###
    #####################


    public static function DumpPayload()
    {
        return clone self::boot()::$_payload;
    }

    public static function DumpResponse()
    {
        return clone self::boot()::$_response;
    }

    public static function DumpSignedRequest()
    {
        return clone self::boot()::$_SignedRequest;
    }


}
