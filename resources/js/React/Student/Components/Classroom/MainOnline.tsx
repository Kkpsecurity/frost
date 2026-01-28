import React, { useState } from "react";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import PauseOverlay from "../Common/PauseOverlay";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import LessonSideBar from "../Common/LessonSideBar";
import { useClassroomSessionMode } from "@/React/Hooks/ClassroomAskInstructorHooks";
import { useLessonSidebar } from "../../hooks/useLessonSidebar";

interface MainOnlineProps {
    classroom: any;
    student: any;
    validations?: any;
    onBackToDashboard: () => void;
    devModeToggle?: React.ReactNode;
}

/**
 * MainOnline - Live classroom mode
 *
 * Shown when:
 * - CourseDate exists (class scheduled)
 * - InstUnit exists (instructor has started class)
 *
 * Features:
 * - Live video/audio
 * - Screen sharing
 * - Real-time chat
 * - Live lesson presentation
 * - Student interactions
 * - Attendance tracking
 */
const MainOnline: React.FC<MainOnlineProps> = ({
    classroom,
    student,
    validations,
    onBackToDashboard,
    devModeToggle,
}) => {
    // Extract classroom data (handle if classroom is wrapped in context)
    const classroomData = classroom?.data || classroom;
    const { courseDate, instructor, instUnit } = classroomData || {};
    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(
        null,
    );
    const [pauseRemainingSeconds, setPauseRemainingSeconds] =
        useState<number>(0);
    const [pauseStartTime, setPauseStartTime] = useState<number | null>(null);
    const PAUSE_DURATION_MINUTES = 5;
    const PAUSE_DURATION_SECONDS = PAUSE_DURATION_MINUTES * 60;

    const courseDateId: number | null = courseDate?.id ?? null;
    const sessionModeQuery = useClassroomSessionMode(courseDateId);
    const sessionMode = sessionModeQuery.data?.mode ?? "TEACHING";

    // ONLINE MODE: Lessons for TODAY only (based on courseUnit/day_number)
    // Backend returns lessons for current CourseUnit (e.g., Wednesday = Day 3 lessons)
    const lessons = classroomData?.lessons || [];
    const studentLessons = classroomData?.studentLessons || [];
    const activeLesson = classroomData?.activeLesson || null;
    const isLoadingLessons = false; // Replace with real loading state

    // 🔍 DEBUG: Log lessons data
    console.log("📚 MainOnline Lessons:", {
        lessonsCount: lessons.length,
        lessons: lessons,
        studentLessons: studentLessons,
        classroom: classroom,
        classroomData: classroomData,
    });

    // Use lesson sidebar hook for helper functions
    const {
        isLessonCompletedByStudent,
        isLessonInProgress,
        getLessonStatusColor,
        getLessonTextColor,
        getLessonStatusIcon,
    } = useLessonSidebar({
        lessons,
        studentLessons,
        activeLesson,
    });

    // Zoom screen share data from backend
    const zoomData = classroomData?.zoom || {};
    const isZoomReady = zoomData?.is_ready ?? false;
    const screenShareUrl = zoomData?.screen_share_url ?? null;

    const instructorName =
        instructor?.name || instructor?.fname || "Instructor";
    const instructorEmail = instructor?.email || null;
    const instructorAvatar = instructor?.avatar || "/images/default-avatar.png";

    return (
        <FrostDashboardWrapper>
            <PauseOverlay pauseRemainingSeconds={pauseRemainingSeconds} />
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title="Live Classroom"
                subtitle={`Instructor: ${instructorName}`}
                icon={<i className="fas fa-video"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="ONLINE"
                devModeToggle={devModeToggle}
            />
            <div
                className="container-fluid px-0"
                style={{
                    pointerEvents: pauseRemainingSeconds > 0 ? "none" : "auto",
                }}
            >
                <div className="row g-0">
                    <div className="col-12 px-0">
                        {/* Main Classroom Layout */}
                        <div className="row g-0">
                            {/* Left Sidebar - Lessons */}
                            <div className="col-md-2">
                                <LessonSideBar
                                    lessons={lessons}
                                    isLoadingLessons={isLoadingLessons}
                                    isLessonCompletedByStudent={
                                        isLessonCompletedByStudent
                                    }
                                    isLessonInProgress={isLessonInProgress}
                                    getLessonStatusColor={getLessonStatusColor}
                                    getLessonTextColor={getLessonTextColor}
                                    getLessonStatusIcon={getLessonStatusIcon}
                                />
                            </div>
                            {/* Main Content Area */}
                            <div className="col-md-10">
                                {/* Video, Chat, Presentation, etc. */}
                            </div>
                        </div>
                    </div>
                </div>
                MainOnline
            </div>
        </FrostDashboardWrapper>
    );
};

export default MainOnline;
