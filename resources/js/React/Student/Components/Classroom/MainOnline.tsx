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

            <div className="container-fluid">
                <div className="row">
                    <div className="col-12">

                        {/* Main Classroom Layout */}
                        <div className="row">
                            {/* Left Sidebar - Lessons */}
                            <div className="col-md-2">
                                <div className="card" style={{ backgroundColor: "#34495e", border: "none", height: "calc(100vh - 250px)" }}>
                                    <div className="card-header" style={{ backgroundColor: "#2c3e50", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-list me-2"></i>
                                            Today's Lessons
                                        </h6>
                                    </div>
                                    <div className="card-body" style={{ overflowY: "auto" }}>
                                        <p style={{ color: "#95a5a6", fontSize: "0.875rem" }}>
                                            Lessons for today will appear here.
                                        </p>
                                        {/* TODO: Add lessons sidebar component */}
                                    </div>
                                </div>
                            </div>

                            {/* Center - Main Content */}
                            <div className="col-md-7">
                                <div className="card" style={{ backgroundColor: "#34495e", border: "none", height: "calc(100vh - 250px)" }}>
                                    <div className="card-header" style={{ backgroundColor: "#2c3e50", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-desktop me-2"></i>
                                            Screen Share / Presentation
                                        </h6>
                                    </div>
                                    <div className="card-body d-flex align-items-center justify-content-center" style={{ backgroundColor: "#000" }}>
                                        <div className="text-center">
                                            <i className="fas fa-tv fa-4x mb-3" style={{ color: "#95a5a6" }}></i>
                                            <p style={{ color: "#95a5a6" }}>
                                                Screen share will appear here
                                            </p>
                                        </div>
                                        {/* TODO: Add screen share component */}
                                    </div>
                                </div>

                                {/* Chat Section Below */}
                                <div className="card mt-3" style={{ backgroundColor: "#34495e", border: "none", height: "200px" }}>
                                    <div className="card-header" style={{ backgroundColor: "#2c3e50", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-comments me-2"></i>
                                            Class Chat
                                        </h6>
                                    </div>
                                    <div className="card-body" style={{ overflowY: "auto" }}>
                                        <p style={{ color: "#95a5a6", fontSize: "0.875rem" }}>
                                            Chat messages will appear here.
                                        </p>
                                        {/* TODO: Add chat component */}
                                    </div>
                                </div>
                            </div>

                            {/* Right Sidebar - Students */}
                            <div className="col-md-3">
                                <div className="card" style={{ backgroundColor: "#34495e", border: "none", height: "calc(100vh - 250px)" }}>
                                    <div className="card-header" style={{ backgroundColor: "#2c3e50", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-users me-2"></i>
                                            Students Online
                                        </h6>
                                    </div>
                                    <div className="card-body" style={{ overflowY: "auto" }}>
                                        <div className="mb-3 p-2" style={{ backgroundColor: "rgba(46, 204, 113, 0.1)", borderRadius: "0.25rem", border: "1px solid rgba(46, 204, 113, 0.3)" }}>
                                            <small style={{ color: "#2ecc71" }}>
                                                <i className="fas fa-circle fa-xs me-2"></i>
                                                You are in the classroom
                                            </small>
                                        </div>
                                        <p style={{ color: "#95a5a6", fontSize: "0.875rem" }}>
                                            Other students will appear here.
                                        </p>
                                        {/* TODO: Add students roster component */}
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
