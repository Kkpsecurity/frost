import React from "react";
import StudentSidebar from "./StudentSidebar";


interface OnlineClassroomDashboardProps {
    student: StudentType;
    instructor: InstructorType;
    courseAuths: CourseAuthType;
}

const OnlineClassroomDashboard: React.FC<OnlineClassroomDashboardProps> = ({ student, instructor, courseAuths }) => {
    return (
        <div className="dashboard-container">
            {/* Left Sidebar - Classroom Modules */}
            <StudentSidebar
                instructor={instructor}
                classroomStatus={classroomStatus}
            />

            {/* Main Content - Classroom View */}
            <SchoolDashboard student={student} instructor={instructor} courseAuths={courseAuths} />
        </div>
    );
};

export default OnlineClassroomDashboard;
