import React, { useEffect, useState } from "react";
import { LaravelAdminShape, CourseMeetingShape } from "../../../../Config/types";
import { useActiveCourseData } from "../../../../Hooks/Admin/useInstructorHooks";
import PauseOverlay from "./Partials/PauseOverlay";
import { Alert, Col, Row } from "react-bootstrap";
import ClassTopNav from "./ClassTopNav";
import PageLoader from "../../../../Components/Widgets/PageLoader";
import { toast } from "react-toastify";
import { ToastContainer } from "react-toastify";

type Props = {
    laravel: LaravelAdminShape;
    courseDateId: number;
    CourseMeetingData: CourseMeetingShape;
    handleTakeOver: (event: React.MouseEvent<HTMLButtonElement>) => void;
    handleAssignAssistant: (event: React.MouseEvent<HTMLButtonElement>) => void;
    setAssignedAssistantId: () => void;
    debug: boolean;
};

const ClassRoomAssistance: React.FC<Props> = ({
    laravel,
    courseDateId,
    CourseMeetingData,
    handleTakeOver,
    handleAssignAssistant,
    setAssignedAssistantId,
    debug,
}) => {
    const [isPaused, setIsPaused] = useState<boolean>(false);

    const activeLesson = null;

    const handleClose = () => {
        // Code to handle closing the class
    };

    type ActionsBarProps = {
        handleTakeOver: (event: React.MouseEvent<HTMLButtonElement>) => void;
        handleAssist: (event: React.MouseEvent<HTMLButtonElement>) => void;
    };

    const ActionsBar: React.FC<ActionsBarProps> = ({
        handleTakeOver,
        handleAssist,
    }) => (
        <div className="d-flex align-items-center justify-content-center">
            <button className="btn btn-primary mr-2" onClick={handleTakeOver}>
                Take Over
            </button>
            <button
                className="btn btn-success mr-2"
                onClick={handleAssignAssistant}
            >
                Assist
            </button>
        </div>
    );


    if(!CourseMeetingData) return <Alert variant="danger">No Course Meeting Data</Alert> ;

    return (
        <div>
            <PauseOverlay isPaused={isPaused} handleClose={handleClose} />
            <ToastContainer />
            <Row className="d-flex h-100">
                <Col xs={12} className="bg-dark text-white">
                    <ClassTopNav
                        laravel={laravel}
                        activeLesson={activeLesson}
                        data={CourseMeetingData}
                        markLessonComplete={() => { } }
                        markCourseComplete={() => { } }
                        pauseLesson={() => { } } 
                        setAssignedAssistantId={setAssignedAssistantId} 
                        isPaused={isPaused}                   
                    />
                </Col>
            </Row>

            <Row className="d-flex justify-content-center vh-100">
                <Col xs={12} className="bg-light text-white">
                    <Alert
                        variant="info"
                        className="mt-4"
                        style={{
                            width: "70%",
                            margin: "auto",
                            borderRadius: "10px",
                            boxShadow: "0px 0px 10px 0px rgba(0,0,0,0.75)",
                        }}
                    >
                        <Alert.Heading
                            style={{
                                marginBottom: "1rem",
                            }}
                        >
                            {CourseMeetingData.course.title_long} | is currently
                            in session with {CourseMeetingData.instructor.fname}
                        </Alert.Heading>
                        <p>
                            You have two options:
                            <ol>
                                <li>
                                    <strong>Take Over:</strong> You can take
                                    over the class from the current instructor.
                                    Note: Taking over the class does not require
                                    any approval methods; hence, you will kick
                                    out the instructor. Unless you are positive
                                    about this step, do not proceed.
                                </li>
                                <li>
                                    <strong>Assist:</strong> You can assist the
                                    instructor. This will load a new view that
                                    will allow you to communicate and perform
                                    validation tasks.
                                </li>
                            </ol>
                        </p>
                        <ActionsBar
                            handleTakeOver={handleTakeOver}
                            handleAssist={handleAssignAssistant}
                        />
                    </Alert>
                </Col>
            </Row>
        </div>
    );
};

export default ClassRoomAssistance;
