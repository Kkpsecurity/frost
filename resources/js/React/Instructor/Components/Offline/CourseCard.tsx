import React from "react";
import { CourseDate } from "./types";

interface CourseCardProps {
    course: CourseDate;
    onCourseSelect?: (course: CourseDate) => void;
}

const CourseCard: React.FC<CourseCardProps> = ({ course, onCourseSelect }) => {
    const handleCardClick = () => {
        if (onCourseSelect) {
            onCourseSelect(course);
        }
    };

    const handleButtonClick = (action: string, course: CourseDate) => {
        console.log(`Action: ${action} for course ${course.id}`);
        // TODO: Implement actual button actions
        if (action === "start_class" || action === "take_control") {
            // This action creates an InstUnit for the CourseDate
            alert(`Taking control of scheduled class: ${course.course_name}`);
            console.log(`Creating InstUnit for CourseDate ID: ${course.id}`);
            // Call API to create InstUnit (instructor takes control)
        } else if (action === "assist") {
            // Join existing InstUnit as assistant
            alert(`Assisting with: ${course.course_name}`);
            console.log(
                `Joining InstUnit ID: ${course.inst_unit?.id} as assistant`
            );
            // Call API to join as assistant
        } else if (action === "complete") {
            // Mark InstUnit as completed
            alert(`Completing class: ${course.course_name}`);
            console.log(`Completing InstUnit ID: ${course.inst_unit?.id}`);
            // Call API to mark InstUnit as completed
        }
    };

    const getButtonStyle = (action: string) => {
        switch (action) {
            case "start_class":
            case "take_control":
                return {
                    className: "btn-primary",
                    style: {
                        backgroundColor: "var(--frost-primary-color, #3b82f6)",
                    },
                    icon: "fas fa-play me-1",
                };
            case "complete":
                return {
                    className: "btn-success",
                    style: {
                        backgroundColor: "var(--frost-success-color, #22c55e)",
                    },
                    icon: "fas fa-check me-1",
                };
            case "assist":
                return {
                    className: "btn-info",
                    style: {
                        backgroundColor: "var(--frost-info-color, #17aac9)",
                    },
                    icon: "fas fa-hands-helping me-1",
                };
            case "info":
                return {
                    className: "btn-outline-secondary",
                    style: {
                        color: "var(--frost-text-color, #374151)",
                        borderColor:
                            "var(--frost-light-primary-color, #e2e8f0)",
                        backgroundColor: "transparent",
                    },
                    icon: "fas fa-info-circle me-1",
                };
            default:
                return {
                    className: "btn-secondary",
                    style: {
                        backgroundColor:
                            "var(--frost-secondary-color, #6b7280)",
                    },
                    icon: "fas fa-info me-1",
                };
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case "assigned":
                return "var(--frost-success-color, #22c55e)"; // Green - InstUnit exists, class is active
            case "unassigned":
                return "var(--frost-warning-color, #f59e0b)"; // Orange - CourseDate exists, no InstUnit yet
            case "completed":
                return "var(--frost-info-color, #17aac9)"; // Blue - InstUnit completed
            case "expired":
                return "var(--frost-danger-color, #ef4444)"; // Red - CourseDate time passed without InstUnit
            case "scheduled":
                return "var(--frost-secondary-color, #6b7280)"; // Gray - Future CourseDate
            // Legacy support
            case "live":
                return "var(--frost-success-color, #22c55e)";
            case "ready_to_start":
                return "var(--frost-warning-color, #f59e0b)";
            case "ready_to_restart":
                return "var(--frost-warning-color, #f59e0b)";
            default:
                return "var(--frost-secondary-color, #6b7280)";
        }
    };

    return (
        <div
            className="card h-100"
            style={{
                backgroundColor: "#f3f4f6",
                border: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                boxShadow:
                    "var(--frost-shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1))",
                transition: "var(--frost-transition-base, 0.2s ease-in-out)",
                cursor: onCourseSelect ? "pointer" : "default",
            }}
            onClick={handleCardClick}
        >
            {/* Card Header with Status */}
            <div
                className="card-header"
                style={{
                    backgroundColor: course.inst_unit // InstUnit exists = instructor has taken control
                        ? "var(--frost-success-color, #22c55e)"
                        : "var(--frost-secondary-color, #394867)", // CourseDate only, no InstUnit yet
                    color: "var(--frost-white-color, #ffffff)",
                    borderBottom: "none",
                }}
            >
                <h6 className="card-title mb-0 d-flex align-items-center">
                    <i
                        className={`fas ${
                            course.inst_unit
                                ? "fa-play-circle" // InstUnit exists - class is controlled
                                : "fa-clock" // CourseDate only - waiting for instructor control
                        } mr-2 me-2`}
                    ></i>
                    {course.course_name} - {course.module}
                </h6>
            </div>

            {/* Card Body with Course Stats in Circles */}
            <div className="card-body">
                <div className="row text-center mb-3">
                    <div className="col-4">
                        <div className="d-flex flex-column align-items-center">
                            <div
                                className="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "50px",
                                    height: "50px",
                                    backgroundColor:
                                        "var(--frost-info-color, #17aac9)",
                                    color: "var(--frost-white-color, #ffffff)",
                                    fontSize: "1.2rem",
                                    fontWeight: "bold",
                                }}
                            >
                                {course.lesson_count || 0}
                            </div>
                            <div
                                className="small"
                                style={{
                                    color: "var(--frost-base-color, #6b7280)",
                                    fontWeight: "500",
                                }}
                            >
                                Lessons
                            </div>
                        </div>
                    </div>

                    <div className="col-4">
                        <div className="d-flex flex-column align-items-center">
                            <div
                                className="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "50px",
                                    height: "50px",
                                    backgroundColor:
                                        "var(--frost-success-color, #22c55e)",
                                    color: "var(--frost-white-color, #ffffff)",
                                    fontSize: "0.9rem",
                                    fontWeight: "bold",
                                }}
                            >
                                {course.student_count || 0}
                            </div>
                            <div
                                className="small"
                                style={{
                                    color: "var(--frost-base-color, #6b7280)",
                                    fontWeight: "500",
                                }}
                            >
                                Students
                            </div>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="d-flex flex-column align-items-center">
                            <div
                                className="rounded-circle d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "50px",
                                    height: "50px",
                                    backgroundColor:
                                        "var(--frost-warning-color, #f59e0b)",
                                    color: "var(--frost-white-color, #ffffff)",
                                    fontSize: "0.8rem",
                                    fontWeight: "bold",
                                    textAlign: "center",
                                }}
                            >
                                {course.time || "N/A"}
                            </div>
                            <div
                                className="small"
                                style={{
                                    color: "var(--frost-base-color, #6b7280)",
                                    fontWeight: "500",
                                }}
                            >
                                Start Time
                            </div>
                        </div>
                    </div>
                </div>

                {/* Status and Instructor/Assistant Info */}
                <div className="d-flex justify-content-between align-items-center">
                    <span
                        className="badge"
                        style={{
                            backgroundColor: getStatusColor(
                                course.class_status || "unassigned"
                            ),
                            color: "var(--frost-white-color, #ffffff)",
                        }}
                    >
                        {(course.class_status || "unassigned")
                            .replace("_", " ")
                            .toUpperCase()}
                    </span>

                    <div className="text-end">
                        <small
                            className="d-block"
                            style={{
                                color: "var(--frost-base-color, #6b7280)",
                                fontSize: "0.75rem",
                            }}
                        >
                            Instructor:{" "}
                            {course.inst_unit
                                ? course.instructor_name
                                : "Not Taken Control"}
                        </small>

                        <small
                            className="d-block"
                            style={{
                                color: "var(--frost-info-color, #17aac9)",
                                fontSize: "0.7rem",
                            }}
                        >
                            Assistant:{" "}
                            {course.inst_unit
                                ? course.assistant_name || "None"
                                : "TBD"}
                        </small>
                    </div>
                </div>
            </div>

            {/* Card Footer with Action Buttons */}
            {course.buttons && (
                <div
                    className="card-footer"
                    style={{
                        backgroundColor: "var(--frost-light-bg-color, #f8fafc)",
                        borderTop:
                            "1px solid var(--frost-light-primary-color, #e2e8f0)",
                        padding: "0.75rem 1rem",
                    }}
                >
                    <div className="d-flex flex-wrap gap-2">
                        {Object.entries(course.buttons).map(
                            ([action, label]) => {
                                const buttonStyle = getButtonStyle(action);
                                return (
                                    <button
                                        key={action}
                                        className={`btn ${buttonStyle.className} btn-sm`}
                                        style={buttonStyle.style}
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleButtonClick(action, course);
                                        }}
                                    >
                                        <i className={buttonStyle.icon}></i>
                                        {label}
                                    </button>
                                );
                            }
                        )}
                    </div>
                </div>
            )}
        </div>
    );
};

export default CourseCard;
