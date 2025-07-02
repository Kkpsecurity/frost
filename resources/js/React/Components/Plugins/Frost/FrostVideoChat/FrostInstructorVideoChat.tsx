/**
 * Parent Component for the Frost Video Chat
 * Instructor Side:
 * Handles most of the request and calls in queue states
 * logic for Agroa in child component
 */

import React, { useEffect, useRef, useState } from "react";
import styled from "styled-components";
import { FrostVideoTheme } from "../../../../Styles/FrostVideoTheme";
import VideoCall from "./Partials/instructor/VideoCall";

import {
    LaravelAdminShape,
    LaravelDataShape,
    StudentType,
    UserListBlockType,
} from "../../../../Config/types";

import VideoChatTitleBar from "./Partials/instructor/VideoChatTitleBar";
import MiniVideoCall from "./Partials/instructor/MiniVideoCall";
import QueuedUsersBlock from "./blocks/instructor/QueuedUsersBlock";
import { useVideoCallBlock } from "./blocks/instructor/VideoCallBlock";

import {
    InstructorVideoContext,
    FrostInstructorVideoProvider,
    InstructorVideoInterface,
} from "../../../../Context/Admin/FrostVideoContext";
import { listenForCallAccepted } from "../../../../Hooks/Admin/useVideoCallQueuedUsers";

interface FrostWebRTCVideoProps {
    /**
     * The makeCall state
     */
    makeCall: boolean;
    setMakeCall: React.Dispatch<React.SetStateAction<boolean>>;

    /**
     * The list of all students
     */
    allStudents: StudentType[];

    /**
     * The courseDateId and the logged in user ID
     */
    courseDateId: number;
    laravelUserId: number;

    laravel: LaravelAdminShape;
}

const VideoCallContainer = styled.div`
    position: relative;
    width: 100%;
    height: ${(props) => (props.makeCall ? "320px" : "60px")};
    background-color: #444;
    overflow: hidden;
    transition: height 0.5s ease-in-out;
    box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.75);
`;

const VideoCallSection = styled.div`
    position: "absolute",
    width: "100%",
    height: "100%",
    zIndex: 2,
`;

const FrostInstructorVideoCall: React.FC<FrostWebRTCVideoProps> = ({
    makeCall, // Toogle the Video Call
    setMakeCall, // Set the makeCall state
    allStudents, // The list of all students
    courseDateId, // The courseDateId
    laravelUserId, // The logged in user ID
    laravel,
}) => {
    const [errorState, setErrorState] = useState<string | null>(null);

    /**
     * The list of all students in the queue
     */
    const queued = QueuedUsersBlock(courseDateId);
    console.log("allQueueStudents", queued.allQueueStudents);
    const { allQueueStudents } = queued;

    /**
     * Listen for the call to be accepted
     */
    const {
        data: listenCall,
        status,
        error,
    } = listenForCallAccepted(courseDateId, null);

    useEffect(() => {
        if (error) {
            setErrorState(error as string);
        } else {
            setErrorState(null);
        }
    }, [error]);

    /**
     *
     * @param studentId Required for the a prop
     */
    function handleAcceptCall(studentId) {}

    const {
        callStudentId,
        handleCallStudent,
        handleEndCall,
        callHasEnded,
        activeCallRequest,
        acceptUserId,
    } = useVideoCallBlock({
        listenCall,
        courseDateId,
        allQueueStudents,
        allStudents,
        setMakeCall,
    });

    const videoContext: InstructorVideoInterface = {
        makeCall,
        isInstructor: true,

        allStudents,
        allQueueStudents,

        handleCallStudent,
        handleEndCall,
        callHasEnded,

        activeCallRequest,
        callStudentId,
        acceptUserId,

        courseDateId,
        laravelUserId,
    };

    return (
        <>
            {errorState && (
                <div style={{ color: "red", padding: "10px" }}>
                    {errorState}
                </div>
            )}

            <VideoCallContainer makeCall={makeCall}>
                <VideoCallSection>
                    {makeCall ? (
                        <FrostVideoTheme>
                            <FrostInstructorVideoProvider value={videoContext}>
                                <VideoCall laravel={laravel} />
                            </FrostInstructorVideoProvider>
                        </FrostVideoTheme>
                    ) : (
                        <FrostInstructorVideoProvider value={videoContext}>
                            <MiniVideoCall />
                        </FrostInstructorVideoProvider>
                    )}
                </VideoCallSection>
            </VideoCallContainer>
            <VideoChatTitleBar
                makeCall={makeCall}
                allQueueStudents={allQueueStudents}
                setMakeCall={setMakeCall}
            />
        </>
    );
};

export default React.memo(FrostInstructorVideoCall);
