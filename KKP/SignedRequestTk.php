<?php

namespace KKP;

use Exception;


class SignedRequestTk
{

    //
    // cURL options
    //

    public $curl_ignoressl = false;
    public $curl_timeout   = 10;
    public $curl_opts      = [];
    public $curl_headers;


    //
    // client data
    //

    public $signature;

    //
    // server response
    //

    public $http_code = 0;
    public $response  = null;
    public $is_json   = false;

    //
    // these require validation
    //

    private $_url;
    private $_privkey;
    private $_pubkey;
    private $_algo = 'sha512';  // valid list: openssl_get_md_methods()



    public function __construct( $url = null, $allow_insecure = false )
    {

        //
        // verify openssl and cURL are available
        //

        foreach([ 'openssl_get_md_methods', 'openssl_sign', 'openssl_verify', 'curl_init' ] as $function )
        {
            if ( ! function_exists( $function ) )
            {
                throw new Exception( "PHP Function {$function} not available" );
            }
        }

        if ( $url ) $this->SetURL( $url, $allow_insecure );

        return $this;

    }


    public function SetURL( $url, $allow_insecure = false  ) : self
    {

        if ( ! $allow_insecure && strpos( $url, 'https' ) !== 0 )
        {
            throw new Exception( get_class() . " :: URL must be https ({$url})" );
        }
        else
        {
            $this->_url = $url;
        }

        return $this;

    }


    public function SetAlgo( $algo ) : self
    {

        if ( in_array( $algo, openssl_get_md_methods() ) )
        {
            $this->_algo = $algo;
            return $this;
        }

        throw new Exception( "Invalid Signature Algorithm '{$algo}'" );

    }


    public function SetCurlIgnoreSSL( $bool ) : self
    {
        $this->curl_ignoressl = (bool) $bool;
        return $this;
    }


    public function SetCurlTimeout( $seconds ) : self
    {
        $this->curl_timeout = intval( $seconds );
        return $this;
    }


    public function SetCurlOpts( $arr ) : self
    {

        foreach ( $arr as $key => $val )
        {
            $this->curl_opts[ $key ] = $val;
        }

        return $this;

    }


    public function SetCurlHeaders( $value ) : self
    {
        $this->curl_headers = ( is_array( $value ) ? $value : [ $value ] );
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


    public function SendRequest( &$payload, $payload_name = 'payload', $signature_name = 'signature' ) : void
    {

        $this->http_code = 0;
        $this->response  = null;
        $this->is_json   = false;

        if ( $msg = $this->_ValidateClient( $payload ) )
        {
            throw new Exception( $msg );
            return;
        }


        //
        // generate signature
        //

        $this->GenSignature( $payload );


        //
        // prepare request
        //

        $request = [
            $payload_name   => $payload,
            $signature_name => base64_encode( $this->signature )
        ];


        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL,            $this->_url );
        curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $this->curl_timeout );
        curl_setopt( $curl, CURLOPT_POST,           true  );
        curl_setopt( $curl, CURLOPT_POSTFIELDS,     $request );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true  );  // don't output response to browser
        curl_setopt( $curl, CURLOPT_FAILONERROR,    false );  // get error message from server

        //
        // additional cURL options
        //

        if ( $this->curl_ignoressl )
        {
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        }

        foreach ( $this->curl_opts as $key => $val )
        {
            curl_setopt( $curl, $key, $val );
        }


        //
        // add HTTP headers
        //

        if ( $this->curl_headers )
        {
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $this->curl_headers );
        }


        //
        // contact server
        //

        $this->response  = curl_exec( $curl );
        $this->http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );


        //
        // handle known http responses
        //

        if ( $this->http_code == 0 )
        {
            $this->response = 'cURL: unable to connect';
        }


        //
        // set is_json
        //

        if ( 'application/json' == curl_getinfo( $curl, CURLINFO_CONTENT_TYPE ) )
        {
            $this->is_json = true;
        }

        // JSONP - probably should not be accepting this
        if ( 'application/javascript' == curl_getinfo( $curl, CURLINFO_CONTENT_TYPE ) )
        {
            $this->is_json = true;
        }

    }


    public static function GenEntropy( $length = 64 ) : string
    {
        return substr( bin2hex( openssl_random_pseudo_bytes( ( $length / 2 ) + 1 ) ), 0, $length );
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


}
