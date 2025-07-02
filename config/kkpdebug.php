<?php

return [

    /**
     *
     * System
     *
     */

    'Guard'     => false,
    'Observer'  => true,
    'Router'    => false,
    'UserEvent' => false,  // app/Listeners/UserEventSubscriber

    'Notification' => true,


    /**
     *
     * Registered singletons
     *
     */

    'RCache'        => true,
    'RCacheWarmer'  => true,
    'RCacheDebug'   => false,
    'RCacheRedis'   => false,

    'Keymaster'     => false,


    /**
     *
     * Classes etc
     *
     */

    'ExamAuthObj'   => true,

    'PayFlowProObj' => true,
    'PayPalRESTObj' => true,
    'OrderCalc'     => true,

    'Challenger'    => true,

    'Challenger_Msg' => true,
    'Challenger_ERR' => true,
    'Challenger_Dbg' => false,
    'Challenger_eol' => true,  // EOL debug

    'CAO_Msg'        => false,
    'CAO_Dbg'        => false,

    'ClassroomQueries'     => true,
    'ClassroomQueries_Dbg' => false,

    'TrackingQueries_Msg' => false,
    'TrackingQueries_Dbg' => false,

    'VideoCallRequest' => false,


    /**
     *
     * Controllers
     *
     */

    'AgoraRTCToken' => true,
    'AgoraRTMToken' => true,
    #'Enrollment'    => true,
    'SATTest'       => true,


];
