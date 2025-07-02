import React, { useEffect, useState } from "react";
import { VideoPlayerContainer } from "../../../../../../Styles/StyledVideoComponenet.styled";
import PhoneIcons from "../player/PhoneIcons";
import VideoPlayer from "./VideoPlayer";

interface InstructorVideoInterface {
    inQueue: boolean;
    callStudentId: number;
    acceptUserId: number | null | undefined;
    video: any; // Consider refining this type later
    localVideoRef: React.RefObject<HTMLVideoElement>;
    remoteVideoRef: React.RefObject<HTMLVideoElement>;
}

const instructorVideoComponents = ({
    inQueue,
    callStudentId,
    acceptUserId,
    video,
    localVideoRef,
    remoteVideoRef,
}: InstructorVideoInterface) => {
    // Instructor
    // If there are no students in the queue and there are no calls
    if (!inQueue && callStudentId === 0) {
        // Render the default UI
        return (
            <VideoPlayerContainer id="video-container">
                <div
                    style={{
                        backgroundColor: "#222",
                        height: "320px",
                    }}
                >
                    <PhoneIcons calling="idle" />
                </div>
            </VideoPlayerContainer>
        );
    }

    // If there are students in the queue and the instructor has not initiated the call
    if (
        inQueue &&
        callStudentId === 0 &&
        (acceptUserId === 0 ||
            acceptUserId === null ||
            acceptUserId === undefined)
    ) {
        // Render incoming call UI
        return (
            <VideoPlayerContainer id="video-container">
                <div
                    style={{
                        backgroundColor: "#111",
                        height: "320px",
                    }}
                >
                    <PhoneIcons calling="incoming" />
                </div>
            </VideoPlayerContainer>
        );
    }

    // If the instructor initiates a call from the user list and there are no
    // users in the queue
    if (
        !inQueue &&
        callStudentId !== 0 &&
        (acceptUserId === 0 ||
            acceptUserId === null ||
            acceptUserId === undefined)
    ) {
        // Render the call UI
        return (
            <VideoPlayerContainer id="video-container">
                <div
                    style={{
                        backgroundColor: "#111",
                        height: "320px",
                    }}
                >
                    <PhoneIcons calling="calling" />
                </div>
            </VideoPlayerContainer>
        );
    }

    // the instructor initated the call from the queue and the call has not been accepted
    if (
        inQueue &&
        callStudentId !== 0 &&
        (acceptUserId === 0 ||
            acceptUserId === null ||
            acceptUserId === undefined)
    ) {
        // Render the call UI
        return (
            <VideoPlayerContainer id="video-container">
                <VideoPlayer
                    video={video}
                    localVideoRef={localVideoRef}
                    remoteVideoRef={remoteVideoRef}
                />
            </VideoPlayerContainer>
        );
    }

    // the call is connected
    if (
        acceptUserId !== 0 &&
        acceptUserId !== null &&
        acceptUserId !== undefined
    ) {
        // Render the call UI
        return (
            <VideoPlayerContainer id="video-container">
                <VideoPlayer
                    video={video}
                    localVideoRef={localVideoRef}
                    remoteVideoRef={remoteVideoRef}
                />
            </VideoPlayerContainer>
        );
    }

    // Consider adding a default return for any other cases if needed
    return null;
};

interface StudentVideoInterface {
    inComingRequest: boolean;
    callAccepted: boolean;
    video: any;
    localVideoRef: React.RefObject<HTMLVideoElement>;
    remoteVideoRef: React.RefObject<HTMLVideoElement>;
}

const studentVideoComponents = ({
    inComingRequest,
    callAccepted,
    video,
    localVideoRef,
    remoteVideoRef,
}: StudentVideoInterface) => {
    // Student has no incoming calls
    if (!inComingRequest) {
        return (
            <VideoPlayerContainer id="video-container">
                <div
                    style={{
                        backgroundColor: "#111",
                        height: "320px",
                    }}
                >
                    <PhoneIcons calling="idle" />
                </div>
            </VideoPlayerContainer>
        );
    }

    // Student has incoming calls but has not accepted the call
    if (!callAccepted) {
        return (
            <VideoPlayerContainer id="video-container">
                <div
                    style={{
                        backgroundColor: "#111",
                        height: "320px",
                    }}
                >
                    <PhoneIcons calling="incoming" />
                </div>
            </VideoPlayerContainer>
        );
    }

    // Student has accepted the call
    return (
        <VideoPlayerContainer id="video-container">
            <VideoPlayer
                video={video}
                localVideoRef={localVideoRef}
                remoteVideoRef={remoteVideoRef}
            />
        </VideoPlayerContainer>
    );
};

interface DisplayVideoComponentInterface {
    isInstructor: boolean;
    inQueue: boolean;
    callStudentId: number;
    acceptUserId: number | null | undefined;
    inComingRequest: boolean;
    callAccepted: boolean;
    video: any; // Consider refining this type later
    localVideoRef: React.RefObject<HTMLVideoElement>;
    remoteVideoRef: React.RefObject<HTMLVideoElement>;
}

/**
 * Display Video Components
 * @returns
 */
const DisplayVideoComponent = ({
    isInstructor,
    inQueue,
    callStudentId,
    acceptUserId,
    inComingRequest,
    callAccepted,
    video,
    localVideoRef,
    remoteVideoRef,
}: DisplayVideoComponentInterface) => {
    console.log("isInstructor", inQueue, callStudentId, acceptUserId);

    if (isInstructor) {
        return instructorVideoComponents({
            inQueue,
            callStudentId,
            acceptUserId,
            video,
            localVideoRef,
            remoteVideoRef,
        });
    } else {
        return studentVideoComponents({
            inComingRequest,
            callAccepted,
            video,
            localVideoRef,
            remoteVideoRef,
        });
    }
};

const FrostAgoraPlayer = ({ video, laravel }) => {
    // Instructor Props
    const callStudentId = video.callStudentId;
    const acceptUserId = video.acceptUserId; // Tells the instructor who accepted the call
    const allQueueStudents = video.allQueueStudents; // Tells the instructor if the student is in the queue

    /**
     * Shared Props
     */
    const isInstructor = video.isInstructor; // Tell who accessing the app instructor or student
    const handleEndCall = video.handleEndCall; // End the call
    const makeCall = video.makeCall; // Toogle the Video Call

    /**
     * Student Props
     */
    const handleMakeCall = video.handleMakeCall; // Students sends a call request and Instructor makes a call
    const handelAcceptCall = video.handelAcceptCall; // Accept the call
    const inComingRequest = video.inComingRequest; // Tells the student if there is a call request
    const callAccepted = video.callAccepted; // The Call has been accepted by the student

    /**
     * InQueue set the Request of a student to true 
     */
    const [inQueue, setInQueue] = useState(false);

    /**
     * Initiate the video call will initate the Agora SDK
     * and enable local video and audio
     */
    const initVideoCall = () => {};

    /**
     * Instructor Listens for the call to be accepted
     */
    useEffect(() => {
        if (isInstructor) {
            if (video.callStudentId > 0) {
                initVideoCall();
            }
        }
    }, [video.callStudentId]);

    /**
     * Check if the student is in the queue
     */
    useEffect(() => {
        if (allQueueStudents?.length > 0) {
            setInQueue(true);
        } else {
            setInQueue(false);
        }
    }, [allQueueStudents]);

    /// STUDENT Effects //////////////////////////////////////////

    /**
     * Student Listens for the call to be accepted
     */
    useEffect(() => {
        if (!isInstructor) {
            if (inComingRequest && callAccepted) {
                initVideoCall();
            }
        }
    }, [inComingRequest, callAccepted]);

    return DisplayVideoComponent({
        isInstructor,
        inQueue,
        callStudentId,
        acceptUserId,
        inComingRequest,
        callAccepted,
        video,
        localVideoRef: video.localVideoRef,
        remoteVideoRef: video.remoteVideoRef,
    });
};

export default FrostAgoraPlayer;
