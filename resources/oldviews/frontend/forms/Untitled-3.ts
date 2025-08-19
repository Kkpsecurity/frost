/**
 * Instructor Dashboard Component - Decision Maker
 * Determines what view to show based on instructor data:
 * - If no active courses: Show Bulletin Board
 * - If course selected: Show Course Teaching Interface
 * - If instructor selects a course: Create InstUnit
 */

import React, { useState } from "react";
import BulletinBoard from "./BulletinBoard";

interface InstructorDashboardProps {
    instructorData: {
        instructor: any;
        bulletin_board: any;
        active_courses: any[];
        classroom_data: any;
        students_data: any;
    };
    isLoading?: boolean;
    error?: any;
    debug?: boolean;
}

const InstructorDashboard: React.FC<InstructorDashboardProps> = ({
    instructorData,
    isLoading = false,
    error = null,
    debug = false,
}) => {
    const [selectedCourse, setSelectedCourse] = useState<any>(null);
    const [activeInstUnit, setActiveInstUnit] = useState<any>(null);

    if (debug) {
        console.log("🔧 InstructorDashboard data:", instructorData);
    }

    // Handle loading state
    if (isLoading) {
        return (
            <div className="instructor-dashboard">
                <div
                    className="d-flex justify-content-center align-items-center"
                    style={{ minHeight: "400px" }}
                >
                    <div className="text-center">
                        <div
                            className="spinner-border text-primary mb-3"
                            role="status"
                        >
                            <span className="visually-hidden">Loading...</span>
                        </div>
                        <h5 className="text-muted">Loading Dashboard...</h5>
                    </div>
                </div>
            </div>
        );
    }

    // Handle error state
    if (error) {
        return (
            <div className="instructor-dashboard">
                <div className="alert alert-danger mx-3 mt-3">
                    <h5 className="alert-heading">
                        <i className="fas fa-exclamation-triangle me-2"></i>
                        Dashboard Error
                    </h5>
                    <p>Unable to load instructor dashboard data.</p>
                    <p className="small text-muted mb-0">
                        Error: {error?.message || "Unknown error occurred"}
                    </p>
                </div>
            </div>
        );
    }

    // Dashboard Header
    const renderDashboardHeader = () => (
        <div className="row mb-4">
            <div className="col-12">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 className="mb-1">
                            Instructor Dashboard
                            {instructorData?.instructor?.name && (
                                <small className="text-muted ms-2">
                                    Welcome, {instructorData.instructor.name}
                                </small>
                            )}
                        </h2>
                        <p className="text-muted mb-0">
                            {activeInstUnit
                                ? `Teaching: ${activeInstUnit.course_name}`
                                : selectedCourse
                                ? `Course Selected: ${selectedCourse.name}`
                                : "Manage your courses, students, and classroom activities"}
                        </p>
                    </div>
                    <div className="d-flex gap-2">
                        {activeInstUnit && (
                            <button
                                onClick={() => {
                                    setActiveInstUnit(null);
                                    setSelectedCourse(null);
                                }}
                                className="btn btn-outline-warning"
                                type="button"
                            >
                                <i className="fas fa-stop me-2"></i>
                                End Session
                            </button>
                        )}
                        <button
                            onClick={() => console.log("Help clicked")}
                            className="btn btn-outline-secondary"
                            type="button"
                        >
                            <i className="fas fa-question-circle me-2"></i>
                            Help
                        </button>
                        <button
                            onClick={() => console.log("Settings clicked")}
                            className="btn btn-outline-primary"
                            type="button"
                        >
                            <i className="fas fa-cog me-2"></i>
                            Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );

    // Course Selection Handler
    const handleCourseSelection = (course: any) => {
        console.log("🎯 Course selected:", course);
        setSelectedCourse(course);

        // TODO: Create InstUnit when instructor selects a course to teach
        // This is where you would call the API to create an instructional unit
        const instUnit = {
            id: `inst-${Date.now()}`,
            course_id: course.id,
            course_name: course.name,
            instructor_id: instructorData.instructor.id,
            start_time: new Date().toISOString(),
            status: "active",
        };

        setActiveInstUnit(instUnit);
        console.log("🏫 InstUnit created:", instUnit);
    };

    // Decision Logic: What view to show?
    const renderMainContent = () => {
        // If instructor has an active InstUnit (teaching session)
        if (activeInstUnit) {
            return (
                <div className="active-teaching-session">
                    <div className="alert alert-success">
                        <h4 className="alert-heading">
                            <i className="fas fa-chalkboard-teacher me-2"></i>
                            Active Teaching Session
                        </h4>
                        <p>
                            You are currently teaching:{" "}
                            <strong>{activeInstUnit.course_name}</strong>
                        </p>
                        <p className="mb-0">InstUnit ID: {activeInstUnit.id}</p>
                    </div>
                    {/* TODO: Render active teaching interface */}
                    <div className="text-center mt-4">
                        <h3>🚧 Active Teaching Interface Coming Soon</h3>
                        <p className="text-muted">
                            This is where the live classroom interface will be
                            displayed
                        </p>
                    </div>
                </div>
            );
        }

        // If instructor has selected a course (ready to start teaching)
        if (selectedCourse) {
            return (
                <div className="course-ready">
                    <div className="alert alert-info">
                        <h4 className="alert-heading">
                            <i className="fas fa-play-circle me-2"></i>
                            Course Ready to Begin
                        </h4>
                        <p>
                            Course: <strong>{selectedCourse.name}</strong>
                        </p>
                        <button
                            onClick={() =>
                                handleCourseSelection(selectedCourse)
                            }
                            className="btn btn-success"
                        >
                            <i className="fas fa-play me-2"></i>
                            Start Teaching Session
                        </button>
                        <button
                            onClick={() => setSelectedCourse(null)}
                            className="btn btn-outline-secondary ms-2"
                        >
                            <i className="fas fa-arrow-left me-2"></i>
                            Back to Dashboard
                        </button>
                    </div>
                </div>
            );
        }

        // Default: Show Bulletin Board (no active courses)
        if (instructorData?.bulletin_board) {
            return (
                <BulletinBoard
                    bulletinData={instructorData.bulletin_board}
                    onCourseSelect={handleCourseSelection}
                />
            );
        }

        // Fallback: No data available
        return (
            <div className="alert alert-warning">
                <h4 className="alert-heading">
                    <i className="fas fa-info-circle me-2"></i>
                    No Data Available
                </h4>
                <p className="mb-0">
                    Unable to load instructor dashboard data.
                </p>
            </div>
        );
    };

    return (
        <div className="instructor-dashboard">
            {renderDashboardHeader()}
            {renderMainContent()}

            {/* Debug Information */}
            {debug && (
                <div className="row mt-4">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header">
                                <h6 className="card-title mb-0">
                                    <i className="fas fa-bug me-2"></i>
                                    Debug Information
                                </h6>
                            </div>
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-md-6">
                                        <h6>Selected Course:</h6>
                                        <pre className="small">
                                            {JSON.stringify(
                                                selectedCourse,
                                                null,
                                                2
                                            )}
                                        </pre>
                                    </div>
                                    <div className="col-md-6">
                                        <h6>Active InstUnit:</h6>
                                        <pre className="small">
                                            {JSON.stringify(
                                                activeInstUnit,
                                                null,
                                                2
                                            )}
                                        </pre>
                                    </div>
                                </div>
                                <hr />
                                <h6>Instructor Data:</h6>
                                <pre
                                    className="small"
                                    style={{
                                        maxHeight: "200px",
                                        overflow: "auto",
                                    }}
                                >
                                    {JSON.stringify(instructorData, null, 2)}
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default InstructorDashboard;
