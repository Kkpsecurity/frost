import React from "react";
import { Container, Row, Col, Card } from "react-bootstrap";
import {
    LocalVideoPlayer,
    RemoteVideoPlayer,
    VideoControlsContainer,
    VideoPlayerContainer,
} from "../../../../../../Styles/StyledVideoComponenet.styled";
import VideoControls from "./VideoControls";
import PhoneIcons from "./PhoneIcons";

interface VideoPlayerProps {
    video: any;
    localVideoRef: any;
    remoteVideoRef: any;
}

const VideoPlayer = ({
    video,
    localVideoRef,
    remoteVideoRef,
}: VideoPlayerProps) => {
    const callAnswered = video.callAccepted || video.acceptUserId > 0;

    return (
        <VideoPlayerContainer id="video-container">
            <LocalVideoPlayer
                ref={localVideoRef}
                playsInline
                autoPlay
            ></LocalVideoPlayer>

            {video.isInstructor ? (
                <>
                    {video.callStudentId > 0 && video.acceptUserId > 0 ? (
                        <RemoteVideoPlayer
                            ref={remoteVideoRef}
                            playsInline
                            autoPlay
                        />
                    ) : video.callStudentId > 0 ? (
                        <div
                            style={{
                                backgroundColor: "#111",
                                height: "320px",
                                zIndex: 1,
                            }}
                        >
                            <PhoneIcons calling="waiting" />
                        </div>
                    ) : null}
                </>
            ) : video.isInstructor === false ? (
                <>
                    {video.callAccepted ? (
                        <RemoteVideoPlayer
                            ref={remoteVideoRef}
                            playsInline
                            autoPlay
                        />
                    ) : video.inComingRequest ? (
                        <div
                            style={{
                                backgroundColor: "#111",
                                height: "320px",
                                zIndex: 1,
                            }}
                        >
                            <PhoneIcons calling="waiting" />
                        </div>
                    ) : (
                        <div
                            style={{
                                backgroundColor: "#111",
                                height: "320px",
                                zIndex: 1,
                            }}
                        >
                            <PhoneIcons calling="idle" />
                        </div>
                    )}
                </>
            ) : null}

            <VideoControlsContainer>
                <VideoControls
                    handleEndCall={video.handleEndCall}
                    handleAcceptCall={video.handleAcceptCall}
                    handleMuteStream={video.handleMuteStream}
                    selectedStudentId={video.callStudentId}
                    callAccepted={callAnswered}
                    remoteStream={remoteVideoRef}
                />
            </VideoControlsContainer>
        </VideoPlayerContainer>
    );
};

export default VideoPlayer;
