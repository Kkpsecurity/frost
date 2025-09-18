import React from "react";
import SchoolDashboardTitleBar from "./ShcoolDashboardTitleBar";
import SchoolNavBar from "./SchoolNavBar";
import SchoolDashboardTabContent from "./SchoolDashboardTabContent";
import { SchoolDashboardProps } from "../types/props/classroom.props";
import StudentSidebar from "./StudentSidebar";

const SchoolDashboard: React.FC<SchoolDashboardProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
    onBackToDashboard,
}) => {
    return (
        <div className="dashboard-content">
            <SchoolDashboardTitleBar
                title="Student Dashboard"
                subtitle="Overview of your courses and activities"
                icon={<i className="fas fa-school"></i>}
                onBackToDashboard={onBackToDashboard}
            />

            <div className="d-flex" style={{ height: "calc(100vh - 120px)" }}>
                <StudentSidebar
                    instructor={instructor}
                    classroomStatus={
                        courseDates.length > 0 ? "active" : "inactive"
                    }
                />
                <div className="flex-fill d-flex flex-column frost-secondary-bg">
                    {/* Navigation Tabs */}
                    <SchoolNavBar
                        student={student}
                        instructor={instructor}
                        courseAuths={courseAuths}
                        courseDates={courseDates}
                    />

                    {/* Tab Content Area */}
                    <div className="flex-grow-1 p-4 frost-secondary-bg">
                        <SchoolDashboardTabContent
                            student={student}
                            instructor={instructor}
                            courseAuths={courseAuths}
                            courseDates={courseDates}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SchoolDashboard;
