import React from "react";
import { CourseDateType } from "@/React/Student/types/classroom";

interface DynamicTitleBarProps {
    title?: string;
    subtitle?: string;
    icon?: React.ReactNode;
    courseDate?: CourseDateType | null;
    onBackToDashboard?: () => void;
}

const DynamicTitleBar: React.FC<DynamicTitleBarProps> = ({
    title,
    subtitle,
    icon,
    courseDate,
    onBackToDashboard,
}) => {
    const isLive = !!courseDate;

    const heading =
        title ?? (isLive ? "Live Classroom Session" : "Offline Classroom");
    const sub =
        subtitle ??
        (isLive
            ? "Interactive learning experience"
            : "No live session scheduled");

    return (
        <div className="d-flex align-items-center justify-content-between mt-2 shadow-md">
            {/* Inline styles for pulse kept local to the component */}
            <style>{`
        .status-dot{width:16px;height:16px;border-radius:50%;background:#6c757d}
        .status-dot.live{background:#28a745;box-shadow:0 0 0 0 rgba(40,167,69,.7);animation:pulse 2s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(40,167,69,.7)}70%{box-shadow:0 0 0 10px rgba(40,167,69,0)}100%{box-shadow:0 0 0 0 rgba(40,167,69,0)}}
      `}</style>

            <div>
                <h4 className="mb-0 fw-semibold">
                    {icon ? (
                        <span className="me-2">{icon}</span>
                    ) : (
                        <i
                            className={`me-2 ${
                                isLive
                                    ? "fas fa-video text-success"
                                    : "fas fa-chalkboard text-secondary"
                            }`}
                            aria-hidden="true"
                        />
                    )}
                    {heading}
                    <span
                        className={`badge ms-2 ${
                            isLive ? "bg-success" : "bg-secondary"
                        }`}
                    >
                        {isLive ? "LIVE" : "OFFLINE"}
                    </span>
                </h4>

                <p
                    className="mb-0 mt-2 opacity-75"
                    style={{ fontSize: "1rem" }}
                >
                    {sub}
                </p>
            </div>

            <div className="d-flex align-items-center gap-3">
                {onBackToDashboard && (
                    <button
                        type="button"
                        className="btn btn-outline-light btn-sm d-flex align-items-center"
                        onClick={onBackToDashboard}
                        title="Back to Dashboard"
                        aria-label="Back to Dashboard"
                    >
                        <i className="fas fa-tachometer-alt" aria-hidden="true" />
                    </button>
                )}
            </div>
        </div>
    );
};

export default DynamicTitleBar;
