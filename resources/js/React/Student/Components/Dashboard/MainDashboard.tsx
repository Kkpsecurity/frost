import React from "react";
import { useStudent } from "../../context/StudentContext";
import { useClassroom } from "../../context/ClassroomContext";
import OrderDashboard from "./OrderDashboard";
import MainClassroom from "../Classroom/MainClassroom";

interface MainDashboardProps {
    courseAuthId?: number | null;
}

/**
 * MainDashboard - Top-level orchestrator
 *
 * Routes between:
 * 1. OrderDashboard - Shows purchased courses (default view, 12-hour session)
 * 2. MainClassroom - Handles classroom experience (online/offline logic)
 */
const MainDashboard: React.FC<MainDashboardProps> = ({ courseAuthId }) => {
    // Get student data from context
    const studentContext = useStudent();

    // Handler to go back to order dashboard
    const handleBackToDashboard = () => {
        studentContext.setSelectedCourseAuthId(null);
    };

    // Pass the entire context data to OrderDashboard
    const dashboardData = {
        student: studentContext.student,
        courses: studentContext.courses,
        progress: studentContext.progress,
        notifications: studentContext.notifications,
        assignments: studentContext.assignments,
    };

    // No courseAuthId = Show order dashboard (purchased courses list)
    if (!courseAuthId) {
        return <OrderDashboard data={dashboardData} courseAuthId={courseAuthId} />;
    }

    // Has courseAuthId = Show classroom (MainClassroom handles online/offline)
    return (
        <MainClassroom 
            courseAuthId={courseAuthId} 
            student={studentContext.student}
            onBackToDashboard={handleBackToDashboard}
        />
    );
};

export default MainDashboard;
