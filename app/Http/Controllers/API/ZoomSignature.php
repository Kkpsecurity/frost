<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class ZoomSignature
{


    public function __invoke( Request $Request )
    {

        $this->_ValidateCredentials();
        $this->_ValidateRequest( $Request );

        return response()->json([
            'signature' => $this->_GenerateSignature( $Request )
        ]);

    }


    private function _ValidateCredentials() : void
    {
        foreach ([ 'zoom.api_key', 'zoom.api_secret' ] as $config_key )
        {
            if ( ! config( $config_key ) )
            {
                logger( "ZoomSignature: Missing config( {$config_key} )" );
                api_abort( 500, "Missing config( {$config_key} )" );
            }
        }
    }


    private function _ValidateRequest( Request $Request ) : void
    {

        if ( ! $Request->isJson() )
        {
            logger( 'ZoomSignature: Invalid Request (JSON)' );
            api_abort( 400, 'Invalid Request (JSON)' );
        }

        if ( ! $Request->has( 'meetingNumber' ) )
        {
            logger( 'ZoomSignature: Missing meetingNumber' );
            api_abort( 400, 'Invalid Request (missing meetingNumber)' );
        }

        if ( ! $Request->has( 'role' ) )
        {
            logger( 'ZoomSignature: Missing role' );
            api_abort( 400, 'Invalid Request (missing role)' );
        }

    }


    private function _GenerateSignature( Request $Request ) : string
    {

        //
        // set start time 30s early to prevent clock skew issues
        //
        $iat = time() - 30;

        //
        // min: 30m
        // max: 48h
        //
        $exp = $iat + ( 60 * 60 * 2 );

        $payload = [
            // sdkKey is deprecated
            //   https://developers.zoom.us/docs/meeting-sdk/auth/
            //  'sdkKey' => config( 'zoom.api_key' ),
            'appKey'   => config( 'zoom.api_key' ),
            'mn'       => $Request->input( 'meetingNumber' ),
            'role'     => $Request->input( 'role' ),
            'iat'      => $iat,
            'exp'      => $exp,
            'tokenExp' => $exp,
        ];

        $signature = JWT::encode( $payload, config( 'zoom.api_secret' ), 'HS256' );

        return $signature;

    }


}
