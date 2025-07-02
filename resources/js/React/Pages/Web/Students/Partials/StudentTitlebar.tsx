import React, { useContext } from "react";
import { ClassContext } from "../../../../Context/ClassContext";
import {
    faToggleOn,
    faToggleOff,
    faFileAlt,
    faQuestionCircle,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { Col, Container, Row } from "react-bootstrap";

import {
    Navbar,
    Title,
} from "../../../../Styles/StyledStudentTitleBar.styled";
import { SpotlightTourContext } from "../../../../Context/SpotLightContext";
import { beginHelp } from "../Dashboard/FrostTour/FrostTourManager";

const StudentTitlebar = ({
    laravel,
    darkMode,
    toggleDarkMode,
    debug = false,
}) => {
    if (debug) console.log("StudentTitlebar Initialized");
    const data = useContext(ClassContext);
    const { setIsHelpModalOpen } = useContext(SpotlightTourContext);
    
    const getNextExamAttempt = () => {
        const nextAttemptDate = new Date(data.studentExam.next_attempt_at);
        const currentDate = new Date();
        const diff = nextAttemptDate.getTime() - currentDate.getTime();
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        return (
            <div className="btn btn-warning bold">
                Next Exam Attempt: {hours} hrs {minutes} mins {seconds} secs
            </div>
        );
    };

    /**
     * Test Settings
     */
     //data.studentExam.is_ready = false;
     //data.studentExam.next_attempt_at = "2023-10-11 08:00";
    
    const url = window.location.origin;
    
    return (
        <Navbar className="student-navbar" darkMode={darkMode}>
            <style>
                {`
                    .exam-text {
                        font-size: 1.1rem;
                    }

                    @media (max-width: 768px) {
                        .exam-text {
                            display: none;
                        }s
                    }
                `}
            </style>
            <Container fluid>
                <Row>
                    <Col
                        lg={6}
                        xs={10}
                        style={{
                            display: "flex",
                            justifyContent: "flex-start",
                            alignItems: "center",
                        }}
                    >
                        <Title darkMode={darkMode}>
                            {data.course.title_long}
                        </Title>
                    </Col>
                    <Col
                        lg={6}
                        xs={2}
                        style={{
                            display: "flex",
                            justifyContent: "flex-end",
                            alignItems: "center",
                        }}
                    >
                        {data.studentExam.is_ready ? (
                            <a
                                href={`${url}/classroom/exam/authorize/${laravel.user.course_auth_id}`}
                                rel="noopener noreferrer"
                                className="btn btn-success"
                            >
                                <FontAwesomeIcon
                                    icon={faFileAlt}
                                    className="mr-2"
                                />
                                <span className="exam-text">Take Exam</span>
                            </a>
                        ) : data.studentExam.next_attempt_at ? (
                            getNextExamAttempt()
                        ) : null}
                        <button
                            //darkMode={darkMode}
                            onClick={toggleDarkMode}
                            className="btn btn-dark ml-2"                           
                        >
                            <FontAwesomeIcon
                                icon={darkMode ? faToggleOn : faToggleOff}
                            />
                        </button>
                        {laravel.user.id === 2 && (
                            <a
                                href="#"
                                className="btn btn-success ml-2"
                                onClick={() => beginHelp(setIsHelpModalOpen)}
                            >
                                <FontAwesomeIcon
                                    icon={faQuestionCircle}
                                    className="mr-2"
                                />
                            </a>
                        )}
                    </Col>
                </Row>
            </Container>
        </Navbar>
    );
};

export default StudentTitlebar;
