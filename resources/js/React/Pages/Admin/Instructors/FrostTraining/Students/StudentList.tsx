import React, { useEffect, useMemo, useState } from "react";
import {
    Alert,
    ListGroup,
    Button,
    OverlayTrigger,
    Tooltip,
} from "react-bootstrap";

import {
    CourseAuthType,
    InstUnitType,
    StudentLessonType,
    StudentType,
    StudentUnitType,
} from "../../../../../Config/types";

import VerifiedStudentToolBar from "./VerifiedStudentToolBar";
import { StyledUserListItem } from "../../../../../Styles/StyledUserListItem.styled";
import UserListGroupItem from "./UserListGroupItem";
import Loader from "../../../../../Components/Widgets/Loader";
import colorConfig from "../Partials/colorConfig";

interface StudentListProps {
    students: StudentType[];
    studentUnit: StudentUnitType;
    studentUnitLesson: StudentLessonType[] | null;
    selectedStudentId: number | null;
    ValidateStudent: React.MouseEventHandler<HTMLButtonElement>;
    SelectStudent: Function;
    activeLesson: number | null;
    instUnit: InstUnitType | null;
}

const StudentList: React.FC<StudentListProps> = ({
    students,
    studentUnit,
    studentUnitLesson,
    selectedStudentId,
    ValidateStudent,
    SelectStudent,
    activeLesson,
    instUnit,
}) => {
    const [studentLesson, setStudentLesson] =
        useState<StudentLessonType | null>(null);

    const verificationLookup = useMemo(() => {
        if (!studentUnit) return new Map();

        const lookup = new Map();
        const status =
            studentUnit?.validation_action === "verified"
                ? "accepted"
                : "declined";
        lookup.set(studentUnit?.course_auth_id, status);
        return lookup;
    }, [studentUnit]);

    const ColorInfoButtons = () => {
        const buttons = [
            {
                color: colorConfig.successItem,
                tooltip: "All tasks have been completed",
            },
            {
                color: colorConfig.dangerItem,
                tooltip: "DNC Ban, Ejection, or similar issues",
            },
            {
                color: colorConfig.primaryItem,
                tooltip: "Student ready for validation",
            },
            {
                color: colorConfig.dangerItem,
                tooltip: "Student has not yet Excepted the Agreement",
            },
            {
                color: colorConfig.warningItem,
                tooltip: "One or more images are missing",
            },
            { color: colorConfig.defaultItem, tooltip: "Default status" },
        ];

        return (
            <div className="d-flex flex-wrap bg-dark p-2 m-0">
                {buttons.map((btn, index) => (
                    <OverlayTrigger
                        key={index}
                        placement="top"
                        overlay={
                            <Tooltip id={`tooltip-${index}`}>
                                {btn.tooltip}
                            </Tooltip>
                        }
                    >
                        <Button
                            style={{
                                backgroundColor: btn.color,
                                width: "25px",
                                height: "25px",
                                padding: 0,
                                margin: "2px",
                                border: "none",
                            }}
                        />
                    </OverlayTrigger>
                ))}
            </div>
        );
    };

    const presentForLesson = (
        studentUnit: StudentUnitType,
        studentUnitLesson: StudentLessonType[],
        activeLessonId: number | null
    ) => {
        if (!activeLessonId) return null;

        const lessonData =
            studentUnitLesson &&
            studentUnitLesson.find(
                (lesson) => lesson.lesson_id === activeLessonId
            );
        return lessonData;
    };

    useEffect(() => {
        // Find the student lesson data for the active lesson
        const lessonData: StudentLessonType = presentForLesson(
            studentUnit,
            studentUnitLesson,
            activeLesson
        )!;
        setStudentLesson(lessonData);
    }, [studentUnit, activeLesson, selectedStudentId]);

    return (
        <StyledUserListItem>
            <ColorInfoButtons />
            <ListGroup defaultActiveKey={0} className="student-list p-0">
                {students.length > 0 ? (
                    students.map((student) => {
                        const isVerified =
                            verificationLookup.get(student.id) || null;
                        const isSelected = selectedStudentId === student.id;

                        return (
                            <UserListGroupItem
                                key={student.id}
                                student={student}
                                courseAuth={student.courseAuth}
                                studentLesson={studentLesson}
                                ValidateStudent={ValidateStudent}
                                SelectStudent={() => SelectStudent(student.id)}
                                courseDateId={student.course_date_id}
                                studentUnit={studentUnit}
                                activeLesson={activeLesson}
                                isSelected={isSelected}
                                isVerified={isVerified}
                                instUnit={instUnit}
                            />
                        );
                    })
                ) : (
                    <Alert variant="danger">No students found.</Alert>
                )}
            </ListGroup>
        </StyledUserListItem>
    );
};

export default StudentList;
