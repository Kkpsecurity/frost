import React from "react";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";

interface MainOnlineProps {
    classroom: any;
    student: any;
    onBackToDashboard: () => void;
}

/**
 * MainOnline - Live classroom mode
 *
 * Shown when:
 * - CourseDate exists (class scheduled)
 * - InstUnit exists (instructor has started class)
 *
 * Features:
 * - Live video/audio
 * - Screen sharing
 * - Real-time chat
 * - Live lesson presentation
 * - Student interactions
 * - Attendance tracking
 */
const MainOnline: React.FC<MainOnlineProps> = ({ classroom, student, onBackToDashboard }) => {
    const { courseDate, instructor, instUnit } = classroom;

    const zoom = classroom?.data?.zoom;
    const isZoomReady = !!zoom?.is_ready;
    const screenShareUrl = zoom?.screen_share_url as string | undefined;

    return (
        <div
            className="online-classroom"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                paddingTop: "60px", // Space for main site header
                paddingBottom: "3rem",
            }}
        >
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title="Live Classroom"
                subtitle={`Instructor: ${instructor?.name || "N/A"}`}
                icon={<i className="fas fa-video"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="ONLINE"
            />

            <div className="container-fluid px-0">
                <div className="row g-0">
                    <div className="col-12 px-0">
                        {/* Main Classroom Layout */}
                        <div className="row g-0">
                            {/* Left Sidebar - Lessons */}
                            <div className="col-md-2">
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        height: "calc(100vh - 250px)",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-list me-2"></i>
                                            Today's Lessons
                                        </h6>
                                    </div>
                                    <div
                                        className="card-body"
                                        style={{ overflowY: "auto" }}
                                    >
                                        <p
                                            style={{
                                                color: "#95a5a6",
                                                fontSize: "0.875rem",
                                            }}
                                        >
                                            Lessons for today will appear here.
                                        </p>
                                        {/* TODO: Add lessons sidebar component */}
                                    </div>
                                </div>
                            </div>

                            {/* Center - Main Content */}
                            <div className="col-md-7">
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        borderRadius: "0",
                                        overflow: "hidden",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-desktop me-2"></i>
                                            Screen Share / Presentation
                                        </h6>
                                    </div>
                                    <div
                                        className="card-body p-0"
                                        style={{
                                            backgroundColor: "transparent",
                                        }}
                                    >
                                        <div
                                            className="ratio ratio-16x9"
                                            style={{
                                                backgroundColor: "#000",
                                                borderRadius: "0",
                                                overflow: "hidden",
                                            }}
                                        >
                                            {isZoomReady && screenShareUrl ? (
                                                <iframe
                                                    title="Zoom Screen Share"
                                                    src={screenShareUrl}
                                                    style={{
                                                        width: "100%",
                                                        height: "100%",
                                                        border: "none",
                                                    }}
                                                    allow="camera; microphone; fullscreen; display-capture"
                                                />
                                            ) : (
                                                <div className="d-flex align-items-center justify-content-center">
                                                    <div className="text-center">
                                                        <i
                                                            className="fas fa-tv fa-4x mb-3"
                                                            style={{
                                                                color: "#95a5a6",
                                                            }}
                                                        ></i>
                                                        <p
                                                            style={{
                                                                color: "#95a5a6",
                                                                marginBottom:
                                                                    "0.5rem",
                                                            }}
                                                        >
                                                            Wait for instructor
                                                            to start screen
                                                            share
                                                        </p>
                                                        <small
                                                            style={{
                                                                color: "#95a5a6",
                                                                opacity: 0.8,
                                                            }}
                                                        >
                                                            This panel will
                                                            auto-load when
                                                            ready.
                                                        </small>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Right Sidebar - Students */}
                            <div className="col-md-3">
                                <div
                                    className="card"
                                    style={{
                                        backgroundColor: "#34495e",
                                        border: "none",
                                        height: "calc(100vh - 250px)",
                                    }}
                                >
                                    <div
                                        className="card-header"
                                        style={{
                                            backgroundColor: "#2c3e50",
                                            borderBottom:
                                                "1px solid rgba(255,255,255,0.1)",
                                        }}
                                    >
                                        <h6
                                            className="mb-0"
                                            style={{ color: "white" }}
                                        >
                                            <i className="fas fa-info-circle me-2"></i>
                                            Class Info
                                        </h6>
                                    </div>
                                    <div
                                        className="card-body"
                                        style={{ overflowY: "auto" }}
                                    >
                                        <p
                                            style={{
                                                color: "#95a5a6",
                                                fontSize: "0.875rem",
                                            }}
                                        >
                                            This panel is reserved for
                                            instructor tools (chat, roster,
                                            verification).
                                        </p>
                                        <div
                                            className="mt-3"
                                            style={{
                                                color: "#ecf0f1",
                                                fontSize: "0.875rem",
                                            }}
                                        >
                                            <div className="mb-2">
                                                <strong>Instructor:</strong>{" "}
                                                {instructor?.name || "N/A"}
                                            </div>
                                            <div className="mb-2">
                                                <strong>Session:</strong>{" "}
                                                {instUnit?.id
                                                    ? `#${instUnit.id}`
                                                    : "N/A"}
                                            </div>
                                            <div className="mb-2">
                                                <strong>Course Date:</strong>{" "}
                                                {courseDate?.id
                                                    ? `#${courseDate.id}`
                                                    : "N/A"}
                                            </div>
                                            <div>
                                                <strong>Student:</strong>{" "}
                                                {student?.name ||
                                                    student?.email ||
                                                    "N/A"}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MainOnline;
