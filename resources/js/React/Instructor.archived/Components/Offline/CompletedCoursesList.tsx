import React from "react";
import { CompletedCourse } from "./useCompletedCourses";

interface CompletedCoursesListProps {
    courses: CompletedCourse[];
}

const CompletedCoursesList: React.FC<CompletedCoursesListProps> = ({ courses }) => {
    if (courses.length === 0) {
        return (
            <div className="text-center py-4">
                <i className="fas fa-check-circle fa-3x text-muted mb-3"></i>
                <h5 className="text-muted">No Completed Courses</h5>
                <p className="text-muted">No courses have been completed yet.</p>
            </div>
        );
    }

    return (
        <div className="row">
            {courses.map((course) => (
                <div key={course.id} className="col-md-6 col-lg-4 mb-3">
                    <div
                        className="card h-100"
                        style={{
                            backgroundColor: "#f3f4f6",
                            border: "1px solid var(--frost-light-primary-color, #e2e8f0)",
                            boxShadow: "var(--frost-shadow-sm, 0 1px 3px rgba(0, 0, 0, 0.1))",
                        }}
                    >
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-start mb-2">
                                <h6
                                    className="card-title mb-1"
                                    style={{
                                        color: "var(--frost-primary-color, #212a3e)",
                                        fontSize: "0.95rem",
                                        fontWeight: "600",
                                    }}
                                >
                                    {course.course_name}
                                </h6>
                                <span
                                    className="badge"
                                    style={{
                                        backgroundColor: "var(--frost-success-color, #10b981)",
                                        color: "white",
                                        fontSize: "0.75rem",
                                    }}
                                >
                                    Completed
                                </span>
                            </div>

                            <p
                                className="card-text mb-2"
                                style={{
                                    color: "var(--frost-base-color, #6b7280)",
                                    fontSize: "0.875rem",
                                }}
                            >
                                {course.course_unit_title}
                            </p>

                            <div className="row text-center mb-3">
                                <div className="col-4">
                                    <div
                                        style={{
                                            fontSize: "0.75rem",
                                            color: "var(--frost-base-color, #6b7280)",
                                        }}
                                    >
                                        Students
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "1.1rem",
                                            fontWeight: "600",
                                            color: "var(--frost-primary-color, #212a3e)",
                                        }}
                                    >
                                        {course.student_count}
                                    </div>
                                </div>
                                <div className="col-4">
                                    <div
                                        style={{
                                            fontSize: "0.75rem",
                                            color: "var(--frost-base-color, #6b7280)",
                                        }}
                                    >
                                        Duration
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "0.85rem",
                                            fontWeight: "600",
                                            color: "var(--frost-primary-color, #212a3e)",
                                        }}
                                    >
                                        {course.duration}
                                    </div>
                                </div>
                                <div className="col-4">
                                    <div
                                        style={{
                                            fontSize: "0.75rem",
                                            color: "var(--frost-base-color, #6b7280)",
                                        }}
                                    >
                                        Sequence
                                    </div>
                                    <div
                                        style={{
                                            fontSize: "1.1rem",
                                            fontWeight: "600",
                                            color: "var(--frost-primary-color, #212a3e)",
                                        }}
                                    >
                                        {course.sequence}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-auto">
                                <small
                                    style={{
                                        color: "var(--frost-base-color, #6b7280)",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    <i className="fas fa-calendar me-1"></i>
                                    Course Date: {course.course_date}
                                </small>
                                <br />
                                <small
                                    style={{
                                        color: "var(--frost-base-color, #6b7280)",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    <i className="fas fa-check me-1"></i>
                                    Completed: {course.completion_date}
                                </small>
                                <br />
                                <small
                                    style={{
                                        color: "var(--frost-base-color, #6b7280)",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    <i className="fas fa-user me-1"></i>
                                    By: {course.completed_by_name}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
};

export default CompletedCoursesList;
