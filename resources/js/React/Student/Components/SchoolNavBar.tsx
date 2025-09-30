import React from "react";
import { SchoolNavBarProps } from "../types/props/classroom.props";

const SchoolNavBar: React.FC<SchoolNavBarProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    return (
        <nav className="mb-0">
            {/* Add CSS styles for Frost theme tabs */}
            <style>{`
                .frost-nav-tabs {
                    background: linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color));
                    border-bottom: 3px solid var(--frost-highlight-color);
                    padding: 0 20px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }

                .frost-nav-tabs .nav-link {
                    color: var(--frost-light-color);
                    background: transparent;
                    border: none;
                    border-radius: var(--frost-radius-lg) var(--frost-radius-lg) 0 0;
                    margin-right: 4px;
                    transition: all var(--frost-transition-base);
                    position: relative;
                    overflow: hidden;
                }

                .frost-nav-tabs .nav-link:hover {
                    color: var(--frost-highlight-color);
                    background: rgba(255,255,255,0.1);
                    transform: translateY(-2px);
                }

                .frost-nav-tabs .nav-link.active {
                    color: var(--frost-primary-color);
                    background: var(--frost-white-color);
                    border-bottom: 3px solid var(--frost-highlight-color);
                    font-weight: var(--font-weight-semibold);
                }

                .frost-nav-tabs .nav-link.active:hover {
                    transform: none;
                }

                .frost-nav-tabs .nav-link::before {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 0;
                    height: 3px;
                    background: var(--frost-highlight-color);
                    transition: width var(--frost-transition-base);
                }

                .frost-nav-tabs .nav-link:hover::before {
                    width: 100%;
                }

                .frost-nav-tabs .nav-link.active::before {
                    width: 100%;
                }

                /* Video Lesson Styles */
                .lesson-item:hover {
                    background: rgba(var(--frost-primary-rgb), 0.05);
                    border-radius: var(--frost-radius-md);
                }

                .lesson-item .status-indicator {
                    transition: all var(--frost-transition-base);
                }

                .lesson-item:hover .status-indicator {
                    transform: scale(1.1);
                }

                .sidebar-card {
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    transition: all var(--frost-transition-base);
                }

                .sidebar-card:hover {
                    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
                }

                .pool-status-card {
                    transition: all var(--frost-transition-base);
                }

                .pool-status-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
            `}</style>

            <div
                className="nav nav-tabs frost-nav-tabs"
                id="nav-tab"
                role="tablist"
            >
                <button
                    className="nav-link active"
                    id="nav-home-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-home"
                    type="button"
                    role="tab"
                    style={{
                        padding: "16px 24px",
                        fontSize: "var(--frost-font-size-base)",
                        fontWeight: "var(--font-weight-medium)",
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
                        padding: "16px 24px",
                        fontSize: "var(--frost-font-size-base)",
                        fontWeight: "var(--font-weight-medium)",
                    }}
                >
                    <i className="fas fa-play-circle me-2"></i>
                    Video Lessons
                </button>
                <button
                    className="nav-link"
                    id="nav-documents-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-documents"
                    type="button"
                    role="tab"
                    style={{
                        padding: "16px 24px",
                        fontSize: "var(--frost-font-size-base)",
                        fontWeight: "var(--font-weight-medium)",
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
