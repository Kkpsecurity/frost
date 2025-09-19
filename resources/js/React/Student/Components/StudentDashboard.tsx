import React from "react";
import { Student, CourseAuth, LessonsData } from "../types/LaravelProps";
import SchoolDashboard from "./SchoolDashboard";
import StudentPurchaseTable from "./StudentPurchaseTable";
import StudentStatsBlock from "./StudentStatsBlock";
import { useStudentDashboard } from "../hooks/useStudentDashboard";

interface StudentDashboardProps {
    student: Student;
    courseAuths: CourseAuth[];
    lessons?: LessonsData;
    hasLessons?: boolean;
    selectedCourseAuthId?: number | null;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    student,
    courseAuths = [],
    lessons,
    hasLessons = false,
    selectedCourseAuthId = null,
}) => {
    // Use the custom hook for all dashboard logic
    const {
        currentView,
        selectedCourse,
        stats,
        table,
        globalFilter,
        setGlobalFilter,
        handleBackToDashboard,
        flexRender,
    } = useStudentDashboard({
        student,
        courseAuths,
        lessons,
        hasLessons,
        selectedCourseAuthId,
    });

    // Render classroom dashboard when a course is selected
    if (currentView === "course" && selectedCourse) {
        // Convert to the format expected by SchoolDashboard
        const studentForClassroom = {
            id: student.id,
            fname: student.fname,
            lname: student.lname,
            email: student.email,
            name: `${student.fname} ${student.lname}`,
            fullname: `${student.fname} ${student.lname}`,
            is_active: true,
            role_id: 5 as 5,
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        // Mock instructor data (since this is student view)
        const instructor = {
            id: 1,
            fname: "Instructor",
            lname: "Name",
            email: "instructor@example.com",
            name: "Instructor Name",
            fullname: "Instructor Name",
            is_active: true,
            role_id: 2 as 2, // Ensure role_id is typed as one of the allowed values
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        // Convert selectedCourse to the format expected by SchoolDashboard
        const courseAuthForClassroom = {
            ...selectedCourse,
            course: selectedCourse.course
                ? {
                      ...selectedCourse.course,
                      price: 0,
                      total_minutes: 0,
                      policy_expire_days: 0,
                      is_active: true,
                      needs_range: false,
                  }
                : undefined,
            progress: 0,
            status: selectedCourse.completed_at
                ? "completed"
                : selectedCourse.start_date
                ? "in_progress"
                : "not_started",
            is_active: !selectedCourse.disabled_at,
            is_expired: false,
            is_failed: false,
            created_at:
                typeof selectedCourse.created_at === "string"
                    ? selectedCourse.created_at
                    : new Date(selectedCourse.created_at).toISOString(),
            updated_at:
                typeof selectedCourse.updated_at === "string"
                    ? selectedCourse.updated_at
                    : new Date(selectedCourse.updated_at).toISOString(),
        };

        return (
            <div className="classroom-container">
                {/* Classroom content will go here */}
                <SchoolDashboard
                    student={studentForClassroom}
                    instructor={instructor}
                    courseAuths={[courseAuthForClassroom]}
                    courseDates={[]}
                    onBackToDashboard={handleBackToDashboard}
                    lessons={lessons}
                    hasLessons={hasLessons}
                />
            </div>
        );
    }

    // Default dashboard view
    return (
        <div className="container-lg py-4">
            {/* Header Section */}
            <div className="row mb-4 mt-3">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 className="mb-1 text-white">
                                {student.fname} {student.lname}'s Dashboard
                            </h2>
                            <p className="text-white-50 mb-0">
                                Welcome back, {student.fname}
                            </p>
                        </div>
                        <div className="text-end">
                            <small className="text-success">
                                Student ID: {student.id}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {/* Stats Cards Section */}
            <StudentStatsBlock
                totalCourses={stats.totalCourses}
                completedCourses={stats.completedCourses}
                activeCourses={stats.activeCourses}
                passedCourses={stats.passedCourses}
            />

            {/* Course Purchases Table */}
            <div className="row">
                <div className="col-12">
                    <StudentPurchaseTable
                        courseAuths={courseAuths}
                        table={table}
                        flexRender={flexRender}
                        globalFilter={globalFilter}
                        setGlobalFilter={setGlobalFilter}
                    />
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
