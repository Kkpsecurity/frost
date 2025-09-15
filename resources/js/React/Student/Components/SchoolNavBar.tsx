import React from "react";
import { SchoolNavBarProps } from "../types/props/classroom.props";

const SchoolNavBar: React.FC<SchoolNavBarProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    return (
        <nav className="mb-4 p-3">
            <div
                className="nav nav-tabs"
                id="nav-tab"
                role="tablist"
                style={{
                    borderBottom: "2px solid var(--frost-primary-color)",
                }}
            >
                <button
                    className="nav-link active"
                    id="nav-home-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-home"
                    type="button"
                    role="tab"
                    style={{
                        borderRadius: "8px 8px 0 0",
                        fontWeight: "500",
                        padding: "12px 20px",
                    }}
                >
                    <i className="fas fa-home me-2"></i>
                    Home
                </button>
                <button
                    className="nav-link"
                    id="nav-videos-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-videos"
                    type="button"
                    role="tab"
                    style={{
                        borderRadius: "8px 8px 0 0",
                        fontWeight: "500",
                        padding: "12px 20px",
                    }}
                >
                    <i className="fas fa-video me-2"></i>
                    Videos
                </button>
                <button
                    className="nav-link"
                    id="nav-documents-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-documents"
                    type="button"
                    role="tab"
                    style={{
                        borderRadius: "8px 8px 0 0",
                        fontWeight: "500",
                        padding: "12px 20px",
                    }}
                >
                    <i className="fas fa-file-pdf me-2"></i>
                    Documents
                </button>
            </div>
        </nav>
    );
};

export default SchoolNavBar;
