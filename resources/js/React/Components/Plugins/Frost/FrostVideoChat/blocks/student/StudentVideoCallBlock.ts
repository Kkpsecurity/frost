import React, { useEffect, useState } from "react";
import apiClient from "../../../../../../Config/axios";
import { StudentType, UserListBlockType } from "../../../../../../Config/types";
import useLocalStorage from "../../../../../../Hooks/useLocalStorage"; // Assuming the path

import {
    sendCallAcceptedRequest,
    sendCallCancelRequest,
    sendCallRequest,
    validateCallRequest,
} from "../../../../../../Hooks/FrostVideoChatHooks";

const useStudentVideoCallBlock = ({ data, laravel }) => {

   
    /**
     * Sends the call request to the instructor
     */
    const [makeCall, setMakeCall] = useState<boolean>(() => {
        const storedMakeCall = localStorage.getItem("makeCall");
        return storedMakeCall === "true";
    });

    /**
     * Student Accepts the call
     */
    const [callAccepted, setCallAccepted] = useState<boolean>(false);

    /**
     * Student is in the queue
     * if the student loses his conectivity then we check the queue to
     * see if he is still in the queue if so then we set to true
     */
    const [inQueue, setInQueue] = useState<boolean>(false);

    /**
     * An Incoming call request from the instructor
     * this gets set via a polling request
     */
    const [inComingRequest, setInComingRequest] = useState<boolean>(false);

    /**
     * Checks to see if the instructor is intiated the
     * running on a polling request
     */
    const { data: incomeCallResponse, isSuccess: incomeCallIsSuccess } = validateCallRequest(
        data.courseDate.id,
        laravel.user.id
    );

    /**
     * Handles the make call button Sends the call request to the server
     * or a cancel request if the call is already made
     */
    const handleMakeCall = async () => {
        if (makeCall) {
            // Close the video call request and cancel the request
            setMakeCall(false);
            setInComingRequest(false);
            const cancelRequestResponse = await sendCallCancelRequest({
                course_date_id: data.courseDate.id,
                user_id: laravel.user.id,
            });

            // Check if the cancel request was successful
            // Here, you need to use the correct condition based on your API
            if (cancelRequestResponse && cancelRequestResponse.success) {
                // Store the updated state in local storage
                localStorage.removeItem("makeCall");
            } else {
                throw new Error("Could not cancel the call");
            }
        } else {
            // Open the video call request and send the request
            setMakeCall(true);
            // Open the video call request and send the request
            const requestCallResponse = await sendCallRequest({
                course_date_id: data.courseDate.id,
                user_id: laravel.user.id,
            });

            // Check if the call request was successful
            if (requestCallResponse && requestCallResponse.success) {
                setMakeCall(true);
                // Store the updated state in local storage
                localStorage.setItem("makeCall", "true");
            } else {
                throw new Error("Could not initiate the call");
            }
        }
    };

    /**
     * The Instructor has initiated the call if so set acceptCallRequest
     */
    const handleAcceptCall = () => {
        sendCallAcceptedRequest({
            course_date_id: data.courseDate.id,
            user_id: laravel.user.id,
        });

        setCallAccepted(true);
        localStorage.setItem("callAccepted", "true");
    };

    /**
     * The Student has ended the call
     */
    const handleEndCall = () => {
        sendCallCancelRequest({
            course_date_id: data.courseDate.id,
            user_id: laravel.user.id,
        });

        setCallAccepted(false);
        setInComingRequest(false);
        setMakeCall(false);

        localStorage.removeItem("callAccepted");
        localStorage.removeItem("makeCall");
    };

    /**
     * Is Student In Queue:
     * Checks the students request is in storage or if the student is in the queue
     * This is used in case the student loses his connection
     */
    useEffect(() => {
       
        const checkQueue = () => {
            apiClient
                .get(
                    "services/frost_video/student/inqueue/" +
                        data.courseDate.id +
                        "/" +
                        laravel.user.id
                )
                .then((response) => {
                    console.log("QUEUE", response.data);
                    if (response.data.inQueue === true) {
                        setInQueue(true);
                    } else {
                        const endCall = async () => {
                            setInQueue(false);
                            setMakeCall(false);  // Update using the setter from useLocalStorage hook
                            setInComingRequest(false);
                            const cancelRequestResponse =
                                await sendCallCancelRequest({
                                    course_date_id: data.courseDate.id,
                                    user_id: laravel.user.id,
                                });

                                // Check if the cancel request was successful
                                // Here, you need to use the correct condition based on your API
                                if (
                                    cancelRequestResponse &&
                                    cancelRequestResponse.success
                                ) {
                                    // Remove the key from local storage using useLocalStorage setter
                                    setMakeCall(null);
                                } else {
                                    throw new Error("Could not cancel the call");
                                }
                        };

                        endCall();
                    }
                })
                .catch((error) => {
                    console.log("Error checking queue:", error);
                });
        };

        checkQueue();
    }, [data.courseDate.id, laravel.user.id]);

    /**
     * Checks the students request is in storage 
     * or if the student is in the queue
     * This is used in case the student loses his connection
     */
    useEffect(() => {
        const storedMakeCall = localStorage.getItem("makeCall");

        // if storedMakeCall is true and student is in queue, then set the 
        // makeCall state to true    
        if (inQueue) {
            setMakeCall(true);

            if (storedMakeCall === null) {
                localStorage.setItem("makeCall", "true");
                if (inComingRequest === true) {
                    setCallAccepted(false); // Student has to accept call again
                }
            }
        }

        // Function to end the call and clear call data
        const endCall = () => {
            setMakeCall(false);

            // Clean up the local storage
            localStorage.removeItem("makeCall");
        };

        // If no activity for 15 minutes then end the call
        const callDuration = 15 * 60 * 1000; // 15 minutes in milliseconds
        const callTimer = setTimeout(endCall, callDuration);

        return () => {
            // Clean up the local storage
            endCall();

            // Clear the call timer if the call is handled before it expires
            clearTimeout(callTimer);
        };
    }, [inQueue]);

    /**
     * Check CallRequestResponse for incoming call
     */
    useEffect(() => {
        console.log("TheCallResponse: ", incomeCallIsSuccess);
        
        if (incomeCallIsSuccess === true) {
            setInComingRequest(true);
        } else {
            setInComingRequest(false);
            setCallAccepted(false);
        }
    }, [incomeCallIsSuccess]);

    return {
        makeCall,
        callAccepted,
        inComingRequest,
        handleMakeCall,
        handleAcceptCall,
        handleEndCall,
    };
};

export default useStudentVideoCallBlock;
