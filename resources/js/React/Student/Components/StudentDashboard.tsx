/**
 * Student Dashboard Component - Professional UI with OFFLINE/ONLINE Status Detection
 * Uses existing Frost theme CSS classes and color variables
 * OFFLINE: Shows student courses table with purchase history
 * ONLINE: Shows classroom dashboard with sidebar navigation and tabbed content
 */

import React from "react";
import {
    StudentDashboardData,
    ClassDashboardData,
} from "../types/LaravelProps";

interface StudentDashboardProps {
    studentData: StudentDashboardData;
    classData: ClassDashboardData;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    studentData,
    classData,
}) => {
    console.log("🎓 StudentDashboard: Component rendering with Laravel data:");
    console.log("   Student data:", studentData);
    console.log("   Class data:", classData);

    const { student, course_auths } = studentData;
    const { instructor, course_dates } = classData;

    // Status detection: derive OFFLINE/ONLINE from courseDates presence only
    const isClassroomOnline = course_dates && course_dates.length > 0;
    const classroomStatus = isClassroomOnline ? "ONLINE" : "OFFLINE";

    console.log("🔍 Classroom Status Detection:");
    console.log("   course_dates length:", course_dates?.length || 0);
    console.log("   Detected status:", classroomStatus);

    return (
        <div className="dashboard-area">
            <div className="container-fluid">
                {isClassroomOnline ? (
                    /* ONLINE: Classroom Dashboard Layout */
                    <div className="dashboard-container">
                        {/* Left Sidebar - Classroom Modules */}
                        <div className="dashboard-side">
                            <div className="dashboard-profile">
                                <div className="pro-name">
                                    {classroomStatus} Classroom
                                </div>
                                <small
                                    style={{
                                        color: "var(--frost-light-color)",
                                    }}
                                >
                                    {instructor
                                        ? `${instructor.fname} ${instructor.lname}`
                                        : "No Instructor"}
                                </small>
                            </div>

                            <ul className="dashboard-nav">
                                <li className="dashboard-nav-item">
                                    <a
                                        href="#"
                                        className="dashboard-nav-link active"
                                    >
                                        <i className="fas fa-play dashboard-nav-icon"></i>
                                        Course Overview
                                    </a>
                                </li>
                                <li className="dashboard-nav-item">
                                    <a href="#" className="dashboard-nav-link">
                                        <i className="fas fa-video dashboard-nav-icon"></i>
                                        Live Session
                                    </a>
                                </li>
                                <li className="dashboard-nav-item">
                                    <a href="#" className="dashboard-nav-link">
                                        <i className="fas fa-file-alt dashboard-nav-icon"></i>
                                        Course Materials
                                    </a>
                                </li>
                                <li className="dashboard-nav-item">
                                    <a href="#" className="dashboard-nav-link">
                                        <i className="fas fa-users dashboard-nav-icon"></i>
                                        Class Participants
                                    </a>
                                </li>
                            </ul>

                            <div className="support-section">
                                <a href="#" className="support-link">
                                    <i className="fas fa-headset support-icon"></i>
                                    Technical Support
                                </a>
                            </div>
                        </div>

                        {/* Main Content - Classroom View */}
                        <div className="dashboard-content">
                            <div className="section-title">
                                Live Classroom Session
                            </div>

                            {/* Tab Navigation */}
                            <nav className="mb-4">
                                <div
                                    className="nav nav-tabs"
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
                                    >
                                        <i className="fas fa-home me-2"></i>Home
                                    </button>
                                    <button
                                        className="nav-link"
                                        id="nav-videos-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#nav-videos"
                                        type="button"
                                        role="tab"
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
                                    >
                                        <i className="fas fa-file-pdf me-2"></i>
                                        Documents
                                    </button>
                                </div>
                            </nav>

                            {/* Tab Content */}
                            <div className="tab-content" id="nav-tabContent">
                                <div
                                    className="tab-pane fade show active"
                                    id="nav-home"
                                    role="tabpanel"
                                >
                                    <div className="row">
                                        <div className="col-md-8">
                                            <div className="dashboard-card">
                                                <div className="dashboard-card-header">
                                                    <h5 className="dashboard-card-title">
                                                        Course Details
                                                    </h5>
                                                    <div className="dashboard-card-icon success">
                                                        <i className="fas fa-graduation-cap"></i>
                                                    </div>
                                                </div>
                                                <div className="dashboard-card-body">
                                                    <p className="text-success mb-3">
                                                        <i className="fas fa-circle me-2"></i>
                                                        Classroom is currently
                                                        ONLINE
                                                    </p>
                                                    {instructor && (
                                                        <div className="mb-3">
                                                            <strong>
                                                                Instructor:
                                                            </strong>{" "}
                                                            {instructor.fname}{" "}
                                                            {instructor.lname}
                                                            <br />
                                                            <small className="text-muted">
                                                                {
                                                                    instructor.email
                                                                }
                                                            </small>
                                                        </div>
                                                    )}

                                                    <div className="mb-3">
                                                        <strong>
                                                            Scheduled Sessions:
                                                        </strong>
                                                        <div className="mt-2">
                                                            {course_dates.map(
                                                                (
                                                                    date,
                                                                    index
                                                                ) => (
                                                                    <div
                                                                        key={
                                                                            index
                                                                        }
                                                                        className="border rounded p-2 mb-2"
                                                                        style={{
                                                                            backgroundColor:
                                                                                "#f8f9fa",
                                                                        }}
                                                                    >
                                                                        <div className="d-flex justify-content-between">
                                                                            <span>
                                                                                <i className="fas fa-calendar me-2"></i>
                                                                                {new Date(
                                                                                    date.start_date
                                                                                ).toLocaleDateString()}
                                                                            </span>
                                                                            <span>
                                                                                {new Date(
                                                                                    date.start_date
                                                                                ).toLocaleTimeString()}{" "}
                                                                                -
                                                                                {new Date(
                                                                                    date.end_date
                                                                                ).toLocaleTimeString()}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                )
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="dashboard-card-footer">
                                                    <button className="btn btn-success">
                                                        <i className="fas fa-play me-2"></i>
                                                        Join Classroom Now
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="col-md-4">
                                            <div className="dashboard-card">
                                                <div className="dashboard-card-header">
                                                    <h6 className="dashboard-card-title">
                                                        Student Info
                                                    </h6>
                                                    <div className="dashboard-card-icon primary">
                                                        <i className="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div className="dashboard-card-body">
                                                    {student ? (
                                                        <div>
                                                            <p>
                                                                <strong>
                                                                    Name:
                                                                </strong>{" "}
                                                                {student.fname}{" "}
                                                                {student.lname}
                                                            </p>
                                                            <p>
                                                                <strong>
                                                                    Email:
                                                                </strong>{" "}
                                                                {student.email}
                                                            </p>
                                                            <p>
                                                                <strong>
                                                                    ID:
                                                                </strong>{" "}
                                                                {student.id}
                                                            </p>
                                                        </div>
                                                    ) : (
                                                        <p className="text-muted">
                                                            No student data
                                                            available
                                                        </p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ) : (
                    /* OFFLINE: Student Dashboard - Course Table */
                    <div className="row justify-content-center">
                        <div className="col-12">
                            <div className="dashboard-content">
                                <div className="section-title">
                                    <i className="fas fa-tachometer-alt me-3"></i>
                                    Student Dashboard
                                    <span className="badge bg-secondary ms-3">
                                        {classroomStatus}
                                    </span>
                                </div>

                                {/* Student Welcome Card */}
                                {student && (
                                    <div className="dashboard-card mb-4">
                                        <div className="dashboard-card-header">
                                            <h5 className="dashboard-card-title">
                                                Welcome, {student.fname}{" "}
                                                {student.lname}
                                            </h5>
                                            <div className="dashboard-card-icon highlight">
                                                <i className="fas fa-user-graduate"></i>
                                            </div>
                                        </div>
                                        <div className="dashboard-card-body">
                                            <div className="row">
                                                <div className="col-md-8">
                                                    <p className="dashboard-card-text">
                                                        Welcome to your student
                                                        dashboard. Here you can
                                                        view your purchased
                                                        courses, track your
                                                        progress, and access
                                                        learning materials.
                                                    </p>
                                                    <p>
                                                        <strong>Email:</strong>{" "}
                                                        {student.email}
                                                    </p>
                                                    <p>
                                                        <strong>
                                                            Student ID:
                                                        </strong>{" "}
                                                        {student.id}
                                                    </p>
                                                </div>
                                                <div className="col-md-4 text-center">
                                                    <div className="stats-card">
                                                        <span className="stats-number">
                                                            {
                                                                course_auths.length
                                                            }
                                                        </span>
                                                        <div className="stats-label">
                                                            Enrolled Courses
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Purchased Courses Table */}
                                <div className="dashboard-card">
                                    <div className="dashboard-card-header">
                                        <h5 className="dashboard-card-title">
                                            My Purchased Courses
                                        </h5>
                                        <div className="dashboard-card-icon primary">
                                            <i className="fas fa-graduation-cap"></i>
                                        </div>
                                    </div>
                                    <div className="dashboard-card-body">
                                        {course_auths.length > 0 ? (
                                            <div className="table-responsive">
                                                <table className="table table-hover">
                                                    <thead
                                                        style={{
                                                            backgroundColor:
                                                                "var(--frost-light-color)",
                                                        }}
                                                    >
                                                        <tr>
                                                            <th>
                                                                <i className="fas fa-calendar me-2"></i>
                                                                Purchase Date
                                                            </th>
                                                            <th>
                                                                <i className="fas fa-book me-2"></i>
                                                                Course Name
                                                            </th>
                                                            <th>
                                                                <i className="fas fa-clock me-2"></i>
                                                                Last Access
                                                            </th>
                                                            <th>
                                                                <i className="fas fa-chart-line me-2"></i>
                                                                Progress
                                                            </th>
                                                            <th>
                                                                <i className="fas fa-cog me-2"></i>
                                                                Actions
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {course_auths.map(
                                                            (auth, index) => (
                                                                <tr
                                                                    key={
                                                                        auth.id
                                                                    }
                                                                >
                                                                    <td>
                                                                        <span className="badge bg-light text-dark">
                                                                            {auth.created_at
                                                                                ? new Date(
                                                                                      auth.created_at
                                                                                  ).toLocaleDateString()
                                                                                : "N/A"}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div>
                                                                            <strong
                                                                                style={{
                                                                                    color: "var(--frost-primary-color)",
                                                                                }}
                                                                            >
                                                                                {auth
                                                                                    .course
                                                                                    ?.title ||
                                                                                    `Course ${auth.course_id}`}
                                                                            </strong>
                                                                            {auth
                                                                                .course
                                                                                ?.description && (
                                                                                <div>
                                                                                    <small className="text-muted">
                                                                                        {auth.course.description.substring(
                                                                                            0,
                                                                                            80
                                                                                        )}
                                                                                        ...
                                                                                    </small>
                                                                                </div>
                                                                            )}
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span className="text-muted">
                                                                            {auth.updated_at
                                                                                ? new Date(
                                                                                      auth.updated_at
                                                                                  ).toLocaleDateString()
                                                                                : "Never"}
                                                                        </span>
                                                                    </td>
                                                                    <td>
                                                                        <div className="progress-container">
                                                                            <div className="progress-bar-container">
                                                                                <div
                                                                                    className="progress-bar success"
                                                                                    style={{
                                                                                        width: `${
                                                                                            auth.progress ||
                                                                                            0
                                                                                        }%`,
                                                                                    }}
                                                                                ></div>
                                                                            </div>
                                                                            <small className="text-muted">
                                                                                {auth.progress ||
                                                                                    0}

                                                                                %
                                                                                Complete
                                                                            </small>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <button
                                                                            className="btn btn-sm me-2"
                                                                            style={{
                                                                                backgroundColor:
                                                                                    "var(--frost-secondary-color)",
                                                                                color: "white",
                                                                                border: "none",
                                                                            }}
                                                                            onClick={() =>
                                                                                (window.location.href = `/course/${auth.course_id}`)
                                                                            }
                                                                        >
                                                                            <i className="fas fa-play me-1"></i>
                                                                            Access
                                                                        </button>
                                                                        <button className="btn btn-outline-secondary btn-sm">
                                                                            <i className="fas fa-info-circle"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            )
                                                        )}
                                                    </tbody>
                                                </table>
                                            </div>
                                        ) : (
                                            <div className="text-center py-5">
                                                <div className="mb-4">
                                                    <i
                                                        className="fas fa-graduation-cap"
                                                        style={{
                                                            fontSize: "4rem",
                                                            color: "var(--frost-gray-color)",
                                                        }}
                                                    ></i>
                                                </div>
                                                <h5 className="text-muted mb-3">
                                                    No Courses Found
                                                </h5>
                                                <p className="text-muted mb-4">
                                                    You haven't purchased any
                                                    courses yet.
                                                    <br />
                                                    Browse our course catalog to
                                                    get started with your
                                                    learning journey!
                                                </p>
                                                <button
                                                    className="btn"
                                                    style={{
                                                        backgroundColor:
                                                            "var(--frost-login-btn-color)",
                                                        color: "white",
                                                        border: "none",
                                                    }}
                                                    onClick={() =>
                                                        (window.location.href =
                                                            "/courses")
                                                    }
                                                >
                                                    <i className="fas fa-search me-2"></i>
                                                    Browse Courses
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* School Information */}
                                <div className="row mt-4">
                                    <div className="col-md-6">
                                        <div className="dashboard-card">
                                            <div className="dashboard-card-header">
                                                <h6 className="dashboard-card-title">
                                                    School Information
                                                </h6>
                                                <div className="dashboard-card-icon secondary">
                                                    <i className="fas fa-school"></i>
                                                </div>
                                            </div>
                                            <div className="dashboard-card-body">
                                                <p>
                                                    <strong>
                                                        Institution:
                                                    </strong>{" "}
                                                    Security Training Group
                                                </p>
                                                <p>
                                                    <strong>Programs:</strong>{" "}
                                                    Professional Security
                                                    Training
                                                </p>
                                                <p>
                                                    <strong>
                                                        Certification:
                                                    </strong>{" "}
                                                    State Licensed Training
                                                    Provider
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-md-6">
                                        <div className="dashboard-card">
                                            <div className="dashboard-card-header">
                                                <h6 className="dashboard-card-title">
                                                    Quick Actions
                                                </h6>
                                                <div className="dashboard-card-icon highlight">
                                                    <i className="fas fa-bolt"></i>
                                                </div>
                                            </div>
                                            <div className="dashboard-card-body">
                                                <div className="quick-actions">
                                                    <a
                                                        href="/profile"
                                                        className="quick-action"
                                                    >
                                                        <i className="fas fa-user quick-action-icon"></i>
                                                        <span className="quick-action-label">
                                                            Edit Profile
                                                        </span>
                                                    </a>
                                                    <a
                                                        href="/certificates"
                                                        className="quick-action"
                                                    >
                                                        <i className="fas fa-certificate quick-action-icon"></i>
                                                        <span className="quick-action-label">
                                                            Certificates
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* Debug Info - Development Only */}
                {process.env.NODE_ENV === "development" && (
                    <div className="row mt-4">
                        <div className="col-12">
                            <div className="card border-info">
                                <div className="card-header bg-info text-white">
                                    <h6 className="mb-0">
                                        🔍 DataLayer Debug Info
                                    </h6>
                                </div>
                                <div className="card-body">
                                    <div className="row">
                                        <div className="col-md-6">
                                            <strong>Status Detection:</strong>{" "}
                                            {classroomStatus}
                                            <br />
                                            <strong>Course Dates:</strong>{" "}
                                            {course_dates?.length || 0} found
                                            <br />
                                            <strong>Student Data:</strong>{" "}
                                            {student
                                                ? "✅ Loaded"
                                                : "❌ Missing"}
                                            <br />
                                            <strong>Course Auths:</strong>{" "}
                                            {course_auths.length} found
                                        </div>
                                        <div className="col-md-6">
                                            <strong>Instructor Data:</strong>{" "}
                                            {instructor
                                                ? "✅ Loaded"
                                                : "❌ Missing"}
                                            <br />
                                            <strong>Data Source:</strong>{" "}
                                            Laravel Props (script tag)
                                            <br />
                                            <strong>
                                                Classroom Online:
                                            </strong>{" "}
                                            {isClassroomOnline ? "Yes" : "No"}
                                            <br />
                                            <strong>Loading Rules:</strong>{" "}
                                            courseDates presence only
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
};

export default StudentDashboard;


//             {/* Main Content Area */}
//             <div className="flex-grow-1 d-flex flex-column">
//                 {/* Title Bar */}
//                 <div className="bg-white border-bottom p-3">
//                     <div className="d-flex justify-content-between align-items-center">
//                         <div>
//                             <h4 className="mb-1">
//                                 🎓 Student Dashboard
//                                 {student && ` - ${student.fname} ${student.lname}`}
//                             </h4>
//                             <small className="text-muted">
//                                 Classroom Status:
//                                 <span className={`badge ms-1 ${isClassroomOnline ? 'bg-success' : 'bg-secondary'}`}>
//                                     {classroomStatus}
//                                 </span>
//                                 {student && ` • ${student.email}`}
//                             </small>
//                         </div>
//                         <div>
//                             <button className="btn btn-outline-secondary btn-sm me-2">
//                                 <i className="fas fa-sync-alt me-1"></i>
//                                 Refresh
//                             </button>
//                         </div>
//                     </div>
//                 </div>

//                 {/* Main Content */}
//                 <div className="flex-grow-1 p-4" style={{ backgroundColor: "#f8f9fa" }}>
//                     {isClassroomOnline ? (
//                         /* ONLINE: Show current classroom view */
//                         <div className="row">
//                             <div className="col-12 mb-4">
//                                 <div className="card border-success">
//                                     <div className="card-header bg-success text-white">
//                                         <h5 className="mb-0">
//                                             <i className="fas fa-video me-2"></i>
//                                             Live Classroom Session
//                                         </h5>
//                                     </div>
//                                     <div className="card-body">
//                                         <p className="text-success mb-3">
//                                             <i className="fas fa-circle me-2"></i>
//                                             Classroom is currently ONLINE
//                                         </p>

//                                         {instructor && (
//                                             <div className="mb-3">
//                                                 <strong>Instructor:</strong> {instructor.fname} {instructor.lname}
//                                                 <br />
//                                                 <small className="text-muted">{instructor.email}</small>
//                                             </div>
//                                         )}

//                                         <div className="mb-3">
//                                             <strong>Scheduled Sessions:</strong>
//                                             <div className="mt-2">
//                                                 {course_dates.map((date, index) => (
//                                                     <div key={index} className="border rounded p-2 mb-2 bg-light">
//                                                         <div className="d-flex justify-content-between">
//                                                             <span>
//                                                                 <i className="fas fa-calendar me-2"></i>
//                                                                 {new Date(date.start_time).toLocaleDateString()}
//                                                             </span>
//                                                             <span>
//                                                                 {new Date(date.start_time).toLocaleTimeString()} -
//                                                                 {new Date(date.end_time).toLocaleTimeString()}
//                                                             </span>
//                                                         </div>
//                                                     </div>
//                                                 ))}
//                                             </div>
//                                         </div>

//                                         <button className="btn btn-success">
//                                             <i className="fas fa-play me-2"></i>
//                                             Join Classroom
//                                         </button>
//                                     </div>
//                                 </div>
//                             </div>
//                         </div>
//                     ) : (
//                         /* OFFLINE: Show offline state + student/course-auth summary */
//                         <div className="row">
//                             <div className="col-12 mb-4">
//                                 <div className="card border-secondary">
//                                     <div className="card-header bg-secondary text-white">
//                                         <h5 className="mb-0">
//                                             <i className="fas fa-moon me-2"></i>
//                                             Classroom Offline
//                                         </h5>
//                                     </div>
//                                     <div className="card-body text-center py-4">
//                                         <div className="mb-3">
//                                             <i className="fas fa-power-off fa-3x text-muted"></i>
//                                         </div>
//                                         <h5 className="text-muted mb-3">No Active Classroom Sessions</h5>
//                                         <p className="text-muted mb-4">
//                                             There are currently no scheduled classroom sessions.
//                                             Check back later or review your purchased courses below.
//                                         </p>
//                                     </div>
//                                 </div>
//                             </div>
//                         </div>
//                     )}

//                     {/* Student & Course Authorization Summary */}
//                     <div className="row">
//                         <div className="col-md-6 mb-4">
//                             <div className="card">
//                                 <div className="card-header">
//                                     <h6 className="mb-0">
//                                         <i className="fas fa-user me-2"></i>
//                                         Student Information
//                                     </h6>
//                                 </div>
//                                 <div className="card-body">
//                                     {student ? (
//                                         <div>
//                                             <p><strong>Name:</strong> {student.fname} {student.lname}</p>
//                                             <p><strong>Email:</strong> {student.email}</p>
//                                             <p><strong>ID:</strong> {student.id}</p>
//                                         </div>
//                                     ) : (
//                                         <p className="text-muted">No student data available</p>
//                                     )}
//                                 </div>
//                             </div>
//                         </div>

//                         <div className="col-md-6 mb-4">
//                             <div className="card">
//                                 <div className="card-header">
//                                     <h6 className="mb-0">
//                                         <i className="fas fa-graduation-cap me-2"></i>
//                                         Course Access Summary
//                                     </h6>
//                                 </div>
//                                 <div className="card-body">
//                                     <p><strong>Total Authorized Courses:</strong> {course_auths.length}</p>
//                                     {course_auths.length > 0 ? (
//                                         <div className="mt-3">
//                                             <small className="text-muted">Recent Purchases:</small>
//                                             {course_auths.slice(0, 3).map((auth, index) => (
//                                                 <div key={auth.id} className="border-bottom py-2">
//                                                     <div className="d-flex justify-content-between">
//                                                         <span className="small">
//                                                             {auth.course?.title || `Course ${auth.course_id}`}
//                                                         </span>
//                                                         <span className="small text-muted">
//                                                             {auth.created_at ?
//                                                                 new Date(auth.created_at).toLocaleDateString() :
//                                                                 'N/A'
//                                                             }
//                                                         </span>
//                                                     </div>
//                                                 </div>
//                                             ))}
//                                             {course_auths.length > 3 && (
//                                                 <small className="text-muted">
//                                                     ...and {course_auths.length - 3} more
//                                                 </small>
//                                             )}
//                                         </div>
//                                     ) : (
//                                         <p className="text-muted small mt-2">No course authorizations found</p>
//                                     )}
//                                 </div>
//                             </div>
//                         </div>
//                     </div>

//                     {/* Debug Info - Development Only */}
//                     {process.env.NODE_ENV === 'development' && (
//                         <div className="row">
//                             <div className="col-12">
//                                 <div className="card border-info">
//                                     <div className="card-header bg-info text-white">
//                                         <h6 className="mb-0">
//                                             🔍 DataLayer Debug Info
//                                         </h6>
//                                     </div>
//                                     <div className="card-body">
//                                         <div className="row">
//                                             <div className="col-md-6">
//                                                 <strong>Status Detection:</strong> {classroomStatus}
//                                                 <br />
//                                                 <strong>Course Dates:</strong> {course_dates?.length || 0} found
//                                                 <br />
//                                                 <strong>Student Data:</strong> {student ? "✅ Loaded" : "❌ Missing"}
//                                                 <br />
//                                                 <strong>Course Auths:</strong> {course_auths.length} found
//                                             </div>
//                                             <div className="col-md-6">
//                                                 <strong>Instructor Data:</strong> {instructor ? "✅ Loaded" : "❌ Missing"}
//                                                 <br />
//                                                 <strong>Data Source:</strong> Laravel Props (script tag)
//                                                 <br />
//                                                 <strong>Classroom Online:</strong> {isClassroomOnline ? "Yes" : "No"}
//                                                 <br />
//                                                 <strong>Loading Rules:</strong> courseDates presence only
//                                             </div>
//                                         </div>
//                                     </div>
//                                 </div>
//                             </div>
//                         </div>
//                     )}
//                         </div>
//                     </div>
//                 </div>
//             </div>
//         </div>
