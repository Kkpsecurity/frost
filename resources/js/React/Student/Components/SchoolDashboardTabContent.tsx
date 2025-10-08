import React from "react";
import { SchoolDashboardTabContentProps } from "../types/props/classroom.props";
import VideoLessonTab from "./VideoLessonTab";
import { useClassAttendance } from "../../hooks/useClassAttendance";

const SchoolDashboardTabContent: React.FC<SchoolDashboardTabContentProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    // Use the class attendance hook to check for active classes
    const classAttendance = useClassAttendance();

    // Show loading state while checking for active classes
    if (classAttendance.loading) {
        return (
            <div className="tab-content" id="nav-tabContent">
                <div
                    className="tab-pane fade show active"
                    id="nav-home"
                    role="tabpanel"
                >
                    <div className="row">
                        <div className="col-12 text-center py-5">
                            <div
                                className="spinner-border text-primary"
                                role="status"
                            >
                                <span className="visually-hidden">
                                    Checking class status...
                                </span>
                            </div>
                            <p className="mt-3 text-muted">
                                Checking for active classes today...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // Show redirect message (shouldn't normally be seen due to redirect in hook)
    if (classAttendance.hasActiveClass) {
        return (
            <div className="tab-content" id="nav-tabContent">
                <div
                    className="tab-pane fade show active"
                    id="nav-home"
                    role="tabpanel"
                >
                    <div className="row">
                        <div className="col-12">
                            <div className="alert alert-info text-center">
                                <i className="fas fa-graduation-cap fa-2x mb-3"></i>
                                <h4>Class Onboarding Required</h4>
                                <p>{classAttendance.message}</p>
                                <p>Redirecting to class onboarding...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="tab-content" id="nav-tabContent">
            {/* Home Tab */}
            <div
                className="tab-pane fade show active"
                id="nav-home"
                role="tabpanel"
            >
                <div className="row">
                    {/* Course Details Section */}
                    <div className="col-md-6">
                        <div
                            className="card shadow-sm border-0 mb-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, var(--frost-secondary-color), var(--frost-primary-color))",
                                color: "white",
                            }}
                        >
                            <div
                                className="card-header border-0"
                                style={{ background: "transparent" }}
                            >
                                <h5 className="mb-0 d-flex align-items-center">
                                    <i
                                        className="fas fa-graduation-cap me-2"
                                        style={{
                                            color: "var(--frost-highlight-color)",
                                        }}
                                    ></i>
                                    Course Details
                                </h5>
                            </div>
                            <div className="card-body">
                                {courseAuths.length > 0 && (
                                    <div className="course-details">
                                        {/* Course Title */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Title:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {courseAuths[0].course?.title ||
                                                    "Course Title"}
                                            </div>
                                        </div>

                                        {/* Purchased Date */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Purchased Date:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {courseAuths[0].created_at
                                                    ? new Date(
                                                          courseAuths[0].created_at
                                                      ).toLocaleDateString(
                                                          "en-US",
                                                          {
                                                              month: "2-digit",
                                                              day: "2-digit",
                                                              year: "numeric",
                                                          }
                                                      ) +
                                                      " " +
                                                      new Date(
                                                          courseAuths[0].created_at
                                                      ).toLocaleTimeString(
                                                          "en-US",
                                                          {
                                                              hour12: false,
                                                              hour: "2-digit",
                                                              minute: "2-digit",
                                                              second: "2-digit",
                                                          }
                                                      )
                                                    : "N/A"}
                                            </div>
                                        </div>

                                        {/* Start Date */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Start Date:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {courseAuths[0].start_date
                                                    ? new Date(
                                                          courseAuths[0].start_date
                                                      ).toLocaleDateString(
                                                          "en-US",
                                                          {
                                                              month: "2-digit",
                                                              day: "2-digit",
                                                              year: "numeric",
                                                          }
                                                      ) + " 00:00:00"
                                                    : "N/A"}
                                            </div>
                                        </div>

                                        {/* Expires Date */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Expires Date:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {courseAuths[0].expire_date
                                                    ? new Date(
                                                          courseAuths[0].expire_date
                                                      ).toLocaleDateString(
                                                          "en-US",
                                                          {
                                                              month: "2-digit",
                                                              day: "2-digit",
                                                              year: "numeric",
                                                          }
                                                      )
                                                    : "Open"}
                                            </div>
                                        </div>

                                        {/* Completed Date */}
                                        <div className="row mb-0">
                                            <div className="col-4">
                                                <strong>Completed Date:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {courseAuths[0].completed_at
                                                    ? new Date(
                                                          courseAuths[0].completed_at
                                                      ).toLocaleDateString(
                                                          "en-US",
                                                          {
                                                              month: "2-digit",
                                                              day: "2-digit",
                                                              year: "numeric",
                                                          }
                                                      )
                                                    : "Pending"}
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Student Info Section */}
                    <div className="col-md-6">
                        <div
                            className="card shadow-sm border-0 mb-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, var(--frost-secondary-color), var(--frost-primary-color))",
                                color: "white",
                            }}
                        >
                            <div
                                className="card-header border-0"
                                style={{ background: "transparent" }}
                            >
                                <h5 className="mb-0 d-flex align-items-center">
                                    <i
                                        className="fas fa-user me-2"
                                        style={{
                                            color: "var(--frost-highlight-color)",
                                        }}
                                    ></i>
                                    Student Info
                                </h5>
                            </div>
                            <div className="card-body">
                                {student ? (
                                    <div className="student-details">
                                        {/* Name */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Name:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {student.fname} {student.lname}
                                            </div>
                                        </div>

                                        {/* Email */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Email:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {student.email}
                                            </div>
                                        </div>

                                        {/* Initials (derived from name) */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>initials:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {student.fname
                                                    ?.charAt(0)
                                                    ?.toUpperCase()}
                                                {student.lname
                                                    ?.charAt(0)
                                                    ?.toUpperCase()}
                                            </div>
                                        </div>

                                        {/* Date of Birth (placeholder - would need to be added to student data) */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>dob:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {/* Placeholder - this would come from student profile */}
                                                1985-04-25
                                            </div>
                                        </div>

                                        {/* Suffix (placeholder) */}
                                        <div className="row mb-3">
                                            <div className="col-4">
                                                <strong>Suffix:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {/* Placeholder - this would come from student profile */}
                                                -
                                            </div>
                                        </div>

                                        {/* Phone (placeholder) */}
                                        <div className="row mb-0">
                                            <div className="col-4">
                                                <strong>phone:</strong>
                                            </div>
                                            <div className="col-8 text-end">
                                                {/* Placeholder - this would come from student profile */}
                                                7272908016
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <p className="text-muted">
                                        No student data available
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* ID Verification Section */}
                    <div className="col-md-6">
                        <div
                            className="card shadow-sm border-0 mb-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, var(--frost-secondary-color), var(--frost-primary-color))",
                                color: "white",
                            }}
                        >
                            <div
                                className="card-header border-0"
                                style={{ background: "transparent" }}
                            >
                                <h5 className="mb-0 d-flex align-items-center">
                                    <i
                                        className="fas fa-id-card me-2"
                                        style={{
                                            color: "var(--frost-highlight-color)",
                                        }}
                                    ></i>
                                    ID Verification
                                </h5>
                            </div>
                            <div className="card-body">
                                <div className="id-verification-details">
                                    {/* ID Status */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>Status:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            <span className="badge bg-warning text-dark">
                                                Pending
                                            </span>
                                        </div>
                                    </div>

                                    {/* ID Type */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>ID Type:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            Not Provided
                                        </div>
                                    </div>

                                    {/* Verification Date */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>Verified Date:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            Pending
                                        </div>
                                    </div>

                                    {/* Verification Button */}
                                    <div className="row mb-0">
                                        <div className="col-12 text-center">
                                            <button className="btn btn-outline-light btn-sm">
                                                <i className="fas fa-camera me-1"></i>
                                                Upload ID
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Signature Verification Section */}
                    <div className="col-md-6">
                        <div
                            className="card shadow-sm border-0 mb-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, var(--frost-secondary-color), var(--frost-primary-color))",
                                color: "white",
                            }}
                        >
                            <div
                                className="card-header border-0"
                                style={{ background: "transparent" }}
                            >
                                <h5 className="mb-0 d-flex align-items-center">
                                    <i
                                        className="fas fa-signature me-2"
                                        style={{
                                            color: "var(--frost-highlight-color)",
                                        }}
                                    ></i>
                                    Signature Verification
                                </h5>
                            </div>
                            <div className="card-body">
                                <div className="signature-verification-details">
                                    {/* Signature Status */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>Status:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            <span className="badge bg-warning text-dark">
                                                Pending
                                            </span>
                                        </div>
                                    </div>

                                    {/* Signature Date */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>Signed Date:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            Pending
                                        </div>
                                    </div>

                                    {/* Digital Signature */}
                                    <div className="row mb-3">
                                        <div className="col-4">
                                            <strong>Method:</strong>
                                        </div>
                                        <div className="col-8 text-end">
                                            Digital Signature
                                        </div>
                                    </div>

                                    {/* Signature Button */}
                                    <div className="row mb-0">
                                        <div className="col-12 text-center">
                                            <button className="btn btn-outline-light btn-sm">
                                                <i className="fas fa-pen me-1"></i>
                                                Sign Document
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Student Lessons Completed Section */}
                <div className="row mt-4">
                    <div className="col-12">
                        <div
                            className="card shadow-sm border-0"
                            style={{
                                background:
                                    "linear-gradient(135deg, var(--frost-secondary-color), var(--frost-primary-color))",
                                color: "white",
                            }}
                        >
                            <div
                                className="card-header border-0"
                                style={{ background: "transparent" }}
                            >
                                <h5 className="mb-0 d-flex align-items-center">
                                    <i
                                        className="fas fa-chart-line me-2"
                                        style={{
                                            color: "var(--frost-highlight-color)",
                                        }}
                                    ></i>
                                    Student Lessons Completed
                                </h5>
                            </div>
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-6">
                                        <strong>All lessons</strong>
                                    </div>
                                    <div className="col-6 text-end">
                                        {/* Placeholder values - these would come from course progress data */}
                                        <span className="badge bg-light text-dark px-3 py-2">
                                            2 out of 14
                                        </span>
                                    </div>
                                </div>

                                {/* Progress Bar */}
                                <div className="mt-3">
                                    <div
                                        className="progress"
                                        style={{
                                            height: "8px",
                                            background: "rgba(255,255,255,0.3)",
                                        }}
                                    >
                                        <div
                                            className="progress-bar"
                                            role="progressbar"
                                            style={{
                                                width: `${(2 / 14) * 100}%`,
                                                background:
                                                    "var(--frost-highlight-color)",
                                            }}
                                            aria-valuenow={2}
                                            aria-valuemin={0}
                                            aria-valuemax={14}
                                        ></div>
                                    </div>
                                    <small className="text-light mt-1 d-block">
                                        {Math.round((2 / 14) * 100)}% Complete
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* Video Lessons Tab */}
            <div className="tab-pane fade" id="nav-videos" role="tabpanel">
                <VideoLessonTab />
            </div>{" "}
            {/* Documents Tab */}
            <div className="tab-pane fade" id="nav-documents" role="tabpanel">
                <div className="text-center py-5">
                    <i className="fas fa-file-pdf fa-3x text-muted mb-3"></i>
                    <h5 className="text-muted">Documents Section</h5>
                    <p className="text-muted">
                        Course documents and materials will appear here.
                    </p>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboardTabContent;
