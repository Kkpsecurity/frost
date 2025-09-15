import React from "react";
import { 
    SchoolDashboardTabContentProps 
} from "../types/props/classroom.props";

const SchoolDashboardTabContent: React.FC<SchoolDashboardTabContentProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    return (
        <div className="tab-content" id="nav-tabContent">
            <div
                className="tab-pane fade show active"
                id="nav-home"
                role="tabpanel"
            >
                <div className="row">
                    {/* Course Details Section */}
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
                                    Classroom is currently ONLINE
                                </p>
                                
                                {/* Instructor Information */}
                                {instructor && (
                                    <div className="mb-3">
                                        <strong>Instructor:</strong>{" "}
                                        {instructor.fname} {instructor.lname}
                                        <br />
                                        <small className="text-muted">
                                            {instructor.email}
                                        </small>
                                    </div>
                                )}

                                {/* Scheduled Sessions */}
                                <div className="mb-3">
                                    <strong>Scheduled Sessions:</strong>
                                    <div className="mt-2">
                                        {courseDates.map((date, index) => (
                                            <div
                                                key={date.id || index}
                                                className="border rounded p-2 mb-2"
                                                style={{
                                                    backgroundColor: "#f8f9fa",
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
                                        ))}
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

                    {/* Student Info Section */}
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
                                            <strong>Name:</strong>{" "}
                                            {student.fname} {student.lname}
                                        </p>
                                        <p>
                                            <strong>Email:</strong>{" "}
                                            {student.email}
                                        </p>
                                        <p>
                                            <strong>ID:</strong> {student.id}
                                        </p>
                                    </div>
                                ) : (
                                    <p className="text-muted">
                                        No student data available
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboardTabContent;
