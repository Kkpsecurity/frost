<?php

namespace App\Classes\Frost;

/**
 * @file ZoomMeetingApi.php
 * @brief Class for interacting with Zoom Meeting API.
 * @details Provides methods to create and manage Zoom meetings.
 */

use Exception;
use stdClass;
use Carbon\Carbon;
use MacsiDigital\Zoom\Facades\Zoom;
use MacsiDigital\Zoom\Requests\StoreMeeting;

class ZoomMeetingApi
{

    /**
     * @Note: API Limit 100 create requests per 24hrs
     *
     * @param $meetingObj
     * @return string
     */
    public function createZoomMeeting(stdClass $zoomprarms)
    {

        /**
         * Imstructors - Retrieve The Zoom User Account
         * @note: Your email account must match your zoom email account
         */
        $user = Zoom::user()->find(Auth()->user()->email);

        if ($user == null) {
            return 'account-not-found';
        } else {

            try {

                /**
                 * Create the Meeting
                 */
                $meeting = Zoom::meeting()->make([
                    # The Title
                    "topic"                             => $zoomprarms->title,

                    # The Type: (Options: 1, 2, 3, 8) Zoom Default 2
                    # 1. Instant Meeting
                    # 2. Scheduled Meeting
                    # 3. Recurring Meeting
                    # 8. Recurring Meeting with fixed time
                    "type"                              => 2,

                    # Requires Registration
                    'registered'                        => true,

                    # Start Date
                    "date"                              => $zoomprarms->start_date,

                    # Duration of meeting 10hrs
                    "duration"                          => $zoomprarms->duration,

                    # Start Time
                    "start_time"                        => $zoomprarms->start_time,

                    # Status
                    "status"                            => 'waiting',

                    # TimeZone
                    "timezone"                          => "America/New_York"
                ]);

                /**
                 * The Meeting Settings
                 */
                $meeting->settings()->make([
                    'host_video'                        => true,
                    "participant_video"                 => true,

                    # Join Before Host - Valid for meeting type 2 and 3
                    'join_before_host'                  => false,

                    # Options (0, 5, 10) in minutes
                    'jbh_time'                          => 0,

                    # Register for each Occurrence
                    # Note: To enable registration required, set approval type to 0 or 1
                    # 0 Automatic Approval
                    # 1 Manuel Approval
                    'approval_type'                     => 0,

                    # Enable Waiting Room
                    'waiting_room'                      => false,

                    # Registration Type
                    # Used With Recurring Meeting ONLY
                    # 1. User Register Once and Can join any Meeting
                    # 2. User need to register for each meeting
                    # 3. User Registers once and can choice one or more meeting to attempted
                    'registration_type'                 => 1,
                    'meeting_authentication'            => false,
                    "registrants_email_notification"    => false
                ]);

                /**
                 * Save
                 */
                return $user->meetings()->save($meeting);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
