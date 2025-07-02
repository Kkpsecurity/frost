<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use BoogieFromZk\AgoraToken\RtmTokenBuilder2;

use App\RCache;
use App\Models\CourseDate;
use KKP\Laravel\Traits\AssertConfigTrait;


class AgoraRTMToken
{

    use AssertConfigTrait;

    protected $_config;


    public function __invoke( Request $Request )
    {

        #kkpdebug( 'AgoraRTMToken', 'AgoraRTMToken::__invoke' );

        $this->_config = self::AssertConfig( 'agora', [ 'app_id', 'certificate', 'rtm.expire_minutes' ] );

        $this->_ValidateRequest( $Request );

        kkpdebug( 'AgoraRTMToken',
            'course_date_id ' . $Request->input( 'course_date_id' )
                . ' user_id ' . $Request->input( 'user_id' )
        );

        return response()->json( $this->_GenerateToken( $Request ) );

    }


    protected function _ValidateRequest( Request $Request ) : void
    {
        foreach ([ 'course_date_id', 'user_id' ] as $key )
        {
            if ( ! $Request->has( $key ) )
            {
                kkpdebug( 'AgoraRTMToken', "Missing {$key}" );
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

        $expires        = time() + ( $this->_config->rtm->expire_minutes * 60 );

        if ( ! $token = RtmTokenBuilder2::buildToken( $app_id, $certificate, $uid, $expires ) )
        {
            kkpdebug( 'AgoraRTMToken', 'Failed to generate token' );
            api_abort( 500, 'Failed to generate token' );
        }


        kkpdebug( 'AgoraRTMToken', "UID {$uid} returning channelName {$channel_name} token {$token}" );

        return [
            'channelName' => $channel_name,
            'token'       => $token,
            'uid'         => $uid,
        ];


    }


}
