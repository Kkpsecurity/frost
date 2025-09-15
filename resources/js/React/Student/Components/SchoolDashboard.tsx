import React from "react";
import SchoolDashboardTitleBar from "./ShcoolDashboardTitleBar";
import SchoolNavBar from "./SchoolNavBar";
import SchoolDashboardTabContent from "./SchoolDashboardTabContent";
import { 
    SchoolDashboardProps 
} from "../types/props/classroom.props";

const SchoolDashboard: React.FC<SchoolDashboardProps> = ({
    student,
    instructor,
    courseAuths,
    courseDates,
}) => {
    return (
        <div className="dashboard-content">

           <SchoolDashboardTitleBar
               title="Live Classroom Session"
               subtitle="Interactive learning experience"
               icon={<i className="fas fa-video"></i>}
           />

            {/* Tab Navigation */}
           <SchoolNavBar
               student={student}
               instructor={instructor}
               courseAuths={courseAuths}
               courseDates={courseDates}
           />

            {/* Tab Content */}
            <SchoolDashboardTabContent
                student={student}
                instructor={instructor}
                courseAuths={courseAuths}
                courseDates={courseDates}
            />
        </div>
    );
};

export default SchoolDashboard;
