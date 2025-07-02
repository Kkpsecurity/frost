import React from "react";
import { Container, Col, Alert } from "react-bootstrap";
import styled from "styled-components";

import ZoomstudentScreenShare from "../../../../Components/Plugins/Zoom/ZoomstudentScreenShare";
import {
    StyledEmbedContainer,
    StyledLead,
    StyledSubTitle,
    ZoomContainer,
    StyledVideoTitle,
} from "../../../../Styles/ZoomStyledComponent.styled";
import SupportBox from "../Dashboard/SupportBox";

const StudentZoomScreenShare = ({
    laravel,
    data,
    courseLessonsArray,
    currentColors,
    debug = false,
}) => {
    if (debug) console.log("StudentZoomScreenShare data", data);

    function getDay() {
        const today = new Date();
        const day = today.getDay();
        return day === 0 ? 7 : day;
    }

    return (
        <ZoomContainer>
            <div
                className=""
                style={{
                    position: "relative",
                    maxHeight: "520px",
                }}
            >
              
                <ZoomstudentScreenShare
                    laravel={laravel}
                    data={data}
                    debug={debug}
                />
            </div>

            <div
                className="frost-player-meta"
            >
                <StyledEmbedContainer currentColors={currentColors}>
                    <StyledVideoTitle currentColors={currentColors}>
                        <i className="fa fa-play"></i> {data.course.title_long}{" "}
                        - {getDay()}
                    </StyledVideoTitle>
                    <StyledSubTitle currentColors={currentColors}>
                        <StyledLead currentColors={currentColors}>
                            Current Lesson <br />
                            {data?.completedStudentLessons?.length || 0} out of{" "}
                            {courseLessonsArray.length || 0} <br />
                            lessons completed
                        </StyledLead>
                        <StyledLead currentColors={currentColors}>
                            All lessons <br />
                            {data.allCompletedStudentLessonsTotal || 0} out of{" "}
                            {data.allLessonsTotal || 0}
                            <br />
                            lessons completed
                        </StyledLead>
                    </StyledSubTitle>
                </StyledEmbedContainer>
            </div>

            <div
                className="frost-session-reminder"
                style={{
                    position: "relative",
                }}
            >
                <Alert variant="info">
                    <Alert.Heading>Classroom Engagement Reminder</Alert.Heading>
                    <p>
                        Please be advised that navigating away from the
                        classroom page or browsing other sites during the
                        session may register as inactivity in our system. Such
                        inactivity can hinder the system from marking the
                        classroom session as completed. Ensure you remain active
                        and engaged, especially during lesson transitions, to
                        avoid any disruptions or access issues.
                    </p>
                    <hr />
                </Alert>
            </div>

            <div className="support-section">
                <SupportBox />
            </div>
           
        </ZoomContainer>
    );
};

export default StudentZoomScreenShare;
