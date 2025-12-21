import React from "react";
import StudentCoursesTable from "../Tables/StudentCoursesTable";

interface StudentDashboardProps {
    data?: any;
    courseAuthId?: number | null;
}

interface Course {
    id: number;
    course_date_id: number;
    course_name: string;
    start_date: string;
    status: string;
    completion_status?: string;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    data,
    courseAuthId,
}) => {
    const courses: Course[] = data?.courses || [];
    const progress = data?.progress || {
        total_courses: 0,
        completed: 0,
        in_progress: 0,
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    };

    return (
        <div
            className="dashboard-area"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                paddingTop: "6rem",
                paddingBottom: "3rem",
            }}
        ></div>
    );
};

export default StudentDashboard;
