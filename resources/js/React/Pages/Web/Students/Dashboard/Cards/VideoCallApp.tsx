/**
 * Student Dashboard Video Call App
 */

import React, { useContext } from "react";
import { Card, Alert, Button, Col, ThemeProvider } from "react-bootstrap";
import styled from "styled-components";

import FrostStudentVideoChat from "../../../../../Components/Plugins/Frost/FrostVideoChat/FrostStudentVideoChat";
import { colors } from "../../../../../Config/colors";
import { ClassDataShape, LaravelDataShape } from "../../../../../Config/types";
import { th } from "date-fns/locale";
import FrostAgoraPlayer from "../../../../../Components/Plugins/Frost/FrostVideoChat/Partials/player/FrostAgoraPlayer";
import { VideoPlayerContainer } from "../../../../../Styles/StyledVideoComponenet.styled";
import PhoneIcons from "../../../../../Components/Plugins/Frost/FrostVideoChat/Partials/player/PhoneIcons";

const StyledCard = styled(Card).withConfig({
    shouldForwardProp: (prop, defaultValidatorFn) =>
        !["makeCall", "inComingRequest"].includes(prop) &&
        defaultValidatorFn(prop),
})`
    background-color: ${(props) =>
        props?.theme?.colorSet?.navbarBgColor || "#ccc"};
    display: ${(props) =>
        props.makeCall && !props.inComingRequest ? "block" : "none"};
    margin-bottom: 0;
`;

const StyledAlert = styled(Alert)`
    margin-bottom: 0;
`;

const StyledButton = styled(Button)`
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto;
`;

interface VideoCallAppProps {
    laravel: LaravelDataShape;
    data: ClassDataShape;
    inComingRequest: boolean;
    handleAcceptCall: () => void;
    handleEndCall: () => void;
    callAccepted: boolean;
    makeCall: boolean;
    darkMode: boolean;
    debug: boolean;
}

const VideoCallApp: React.FC<VideoCallAppProps> = ({
    laravel,
    data,
    makeCall,
    callAccepted,
    inComingRequest,
    handleAcceptCall,
    handleEndCall,
    darkMode,
    debug = false,
}) => {
    const colorSet = colors[darkMode ? "dark" : "light"];
    const message =
        "You have a call request. Please hold until the instructor can answer your call.";

    const video = {
        isInstructor: false,

        courseDateId: data.course_date_id,
        inComingRequest: inComingRequest,
        callAccepted: callAccepted,

        makeCall: makeCall,
        handleAcceptCall: handleAcceptCall,
        handleEndCall: handleEndCall,

        laravelUserId: laravel.user.id,
    };

    return (
        <ThemeProvider theme={{ colorSet }}>
            <StyledCard makeCall={makeCall} inComingRequest={inComingRequest}>
                <StyledAlert variant="danger">{message}</StyledAlert>
            </StyledCard>
            {inComingRequest === true && callAccepted === true ? (
                <Col md={12} style={{
                    display: "block",
                }}>
                    <FrostAgoraPlayer video={video} laravel={laravel} />
                </Col>
            ) : inComingRequest === true && callAccepted === false ? (
                <div
                    className="d-flex justify-content-center align-items-center"
                    id="video-container" onClick={handleAcceptCall}
                    style={{
                        backgroundColor: "#cccccc",
                        height: "320px",
                        width: "100%",
                        cursor: "pointer",
                    }}
                >
                    <PhoneIcons calling="incoming" />
                </div>
            ) : null}
        </ThemeProvider>
    );
};

export default VideoCallApp;
