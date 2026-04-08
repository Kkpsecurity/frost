import React, { useState } from "react";
import BanEnrollmentModal, { CourseItem } from "./Modals/BanEnrollmentModal";

interface StudentToolsPanelProps {
    courses: CourseItem[];
    onRefresh: () => void;
    toolPermissions: Record<string, boolean>;
}

const StudentToolsPanel: React.FC<StudentToolsPanelProps> = ({
    courses,
    onRefresh,
    toolPermissions,
}) => {
    const [modalCourse, setModalCourse] = useState<CourseItem | null>(null);

    if (!courses || courses.length === 0) {
        return (
            <div className="text-muted small">
                <i className="fas fa-info-circle mr-1"></i>
                No enrollment found for this course.
            </div>
        );
    }

    return (
        <>
            {courses.map((course) => {
                const banned = !!course.disabled_at;

                return (
                    <div
                        key={course.id}
                        className="card mb-2"
                        style={{ borderLeft: `4px solid ${banned ? "#dc3545" : "#28a745"}` }}
                    >
                        <div className="card-body py-2 px-3">

                            {/* Course name + status */}
                            <div className="d-flex align-items-center justify-content-between mb-2">
                                <strong className="small">{course.name}</strong>
                                <small className="text-muted">Auth #{course.id}</small>
                            </div>

                            {/* Tool rows */}
                            <div className="list-group list-group-flush">

                                {/* Enrollment Ban */}
                                <div className="list-group-item px-0 py-2 d-flex align-items-center justify-content-between"
                                    style={{ background: "transparent" }}>
                                    <div>
                                        <span className={`badge ${banned ? "badge-danger" : "badge-success"} mr-2`}>
                                            <i className={`fas fa-${banned ? "ban" : "check"} mr-1`}></i>
                                            {banned ? "Banned" : "Active"}
                                        </span>
                                        <small className="text-muted">Course Enrollment</small>
                                        {banned && course.disabled_reason && (
                                            <div className="small text-danger mt-1">
                                                <em>{course.disabled_reason}</em>
                                            </div>
                                        )}
                                    </div>
                                    {toolPermissions["ban-course-auth"] && (
                                        <button
                                            className={`btn btn-xs ${banned ? "btn-outline-success" : "btn-outline-danger"}`}
                                            onClick={() => setModalCourse(course)}
                                        >
                                            <i className={`fas fa-${banned ? "undo" : "ban"} mr-1`}></i>
                                            {banned ? "Reinstate" : "Ban"}
                                        </button>
                                    )}
                                </div>

                                {/* Class Day Access — info row pointing to History */}
                                <div className="list-group-item px-0 py-2 d-flex align-items-center justify-content-between"
                                    style={{ background: "transparent" }}>
                                    <div>
                                        <span className="badge badge-secondary mr-2">
                                            <i className="fas fa-calendar-day mr-1"></i>
                                            Day Access
                                        </span>
                                        <small className="text-muted">Eject / Reinstate class day</small>
                                    </div>
                                    <small className="text-muted">
                                        <i className="fas fa-arrow-right mr-1"></i>History tab
                                    </small>
                                </div>

                                {/* Grant Lesson */}
                                <div className="list-group-item px-0 py-2 d-flex align-items-center justify-content-between"
                                    style={{ background: "transparent" }}>
                                    <div>
                                        <span className="badge badge-secondary mr-2">
                                            <i className="fas fa-plus-circle mr-1"></i>
                                            Grant Lesson
                                        </span>
                                        <small className="text-muted">Enter student into class</small>
                                    </div>
                                    <small className="text-muted">
                                        <i className="fas fa-arrow-right mr-1"></i>History tab
                                    </small>
                                </div>

                                {/* Lessons Status */}
                                <div className="list-group-item px-0 py-2 d-flex align-items-center justify-content-between"
                                    style={{ background: "transparent" }}>
                                    <div>
                                        <span className="badge badge-secondary mr-2">
                                            <i className="fas fa-book-open mr-1"></i>
                                            Lessons Status
                                        </span>
                                        <small className="text-muted">Reverse DNC / view progress</small>
                                    </div>
                                    <small className="text-muted">
                                        <i className="fas fa-arrow-right mr-1"></i>History tab
                                    </small>
                                </div>

                            </div>
                        </div>
                    </div>
                );
            })}

            {modalCourse && (
                <BanEnrollmentModal
                    course={modalCourse}
                    onClose={() => setModalCourse(null)}
                    onSuccess={onRefresh}
                />
            )}
        </>
    );
};

export default StudentToolsPanel;
