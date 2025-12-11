import React from "react";
import { SchoolDashboardTitleBarProps } from "../types/props/classroom.props";

const SchoolDashboardTitleBar = ({
    title,
    subtitle,
    icon,
    onBackToDashboard,
    classroomStatus = null,
}: SchoolDashboardTitleBarProps) => {
    return (
        <div
            className="section-title"
            style={{
                background:
                    "linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color))",
                color: "white",
                padding: "20px 30px",
                boxShadow: "0 4px 15px rgba(0,0,0,0.1)",
                position: "relative",
                marginTop: "10px",
            }}
        >
            <div className="d-flex align-items-center justify-content-between mt-2">
                <div>
                    <h4 className="mb-0 fw-semibold text-white">
                        {icon && <span className="me-2">{icon}</span>}
                        {title}
                        {classroomStatus && (
                            <span
                                className={`badge ms-2 ${
                                    classroomStatus === "ONLINE"
                                        ? "bg-success"
                                        : classroomStatus === "WAITING"
                                        ? "bg-warning"
                                        : "bg-secondary"
                                }`}
                                style={{
                                    fontSize: "0.9rem",
                                }}
                            >
                                <i
                                    className={`fas ${
                                        classroomStatus === "ONLINE"
                                            ? "fa-wifi"
                                            : classroomStatus === "WAITING"
                                            ? "fa-clock"
                                            : "fa-wifi-slash"
                                    } me-1`}
                                ></i>
                                {classroomStatus}
                            </span>
                        )}
                    </h4>

                    {subtitle && (
                        <p
                            className="mb-0 mt-2 text-white-50"
                            style={{ fontSize: "1rem" }}
                        >
                            {subtitle}
                        </p>
                    )}
                </div>

                <div className="d-flex align-items-center gap-3">
                    <button
                        type="button"
                        className="btn btn-outline-light btn-sm d-flex align-items-center"
                        onClick={onBackToDashboard}
                        title="Back to Dashboard"
                        aria-label="Back to Dashboard"
                    >
                        <i
                            className="fas fa-tachometer-alt"
                            aria-hidden="true"
                        />
                    </button>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboardTitleBar;

