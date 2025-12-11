<?php
namespace App\Http\Controllers\React;


use App\Models\CourseAuth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\VideoCallRequest;


class AgoraVideoCaller
{


    /**
     * Instruct Methods
     */

    /**
     * Instructor Checks for students request and adds to queue
     */
    public function getAllQueues($course_date_id)
    {
        $queues = VideoCallRequest::queue($course_date_id);

        if ($queues->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Queued users found',
                'data' => []
            ]);
        }


        $queues = $queues->map(function ($queue) {
            $queue = (object) $queue; // cast array to object if necessary
            $user = User::select('id', 'fname', 'lname', 'email', 'avatar')->find($queue->user_id);
            $course = CourseAuth::find($user->id);
            $user->avatar = $user->getAvatar('thumb');
            $user->course_auth_id = $course->id;
            $user->create_at = $queue->created_at;

            return $user;
        });

        return response()->json([
            'success' => true,
            'message' => 'List of All Queues',
            'queues' => $queues
        ]);
    }

    public function callStudent(Request $request)
    {
        VideoCallRequest::InstCallSetReady($request->course_date_id, $request->student_id); //void      
        $course_auth_id = CourseAuth::where('user_id', $request->student_id)->first()->id;
        return response()->json([
            'success' => true, 
            'message' => 'Call Request',
            'course_auth_id' => $course_auth_id
        ]);
    }

    public function listenForStudentAcceptCall($course_date_id, $user_id)
    {

        $caller_id = VideoCallRequest::StudentCallIsReady($course_date_id); //boolean

        if ($caller_id) {
            return response()->json(['success' => true, 'message' => 'Call in Session', "caller_id" => $caller_id], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Unable to start call'], 200);
        }

    }

    /**
     * Checks that the instructor has initiated a call
     */
    public function checkCallStatus($course_date_id)
    {
        $user_id = VideoCallRequest::InstCallGetReady($course_date_id); //boolean

        if ($user_id) {
            return response()->json(['success' => true, 'message' => 'Call Status Valid', "user_id" => $user_id], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'No Call request found'], 200);
        }
    }

    
    /**
     * Instructor Ends Call
     */
    public function endCallRequest(Request $request)
    {
        if(!$request->course_date_id || !$request->student_id){
            return response()->json(['success' => false, 'message' => 'Invalid End Call Request'], 400);
        }

        VideoCallRequest::CallDeleteAll($request->course_date_id, $request->student_id); //void
        return response()->json(['success' => true, 'message' => 'Call Ended']);
    }


/**********************************************************
 * Student Methods
 */

    /**
     * Sends A call Request to the Instructor
     * from student side
     */
    public function studentRequestCall($course_date_id, $user_id)
    {

        if (!$user_id || !$course_date_id) {
            return response()->json(['success' => false, 'message' => 'Invalid Call Request'], 400);
        }

        VideoCallRequest::Create($course_date_id, $user_id); // void

        return response()->json(['success' => true, 'message' => 'Call Request Sent']);
    }

    /**
     * Cancles A call Request to the Instructor
     */
    public function studentCancelCall($course_date_id, $user_id)
    {
        
        if (!$course_date_id || !$user_id) {
            return response()->json(['success' => false, 'message' => 'Invalid Call Canceled Request'], 400);
        }   
        
        VideoCallRequest::CallDeleteAll($course_date_id, $user_id);
           
        return response()->json(['success' => true, 'message' => 'Call Request Canceled']);
    }

    public function studentAcceptsCall(Request $request)
    {
        $user_id = $request->user_id;
        $course_date_id = $request->course_date_id;

        if (!$user_id || !$course_date_id) {
            return response()->json(['success' => false, 'message' => 'Invalid Call Accept Fail'], 400);
        }

        VideoCallRequest::StudentAcceptCall($course_date_id, $user_id);

        return response()->json(['success' => true, 'message' => 'Student Accepts Call']);
    }

    public function studentEndCall(Request $request)
    {
        $user_id = $request->user_id;
        $course_date_id = $request->course_date_id;

        if (!$user_id || !$course_date_id) {
            return response()->json(['success' => false, 'message' => 'Invalid End Call Fail'], 400);
        }

        VideoCallRequest::CallDeleteAll($course_date_id, $user_id);

        return response()->json(['success' => true, 'message' => 'Student Accepts Call']);
    }

    /**
     * checks the request form user side
     * if a Instructor initiated a call
     */
    public function validateCallRequest($course_date_id, $user_id)
    {
        if (!$user_id || !$course_date_id) {
            return response()->json(['status' => 'error', 'message' => 'Invalid Call RequestCall Validation'], 400);
        }

        $call_request = VideoCallRequest::InstCallIsReady($course_date_id, $user_id); //boolean

        if ($call_request) {
            return response()->json(['success' => true, 'message' => 'Call Request Validated', "call_request" => $call_request], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'No Call request found'], 200);
        }
    }

    public function CheckIfStudentInQueue($course_date_id, $user_id)
    {
        $inQueue = VideoCallRequest::Queue($course_date_id)->where('user_id', $user_id)->first();

        if ($inQueue) {
            return response()->json(['success' => true, 'message' => 'Student in Queue', "inQueue" => true], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Student not in Queue', 'inQueue' => false], 200);
        }
    }

















}