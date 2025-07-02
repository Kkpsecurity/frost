import React from "react";
import { Card, Col, ListGroup, Row } from "react-bootstrap";

const InstructorData = ({
    data,
    laravel,
    colorSet,
    handleMakeCall,
    inComingRequest,
    makeCall,
    callAccepted,
    darkMode,
    debug,
}) => {
    return (
        <Card className={`list-group-item list-group-item-action mb-2 p-0`}>
            <Card.Header
                style={{
                    backgroundColor: colorSet.navbarBgColor,
                }}
            >
                <div style={{ color: colorSet.navbarTextColor }}>
                    Instructor
                </div>
            </Card.Header>
            <Row className={`g-0`}>
                <Col
                    md={4}
                    className="d-flex justify-content-center align-items-center p-3"
                    style={{
                        backgroundColor: colorSet.navbarBgColor2,
                    }}
                >
                    <div className="avatar-icon">
                        <img
                            src={data?.instructor.avatar}
                            className="avatar"
                            alt="Instructor Avatar"
                        />
                    </div>
                </Col>

                <Col
                    md={8}
                    style={{
                        backgroundColor: colorSet.navbarBgColor,
                    }}
                >
                    <ListGroup variant="flush">
                        <ListGroup.Item
                            className="d-flex justify-content-between"
                            style={{
                                backgroundColor: colorSet.navbarBgColor2,
                                border: "1px solid" + colorSet.navbarBgColor,
                            }}
                        >
                            <strong>Name:</strong> {data?.instructor.fname}{" "}
                            {data?.instructor.lname}
                        </ListGroup.Item>

                        <ListGroup.Item
                            className="d-flex align-items-center justify-content-end"
                            style={{
                                backgroundColor: colorSet.navbarBgColor2,
                                border: "1px solid" + colorSet.navbarBgColor,
                            }}
                        >
                            {laravel.user.id === 17 &&
                                (!callAccepted ? (
                                    inComingRequest ? (
                                        <div
                                            style={{
                                                fontSize: "1.0rem",
                                                fontWeight: "bold",
                                                color: "yellow",
                                            }}
                                        >
                                            Incoming Call{" "}
                                            <i className="fa fa-dot fa-2x"></i>
                                        </div>
                                    ) : (
                                        <button
                                            className={`btn btn-${
                                                darkMode ? "dark" : "light"
                                            }`}
                                            onClick={() => handleMakeCall()}
                                        >
                                            {makeCall
                                                ? "Cancel Call"
                                                : "Request Call"}
                                        </button>
                                    )
                                ) : (
                                    <div
                                        style={{
                                            fontSize: "1.0rem",
                                            fontWeight: "bold",
                                            color: "lightgreen",
                                        }}
                                    >
                                        Call In Progress
                                    </div>
                                ))}
                        </ListGroup.Item>
                    </ListGroup>
                </Col>
            </Row>
        </Card>
    );
};

export default InstructorData;
