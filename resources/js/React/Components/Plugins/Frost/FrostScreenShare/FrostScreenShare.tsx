import React from "react";
import { Container } from "react-bootstrap";
import styled from "styled-components";
import AgoraFrostScreenShare from "./AgoraFrostScreenShare";

const FrostScreenContainer = styled(Container)`
    position: relative;
    padding: 0;
    margin: 0;
    height: 100%;
    width: 100%;
`;

// Responsive styles added with media queries
const StyledEmbedContainer = styled.div`
    display: flex;
    font-size: 1.2rem;
    font-weight: normal;
    align-items: center;
    font-family: "Roboto", sans-serif;
    color: ${(props) => props.currentColors.navbarTextColor};
    background-color: ${(props) => props.currentColors.contentBgColor};
`;

const StyledVideoTitle = styled.h3`
    font-size: 1.4rem;
    font-weight: bold;
    padding-left: 0.5rem;
    line-height: 1.2;
    font-family: "Roboto", sans-serif;
    color: ${(props) => props.currentColors.navbarTextColor};
`;

const StyledSubTitle = styled.div`
    margin: 0;
    display: flex;
    padding-left: 0.8rem;
    align-items: center;
    background-color: ${(props) => props.currentColors.contentBgColor};
    color: ${(props) => props.currentColors.navbarTextColor};
`;

const StyledLead = styled.p`
    font-size: 1.3rem;
    font-weight: normal;
    font-family: "Roboto", sans-serif;
    color: ${(props) => props.currentColors.navbarTextColor};
`;

const FrostScreenShare = ({
    laravel,
    data,
    courseLessonsArray,
    currentColors,
    debug = false,
}) => {
    return (
        <FrostScreenContainer>
            <div className="embed-responsive embed-responsive-16by9">
                <AgoraFrostScreenShare />
            </div>
            <StyledEmbedContainer currentColors={currentColors}>
                <div className="mt-3">
                    <StyledVideoTitle currentColors={currentColors}>
                        <i className="fa fa-video-play"></i>{" "}
                        {data.course.title_long}
                    </StyledVideoTitle>
                    <StyledSubTitle currentColors={currentColors}>
                        <StyledLead currentColors={currentColors}>
                            {data?.completedStudentLessons?.length || 0} out of{" "}
                            {courseLessonsArray.length || 0} lessons completed
                        </StyledLead>
                    </StyledSubTitle>
                </div>
            </StyledEmbedContainer>
        </FrostScreenContainer>
    );
};

export default FrostScreenShare;
