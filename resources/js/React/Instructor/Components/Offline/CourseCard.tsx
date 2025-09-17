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

    return (
        <div
            className="card h-100"
            style={{
                backgroundColor: "#f3f4f6",
                border: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                boxShadow: "var(--frost-shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1))",
                transition: "var(--frost-transition-base, 0.2s ease-in-out)",
                cursor: onCourseSelect ? "pointer" : "default"
            }}
            onClick={handleCardClick}
        >
            {/* Card Header with Status */}
            <div
                className="card-header"
                style={{
                    backgroundColor: course.is_live
                        ? "var(--frost-success-color, #22c55e)"
                        : "var(--frost-secondary-color, #394867)",
                    color: "var(--frost-white-color, #ffffff)",
                    borderBottom: "none",
                }}
            >
                <h6 className="card-title mb-0 d-flex align-items-center">
                    <i
                        className={`fas ${
                            course.is_live ? "fa-play-circle" : "fa-clock"
                        } me-2`}
                    ></i>
                    {course.course_name} - {course.class_day}
                </h6>
            </div>

            {/* Card Body with Course Stats */}
            <div className="card-body">
                <div className="row text-center mb-3">
                    <div className="col-4">
                        <div
                            className="small fw-bold"
                            style={{
                                color: "var(--frost-info-color, #17aac9)",
                            }}
                        >
                            {course.lesson_count}
                        </div>
                        <div
                            className="small"
                            style={{
                                color: "var(--frost-base-color, #6b7280)",
                            }}
                        >
                            Lessons
                        </div>
                    </div>
                    <div className="col-4">
                        <div
                            className="small fw-bold"
                            style={{
                                color: "var(--frost-info-color, #17aac9)",
                            }}
                        >
                            {course.student_count}
                        </div>
                        <div
                            className="small"
                            style={{
                                color: "var(--frost-base-color, #6b7280)",
                            }}
                        >
                            Students
                        </div>
                    </div>
                    <div className="col-4">
                        <div
                            className="small fw-bold"
                            style={{
                                color: "var(--frost-info-color, #17aac9)",
                            }}
                        >
                            {course.start_time}
                        </div>
                        <div
                            className="small"
                            style={{
                                color: "var(--frost-base-color, #6b7280)",
                            }}
                        >
                            Start Time
                        </div>
                    </div>
                </div>

                {/* Footer with Status and Instructor */}
                <div className="d-flex justify-content-between align-items-center">
                    <span
                        className="badge"
                        style={{
                            backgroundColor: course.is_live
                                ? "var(--frost-success-color, #22c55e)"
                                : "var(--frost-warning-color, #f59e0b)",
                            color: "var(--frost-white-color, #ffffff)",
                        }}
                    >
                        {course.status}
                    </span>
                    <small
                        style={{
                            color: "var(--frost-base-color, #6b7280)",
                        }}
                    >
                        Instructor: {course.instructor}
                    </small>
                </div>
            </div>
        </div>
    );
};

export default CourseCard;
