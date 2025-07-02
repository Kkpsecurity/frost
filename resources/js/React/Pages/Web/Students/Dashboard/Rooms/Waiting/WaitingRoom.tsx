import React from "react";
import { Alert, Card, ListGroup } from "react-bootstrap";
import { StudentType } from "../../../../../../Config/types";

interface Props {
    student: StudentType;
    debug: boolean;
}
/**
 *
 * Main Notes: We want to give a better explanation on why user are on this screen
 * when the instructor start the lesson, each student in the class will get a copy of that lesson indicating there attendance how ever if a student is not on in the class during this process they will not recieve there copy indication messing the lesson along with the othjer detail let update this component to do two thing look professional using bootstrap 5 sybtax and clear and easy toread text nad fonts :: \\develc\webroot\frost\docs\Frost\# Student Portal Overview.md
 * @returns
 */

const WaitingRoom: React.FC<Props> = ({ student, debug = false }) => {
    if (debug) console.log("Student Waiting Area Initialized");

    return (
        <div
            className="d-flex h-100 w-100 bg-light mb-0 justify-content-center align-items-center text-white"
            style={{
                width: "55rem",
            }}
        >
            <div>
                <div className="container mt-5 mb-5">
                    <Card className="shadow">
                        <Card.Header>
                            <h3 className="text-dark">Class in Session</h3>
                        </Card.Header>
                        <Card.Body>
                            <Alert variant="light" className="m-2 auto">
                                <h2>Lesson in Progress</h2>
                                <ListGroup
                                    style={{
                                        fontSize: "1.0rem",
                                    }}
                                >
                                    <ListGroup.Item>
                                        <strong>Reasons for being here:</strong>
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        1. **Late Arrival:** Entering the
                                        classroom while a lesson is in progress
                                        will result in landing here.
                                    </ListGroup.Item>
                                    <ListGroup.Item>
                                        2. **Browsing Other Sites:** Avoid
                                        browsing external websites during class.
                                        If you're distracted during lesson
                                        changes, you might miss the attendance
                                        call and end up here.
                                    </ListGroup.Item>

                                    <ListGroup.Item>
                                        3. **Internet Connectivity Issues:** If
                                        your internet connection drops, you
                                        won't be automatically redirected here
                                        if you were already in class. However,
                                        being out of class, including browsing
                                        other sites during lesson changes, may
                                        result in landing here.
                                    </ListGroup.Item>
                                </ListGroup>
                                <p
                                    style={{
                                        fontSize: "1.4rem",
                                        padding: "10px",
                                        fontWeight: "bold",
                                        lineHeight: "28px",
                                    }}
                                >
                                    You won't be able to complete the current
                                    lesson right now, but during the offline
                                    period, you can retake it to make up for the
                                    missed session. If you need assistance or
                                    have questions, contact your instructor for
                                    further guidance.
                                </p>
                            </Alert>

                            <figure className="bg-white rounded w-35rem p-3">
                                <h4 className="text-dark">
                                    Please be patient. The instructor will be
                                    starting a new lesson soon!
                                </h4>
                                <figcaption className="blockquote-footer mb-0 font-italic">
                                    <b>
                                        {student.fname} {student.lname}
                                    </b>
                                </figcaption>
                            </figure>
                        </Card.Body>
                    </Card>
                </div>
            </div>
        </div>
    );
};

export default WaitingRoom;
