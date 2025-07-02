import { useEffect, useReducer } from "react";
import apiClient from "../../../../../../Config/axios";
import useLocalStorage from "../../../../../../Hooks/useLocalStorage";

// Action types
const CALL_START = "CALL_START";
const CALL_END = "CALL_END";
const CALL_RESET = "CALL_RESET";
const CALL_ACCEPTED = "CALL_ACCEPTED";
const CALL_NOT_ACCEPTED = "CALL_NOT_ACCEPTED";

// Initial state for reducer
const initialState = {
    callStudentId: 0,
    callStudentAuthId: 0,
    callHasEnded: false,
    activeCallRequest: false,
};

// Reducer function for managing call actions
const reducer = (state, action) => {
    switch (action.type) {
        case CALL_START:
            return {
                ...state,
                callStudentId: action.payload.id,
                callStudentAuthId: action.payload.authId,
                activeCallRequest: true,
            };
        case CALL_ACCEPTED:
            return {
                ...state,
                acceptUserId: state.callStudentId,
                activeCallRequest: true,
                acceptedFlag: true,
            };
        case CALL_NOT_ACCEPTED:
            return {
                ...state,
                acceptUserId: 0,
                activeCallRequest: false,
                acceptedFlag: false,
            };
        case CALL_END:
            return initialState;
        case CALL_RESET:
            return {
                ...state,
                callStudentId: 0,
                callStudentAuthId: 0,
                activeCallRequest: false,
            };
        default:
            throw new Error("Invalid action type");
    }
};

/**
 * useVideoCallBlock: A custom hook to handle video calls for instructors.
 *
 * @param {Object} options - Options for the hook.
 * @returns {Object} - Returns methods and states related to video calls.
 */
export const useVideoCallBlock = ({
    listenCall,
    setMakeCall,
    courseDateId,
    allQueueStudents,
    allStudents,
}) => {
    const [state, dispatch] = useReducer(reducer, initialState);

    const [storedCallStudentId, setStoredCallStudentId] = useLocalStorage(
        "callStudentId",
        0
    );
    const [storedCallStudentAuthId, setStoredCallStudentAuthId] =
        useLocalStorage("callStudentAuthId", 0);

    /**
     * @instructor Effect
     * Handles the call student event API call
     * @param user
     */
    const callUser = async (user) => {
        try {
            const response = await apiClient.post(
                "/services/frost_video/call_student",
                {
                    course_date_id: courseDateId,
                    student_id: user.id,
                }
            );

            if (response.data.success) {
                dispatch({
                    type: CALL_START,
                    payload: { id: user.id, authId: user.course_auth_id },
                });

                // After successfully initiating a call, set the data in localStorage:
                setStoredCallStudentId(user.id.toString());
                setStoredCallStudentAuthId(user.course_auth_id.toString());
            } else {
                dispatch({ type: CALL_RESET });

                // If the call initialization fails, reset the localStorage values:
                setStoredCallStudentId(0);
                setStoredCallStudentAuthId(0);
            }
        } catch {
            dispatch({ type: CALL_RESET });

            // If there's an exception while initiating the call, reset the localStorage values:
            setStoredCallStudentId(0);
            setStoredCallStudentAuthId(0);
        }
    };

    // This is in the Compoenets folder FrostVideoChat
    // To Organize the instuctor compoents from the student components
    // List Compoents
    // 1. FrostStudentVideoChat

    // 2. FrostInstructorVideoChat

    // /partials
    // 1. ActiveStudentCall
    // 2. AgoraFrostPlayer
    // 3. displayVideoChat
    // 4. FrostAgoraPlayer
    // 5. MiniVideoCall
    // 6. Notifcation
    // 7. Options
    // 9. PhoneIcons
    // 10. Reciever
    // 11. ScreenSharing
    // 12. StudentListBlock
    // 13. VideoCall
    // 14. VideoCallQueue
    // 15. VideoCallTabs
    // 16. VideoChatTitleBar
    // 17. VideoControls
    // 18. videoToCallList
    // 19. Videos

    // /blocks
    // 1. QueuedUsserBlock
    // 2. StudentVideoCallBlock
    // 3. VideoCallBlock
    // 4. WebRTMAgoraPeer

    /**
     * @instructor Effect
     * Handles the call student event
     * @param studentId
     */
    const handleCallStudent = async (studentId) => {
        setMakeCall(true);

        /**
         * When making a call to retrieve the user we first check the queue if the user is not in the queue
         * then we check the allStudents array
         */
        const user =
            allQueueStudents.find((user) => user.id === studentId) ||
            allStudents.find((user) => user.id === studentId);

        if (user) await callUser(user);
        else {
            // If the user is not found, it's a good practice to reset localStorage values to prevent unexpected behaviors:
            setStoredCallStudentId(0);
            setStoredCallStudentAuthId(0);
        }
    };

    /**
     * @shared Effect
     * Handles the end call event
     *
     * @param studentId
     * @returns
     */
    const handleEndCall = async (studentId) => {
        if (!studentId) {
            console.log("No student id provided");
            return;
        }

        try {
            const response = await apiClient.post(
                "/services/frost_video/end_call",
                {
                    course_date_id: courseDateId,
                    student_id: studentId,
                }
            );

            if (response.data.success) {
                dispatch({ type: CALL_END });
            }
        } catch (error) {
            console.error("Error ending call:", error);
        }
    };

    /**
     * @instructor Effect
     * Manages the Call Student event to maintain the call state
     */
    useEffect(() => {
        const handleReconnect = async () => {
            if (storedCallStudentId && storedCallStudentAuthId) {
                try {
                    const response = await apiClient.get(
                        "/services/frost_video/check_call_status/" +
                            courseDateId
                    );
                    console.log("Response API Status:", response);

                    if (response.data.user_id) {
                        // Assuming the server returns a user_id to indicate call status.
                        setMakeCall(true);

                        // Dispatch the state updates using the stored values
                        dispatch({
                            type: CALL_START,
                            payload: {
                                id: parseInt(storedCallStudentId),
                                authId: parseInt(storedCallStudentAuthId),
                            },
                        });
                    }
                } catch (error) {
                    console.error("Error checking call status:", error);
                }
            }
        };

        handleReconnect();
    }, []);

    /**
     * @instructor Effect
     * Listen for the call accepted event
     */
    useEffect(() => {
        const handleListenCall = () => {
            if (
                state.callStudentId &&
                listenCall?.caller_id === state.callStudentId
            ) {
                dispatch({ type: CALL_ACCEPTED });
            } else if (state.acceptUserId) {
                dispatch({ type: CALL_NOT_ACCEPTED });
            }
        };

        handleListenCall();
    }, [listenCall, state.callStudentId, state.acceptUserId]);

    // This useEffect should check if a call has been made the student and the
    // is not in the queue
    useEffect(() => {
        if (storedCallStudentId) {
            // let make an API call to see if a call is made
            const checkCallStatus = async () => {
                try {
                    const response = await apiClient.get(
                        "/services/frost_video/check_call_status/" +
                            courseDateId
                    );
                    console.log("Response API Status:", response);

                    if (response.data.user_id) {
                        // Assuming the server returns a user_id to indicate call status.
                        setMakeCall(true);

                        // Dispatch the state updates using the stored values
                        dispatch({
                            type: CALL_START,
                            payload: {
                                id: parseInt(storedCallStudentId),
                                authId: parseInt(storedCallStudentAuthId),
                            },
                        });
                    }
                } catch (error) {
                    console.error("Error checking call status:", error);
                }
            };
        }
    }, [storedCallStudentId]);

    return {
        callStudentId: state.callStudentId,
        handleCallStudent,
        handleEndCall,
        callHasEnded: state.callHasEnded,
        activeCallRequest: state.activeCallRequest,
        acceptUserId: state.acceptUserId,
    };
};
