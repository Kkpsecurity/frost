import React, { useEffect, useState } from "react";
import { keepPreviousData, useQuery } from "@tanstack/react-query";
import { Alert } from "react-bootstrap";
import MainDashboard from "./Dashboard/MainDashboard";
import PageLoader from "../../Shared/Components/Widgets/PageLoader";
import { StudentContextProvider, StudentContextType } from "../context/StudentContext";
import { ClassroomContextProvider, ClassroomContextType } from "../context/ClassroomContext";
import { isInstructorTeaching, getClassroomStatus } from "../services/classroomService";

interface StudentDataLayerProps {
    courseAuthId?: number | null;
}

/**
 * StudentDataLayer - Handles all API polling for student portal
 *
 * SPA ROUTING:
 * - URL stays /classroom (no query parameters)
 * - courseAuthId managed internally via React state
 * - State changes trigger different views (course list vs classroom)
 *
 * Responsibilities:
 * - Poll /classroom/student/poll endpoint every 5 seconds
 * - Poll /classroom/class/data endpoint every 5 seconds
 * - Pass data down via Context providers
 * - Handle loading and error states
 * - Manage courseAuthId state internally
 * Does NOT handle:
 * - Business logic (that's in StudentDashboard)
 * - UI rendering beyond loaders/errors
 * - Conditional rendering logic
 */
const StudentDataLayer: React.FC<StudentDataLayerProps> = ({
    courseAuthId: initialCourseAuthId,
}) => {
    // Session expiration: 12 hours
    const SESSION_DURATION_MS = 12 * 60 * 60 * 1000; // 12 hours in milliseconds

    // Check if session has expired
    const isSessionExpired = (): boolean => {
        const sessionTimestamp = localStorage.getItem('frost_session_timestamp');
        if (!sessionTimestamp) return true;

        const sessionTime = parseInt(sessionTimestamp, 10);
        const now = Date.now();
        const elapsed = now - sessionTime;

        return elapsed > SESSION_DURATION_MS;
    };

    // Internal state for selected courseAuthId (SPA routing)
    // PERSISTENCE: Restore from localStorage on mount, save on changes
    // SESSION EXPIRATION: Clear after 12 hours
    const [selectedCourseAuthId, setSelectedCourseAuthId] = useState<number | null>(() => {
        // Check if session has expired
        if (isSessionExpired()) {
            console.log('⏰ StudentDataLayer: Session expired (12 hours), returning to dashboard');
            localStorage.removeItem('frost_selected_course_auth_id');
            localStorage.removeItem('frost_session_timestamp');
            return null;
        }

        // Try to restore from localStorage if session is still valid
        const saved = localStorage.getItem('frost_selected_course_auth_id');
        if (saved) {
            const parsedId = parseInt(saved, 10);
            if (!isNaN(parsedId)) {
                console.log('📦 StudentDataLayer: Restored courseAuthId from localStorage:', parsedId);
                return parsedId;
            }
        }
        // Fallback to initialCourseAuthId or null
        console.log('📦 StudentDataLayer: Using initial courseAuthId:', initialCourseAuthId);
        return initialCourseAuthId || null;
    });

    // Track if user explicitly clicked Dashboard (to prevent auto-select)
    // PERSISTENCE: Restore from localStorage on mount
    const [userExplicitlySelectedDashboard, setUserExplicitlySelectedDashboard] = useState(() => {
        const saved = localStorage.getItem('frost_user_on_dashboard');
        return saved === 'true';
    });

    // Save to localStorage whenever selectedCourseAuthId changes
    useEffect(() => {
        if (selectedCourseAuthId !== null) {
            localStorage.setItem('frost_selected_course_auth_id', selectedCourseAuthId.toString());
            // Update session timestamp
            localStorage.setItem('frost_session_timestamp', Date.now().toString());
            console.log('💾 StudentDataLayer: Saved courseAuthId to localStorage:', selectedCourseAuthId);
        } else {
            localStorage.removeItem('frost_selected_course_auth_id');
            localStorage.removeItem('frost_session_timestamp');
            console.log('🗑️ StudentDataLayer: Cleared courseAuthId from localStorage');
        }
    }, [selectedCourseAuthId]);

    // Wrap setSelectedCourseAuthId to track explicit dashboard selection
    const handleSetSelectedCourseAuthId = (id: number | null) => {
        if (id === null) {
            setUserExplicitlySelectedDashboard(true);
            localStorage.setItem('frost_user_on_dashboard', 'true');
            console.log('👤 StudentDataLayer: User explicitly clicked Dashboard');
        } else {
            setUserExplicitlySelectedDashboard(false);
            localStorage.removeItem('frost_user_on_dashboard');
        }
        setSelectedCourseAuthId(id);
    };

    // Fetch student polling data
    const {
        data: studentData,
        isLoading: studentLoading,
        error: studentError,
    } = useQuery({
        queryKey: ["student-poll"],
        queryFn: async () => {
            const response = await fetch(`/classroom/student/poll`);
            if (!response.ok) {
                throw new Error(`Failed to fetch student data: ${response.status}`);
            }
            return response.json();
        },
        placeholderData: keepPreviousData,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });

    // Classroom poll is the authoritative source of whether a CourseDate exists today.
    // It must run even before a courseAuthId is selected.
    const {
        data: classroomData,
        isLoading: classroomLoading,
        error: classroomError,
    } = useQuery({
        queryKey: ["classroom-poll", selectedCourseAuthId],
        queryFn: async () => {
            const url = selectedCourseAuthId
                ? `/classroom/class/data?course_auth_id=${selectedCourseAuthId}`
                : "/classroom/class/data";
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Failed to fetch classroom data: ${response.status}`);
            }
            return response.json();
        },
        enabled: true,
        placeholderData: keepPreviousData,
        refetchInterval: 5000, // Poll every 5 seconds
        staleTime: 4000, // Data is stale after 4 seconds
    });

    // If the classroom poll detects a scheduled class, auto-select the matching courseAuthId so
    // subsequent requests include student-owned data.
    // BUT: Don't auto-select if user explicitly clicked Dashboard button
    useEffect(() => {
        // If user explicitly clicked Dashboard, don't auto-select
        if (userExplicitlySelectedDashboard) {
            console.log('⏸️ StudentDataLayer: Skipping auto-select (user on Dashboard)');
            return;
        }

        if (selectedCourseAuthId) return;

        const classroomCourseDateId = classroomData?.data?.courseDate?.id ?? null;
        if (!classroomCourseDateId) return;

        // Prefer the explicit course_auth_id from the backend when available.
        const resolvedCourseAuthId = classroomData?.data?.course_auth_id ?? null;
        if (resolvedCourseAuthId) {
            const nextId = Number(resolvedCourseAuthId);
            if (!Number.isNaN(nextId) && nextId > 0) {
                console.log(
                    "🎯 StudentDataLayer: Auto-selecting courseAuthId from classroom poll:",
                    nextId
                );
                setSelectedCourseAuthId(nextId);
                return;
            }
        }

        // Fallback: map classroom course_id to the student's enrollment list.
        const classroomCourseId = classroomData?.data?.course?.course_id ?? null;
        if (!classroomCourseId) return;

        const courses = studentData?.data?.courses ?? [];
        const match = courses.find(
            (c: any) => Number(c?.course_id) === Number(classroomCourseId)
        );
        if (!match?.course_auth_id) return;

        const nextId = Number(match.course_auth_id);
        if (!Number.isNaN(nextId) && nextId > 0) {
            console.log(
                "🎯 StudentDataLayer: Auto-selecting courseAuthId from classroom poll (mapped):",
                {
                    courseAuthId: nextId,
                    courseId: classroomCourseId,
                    courseDateId: classroomCourseDateId,
                }
            );
            setSelectedCourseAuthId(nextId);
        }
    }, [classroomData, studentData, selectedCourseAuthId, userExplicitlySelectedDashboard]);

    // IMPORTANT UX BEHAVIOR:
    // - Show the full-page loader ONLY on the initial load when we have no data yet.
    // - During polling/background refetches, keep rendering with the last known data.
    const isInitialStudentLoad = studentLoading && !studentData;
    const isInitialClassroomLoad =
        !!selectedCourseAuthId && classroomLoading && !classroomData;
    const isInitialLoading = isInitialStudentLoad || isInitialClassroomLoad;

    const isLoading = studentLoading || classroomLoading;
    const error = studentError || classroomError;

    // Create student context data
    const studentContextValue: StudentContextType = {
        student: studentData?.data?.student || null,
        courses: studentData?.data?.courses || [],
        progress: studentData?.data?.progress || null,
        validationsByCourseAuth: studentData?.data?.validations_by_course_auth || null,
        notifications: studentData?.data?.notifications || [],
        assignments: studentData?.data?.assignments || [],
        selectedCourseAuthId: selectedCourseAuthId,
        setSelectedCourseAuthId: handleSetSelectedCourseAuthId,
        loading: isLoading,
        error: error instanceof Error ? error.message : null,
    };

    // Create classroom context data from poll response
    const classroomContextValue: ClassroomContextType | null = classroomData?.data ? {
        data: classroomData.data,
        course: classroomData.data.course || null,
        courseDate: classroomData.data.courseDate || null,
        instructor: classroomData.data.courseDate?.instructor || null,
        instUnit: classroomData.data.instUnit || null,
        studentUnit: classroomData.data.studentUnit || null,
        courseUnits: classroomData.data.courseUnit?.course_units || [],
        courseLessons: classroomData.data.lessons || [],
        instLessons: classroomData.data.instUnit?.inst_lessons || [],
        config: classroomData.data.config || null,
        isClassroomActive: isInstructorTeaching(classroomData.data),
        isInstructorOnline: classroomData.data.courseDate?.instructor?.online_status === 'online' || false,
        classroomStatus: getClassroomStatus(classroomData.data) as any,
        loading: classroomLoading,
        error: classroomError instanceof Error ? classroomError.message : null,
    } : null;

    console.log("🎓 StudentDataLayer: Rendering with contexts", {
        selectedCourseAuthId,
        isLoading,
        hasError: !!error,
        studentData: studentContextValue,
        classroomData: classroomContextValue,
    });

    // ALWAYS render with both contexts - let MainDashboard handle state display
    return (
        <StudentContextProvider value={studentContextValue}>
            <ClassroomContextProvider value={classroomContextValue}>
                {isInitialLoading ? (
                    <PageLoader />
                ) : error && !studentData ? (
                    <Alert variant="danger" className="m-4">
                        <Alert.Heading>⚠️ Data Loading Error</Alert.Heading>
                        <p>
                            {error instanceof Error
                                ? error.message
                                : "Unable to load student data"}
                        </p>
                        <p className="mb-0">Please refresh the page or contact support.</p>
                    </Alert>
                ) : (
                    // Render MainDashboard with selectedCourseAuthId from internal state
                    // Dashboard will show course list if no selectedCourseAuthId
                    // Dashboard will show specific classroom if selectedCourseAuthId is set
                    <MainDashboard courseAuthId={selectedCourseAuthId} />
                )}
            </ClassroomContextProvider>
        </StudentContextProvider>
    );
};

export default StudentDataLayer;
