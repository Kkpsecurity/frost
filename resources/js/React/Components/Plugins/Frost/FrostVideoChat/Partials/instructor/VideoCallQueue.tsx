import React from "react";
import { ListGroup, Row, Col } from "react-bootstrap";
import { LaravelAdminShape, StudentType, UserListBlockType } from "../../../../../../Config/types";
import StudentListBlock from "./StudentListBlock";

/**
 * The Video Queue component hold all the students that are in the queue to be called.
 */
interface VideoCallQueueProps {
    queuedStudents: UserListBlockType[] | null;
    callStudentId: number;
    acceptUserId: number;
    courseDateId: number;
    callHasEnded: boolean;
    handleCallStudent: (
        studentId: number,
        studentAuthId: number,
        courseDateId: number
    ) => void;
    handleEndCall: (studentId: number) => void;
    laravel: LaravelAdminShape;
}

const VideoCallQueue: React.FC<VideoCallQueueProps> = ({
    queuedStudents,
    callStudentId,
    acceptUserId,
    courseDateId,
    callHasEnded,
    handleCallStudent,
    handleEndCall,
    laravel,
}) => {
    console.log("queuedStudents", queuedStudents);

    return queuedStudents.length > 0 ? (
        <ListGroup className="d-flex m-0 p-0">
            {queuedStudents.map((student) => (
                <ListGroup.Item
                    key={student.id}
                    className="d-flex justify-content-between align-items-center"
                    style={{
                        width: "100%",
                        margin: "0px",
                        padding: "0px",
                    }}
                >
                    <StudentListBlock
                        student={student}
                        callStudentId={callStudentId}
                        acceptUserId={acceptUserId}
                        courseDateId={courseDateId}
                        callHasEnded={callHasEnded}
                        handleEndCall={handleEndCall}
                        handleCallStudent={handleCallStudent}
                        laravel={laravel}
                    />
                </ListGroup.Item>
            ))}
        </ListGroup>
    ) : (
        <p className="alert alert-danger">No Queued Students</p>
    );
};

export default VideoCallQueue;
