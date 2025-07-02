import React from "react";
import {
    CourseAuthType,
    InstUnitType,
    StudentLessonType,
    StudentType,
    StudentUnitType,
} from "../../../../../Config/types";
import { Col, ListGroup, Row } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faBan, faCheckCircle } from "@fortawesome/free-solid-svg-icons";
import colorConfig from "../Partials/colorConfig";
import useStudentClassroomStatusHook from "../../../../../Hooks/Admin/useStudentClassroomStatusHook";

interface UserListGroupItemProps {
    student: StudentType;
    courseAuth: CourseAuthType | null;
    studentLesson: StudentLessonType | null;
    ValidateStudent: React.MouseEventHandler<HTMLButtonElement>;
    SelectStudent: Function;
    courseDateId: number | null;
    activeLesson: number | null;
    instUnit: InstUnitType | null;
    studentUnit: StudentUnitType | null,
    isSelected: boolean;
    isVerified: string;
}

const UserListGroupItem: React.FC<UserListGroupItemProps> = ({
    student,
    studentLesson,
    courseDateId,
    ValidateStudent,
    SelectStudent,
    studentUnit,
    activeLesson,
    instUnit,
    isSelected,
    isVerified,
}) => {
    const { studentCurrentStatus, isStudentPresent } =
        useStudentClassroomStatusHook({
            student,
            courseAuth: {} as CourseAuthType,
            studentLesson,
            courseDateId,
            studentUnit,
            instUnit,
            activeLessonId: activeLesson,
        });

    const determineRowColor = () => {
        const statusColorMap = {
            NotInLesson: colorConfig.dangerItem,
            DNCed: colorConfig.dangerItem,
            Banned: colorConfig.dangerItem,
            NotAgreedTo: colorConfig.dangerItem,
            PhotosMissing: colorConfig.warningItem,
            Validated: colorConfig.successItem,
            Ejected: colorConfig.dangerItem, // Assuming "Ejected" is similar in severity to "Banned"
            NotInClass: colorConfig.dangerItem, // Assuming you want to treat "NotInClass" similar to "NotInLesson"
            CheckPending: colorConfig.defaultItem, // Default or catch-all status
        };

        // Default to CheckPending or another status if not in map
        let color =
            statusColorMap[studentCurrentStatus] || colorConfig.defaultItem;

        if (isSelected) {
            color = colorConfig.activeItem;
        }

        return color;
    };

    // Usage example within a component
    const validateRowStatusColor: React.CSSProperties = {
        backgroundColor: determineRowColor(),
    };

    return (
        <ListGroup.Item
            key={student.course_auth_id}
            style={validateRowStatusColor}
        >
            <Row style={validateRowStatusColor}>
                <Col
                    md={2}
                    sm={2}
                    style={{
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                    }}
                >
                    <div className="avatar">
                        <img
                            src={student.avatar ?? ""}
                            alt="student-profile"
                            className="profile-photo"
                        />
                    </div>
                </Col>
                <Col md={10} sm={10}>
                    <h5>
                        <span className="profile-link">
                            {student.fname} {student.lname}
                            {isVerified === "accepted" ? (
                                <FontAwesomeIcon
                                    icon={faCheckCircle}
                                    style={{ color: "green" }}
                                />
                            ) : isVerified === "declined" ? (
                                <FontAwesomeIcon
                                    icon={faBan}
                                    style={{ color: "red" }}
                                />
                            ) : null}
                        </span>
                    </h5>
                    <p className="text-muted">{student.email}</p>
                    {isSelected ? (
                        <div>
                            <button
                                id={JSON.stringify(student.course_auth_id)}
                                type="button"
                                className="btn btn-sm btn-success float-right"
                                onClick={(e) => ValidateStudent(e)}
                            >
                                Verify Student
                            </button>
                        </div>
                    ) : null}
                    <button
                        id={JSON.stringify(student.id)}
                        className="btn btn-sm btn-primary m-1 float-end"
                        onClick={() => SelectStudent(student.id)}
                    >
                        View Student Card
                    </button>
                </Col>
            </Row>
        </ListGroup.Item>
    );
};

export default UserListGroupItem;
