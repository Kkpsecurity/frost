import { defaultAvatar } from "@/React/Config/helper";
import React from "react";

interface CourseCardInstructorListProps {
    course: {
        inst_unit?: any;
    };
    instructorName: string;
    instructorAvatar?: string;
    assistantName: string;
    assistantAvatar?: string;
}

const CourseCardInstructorList: React.FC<CourseCardInstructorListProps> = ({
    course,
    instructorName,
    instructorAvatar,
    assistantName,
    assistantAvatar,
}) => {
    return (
        <div className="d-flex flex-column gap-2 text-light">
            <div className="col-12">
                <div
                    className="d-flex align-items-center gap-2 p-2 border rounded"
                    style={{
                        background: "#2f3a46",
                        borderColor: "#3d4b58",
                    }}
                >
                    {course.inst_unit && instructorName !== "Not Assigned" ? (
                        <>
                            <img
                                src={
                                    instructorAvatar ||
                                    defaultAvatar(instructorName)
                                }
                                alt={instructorName}
                                width="28"
                                height="28"
                                className="rounded-circle mr-2"
                            />
                            <div className="d-flex flex-column flex-grow-1">
                                <span
                                    className="small text-dark text-uppercase fw-bold mr-2"
                                    style={{
                                        fontSize: "0.65rem",
                                        letterSpacing: "0.5px",
                                    }}
                                >
                                    INSTRUCTOR
                                </span>
                                <span
                                    className="fw-bold"
                                    style={{
                                        fontSize: "1rem",
                                        color: "#EEEEEE",
                                        fontWeight: "700",
                                    }}
                                >
                                    {instructorName}
                                </span>
                            </div>
                        </>
                    ) : (
                        <>
                            <div
                                className="rounded-circle d-flex align-items-center justify-content-center mr-2"
                                style={{
                                    width: 28,
                                    height: 28,
                                    background: "#44515f",
                                    color: "#e5e7eb",
                                }}
                            >
                                <i
                                    className="fas fa-user"
                                    aria-hidden="true"
                                    style={{ fontSize: "0.7rem" }}
                                />
                            </div>
                            <div className="d-flex flex-column flex-grow-1">
                                <span
                                    className="small text-uppercase fw-bold"
                                    style={{
                                        fontSize: "0.65rem",
                                        letterSpacing: "0.5px",
                                        color: "#9ca3af",
                                    }}
                                >
                                    INSTRUCTOR
                                </span>
                                <span
                                    className="small"
                                    style={{ color: "#eeeeee" }}
                                >
                                    {instructorName}
                                </span>
                            </div>
                        </>
                    )}
                </div>
            </div>
            <div className="col-12">
                <div
                    className="d-flex align-items-center gap-2 p-2 border rounded"
                    style={{
                        background: "#2f3a46",
                        borderColor: "#3d4b58",
                    }}
                >
                    {course.inst_unit && assistantName !== "TBD" ? (
                        <>
                            <img
                                src={
                                    assistantAvatar ||
                                    defaultAvatar(assistantName)
                                }
                                alt={assistantName}
                                width="28"
                                height="28"
                                className="rounded-circle mr-2"
                            />
                            <div className="d-flex flex-column flex-grow-1">
                                <span
                                    className="small text-dark text-uppercase fw-bold"
                                    style={{
                                        fontSize: "0.65rem",
                                        letterSpacing: "0.5px",
                                    }}
                                >
                                    ASSISTANT
                                </span>
                                <span
                                    className="fw-bold"
                                    style={{
                                        fontSize: "1rem",
                                        color: "#EEEEEE",
                                        fontWeight: "700",
                                    }}
                                >
                                    {assistantName}
                                </span>
                            </div>
                        </>
                    ) : (
                        <>
                            <div
                                className="rounded-circle d-flex align-items-center justify-content-center mr-2Ok lets move to the student side"
                                style={{
                                    width: 28,
                                    height: 28,
                                    background: "#44515f",
                                    color: "#e5e7eb",
                                }}
                            >
                                <i
                                    className="fas fa-user"
                                    aria-hidden="true"
                                    style={{ fontSize: "0.7rem" }}
                                />
                            </div>
                            <div className="d-flex flex-column flex-grow-1">
                                <span
                                    className="small text-uppercase fw-bold"
                                    style={{
                                        fontSize: "0.65rem",
                                        letterSpacing: "0.5px",
                                        color: "#9ca3af",
                                    }}
                                >
                                    ASSISTANT
                                </span>
                                <span
                                    className="small"
                                    style={{ color: "#e5e7eb" }}
                                >
                                    {assistantName}
                                </span>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CourseCardInstructorList;
