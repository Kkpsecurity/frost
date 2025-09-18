import React, { useState } from "react";

const StudentSidebar = ({
    instructor,
    classroomStatus,
}: {
    instructor: { fname: string; lname: string } | null;
    classroomStatus: string;
}) => {
    const [isCollapsed, setIsCollapsed] = useState(false);

    const toggleCollapse = () => {
        setIsCollapsed(!isCollapsed);
    };
    return (
        <div
            className="dashboard-side bg-dark"
            style={{
                width: isCollapsed ? "60px" : "300px",
                minHeight: "100%",
                overflowY: "auto",
                margin: 0,
                padding: 0,
                transition: "width 0.3s ease",
            }}
        >
            {/* Toggle Button */}
            <div className="p-2 text-white border-bottom border-secondary d-flex justify-content-between align-items-center">
                {!isCollapsed && (
                    <h5 className="mb-0">
                        <i className="fas fa-chalkboard-teacher me-2"></i>
                        Instructor
                    </h5>
                )}
                <button
                    type="button"
                    className="btn btn-sm btn-outline-light d-flex align-items-center justify-content-center"
                    onClick={toggleCollapse}
                    style={{ width: "32px", height: "32px" }}
                    title={isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"}
                    aria-label={
                        isCollapsed ? "Expand Sidebar" : "Collapse Sidebar"
                    }
                >
                    <i
                        className={`fas ${
                            isCollapsed ? "fa-chevron-right" : "fa-chevron-left"
                        }`}
                        aria-hidden="true"
                    />
                </button>
            </div>

            {!isCollapsed && (
                <>
                    {instructor && (
                        <div className="px-3 py-2 text-white">
                            <p className="mb-0 opacity-75">
                                {instructor.fname} {instructor.lname}
                            </p>
                        </div>
                    )}
                </>
            )}

            {!isCollapsed && (
                <div className="p-3">
                    <div className="text-white">
                        <h6 className="text-uppercase small text-muted mb-3">
                            Classroom Status
                        </h6>
                        <div className="d-flex align-items-center">
                            <span
                                className={`badge me-2 ${
                                    classroomStatus === "active"
                                        ? "bg-success"
                                        : "bg-secondary"
                                }`}
                            >
                                {classroomStatus === "active"
                                    ? "ACTIVE"
                                    : "INACTIVE"}
                            </span>
                            <small className="text-muted">
                                {classroomStatus === "active"
                                    ? "Live session available"
                                    : "No live session"}
                            </small>
                        </div>
                    </div>
                </div>
            )}

            {/* Collapsed state - show only icons */}
            {isCollapsed && (
                <div className="d-flex flex-column align-items-center py-3">
                    <div
                        className="text-center mb-3"
                        title={`Instructor: ${instructor?.fname} ${instructor?.lname}`}
                    >
                        <i className="fas fa-chalkboard-teacher text-white fs-4"></i>
                    </div>
                    <div
                        className="text-center"
                        title={`Status: ${
                            classroomStatus === "active" ? "Active" : "Inactive"
                        }`}
                    >
                        <i
                            className={`fas fa-circle ${
                                classroomStatus === "active"
                                    ? "text-success"
                                    : "text-secondary"
                            }`}
                        ></i>
                    </div>
                </div>
            )}
        </div>
    );
};

export default StudentSidebar;
