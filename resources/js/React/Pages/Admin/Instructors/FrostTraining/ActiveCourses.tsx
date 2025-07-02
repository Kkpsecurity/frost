import React, { useContext } from "react";
import { Alert, Col, Container, Row } from "react-bootstrap";
import {
    CourseType,
    LaravelAdminShape,
    ValidatedInstructorShape,
} from "../../../../Config/types";
import CourseUnitItem from "./CourseUnitItem";
import InstructorPreviousCourses from "./InstructorPreviousCourses";
import PageLoader from "../../../../Components/Widgets/PageLoader";

interface Props {
    laravel: LaravelAdminShape;
    handleSetView: (view: string) => void;
    handleTakeOver: () => void;
    handleAssignAssistant: () => void;
    validatedInstructor: ValidatedInstructorShape;
    assistantId?: string;
    setAssignedAssistantId: Function;
    debug: boolean;
}

const ActiveCourses = ({
    laravel,
    handleSetView,
    validatedInstructor,
    handleTakeOver,
    handleAssignAssistant,
    assistantId,
    setAssignedAssistantId,
    debug = true,
}) => {
    if (debug === true)
        console.log("Active Courses Initialized", validatedInstructor);

    if (
        validatedInstructor?.courses &&
        validatedInstructor?.courses?.length > 0
    ) {
        return (
            <Container fluid>
                <Row>
                    <Col lg={12} className="p-3 ">
                        <h3 style={{
                            textAlign: "left",                    
                            textTransform: "uppercase",
                        }}>Open Courses {assistantId}</h3>
                        <p className="lead">Select a Course to manage</p>
                        <hr />
                        <Row>
                            {Object.values(validatedInstructor?.courses).map(
                                (course: CourseType, index: number) => (
                                    <Col key={index} xl={3} lg={4} md={6} sm={12} className="text-center mb-3">
                                        <CourseUnitItem
                                            laravel={laravel}
                                            course={course}
                                            handleSetView={handleSetView}
                                            handleTakeOver={handleTakeOver}
                                            handleAssignAssistant={handleAssignAssistant}
                                            assistantId={assistantId}                                         
                                            debug={debug}
                                            setAssignedAssistantId={setAssignedAssistantId}
                                        />
                                    </Col>
                                )
                            )}
                        </Row>
                    </Col>
                </Row>
            </Container>
        );
        
    } else {
        return (
            <div className="d-flex align-items-center justify-content-center vh-100 bg-light">
                <div
                    className="text-center"
                    style={{
                        marginTop: "-200px",
                        width: "40rem",
                    }}
                >
                    <i className="fas fa-info-circle fa-3x mb-3"></i>{" "}
                    {/* Font Awesome Info Circle Icon */}
                    <div
                        className="alert"
                        style={{
                            backgroundColor: "#f8d7da",
                            color: "#721c24",
                            borderColor: "#f5c6cb",
                            width: "75%",
                            margin: "auto",
                        }}
                        role="alert"
                    >
                        Courses are generated based on their corresponding time.
                        For instance, an 8 o'clock class will be generated at 7
                        o'clock, or 1 hour prior to its start. Currently, no
                        open courses have started. Please check back closer to
                        the course start time or try again later.
                    </div>
                </div>
            </div>
        );
    }
};

export default ActiveCourses;
