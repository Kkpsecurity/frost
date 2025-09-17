import React from "react";
import { SchoolDashboardTitleBarProps } from "../types/props/classroom.props";

const SchoolDashboardTitleBar = ({
    title,
    subtitle,
    icon,
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
            }}
        >
            <div className="d-flex align-items-center justify-content-between">
                <div>
                    <h4
                        style={{
                            margin: 0,
                            fontWeight: "600",
                        }}
                    >
                        <i className="fas fa-video me-3"></i>
                        Live Classroom Session
                    </h4>
                    <p
                        style={{
                            margin: "8px 0 0 0",
                            opacity: 0.9,
                            fontSize: "1rem",
                        }}
                    >
                        Interactive learning experience
                    </p>
                </div>
                <div
                    className="pulse-dot"
                    style={{
                        width: "16px",
                        height: "16px",
                        backgroundColor: "#28a745",
                        borderRadius: "50%",
                        animation: "pulse 2s infinite",
                        boxShadow: "0 0 0 0 rgba(40, 167, 69, 1)",
                    }}
                ></div>
            </div>
        </div>
    );
};

export default SchoolDashboardTitleBar;
