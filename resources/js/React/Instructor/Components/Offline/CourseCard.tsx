import React, { useState } from "react";
import { CourseDate } from "./types";
import { classroomSessionAPI } from "../api/classroomSessionAPI";

interface CourseCardProps {
    course: CourseDate;
    onCourseSelect?: (course: CourseDate) => void;
    onStartClass?: (course: CourseDate) => void;
    onRefreshData?: () => void; // Add refresh callback
}

const CourseCard: React.FC<CourseCardProps> = ({
    course,
    onCourseSelect,
    onStartClass,
    onRefreshData,
}) => {
    const [isLoading, setIsLoading] = useState(false);

    const handleCardClick = () => {
        if (onCourseSelect) {
            onCourseSelect(course);
        }
    };

    const handleButtonClick = async (action: string, course: CourseDate) => {
        console.log(`Action: ${action} for course ${course.id}`);

        if (action === "start_class" || action === "take_control") {
            setIsLoading(true);
            try {
                // Call API to create InstUnit
                const response = await classroomSessionAPI.startSession(
                    course.id
                );

                if (response.success) {
                    console.log(
                        "InstUnit created/found successfully:",
                        response.data
                    );

                    // Update course object with InstUnit info if newly created
                    if (response.data) {
                        course.inst_unit = {
                            id: response.data.inst_unit_id,
                            created_by: response.data.instructor.id,
                            created_at: response.data.created_at,
                            assistant_id: response.data.assistant?.id || null,
                        };
                        course.instructor_name = response.data.instructor.name;
                        course.assistant_name =
                            response.data.assistant?.name || null;

                        // Update class status to in_progress
                        course.class_status = "in_progress";
                    }

                    // Call the parent callback to start classroom
                    if (onStartClass) {
                        onStartClass(course);
                    } else {
                        alert(`Class session started: ${course.course_name}`);
                    }

                    // Trigger data refresh to show updated status immediately
                    if (onRefreshData) {
                        setTimeout(() => onRefreshData(), 500); // Small delay to ensure backend is updated
                    }
                } else {
                    alert(`Failed to start class session: ${response.message}`);
                }
            } catch (error: any) {
                console.error("Error starting classroom session:", error);
                alert(`Error starting class: ${error.message}`);
            } finally {
                setIsLoading(false);
            }
        } else if (action === "assist") {
            alert(`Assisting with: ${course.course_name}`);
            console.log(
                `Joining InstUnit ID: ${course.inst_unit?.id} as assistant`
            );
        } else if (action === "complete") {
            if (course.inst_unit?.id) {
                setIsLoading(true);
                try {
                    const response = await classroomSessionAPI.completeSession(
                        course.inst_unit.id
                    );

                    if (response.success) {
                        alert(`Class completed: ${course.course_name}`);
                        console.log(
                            `Completed InstUnit ID: ${course.inst_unit.id}`
                        );

                        // Update class status to completed
                        course.class_status = "completed";

                        // Trigger data refresh to show updated status immediately
                        if (onRefreshData) {
                            setTimeout(() => onRefreshData(), 500);
                        }
                    } else {
                        alert(`Failed to complete class: ${response.message}`);
                    }
                } catch (error: any) {
                    console.error("Error completing classroom session:", error);
                    alert(`Error completing class: ${error.message}`);
                } finally {
                    setIsLoading(false);
                }
            } else {
                alert(`No active session found for: ${course.course_name}`);
            }
        }
    };

    return (
        <div
            className="card card-primary card-outline h-100"
            onClick={handleCardClick}
            style={{ cursor: onCourseSelect ? "pointer" : "default" }}
        >
            {/* Card Header */}
            <div className="card-header">
                <div className="d-flex justify-content-between align-items-center">
                    <h3 className="card-title mb-0">
                        <i className="fas fa-graduation-cap me-2"></i>
                        {course.course_name}
                    </h3>
                    <span
                        className={`badge ${
                            course.class_status === "unassigned"
                                ? "bg-warning"
                                : course.class_status === "assigned"
                                ? "bg-success"
                                : course.class_status === "completed"
                                ? "bg-info"
                                : course.class_status === "in_progress"
                                ? "bg-primary"
                                : "bg-secondary"
                        }`}
                    >
                        {(course.class_status || "unassigned")
                            .replace("_", " ")
                            .toUpperCase()}
                    </span>
                </div>
                <div className="card-tools mt-2">
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
                            <span className="info-box-number text-primary h4">
                                {course.lesson_count || 0}
                            </span>
                            <span className="info-box-text small text-muted">
                                Lessons
                            </span>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="info-box-content">
                            <span className="info-box-number text-success h4">
                                {course.student_count || 0}
                            </span>
                            <span className="info-box-text small text-muted">
                                Students
                            </span>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="info-box-content">
                            <span className="info-box-number text-warning h6">
                                {course.time || "N/A"}
                            </span>
                            <span className="info-box-text small text-muted">
                                Start
                            </span>
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

            {/* Card Footer with Action Buttons */}
            {course.buttons && (
                <div className="card-footer">
                    <div className="d-flex gap-2">
                        {Object.entries(course.buttons).map(
                            ([action, label]) => {
                                const isStartClass =
                                    action === "start_class" ||
                                    action === "take_control";
                                const isComplete = action === "complete";
                                const isAssist = action === "assist";

                                return (
                                    <button
                                        key={action}
                                        className={`btn ${
                                            isStartClass
                                                ? "btn-primary"
                                                : isComplete
                                                ? "btn-success"
                                                : isAssist
                                                ? "btn-info"
                                                : "btn-secondary"
                                        } btn-sm flex-fill`}
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleButtonClick(action, course);
                                        }}
                                        disabled={isLoading}
                                    >
                                        {isLoading ? (
                                            <>
                                                <i className="fas fa-spinner fa-spin me-1"></i>
                                                Processing...
                                            </>
                                        ) : (
                                            <>
                                                <i
                                                    className={`fas ${
                                                        action ===
                                                            "start_class" ||
                                                        action ===
                                                            "take_control"
                                                            ? "fa-play"
                                                            : action ===
                                                              "complete"
                                                            ? "fa-check"
                                                            : action ===
                                                              "assist"
                                                            ? "fa-hands-helping"
                                                            : "fa-info-circle"
                                                    } me-1`}
                                                ></i>
                                                {label}
                                            </>
                                        )}
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
