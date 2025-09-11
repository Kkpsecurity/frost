/**
 * Student Dashboard Component - Baseline UI with Loading Rules
 * Implements OFFLINE/ONLINE status detection based on courseDates presence
 * Uses only existing DataLayer outputs, no synthesized fields
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
        <div className="d-flex h-100" style={{ minHeight: "100vh", marginTop: "20px" }}>
            {/* Left Sidebar */}
            <div className="bg-light border-end" style={{ width: "250px", minHeight: "100vh" }}>
                <div className="p-3 border-bottom">
                    <h6 className="mb-0">Navigation</h6>
                </div>
                <div className="p-3">
                    <ul className="nav nav-pills flex-column">
                        <li className="nav-item mb-2">
                            <a className="nav-link active" href="#">
                                <i className="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        {course_auths && course_auths.length > 0 && (
                            <li className="nav-item mb-2">
                                <a className="nav-link" href="#">
                                    <i className="fas fa-book me-2"></i>
                                    My Courses ({course_auths.length})
                                </a>
                            </li>
                        )}
                        {isClassroomOnline && (
                            <li className="nav-item mb-2">
                                <a className="nav-link" href="#">
                                    <i className="fas fa-video me-2"></i>
                                    Live Classroom
                                </a>
                            </li>
                        )}
                    </ul>
                </div>
            </div>

            {/* Main Content Area */}
            <div className="flex-grow-1 d-flex flex-column">
                {/* Title Bar */}
                <div className="bg-white border-bottom p-3">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 className="mb-1">
                                🎓 Student Dashboard
                                {student && ` - ${student.fname} ${student.lname}`}
                            </h4>
                            <small className="text-muted">
                                Classroom Status: 
                                <span className={`badge ms-1 ${isClassroomOnline ? 'bg-success' : 'bg-secondary'}`}>
                                    {classroomStatus}
                                </span>
                                {student && ` • ${student.email}`}
                            </small>
                        </div>
                        <div>
                            <button className="btn btn-outline-secondary btn-sm me-2">
                                <i className="fas fa-sync-alt me-1"></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>

                {/* Main Content */}
                <div className="flex-grow-1 p-4" style={{ backgroundColor: "#f8f9fa" }}>
                    {isClassroomOnline ? (
                        /* ONLINE: Show current classroom view */
                        <div className="row">
                            <div className="col-12 mb-4">
                                <div className="card border-success">
                                    <div className="card-header bg-success text-white">
                                        <h5 className="mb-0">
                                            <i className="fas fa-video me-2"></i>
                                            Live Classroom Session
                                        </h5>
                                    </div>
                                    <div className="card-body">
                                        <p className="text-success mb-3">
                                            <i className="fas fa-circle me-2"></i>
                                            Classroom is currently ONLINE
                                        </p>
                                        
                                        {instructor && (
                                            <div className="mb-3">
                                                <strong>Instructor:</strong> {instructor.fname} {instructor.lname}
                                                <br />
                                                <small className="text-muted">{instructor.email}</small>
                                            </div>
                                        )}

                                        <div className="mb-3">
                                            <strong>Scheduled Sessions:</strong>
                                            <div className="mt-2">
                                                {course_dates.map((date, index) => (
                                                    <div key={index} className="border rounded p-2 mb-2 bg-light">
                                                        <div className="d-flex justify-content-between">
                                                            <span>
                                                                <i className="fas fa-calendar me-2"></i>
                                                                {new Date(date.start_time).toLocaleDateString()}
                                                            </span>
                                                            <span>
                                                                {new Date(date.start_time).toLocaleTimeString()} - 
                                                                {new Date(date.end_time).toLocaleTimeString()}
                                                            </span>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>

                                        <button className="btn btn-success">
                                            <i className="fas fa-play me-2"></i>
                                            Join Classroom
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : (
                        /* OFFLINE: Show offline state + student/course-auth summary */
                        <div className="row">
                            <div className="col-12 mb-4">
                                <div className="card border-secondary">
                                    <div className="card-header bg-secondary text-white">
                                        <h5 className="mb-0">
                                            <i className="fas fa-moon me-2"></i>
                                            Classroom Offline
                                        </h5>
                                    </div>
                                    <div className="card-body text-center py-4">
                                        <div className="mb-3">
                                            <i className="fas fa-power-off fa-3x text-muted"></i>
                                        </div>
                                        <h5 className="text-muted mb-3">No Active Classroom Sessions</h5>
                                        <p className="text-muted mb-4">
                                            There are currently no scheduled classroom sessions. 
                                            Check back later or review your purchased courses below.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Student & Course Authorization Summary */}
                    <div className="row">
                        <div className="col-md-6 mb-4">
                            <div className="card">
                                <div className="card-header">
                                    <h6 className="mb-0">
                                        <i className="fas fa-user me-2"></i>
                                        Student Information
                                    </h6>
                                </div>
                                <div className="card-body">
                                    {student ? (
                                        <div>
                                            <p><strong>Name:</strong> {student.fname} {student.lname}</p>
                                            <p><strong>Email:</strong> {student.email}</p>
                                            <p><strong>ID:</strong> {student.id}</p>
                                        </div>
                                    ) : (
                                        <p className="text-muted">No student data available</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="col-md-6 mb-4">
                            <div className="card">
                                <div className="card-header">
                                    <h6 className="mb-0">
                                        <i className="fas fa-graduation-cap me-2"></i>
                                        Course Access Summary
                                    </h6>
                                </div>
                                <div className="card-body">
                                    <p><strong>Total Authorized Courses:</strong> {course_auths.length}</p>
                                    {course_auths.length > 0 ? (
                                        <div className="mt-3">
                                            <small className="text-muted">Recent Purchases:</small>
                                            {course_auths.slice(0, 3).map((auth, index) => (
                                                <div key={auth.id} className="border-bottom py-2">
                                                    <div className="d-flex justify-content-between">
                                                        <span className="small">
                                                            {auth.course?.title || `Course ${auth.course_id}`}
                                                        </span>
                                                        <span className="small text-muted">
                                                            {auth.created_at ? 
                                                                new Date(auth.created_at).toLocaleDateString() : 
                                                                'N/A'
                                                            }
                                                        </span>
                                                    </div>
                                                </div>
                                            ))}
                                            {course_auths.length > 3 && (
                                                <small className="text-muted">
                                                    ...and {course_auths.length - 3} more
                                                </small>
                                            )}
                                        </div>
                                    ) : (
                                        <p className="text-muted small mt-2">No course authorizations found</p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Debug Info - Development Only */}
                    {process.env.NODE_ENV === 'development' && (
                        <div className="row">
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
                                                <strong>Status Detection:</strong> {classroomStatus}
                                                <br />
                                                <strong>Course Dates:</strong> {course_dates?.length || 0} found
                                                <br />
                                                <strong>Student Data:</strong> {student ? "✅ Loaded" : "❌ Missing"}
                                                <br />
                                                <strong>Course Auths:</strong> {course_auths.length} found
                                            </div>
                                            <div className="col-md-6">
                                                <strong>Instructor Data:</strong> {instructor ? "✅ Loaded" : "❌ Missing"}
                                                <br />
                                                <strong>Data Source:</strong> Laravel Props (script tag)
                                                <br />
                                                <strong>Classroom Online:</strong> {isClassroomOnline ? "Yes" : "No"}
                                                <br />
                                                <strong>Loading Rules:</strong> courseDates presence only
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
//                 <div className="p-3 border-bottom">
//                     <h6 className="mb-0">Navigation</h6>
//                 </div>
//                 <div className="p-3">
//                     <ul className="nav nav-pills flex-column">
//                         <li className="nav-item mb-2">
//                             <a className="nav-link active" href="#">
//                                 <i className="fas fa-tachometer-alt me-2"></i>
//                                 Dashboard
//                             </a>
//                         </li>
//                         {course_auths && course_auths.length > 0 && (
//                             <li className="nav-item mb-2">
//                                 <a className="nav-link" href="#">
//                                     <i className="fas fa-book me-2"></i>
//                                     My Courses ({course_auths.length})
//                                 </a>
//                             </li>
//                         )}
//                         {isClassroomOnline && (
//                             <li className="nav-item mb-2">
//                                 <a className="nav-link" href="#">
//                                     <i className="fas fa-video me-2"></i>
//                                     Live Classroom
//                                 </a>
//                             </li>
//                         )}
//                     </ul>
//                 </div>
//             </div>

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
