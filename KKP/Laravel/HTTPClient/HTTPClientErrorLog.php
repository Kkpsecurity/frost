<?php

namespace KKP\Laravel\HTTPClient;

use Auth;
use stdClass;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

use App\Models\HTTPClientError;
use KKP\Laravel\HTTPClient\HTTPClientErrorParams;


class HTTPClientErrorLog
{


    public static function ConnectException( ConnectException $e, HTTPClientErrorParams $HTTPClientErrorParams ) : HTTPClientError
    {

        $curl_errorno = ( $e->getHandlerContext()[ 'errno' ] ?? 0 );

        switch( $curl_errorno )
        {
            case  7: $error_msg = 'Connection Refused';      break;
            case 28: $error_msg = 'Connection Timed Out';    break;
            case 35: $error_msg = 'Connection Failed (SSL)'; break;
            default: $error_msg = "Connection Failed ({$curl_errorno})";
        }

        return HTTPClientError::create([

            'facility'	        => $HTTPClientErrorParams->facility,
            'action'	        => ( $HTTPClientErrorParams->action      ?? 'Unspecified' ),
            'user_id'	        => ( $HTTPClientErrorParams->user_id     ?? null ),
            'form_params'       => ( $HTTPClientErrorParams->form_params ?? null ),

            'request_method'    => $e->getRequest()->getMethod(),
            'url'               => $e->getRequest()->getUri(),

            'http_code'         => 0,
            'response_text'     => $error_msg,
            'curl_error'        => $e->getMessage(),

        ])->refresh();

    }


    public static function RequestException( RequestException $e, HTTPClientErrorParams $HTTPClientErrorParams ) : HTTPClientError
    {

        $Request      = $e->getRequest();
        $Response     = $e->getResponse();
        $ResponseBody = self::ParseResponseBody( $Response );

        return HTTPClientError::create([

            'facility'	        => $HTTPClientErrorParams->facility,
            'action'	        => ( $HTTPClientErrorParams->action      ?? 'Unspecified' ),
            'user_id'	        => ( $HTTPClientErrorParams->user_id     ?? null ),
            'form_params'       => ( $HTTPClientErrorParams->form_params ?? null ),

            'request_method'    => $Request->getMethod(),
            'url'               => $Request->getUri(),

            'http_code'         => $Response->getStatusCode(),
            'response_json'     => $ResponseBody->response_json,
            'response_text'     => $ResponseBody->response_text,

        ])->refresh();

    }



    #####################
    ###               ###
    ###   internals   ###
    ###               ###
    #####################


    private static function ParseResponseBody( $response ) : stdClass
    {

        $headers = $response->getHeaders();
        $body    = $response->getBody()->getContents();

        $result  = (object) [
            'response_json' => null,
            'response_text' => null,
        ];

        //
        // not JSON; done here
        //

        if ( strpos( $headers['Content-Type'][0], 'json' ) == false )
        {
            $result->response_text = $body;
            return $result;
        }



        $result->response_json = json_decode( $body, true );

        //
        // try to find error string in json data
        //

        foreach ([ 'message', 'error_description'] as $key )
        {
            if ( isset( $result->response_json[ $key ] ) )
            {
                $result->response_text = strip_tags( $result->response_json[ $key ] );
                break;
            }
        }

        return $result;

    }


}
