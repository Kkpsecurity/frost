import React from "react";
import { CourseDate } from "./types";

interface CourseCardProps {
    course: CourseDate;
    onCourseSelect?: (course: CourseDate) => void;
    onStartClass?: (course: CourseDate) => void;
}

const CourseCard: React.FC<CourseCardProps> = ({
    course,
    onCourseSelect,
    onStartClass,
}) => {
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
            if (onStartClass) {
                onStartClass(course);
            } else {
                alert(
                    `Taking control of scheduled class: ${course.course_name}`
                );
                console.log(
                    `Creating InstUnit for CourseDate ID: ${course.id}`
                );
                // Call API to create InstUnit (instructor takes control)
            }
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

    return (
        <div className="card card-primary card-outline h-100">
            {/* Card Header */}
            <div className="card-header">
                <div className="d-flex justify-content-between align-items-center">
                    <h3 className="card-title">
                        <i className="fas fa-graduation-cap me-2"></i>
                        {course.course_name}
                    </h3>
                    <span className={`badge ${
                        course.class_status === "unassigned"
                            ? "bg-warning"
                            : course.class_status === "assigned"
                            ? "bg-success"
                            : course.class_status === "completed"
                            ? "bg-info"
                            : course.class_status === "in_progress"
                            ? "bg-primary"
                            : "bg-secondary"
                    }`}>
                        {(course.class_status || "unassigned").replace("_", " ").toUpperCase()}
                    </span>
                </div>
                <div className="card-tools">
                    <span className="badge bg-light text-dark">
                        <i className="fas fa-bookmark me-1"></i>
                        {course.module}
                    </span>
                </div>
            </div>

            {/* Card Body */}
            <div className="card-body">
                {/* Stats Row */}
                <div className="row text-center mb-3">
                    <div className="col-4">
                        <div className="info-box-content">
                            <span className="info-box-number text-primary">
                                <i className="fas fa-book me-1"></i>
                                {course.lesson_count || 0}
                            </span>
                            <span className="info-box-text">Lessons</span>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="info-box-content">
                            <span className="info-box-number text-success">
                                <i className="fas fa-users me-1"></i>
                                {course.student_count || 0}
                            </span>
                            <span className="info-box-text">Students</span>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="info-box-content">
                            <span className="info-box-number text-warning">
                                <i className="fas fa-clock me-1"></i>
                                {course.time?.replace(' ', '') || 'N/A'}
                            </span>
                            <span className="info-box-text">Start</span>
                        </div>
                    </div>
                </div>

                {/* Instructor Information */}
                <div className="row">
                    <div className="col-6">
                        <div className="description-block border-right">
                            <span className="description-header">
                                {course.inst_unit && course.instructor_name
                                    ? course.instructor_name
                                    : "Not Assigned"}
                            </span>
                            <span className="description-text">Instructor</span>
                        </div>
                    </div>
                    <div className="col-6">
                        <div className="description-block">
                            <span className="description-header">
                                {course.inst_unit && course.assistant_name
                                    ? course.assistant_name
                                    : "TBD"}
                            </span>
                            <span className="description-text">Assistant</span>
                        </div>
                    </div>
                </div>
            </div>
                            <div
                                className="d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "56px",
                                    height: "56px",
                                    backgroundColor: "rgba(255, 255, 255, 0.2)",
                                    borderRadius: "16px",
                                    margin: "0 auto",
                                    backdropFilter: "blur(10px)",
                                }}
                            >
                                <div className="text-center">
                                    <div
                                        style={{
                                            fontSize: "1.5rem",
                                            fontWeight: "bold",
                                        }}
                                    >
                                        {course.lesson_count || 0}
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "0.7rem",
                                            opacity: 0.8,
                                        }}
                                    >
                                        Lessons
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-4">
                        <div className="text-center">
                            <div
                                className="d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "56px",
                                    height: "56px",
                                    backgroundColor: "rgba(255, 255, 255, 0.2)",
                                    borderRadius: "16px",
                                    margin: "0 auto",
                                    backdropFilter: "blur(10px)",
                                }}
                            >
                                <div className="text-center">
                                    <div
                                        style={{
                                            fontSize: "1.5rem",
                                            fontWeight: "bold",
                                        }}
                                    >
                                        {course.student_count || 0}
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "0.7rem",
                                            opacity: 0.8,
                                        }}
                                    >
                                        Students
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-4">
                        <div className="text-center">
                            <div
                                className="d-flex align-items-center justify-content-center mb-2"
                                style={{
                                    width: "56px",
                                    height: "56px",
                                    backgroundColor: "rgba(255, 255, 255, 0.2)",
                                    borderRadius: "16px",
                                    margin: "0 auto",
                                    backdropFilter: "blur(10px)",
                                }}
                            >
                                <div className="text-center">
                                    <div
                                        style={{
                                            fontSize: "1rem",
                                            fontWeight: "bold",
                                        }}
                                    >
                                        {course.time || "N/A"}
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "0.7rem",
                                            opacity: 0.8,
                                        }}
                                    >
                                        Start Time
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Instructor Info */}
                <div
                    className="mb-4 p-3"
                    style={{
                        backgroundColor: "rgba(255, 255, 255, 0.1)",
                        borderRadius: "12px",
                        backdropFilter: "blur(10px)",
                    }}
                >
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <div style={{ fontSize: "0.85rem", opacity: 0.8 }}>
                                Instructor
                            </div>
                            <div
                                style={{ fontSize: "1rem", fontWeight: "600" }}
                            >
                                {course.instructor_name || "Not Assigned"}
                            </div>
                        </div>
                        <div className="text-end">
                            <div style={{ fontSize: "0.85rem", opacity: 0.8 }}>
                                Assistant
                            </div>
                            <div
                                style={{ fontSize: "1rem", fontWeight: "600" }}
                            >
                                {course.assistant_name || "TBD"}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Action Buttons */}
                {course.buttons && (
                    <div className="d-flex gap-2 flex-wrap">
                        {Object.entries(course.buttons).map(
                            ([action, label]) => {
                                const isStartClass =
                                    action === "start_class" ||
                                    action === "take_control";
                                return (
                                    <button
                                        key={action}
                                        className="btn flex-fill"
                                        style={{
                                            backgroundColor: isStartClass
                                                ? "rgba(255, 255, 255, 1)"
                                                : "rgba(255, 255, 255, 0.2)",
                                            color: isStartClass
                                                ? "#667eea"
                                                : "white",
                                            border: "none",
                                            borderRadius: "10px",
                                            padding: "12px 16px",
                                            fontWeight: "600",
                                            fontSize: "0.9rem",
                                            backdropFilter: "blur(10px)",
                                            transition: "all 0.2s ease",
                                        }}
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleButtonClick(action, course);
                                        }}
                                        onMouseEnter={(e) => {
                                            if (isStartClass) {
                                                e.currentTarget.style.backgroundColor =
                                                    "rgba(255, 255, 255, 0.95)";
                                                e.currentTarget.style.transform =
                                                    "translateY(-1px)";
                                            } else {
                                                e.currentTarget.style.backgroundColor =
                                                    "rgba(255, 255, 255, 0.3)";
                                            }
                                        }}
                                        onMouseLeave={(e) => {
                                            if (isStartClass) {
                                                e.currentTarget.style.backgroundColor =
                                                    "rgba(255, 255, 255, 1)";
                                                e.currentTarget.style.transform =
                                                    "translateY(0)";
                                            } else {
                                                e.currentTarget.style.backgroundColor =
                                                    "rgba(255, 255, 255, 0.2)";
                                            }
                                        }}
                                    >
                                        <i
                                            className={`fas ${
                                                action === "start_class" ||
                                                action === "take_control"
                                                    ? "fa-play"
                                                    : action === "complete"
                                                    ? "fa-check"
                                                    : action === "assist"
                                                    ? "fa-hands-helping"
                                                    : "fa-info"
                                            } me-2`}
                                        ></i>
                                        {label}
                                    </button>
                                );
                            }
                        )}
                    </div>
                )}
            </div>
        </div>
    );
};

export default CourseCard;
