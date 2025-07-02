import React, { useContext } from "react";
import { Row, Col, Container, Alert } from "react-bootstrap";
import { colors } from "../../../../Config/colors"; // Import the colors object

import { ClassDataShape, LaravelDataShape } from "../../../../Config/types";

import { ClassContext } from "../../../../Context/ClassContext";

import InstructorCard from "./Cards/InstructorCard";
import StudentRequirementsCard from "./Cards/StudentRequirementsCard";
import ActiveLessonCard from "./Cards/ActiveLessonCard";

import { FrostConfigContext } from "../../../../Context/FrostConfigContext";

import styled from "styled-components";
import StudentZoomScreenShare from "../Zoom/StudentZoomScreenShare";
import StudendDashboardWidgets from "./StudendDashboardWidgets";
import FrostScreenShare from "../../../../Components/Plugins/Frost/FrostScreenShare/FrostScreenShare";
import SupportBox from "./SupportBox";

interface Props {
    darkMode: boolean;
    laravel: LaravelDataShape;
    debug: boolean;
}

const MeetingArea: React.FC<Props> = ({ darkMode, laravel, debug = false }) => {
    if (debug === true) console.log("Student Player Initialized");

    /**
     * This is the data that will be used to display the Class Room Data
     */
    const data = useContext(ClassContext) as ClassDataShape;

    if (!data) {
        throw new Error("ClassRoom must be used within a ClassDataProvider");
    }

    /**
     * This is the state that will be used to open and close the video chat
     * @note: It should not initate the video chat, just open the window
     */
    const [makeCall, setMakeCall] = React.useState(false);

    /**
     * This is the button text that will be displayed on the page
     */
    const buttonText = makeCall === true ? "Close VideoChat" : "Open VideoChat";

    /**
     * This function will handle the opening and closing of the video chat
     */
    const handelOpenVideoChat = () => {
        setMakeCall(!makeCall);
    };

    /**
     * This is the laravel config object
     *
     */
    const frostConfig = laravel.config;

    /**
     * Determin color pallette
     */
    const currentColors = darkMode ? colors.dark : colors.light;

    const courseLessonsArray = Array.isArray(data?.courseLessons)
        ? data?.courseLessons
        : Object.values(data?.courseLessons);

    if (debug === true) console.log("LaravelUser:", laravel.user.id);

    return (
        <Row>
            <Col lg={8} md={12} sm={12} xs={12}>
                {laravel.user.id === 20000000000 ? (
                    <FrostScreenShare
                        laravel={laravel}
                        data={data}
                        courseLessonsArray={courseLessonsArray}
                        currentColors={currentColors}
                        debug={debug}
                    />
                ) : (
                  <>
                    <StudentZoomScreenShare
                        laravel={laravel}
                        data={data}
                        courseLessonsArray={courseLessonsArray}
                        currentColors={currentColors}
                        debug={debug}
                    />
                  </>
                )}                          
            </Col>
           
            <Col lg={4} md={12} sm={12} xs={12}>
                <StudendDashboardWidgets
                    laravel={laravel}
                    data={data}
                    darkMode={darkMode}
                    debug={debug}
                />
            </Col>
        </Row>
    );
};

export default MeetingArea;
