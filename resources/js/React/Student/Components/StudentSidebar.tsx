import React from "react";

const StudentSidebar = ({
    instructor,
    classroomStatus,
}: {
    instructor: { fname: string; lname: string } | null;
    classroomStatus: string;
}) => {
    return (
        <div className="dashboard-side">
            <div className="dashboard-profile">
                <div className="pro-name">{classroomStatus} Classroom</div>
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
                    <a href="#" className="dashboard-nav-link active">
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
    );
};

export default StudentSidebar;
