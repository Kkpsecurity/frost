// If there is a student that has been called and the page is refresh the user list can have about 100 Students can we make the slected studen slide in focus ::::

import React, { useEffect, useRef } from "react";
import styled from "styled-components";
import {
    LaravelAdminShape,
    UserListBlockType,
} from "../../../../../../Config/types";

import {
    Avatar,
    AvatarImage,
    Details,
    CallButton,
    StudentBlock,
    ButtonBlock,
} from "../../../../../../Styles/StyledListStudent.styled";

interface StudentListBlockProps {
    student: UserListBlockType | null;
    callStudentId: number | null;
    acceptUserId: number | null;
    courseDateId: number;
    callHasEnded: boolean;
    handleEndCall: (studentId: number) => void;
    handleCallStudent: (
        studentId: number,
        studentAuthId: number,
        courseDateId: number
    ) => void;
    laravel: LaravelAdminShape;
}

const StudentListBlock: React.FC<StudentListBlockProps> = ({
    student, 
    callStudentId,
    acceptUserId,
    courseDateId,
    callHasEnded,
    handleEndCall,
    handleCallStudent,
    laravel,
}) => {
    const setCallState = () => {
        if (acceptUserId === callStudentId) {
            return "success";
        } else if (callStudentId && !acceptUserId) {
            return "info";
        } else {
            return "gray    ";
        }
    };

    const studentRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        // 2. Check if this student is the one being called
        if (student.id === callStudentId && studentRef.current) {
            studentRef.current.scrollIntoView({
                behavior: "smooth", // Smooth scrolling
                block: "center", // Center the student in the view
            });
        }
    }, [student.id, callStudentId]);

    return (
        <StudentBlock
            ref={studentRef}
            bgcolor={setCallState}
            style={{
                background:
                    student.id === laravel.user.id
                        ? "#999"
                        : student.id === callStudentId
                        ? "lightgreen" // You can change this color to your preferred highlight color.
                        : undefined,
                cursor: student.id === laravel.user.id && "not-allowed",
            }}
        >
            <Avatar>
                <a href="#" data-abc="true">
                    <AvatarImage src={student.avatar} alt="student avatar" />
                </a>
            </Avatar>
            <Details>
                <div className="name">
                    <h5>
                        <a href="#" className="item-author" data-abc="true">
                            {student.fname} {student.lname}
                        </a>
                    </h5>
                </div>
                <div className="email">
                    <div className="item-except text-md h-2x">
                        {student.email.length > 20 ? (
                            <span title={student.email}>
                                {student.email.slice(0, 20)}...
                            </span>
                        ) : (
                            student.email
                        )}
                    </div>
                </div>
            </Details>
            <ButtonBlock>
                {student.id === laravel.user.id ? (
                    <CallButton
                        disabled
                        style={{
                            background:
                                student.id === laravel.user.id && "#999",
                            cursor: "not-allowed",
                        }}
                    >
                        <i className="fa fa-phone text-muted"></i>
                    </CallButton>
                ) : student.id === callStudentId ? (
                    acceptUserId > 0 ? (
                        <CallButton
                            id={String(student.id)}
                            active
                            onClick={() => handleEndCall(student.id)}
                        >
                            <i className="fa fa-phone text-red"></i>
                        </CallButton>
                    ) : (
                        <CallButton
                            id={String(student.id)}
                            className="bg-danger"
                            onClick={() => handleEndCall(student.id)}
                            active
                        >
                            <i className="fa fa-phone-hangup text-yellow flash"></i>
                        </CallButton>
                    )
                ) : callHasEnded ? (
                    <CallButton
                        id={String(student.id)}
                        onClick={() => {
                            handleCallStudent(
                                student.id,
                                student.course_auth_id,
                                courseDateId
                            );
                        }}
                    >
                        <i className="fa fa-phone text-green"></i>
                    </CallButton>
                ) : (
                    <CallButton
                        id={String(student.id)}
                        onClick={() => {
                            handleCallStudent(
                                student.id,
                                student.course_auth_id,
                                courseDateId
                            );
                        }}
                    >
                        <i className="fa fa-phone text-green"></i>
                    </CallButton>
                )}
            </ButtonBlock>
        </StudentBlock>
    );
};

export default StudentListBlock;
