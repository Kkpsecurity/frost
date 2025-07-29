<?php

namespace App\Classes\Utilities;

use stdClass;
use Exception;
use Illuminate\Support\Facades\Http;


class SignedRequestTk
{

    protected $_http_timeout = 10;
    protected $_http_headers = [];


    //
    // client data
    //

    public $signature;


    //
    // server response
    //

    protected $_response;


    //
    // these require validation
    //

    protected $_url;
    protected $_privkey;
    protected $_pubkey;
    protected $_algo = 'sha512';  // valid list: openssl_get_md_methods()



    public function __construct(string $url = null)
    {

        $this->SetURL($url);

        //
        // verify openssl functions
        //

        foreach (['openssl_get_md_methods', 'openssl_sign', 'openssl_verify'] as $function) {
            if (! function_exists($function)) {
                throw new Exception("PHP Function {$function} not available");
            }
        }
    }


    public function SetURL(string $url = null): self
    {

        if ($url) {
            if (strpos($url, 'https') !== 0) {
                throw new Exception(get_class() . " :: URL must be https ({$url})");
            } else {
                $this->_url = $url;
            }
        }

        return $this;
    }


    public function SetAlgo(string $algo): self
    {

        if (! in_array($algo, openssl_get_md_methods())) {
            throw new Exception("Invalid Signature Algorithm '{$algo}'");
        } else {
            $this->_algo = $algo;
        }

        return $this;
    }


    public function SetHTTPTimeout(int $seconds): self
    {
        $this->_http_timeout = $seconds;
        return $this;
    }


    public function AddHTTPHeaders(array $headers): self
    {

        foreach ($headers as $key => $value) {
            $this->_http_headers[$key] = $value;
        }

        return $this;
    }



    ################
    ###          ###
    ###   keys   ###
    ###          ###
    ################


    public function SetPrivKey($data): self
    {
        $this->_ValidatePrivKey($data);
        return $this;
    }


    public function SetPubKey($data): self
    {
        $this->_ValidatePubKey($data);
        return $this;
    }


    public function LoadPrivKeyFile($filename): self
    {

        if ($data = $this->_ValidateKeyFile($filename)) {
            $this->_ValidatePrivKey($data);
        }

        return $this;
    }


    public function LoadPubKeyFile($filename): self
    {

        if ($data = $this->_ValidateKeyFile($filename)) {
            $this->_ValidatePubKey($data);
        }

        return $this;
    }



    ##################
    ###            ###
    ###   client   ###
    ###            ###
    ##################


    public function GenSignature(&$payload): void
    {
        openssl_sign($payload, $this->signature, $this->_privkey, $this->_algo);
    }


    public static function GenEntropy($length = 64): string
    {
        return substr(bin2hex(openssl_random_pseudo_bytes(($length / 2) + 1)), 0, $length);
    }


    public function SendRequest(&$payload, $payload_name = 'payload', $signature_name = 'signature'): self
    {


        $this->_ResetResponse();

        if ($validation_error = $this->_ValidateClient($payload)) {
            $this->_response->content = $validation_error;
            return $this;
        }


        //
        // generate signature
        //

        $this->GenSignature($payload);


        //
        // form params
        //

        $form_params = [

            $payload_name   => $payload,
            $signature_name => base64_encode($this->signature),

        ];


        //
        // send request
        //

        $Response = Http::withHeaders($this->_http_headers)
            ->timeout($this->_http_timeout)
            ->connectTimeout($this->_http_timeout)
            ->asForm()->post($this->_url, $form_params);

        //
        // parse response
        //

        $this->_response->is_success = $Response->successful();
        $this->_response->http_code  = $Response->getStatusCode();
        $this->_response->content    = $Response->getBody()->getContents();
        $this->_response->is_json    = $Response->header('Content-Type') == 'application/json';


        return $this;
    }


    public function GetResponse(): stdClass
    {
        return clone $this->_response;
    }



    ##################
    ###            ###
    ###   server   ###
    ###            ###
    ##################


    public function ValidSignature($payload, $signature): bool
    {

        if ($msg = $this->_ValidateServer($payload, $signature)) {
            throw new Exception($msg);
            return false;
        }

        return (1 == openssl_verify($payload, base64_decode($signature), $this->_pubkey, $this->_algo));
    }



    ######################
    ###                ###
    ###   validators   ###
    ###                ###
    ######################


    protected function _ValidatePrivKey(&$data): bool
    {

        if (strpos($data, '-----BEGIN PRIVATE KEY-----') === 0) {
            $this->_privkey = $data;
            return true;
        }

        throw new Exception('Invalid Private Key');
        return false;
    }


    protected function _ValidatePubKey(&$data): bool
    {

        if (strpos($data, '-----BEGIN PUBLIC KEY-----') === 0) {
            $this->_pubkey = $data;
            return true;
        }

        throw new Exception('Invalid Public Key');
        return false;
    }


    protected function _ValidateKeyFile($filename): ?string
    {

        if (! file_exists($filename)) {
            throw new Exception("File not found: {$filename}");
            return null;
        }


        $readMsgs = [
            'no such file'      => 'File not found',
            'permission denied' => 'Permission denied',
            'operation failed'  => 'Operation failed',   // $filename was URI
        ];


        if (false === ($filedata = @file_get_contents($filename))) {

            $failMsg = '[Unknown Error]';

            if (! $error = error_get_last()) {
                //
                // framework has captured errors ?
                //
                throw new Exception("Failed to read key file: {$failMsg}");
                return null;
            }

            //
            // return better message
            //
            foreach ($readMsgs as $pattern => $msg) {
                if (stripos($error['message'], $pattern) !== false) {
                    $failMsg = $msg;
                }
            }

            throw new Exception("Failed to read key file: {$failMsg}");
            return null;
        } else if (! $filedata) {

            throw new Exception("Failed to read key file: Empty File");
            return null;
        }

        return $filedata;
    }


    protected function _ValidateClient(&$payload): ?string
    {

        if (empty($payload)) return 'Empty Payload';
        if (! $this->_url) return 'No URL Specified';
        if (! $this->_privkey) return 'No protected Key';

        if (! is_string($payload)) {
            return 'Payload must be a string; type: ' . gettype($payload);
        }

        return null;
    }


    protected function _ValidateServer(&$payload, &$signature): ?string
    {

        if (empty($payload)) return 'Empty Payload';
        if (empty($signature)) return 'Empty Signature';
        if (! $this->_pubkey) return 'No Public Key';

        return null;
    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    protected function _ResetResponse(): void
    {
        $this->_response = (object) [

            'is_success' => false,
            'http_code'  => 0,
            'content'    => null,
            'is_json'    => false,

        ];
    }
}
