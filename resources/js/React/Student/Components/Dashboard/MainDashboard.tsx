import React, { useEffect, useMemo } from "react";
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
    const classroomContext = useClassroom();

    const selectedCourse = useMemo(() => {
        if (!courseAuthId) return null;
        const courses = studentContext.courses || [];
        return (
            courses.find((c: any) => {
                const candidateCourseAuthId = c?.course_auth_id ?? c?.courseAuthId ?? c?.id;
                return Number(candidateCourseAuthId) === Number(courseAuthId);
            }) ?? null
        );
    }, [courseAuthId, studentContext.courses]);

    const selectedCourseDateId = useMemo(() => {
        return selectedCourse?.course_date_id ? Number(selectedCourse.course_date_id) : null;
    }, [selectedCourse]);

    // Agreement is per-course (course_auth.agreed_at), not daily.
    const hasCourseAgreement = useMemo(() => {
        const agreedAt = (selectedCourse as any)?.agreed_at ?? (selectedCourse as any)?.agreedAt ?? null;
        return Boolean(agreedAt);
    }, [selectedCourse]);

    // Fallback: classroom poll data often has the authoritative CourseDate.
    const effectiveCourseDateId = useMemo(() => {
        const fromCourses = selectedCourseDateId;
        const fromClassroom = (classroomContext as any)?.courseDate?.id ?? null;
        return fromCourses ?? (fromClassroom ? Number(fromClassroom) : null);
    }, [selectedCourseDateId, classroomContext]);

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

    // Decide current mode using the *existing* classroom poll data.
    // IMPORTANT: CourseDate is a classroom property ("is the school open?") and must come from
    // the classroom poll (/classroom/class/data), not from the student poll course list.
    const courseDate = (classroomContext as any)?.courseDate ?? null; // CourseDate from classroom poll
    const instUnit = (classroomContext as any)?.instUnit ?? null; // Instructor unit from classroom poll
    const studentUnit = (classroomContext as any)?.studentUnit ?? (classroomContext as any)?.data?.studentUnit ?? null; // Student unit from classroom poll

    // OFFLINE: no CourseDate scheduled today.
    if (!courseDate) {
        return (
            <MainClassroom
                courseAuthId={courseAuthId}
                student={studentContext.student}
                onBackToDashboard={handleBackToDashboard}
            />
        );
    }

    // CLASSROOM: CourseDate exists; let MainClassroom handle waiting/active UI using poll data.
    return (
        <MainClassroom
            courseAuthId={courseAuthId}
            student={studentContext.student}
            onBackToDashboard={handleBackToDashboard}
        />
    );
};

export default MainDashboard;
