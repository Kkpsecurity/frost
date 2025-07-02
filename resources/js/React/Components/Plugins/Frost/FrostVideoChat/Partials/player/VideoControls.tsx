import React, { useEffect, useState } from "react";
import { Button } from "react-bootstrap";
import styled from "styled-components";

const MicIcon = <i className="fas fa-microphone" />;
const MicMuteIcon = <i className="fas fa-microphone-slash" />;
const VideoIcon = <i className="fas fa-video" />;
const VideoMuteIcon = <i className="fas fa-video-slash" />;
const HangUpIcon = <i className="fas fa-phone-slash" />;
const StartIcon = <i className="fas fa-phone" />;

const ControlButton = styled(Button)`
    &.circle {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
        font-size: 1.2rem;
        line-height: 1;
        margin: 0 0.5rem;
    }

    &.circle-lg {
        border-radius: 50%;
        width: 60px;
        height: 60px;
        padding: 0;
        font-size: 1.5rem;
        line-height: 1;
        margin: 0 0.5rem;
    }
`;

const VideoControls = ({
    handleEndCall,
    handleAcceptCall,
    handleMuteStream,
    selectedStudentId,
    callAccepted,
    remoteStream,
}) => {
    console.log(
        "VideoControls",
        callAccepted,
        remoteStream,
        handleEndCall,
        selectedStudentId,
        handleAcceptCall,
        handleMuteStream
    );

    return (
        <>
            <ControlButton
                className="circle"
                variant={remoteStream?.video ? "primary" : "secondary"}
                onClick={() => handleMuteStream("video")}
            >
                {remoteStream?.video ? VideoIcon : VideoMuteIcon}
            </ControlButton>

            {callAccepted ? (
                <ControlButton
                    className="circle"
                    variant={remoteStream?.video ? "success" : "danger"}
                    onClick={(selectedStudentId) => {
                        console.log("selectedStudentId", selectedStudentId);
                        handleEndCall(selectedStudentId);
                    }}
                >
                    {HangUpIcon}
                </ControlButton>
            ) : (
                <ControlButton
                    className="circle-lg"
                    variant="primary"
                    onClick={() => handleAcceptCall()}
                >
                    {StartIcon}
                </ControlButton>
            )}
            <ControlButton
                className="circle"
                variant={remoteStream?.audio ? "primary" : "secondary"}
                onClick={() => handleMuteStream("audio")}
            >
                {remoteStream?.audio ? MicIcon : MicMuteIcon}
            </ControlButton>
        </>
    );
};

export default VideoControls;
