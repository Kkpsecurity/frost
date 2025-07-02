<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

#use BoogieFromZk\AgoraToken\RtcTokenBuilder;
use BoogieFromZk\AgoraToken\RtcTokenBuilder2;

use App\RCache;
use App\Models\CourseDate;
use KKP\Laravel\Traits\AssertConfigTrait;


class AgoraRTCToken
{

    use AssertConfigTrait;

    protected $_config;


    public function __invoke( Request $Request )
    {

        #kkpdebug( 'AgoraRTCToken', 'AgoraRTCToken::__invoke' );

        $this->_config = self::AssertConfig( 'agora', [ 'app_id', 'certificate', 'rtc.expire_minutes' ] );

        $this->_ValidateRequest( $Request );

        kkpdebug( 'AgoraRTCToken',
            'course_date_id ' . $Request->input( 'course_date_id' )
                . ' user_id ' . $Request->input( 'user_id' )
                   . ' role ' . $Request->input( 'role' )
        );

        return response()->json( $this->_GenerateToken( $Request ) );

    }


    protected function _ValidateRequest( Request $Request ) : void
    {
        foreach ([ 'course_date_id', 'user_id', 'role' ] as $key )
        {
            if ( ! $Request->has( $key ) )
            {
                kkpdebug( 'AgoraRTCToken', "Missing {$key}" );
                api_abort( 400, "Invalid Request: Missing {$key}" );
            }
        }
    }


    protected function _GenerateToken( Request $Request ) : array
    {

        $app_id         = $this->_config->app_id;
        $certificate    = $this->_config->certificate;

        $channel_name   = Str::Slug( CourseDate::findOrFail( $Request->input( 'course_date_id' ) )->GetCourse()->title );

        $uid            = $Request->input( 'user_id' );

        #$role           = $Request->input( 'role' ) == 'publisher'
        #                    ? RtcTokenBuilder::RolePublisher
        #                    : RtcTokenBuilder::RoleSubscriber;

        $role           = $Request->input( 'role' ) == 'publisher'
                            ? RtcTokenBuilder2::ROLE_PUBLISHER
                            : RtcTokenBuilder2::ROLE_SUBSCRIBER;

        $expires        = time() + ( $this->_config->rtc->expire_minutes * 60 );


        #if ( ! $token = RtcTokenBuilder::buildTokenWithUid( $app_id, $certificate, $channel_name, $uid, $role, $expires ) )
        if ( ! $token = RtcTokenBuilder2::buildTokenWithUid( $app_id, $certificate, $channel_name, $uid, $role, $expires ) )
        {
            kkpdebug( 'AgoraRTCToken', 'Failed to generate token' );
            api_abort( 500, 'Failed to generate token' );
        }


        kkpdebug( 'AgoraRTCToken', "UID {$uid} returning channelName {$channel_name} token {$token}" );

        return [
            'channelName' => $channel_name,
            'token'       => $token,
        ];

    }


}
