import React from "react";
import { Col, ListGroup, Row } from "react-bootstrap";
import StudentListBlock from "../instructor/StudentListBlock";
import { LaravelAdminShape, UserListBlockType } from "../../../../../../Config/types";

interface ActiveStudentCallListProps {
    allStudents: UserListBlockType[];
    callStudentId: number;
    acceptUserId: number;
    courseDateId: number;
    callHasEnded: boolean;
    handleCallStudent: (studentId: number, studentAuthId: number, courseDateId: number) => void;
    handleEndCall: (studentId: number) => void;
    laravel: LaravelAdminShape;
}

const ActiveStudentCallList: React.FC<ActiveStudentCallListProps> = ({
    allStudents,
    callStudentId,
    acceptUserId,
    courseDateId,
    callHasEnded,
    handleCallStudent,
    handleEndCall,
    laravel,
}) => {
    // console.log("allStudents", allStudents);

    return allStudents?.length > 0 ? (
        <ListGroup className="d-flex m-0 p-0">
            {allStudents.map((student) => (
                <ListGroup.Item
                    key={student.id}
                    className="d-flex justify-content-between align-items-center m-0 p-0"
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
        <p className="alert alert-danger">No Active Students</p>
    );
};

export default ActiveStudentCallList;
