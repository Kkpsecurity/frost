import React, { useContext, useState } from "react";
import { Alert, Col, Row } from "react-bootstrap";
import VideoCallTabs from "./VideoCallTabs";
import {
    InstructorVideoContext,
    InstructorVideoInterface,
} from "../../../../../../Context/Admin/FrostVideoContext";
import FrostStudentVideoChat from "../../FrostStudentVideoChat";
import FrostAgoraPlayer from "../player/FrostAgoraPlayer";
import { LaravelAdminShape } from "../../../../../../Config/types";
import "./VideoCall.css";

interface VideoCallProps {
    laravel: LaravelAdminShape;
}

// New Structure Root FrostVideoChat
// /blocks
//     /instructor
//         /QueuedUsersBlock.tsx
//         /StudentUsersBlock.tsx
//         /VideoCallBlock.tsx  
//    /student
//         /StudentVideoCallBlock.tsx
//    WebRTMAgoraPeer.tsx
// /Partials
//     /instructor
//         /ActiveStudentCallList.tsx
//         /MiniVideoCall.tsx
//         /StudentListBlock.tsx
//         /VideoCall.tsx
//         /VideoCallTabs.tsx
//         /VideoChatTitleBar.tsx
//     /student
//     /player
//         /FrostAgoraPlayer.tsx
//         /MultiChat.tsx
//         /PhoneIcons.tsx
//         /VideoControls.tsx
//         /VideoPlayer.tsx
//     AgoraFrostPlayer.tsx
//     DisplayVideoChat.tsx
//     Notification.tsx
//     Options.tsx
//     Reciever.tsx
// FrostInstructorVideoChat.tsx
// FrostStudentVideoChat.tsx
// settings.ts
// share_settings.ts









const VideoCall:React.FC<VideoCallProps> = ({laravel}) => {
    const videoContext = useContext(InstructorVideoContext) as InstructorVideoInterface;

    /**
     * Sets the Tab State show list of live students or queue
     */
    const [activeTab, setActiveTab] = useState<string>("students");

    return (
        <div className="container video-call-container">
            <Row className="bg-gray" style={{
                height: "100%",
            }}>
                <Col lg={6} className="m-0 p-0">                   
                    <VideoCallTabs
                        activeTab={activeTab}
                        setActiveTab={setActiveTab}
                        activeCallRequest={videoContext.activeCallRequest}
                        allStudents={videoContext.allStudents}
                        callStudentId={videoContext.callStudentId}
                        acceptUserId={videoContext.acceptUserId}
                        course_date_id={videoContext.courseDateId}
                        handleCallStudent={videoContext.handleCallStudent}
                        handleEndCall={videoContext.handleEndCall}
                        callHasEnded={videoContext.callHasEnded}
                        allQueueStudents={videoContext.allQueueStudents}
                        laravel={laravel}
                    />
                </Col>
                <Col lg={6} className="m-0 p-0" style={{
                    background: "#323231",
                }}>
                    <FrostAgoraPlayer video={videoContext} laravel={laravel} />
                </Col>
            </Row>
        </div>
    );
};

export default VideoCall;