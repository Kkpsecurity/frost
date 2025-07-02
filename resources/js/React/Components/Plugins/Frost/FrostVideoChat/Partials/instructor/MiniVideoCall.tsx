import React, { useContext } from "react";
import { Button, Card } from "react-bootstrap";
import { UserListBlockType } from "../../../../../../Config/types";
import {
    InstructorVideoContext,
    InstructorVideoInterface,
} from "../../../../../../Context/Admin/FrostVideoContext";

import "./MiniVideoCall.css";

const MiniVideoCall: React.FC = ({ debug = false }: { debug: boolean }) => {
    const videoContext = useContext(
        InstructorVideoContext
    ) as InstructorVideoInterface;
    const allQueueStudents = videoContext.allQueueStudents;
    if (debug) console.log("VIDEO: ", allQueueStudents);

    return (
        <>
            {Array.isArray(allQueueStudents) && allQueueStudents.length > 0 ? (
                <Card className="bg-gray d-flex align-items-start mb-3">           
                    {allQueueStudents.map((student: UserListBlockType) => (
                        <Button
                            key={student.id}
                            variant="primary"
                            className="btn-sm student-button"
                            onClick={() =>
                                videoContext.handleCallStudent(
                                    student.id,
                                    student.course_auth_id,
                                    videoContext.courseDateId
                                )
                            }
                        >
                            <img
                                src={student.avatar}
                                alt={student.fname}
                                className="student-avatar"
                            />
                            <span className="student-name">{student.fname} {student.lname}</span>
                        </Button>
                    ))}
                </Card>
            ) : (
                <Card className="bg-gray d-flex justify-content-center align-items-center mb-3">
                    <span className="p-3 font-weight-bold">
                        No Queued Users found
                    </span>
                </Card>
            )}
        </>
    );
    
};

export default MiniVideoCall;
