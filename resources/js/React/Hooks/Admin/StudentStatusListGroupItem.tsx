import React, { useEffect, useState } from "react";
import { ListGroup, OverlayTrigger, Tooltip } from "react-bootstrap";
import useStudentClassroomStatusHook from "./useStudentClassroomStatusHook";
import {
    CourseAuthType,
    InstUnitType,
    StudentLessonType,
    StudentType,
    StudentMessageType,
    StudentUnitType,
} from "../../Config/types";

import { statusMessages, progressMessages } from "../../Config/studentStatuses";

interface StudentStatusListGroupItemProps {
    student: StudentType;
    courseDateId: number | null;
    activeLesson: number | null;
    studentLesson: StudentLessonType | null;
    courseAuth: CourseAuthType | null;
    studentUnit: StudentUnitType | null;
    instUnit: InstUnitType | null;
}

const StudentStatusListGroupItem = ({
    student,
    courseDateId,
    activeLesson,
    studentLesson,
    courseAuth,
    studentUnit,
    instUnit,
}: StudentStatusListGroupItemProps) => {
    if (!student) return null;
    
    const [allMessages, setAllMessages] = useState<{ [key: string]: StudentMessageType }>({});
    
    const { studentCurrentStatus, studentProgress, isStudentPresent } =
        useStudentClassroomStatusHook({
            student,
            courseAuth,
            studentLesson,
            courseDateId,
            studentUnit,
            instUnit,
            activeLessonId: activeLesson,
        });
   
    useEffect(() => {
        const displayMessages = { ...statusMessages, ...progressMessages };        
        setAllMessages(displayMessages);
    }, []);

    useEffect(() => {
        const defaultStatus = {
            message: "Status Unknown",
            detail: "An Unknown Status was returned",
            bgColor: "#FFF",
        };

        // Check if the current status or progress is a key in allMessages
        const currentStatusKey = studentCurrentStatus === "Active" ? studentProgress : studentCurrentStatus;
        
        const currentStatus = allMessages[currentStatusKey] || defaultStatus;
        setStatus(currentStatus);
    }, [studentCurrentStatus, studentProgress, allMessages]);

    const [status, setStatus] = useState<StudentMessageType>(allMessages['default'] || {
        message: "Status Unknown",
        detail: "Waiting for status update...",
        bgColor: "#FFF",
    });
    
    return (
        <ListGroup.Item
            style={{ backgroundColor: status.bgColor ?? "#FFF", color: "#000" }}
            className="d-flex justify-content-between"
        >
            <span>
                {status.message} 
                {status.detail && (
                    <OverlayTrigger
                        placement="top"
                        overlay={
                            <Tooltip id={`tooltip-status-${student.id}`}>
                                {status.detail}
                            </Tooltip>
                        }
                    >
                        <span>
                            <sup>
                                <i className="fa fa-exclamation-circle mr-2" />
                            </sup>
                        </span>
                    </OverlayTrigger>
                )}
            </span>
            
            <span>
                {isStudentPresent ? (
                    <i className="fa fa-check-circle text-success" />
                ) : (
                    <i className="fa fa-times-circle text-danger" />
                )}
            </span>
        </ListGroup.Item>
    );
};

export default StudentStatusListGroupItem;
