import React from "react";
import StudentCoursesTable from "../Tables/StudentCoursesTable";

interface StudentDashboardProps {
    student: any;
    courseAuths: any[];
    lessons: any;
    hasLessons: boolean;
    selectedCourseAuthId: number | null;
    validations: any;
    instructor: any;
    courseDates: any[];
    instUnit: any;
    studentAttendance: any;
    studentUnits: any[];
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
    student,
    courseAuths,
    lessons,
    hasLessons,
    selectedCourseAuthId,
    validations,
    instructor,
    courseDates,
    instUnit,
    studentAttendance,
    studentUnits,
}) => {
    const courses: Course[] = courseAuths || [];
    const progress = {
        total_courses: courses.length,
        completed: courses.filter((c) => c.status === "completed").length,
        in_progress: courses.filter((c) => c.status === "in_progress").length,
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
