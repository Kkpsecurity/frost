<?php

namespace KKP\Laravel\HTTPClient;

use Exception;
use stdClass;

use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Exception\ConnectException as HTTPConnectException;
use GuzzleHttp\Exception\RequestException as HTTPRequestException;
use KKP\Laravel\HTTPClient\HTTPClientErrorLog;
use KKP\Laravel\HTTPClient\HTTPClientErrorParams;


class SignedRequestTk
{


    private $_HTTPClient;
    private $_HTTPClientErrorParams;

    private $_http_timeout   = 10;
    private $_http_ignoressl = false;
    private $_http_curl_opts = [];
    private $_http_headers   = [];


    //
    // client data
    //

    public $signature;


    //
    // server response
    //

    private $_response;

    //
    // these require validation
    //

    private $_url;
    private $_privkey;
    private $_pubkey;
    private $_algo = 'sha512';  // valid list: openssl_get_md_methods()



    public function __construct( $url = null, $allow_insecure = false )
    {

        $this->SetURL( $url, $allow_insecure );

        //
        // verify openssl functions
        //

        foreach([ 'openssl_get_md_methods', 'openssl_sign', 'openssl_verify' ] as $function )
        {
            if ( ! function_exists( $function ) )
            {
                throw new Exception( "PHP Function {$function} not available" );
            }
        }

        //
        // initialize HTTPClient
        //

        $this->_HTTPClient = new HTTPClient();

        $this->_HTTPClientErrorParams = new HTTPClientErrorParams( 'SignedRequestTk' );


        return $this;

    }


    public function SetURL( string $url = null, bool $allow_insecure = false  ) : self
    {

        if ( $url )
        {
            if ( ! $allow_insecure && strpos( $url, 'https' ) !== 0 )
            {
                throw new Exception( get_class() . " :: URL must be https ({$url})" );
            }
            else
            {
                $this->_url = $url;
            }
        }

        return $this;

    }


    public function SetAlgo( string $algo ) : self
    {

        // TODO: review this
        if ( ! in_array( $algo, openssl_get_md_methods() ) )
        {
            throw new Exception( "Invalid Signature Algorithm '{$algo}'" );
        }
        else
        {
            $this->_algo = $algo;
        }
        return $this;

    }


    public function SetHTTPTimeout( int $seconds ) : self
    {
        $this->_http_timeout = $seconds;
        return $this;
    }


    public function SetHTTPIgnoreSSL( bool $val = true ) : self
    {
        $this->_http_ignoressl = $val;
        return $this;
    }


    public function SetHTTPErrorFacility( string $facility ) : self
    {
        $this->_HTTPClientErrorParams->facility = $facility;
        return $this;
    }


    public function SetHTTPErrorAction( string $action ) : self
    {

        $this->_HTTPClientErrorParams->action = $action;
        return $this;
    }


    public function AddCurlOpts( array $opts ) : self
    {
        foreach ( $opts as $key => $val )
        {
            $this->_http_curl_opts[ $key ] = $val;
        }
        return $this;
    }


    public function AddHTTPHeaders( array $headers ) : self
    {
        foreach( $headers as $key => $value )
        {
            $this->_http_headers[ $key ] = $value;
        }
        return $this;
    }



    ################
    ###          ###
    ###   keys   ###
    ###          ###
    ################


    public function SetPrivKey( $data ) : self
    {
        $this->_ValidatePrivKey( $data );
        return $this;
    }


    public function SetPubKey( $data ) : self
    {
        $this->_ValidatePubKey( $data );
        return $this;
    }


    public function LoadPrivKeyFile( $filename ) : self
    {

        if ( $data = $this->_ValidateKeyFile( $filename ) )
        {
            $this->_ValidatePrivKey( $data );
        }

        return $this;

    }


    public function LoadPubKeyFile( $filename ) : self
    {

        if ( $data = $this->_ValidateKeyFile( $filename ) )
        {
            $this->_ValidatePubKey( $data );
        }

        return $this;

    }



    ##################
    ###            ###
    ###   client   ###
    ###            ###
    ##################


    public function GenSignature( &$payload ) : void
    {
        openssl_sign( $payload, $this->signature, $this->_privkey, $this->_algo );
    }


    public static function GenEntropy( $length = 64 ) : string
    {
        return substr( bin2hex( openssl_random_pseudo_bytes( ( $length / 2 ) + 1 ) ), 0, $length );
    }


    public function SendRequest( &$payload, $payload_name = 'payload', $signature_name = 'signature' ) : self
    {


        $this->_ResetResponse();

        if ( $validation_error = $this->_ValidateClient( $payload ) )
        {
            $this->_response->error = $validation_error;
            return $this;
        }


        //
        // generate signature
        //

        $this->GenSignature( $payload );


        //
        // cURL options
        //

        $curl_opts = $this->_http_curl_opts;

        if ( $this->_http_ignoressl )
        {
            $curl_opts[ CURLOPT_SSL_VERIFYHOST ] = false;
            $curl_opts[ CURLOPT_SSL_VERIFYPEER ] = false;
        }


        //
        // form params
        //

        $form_params = [
            $payload_name   => $payload,
            $signature_name => base64_encode( $this->signature ),
        ];


        $this->_HTTPClientErrorParams->form_params = $form_params;


        //
        // contact server
        //

        try {

            $HTTPResponse = $this->_HTTPClient->request( 'POST', $this->_url, [
                'timeout'     => $this->_http_timeout,
                'headers'     => $this->_http_headers,
                'curl'        => $curl_opts,
                'form_params' => $form_params,
            ]);

            $this->_response->is_success = true;
            $this->_response->http_code  = $HTTPResponse->getStatusCode();
            $this->_response->content    = $HTTPResponse->getBody()->getContents();
            $this->_response->is_json    = ( strpos( $HTTPResponse->getHeaders()['Content-Type'][0], 'json' ) != false );

            return $this;

        }
        catch ( HTTPConnectException $e )
        {
            $HTTPClientError = HTTPClientErrorLog::ConnectException( $e, $this->_HTTPClientErrorParams );
        }
        catch ( HTTPRequestException $e )
        {
            $HTTPClientError = HTTPClientErrorLog::RequestException( $e, $this->_HTTPClientErrorParams );
        }

        $this->_response->http_code = $HTTPClientError->http_code;
        $this->_response->error_msg = $HTTPClientError->response_text;

        return $this;

    }


    public function GetResponse() : stdClass
    {
        return clone $this->_response;
    }



    ##################
    ###            ###
    ###   server   ###
    ###            ###
    ##################


    public function ValidSignature( $payload, $signature ) : bool
    {

        if ( $msg = $this->_ValidateServer( $payload, $signature ) )
        {
            throw new Exception( $msg );
            return false;
        }

        return ( 1 == openssl_verify( $payload, base64_decode($signature), $this->_pubkey, $this->_algo ) );

    }



    ######################
    ###                ###
    ###   validators   ###
    ###                ###
    ######################


    private function _ValidatePrivKey( &$data ) : bool
    {

        if ( strpos( $data, '-----BEGIN PRIVATE KEY-----' ) === 0 )
        {
            $this->_privkey = $data;
            return true;
        }

        throw new Exception( 'Invalid Private Key' );
        return false;

    }


    private function _ValidatePubKey( &$data ) : bool
    {

        if ( strpos( $data, '-----BEGIN PUBLIC KEY-----' ) === 0 )
        {
            $this->_pubkey = $data;
            return true;
        }

        throw new Exception( 'Invalid Public Key' );
        return false;

    }


    private function _ValidateKeyFile( $filename ) : ?string
    {

        if ( ! file_exists( $filename ) )
        {
            throw new Exception( "File not found: {$filename}" );
            return null;
        }


        $readMsgs = [
            'no such file'      => 'File not found',
            'permission denied' => 'Permission denied',
            'operation failed'  => 'Operation failed',   // $filename was URI
        ];


        if ( false === ( $filedata = @file_get_contents( $filename ) ) )
        {

            $failMsg = '[Unknown Error]';

            if ( ! $error = error_get_last() )
            {
                //
                // framework has captured errors ?
                //
                throw new Exception( "Failed to read key file: {$failMsg}" );
                return null;
            }

            //
            // return better message
            //
            foreach( $readMsgs as $pattern => $msg )
            {
                if ( stripos( $error['message'], $pattern ) !== false )
                {
                    $failMsg = $msg;
                }
            }

            throw new Exception( "Failed to read key file: {$failMsg}" );
            return null;

        }
        else if ( ! $filedata )
        {

            throw new Exception( "Failed to read key file: Empty File" );
            return null;

        }

        return $filedata;

    }


    private function _ValidateClient( &$payload ) : ?string
    {

        if ( empty($payload)   ) return 'Empty Payload';
        if ( ! $this->_url     ) return 'No URL Specified';
        if ( ! $this->_privkey ) return 'No Private Key';

        if ( ! is_string($payload) )
        {
            return 'Payload must be a string; type: ' . gettype($payload);
        }

        return null;

    }


    private function _ValidateServer( &$payload, &$signature ) : ?string
    {

        if ( empty($payload)   ) return 'Empty Payload';
        if ( empty($signature) ) return 'Empty Signature';
        if ( ! $this->_pubkey  ) return 'No Public Key';

        return null;

    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    private function _ResetResponse() : void
    {
        $this->_response = (object) [

            'is_success' => false,
            'error_msg'  => null,
            'http_code'  => 0,
            'content'    => null,
            'is_json'    => false,

        ];
    }


}
