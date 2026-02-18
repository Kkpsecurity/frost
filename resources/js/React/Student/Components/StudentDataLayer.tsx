import React, { useEffect, useState } from "react";
import { Alert } from "react-bootstrap";
import MainDashboard from "./Dashboard/MainDashboard";
import PageLoader from "../../Shared/Components/Widgets/PageLoader";
import StudentLessonPauseModal from "./Classroom/StudentLessonPauseModal";
import ChallengeModal, { ChallengeData } from "./Classroom/ChallengeModal";
import {
    StudentContextProvider,
    StudentContextType,
} from "../context/StudentContext";
import {
    ClassroomContextProvider,
    ClassroomContextType,
} from "../context/ClassroomContext";
import {
    isInstructorTeaching,
    getClassroomStatus,
} from "../services/classroomService";
import { useStudentPoll, useClassroomPoll } from "../hooks";

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
        const sessionTimestamp = localStorage.getItem(
            "frost_session_timestamp",
        );
        if (!sessionTimestamp) return true;

        const sessionTime = parseInt(sessionTimestamp, 10);
        const now = Date.now();
        const elapsed = now - sessionTime;

        return elapsed > SESSION_DURATION_MS;
    };

    // Internal state for selected courseAuthId (SPA routing)
    // PERSISTENCE: Restore from localStorage on mount, save on changes
    // SESSION EXPIRATION: Clear after 12 hours
    const [selectedCourseAuthId, setSelectedCourseAuthId] = useState<
        number | null
    >(() => {
        // Check if session has expired
        if (isSessionExpired()) {
            console.log(
                "⏰ StudentDataLayer: Session expired (12 hours), returning to dashboard",
            );
            localStorage.removeItem("frost_selected_course_auth_id");
            localStorage.removeItem("frost_session_timestamp");
            return null;
        }

        // Try to restore from localStorage if session is still valid
        const saved = localStorage.getItem("frost_selected_course_auth_id");
        if (saved) {
            const parsedId = parseInt(saved, 10);
            if (!isNaN(parsedId)) {
                console.log(
                    "📦 StudentDataLayer: Restored courseAuthId from localStorage:",
                    parsedId,
                );
                return parsedId;
            }
        }
        // Fallback to initialCourseAuthId or null
        console.log(
            "📦 StudentDataLayer: Using initial courseAuthId:",
            initialCourseAuthId,
        );
        return initialCourseAuthId || null;
    });

    // Track if user explicitly clicked Dashboard (to prevent auto-select)
    // PERSISTENCE: Restore from localStorage on mount
    const [
        userExplicitlySelectedDashboard,
        setUserExplicitlySelectedDashboard,
    ] = useState(() => {
        const saved = localStorage.getItem("frost_user_on_dashboard");
        return saved === "true";
    });

    // Track pause modal state
    const [showPauseModal, setShowPauseModal] = useState(false);
    const [pausedLessonTitle, setPausedLessonTitle] = useState<string>("");
    const [breaksRemaining, setBreaksRemaining] = useState<number | undefined>(
        undefined,
    );
    const [breakDurationMinutes, setBreakDurationMinutes] =
        useState<number>(15);
    const [breakStartedAt, setBreakStartedAt] = useState<string | undefined>(
        undefined,
    );

    // Challenge state
    const [activeChallenge, setActiveChallenge] =
        useState<ChallengeData | null>(null);

    // DEBUG: Log when showPauseModal changes
    useEffect(() => {
        console.log("🚨 PAUSE MODAL STATE CHANGED:", {
            showPauseModal,
            pausedLessonTitle,
            breaksRemaining,
        });
    }, [showPauseModal, pausedLessonTitle, breaksRemaining]);

    // Save to localStorage whenever selectedCourseAuthId changes
    useEffect(() => {
        if (selectedCourseAuthId !== null) {
            localStorage.setItem(
                "frost_selected_course_auth_id",
                selectedCourseAuthId.toString(),
            );
            // Update session timestamp
            localStorage.setItem(
                "frost_session_timestamp",
                Date.now().toString(),
            );
            console.log(
                "💾 StudentDataLayer: Saved courseAuthId to localStorage:",
                selectedCourseAuthId,
            );
        } else {
            localStorage.removeItem("frost_selected_course_auth_id");
            localStorage.removeItem("frost_session_timestamp");
            console.log(
                "🗑️ StudentDataLayer: Cleared courseAuthId from localStorage",
            );
        }
    }, [selectedCourseAuthId]);

    // Wrap setSelectedCourseAuthId to track explicit dashboard selection
    const handleSetSelectedCourseAuthId = (id: number | null) => {
        if (id === null) {
            setUserExplicitlySelectedDashboard(true);
            localStorage.setItem("frost_user_on_dashboard", "true");
            console.log(
                "👤 StudentDataLayer: User explicitly clicked Dashboard",
            );
        } else {
            setUserExplicitlySelectedDashboard(false);
            localStorage.removeItem("frost_user_on_dashboard");
        }
        setSelectedCourseAuthId(id);
    };

    // Fetch student polling data using custom hook
    const {
        data: studentData,
        isLoading: studentLoading,
        error: studentError,
    } = useStudentPoll();

    // Classroom poll returns shared classroom data using course_date_id from student poll.
    // This identifies WHICH classroom (not which student).
    const courseDateId = studentData?.data?.active_classroom?.course_date_id;

    const {
        data: classroomData,
        isLoading: classroomLoading,
        error: classroomError,
    } = useClassroomPoll(courseDateId);

    // Auto-select the course if student has an active classroom today
    // Use student poll's active_classroom reference, not classroom poll
    useEffect(() => {
        // If user explicitly clicked Dashboard, don't auto-select
        if (userExplicitlySelectedDashboard) {
            console.log(
                "⏸️ StudentDataLayer: Skipping auto-select (user on Dashboard)",
            );
            return;
        }

        if (selectedCourseAuthId) return;

        // Get active classroom reference from student poll
        const activeClassroom = studentData?.data?.active_classroom;
        if (!activeClassroom?.course_id) return;

        // Find the matching course enrollment from student's courses
        const courses = studentData?.data?.courses ?? [];
        const match = courses.find(
            (c: any) =>
                Number(c?.course_id) === Number(activeClassroom.course_id),
        );

        if (!match?.id) return;

        const nextId = Number(match.id); // This is the course_auth_id
        if (!Number.isNaN(nextId) && nextId > 0) {
            console.log(
                "🎯 StudentDataLayer: Auto-selecting courseAuthId from active classroom:",
                {
                    courseAuthId: nextId,
                    courseId: activeClassroom.course_id,
                    courseDateId: activeClassroom.course_date_id,
                },
            );
            setSelectedCourseAuthId(nextId);
        }
    }, [studentData, selectedCourseAuthId, userExplicitlySelectedDashboard]);

    // =========================================================================
    // PAUSE DETECTION LOGIC
    // =========================================================================
    // Monitor activeLesson from classroom poll for pause status
    useEffect(() => {
        // Log the full classroom poll data structure
        console.log("📡 FULL CLASSROOM POLL DATA:", {
            hasClassroomData: !!classroomData,
            classroomDataKeys: classroomData ? Object.keys(classroomData) : [],
            dataKeys: classroomData?.data
                ? Object.keys(classroomData.data)
                : [],
            fullData: classroomData?.data,
        });

        const activeLesson = classroomData?.data?.activeLesson;

        console.log("🔍 PAUSE DETECTION DEBUG:", {
            hasClassroomData: !!classroomData,
            hasActiveLesson: !!activeLesson,
            activeLesson: activeLesson,
            isPaused: activeLesson?.is_paused,
            activeLessonType: typeof activeLesson,
        });

        if (!activeLesson) {
            // No active lesson - clear pause modal
            setShowPauseModal(false);
            return;
        }

        // Check if the active lesson is paused
        if (activeLesson.is_paused) {
            console.log("🔴 PAUSE DETECTED - SETTING MODAL TO TRUE");

            // Lesson is paused - show modal
            const lessonTitle =
                classroomData?.data?.lessons?.find(
                    (l: any) => l.id === activeLesson.lesson_id,
                )?.title || "Current Lesson";

            setPausedLessonTitle(lessonTitle);

            // Try to get breaks remaining from lesson state if available
            const breaks = (classroomData?.data as any)?.breaks;
            if (breaks) {
                setBreaksRemaining(breaks.breaks_remaining);

                // Extract break duration if available (comes from backend)
                if (breaks.break_duration_minutes) {
                    setBreakDurationMinutes(breaks.break_duration_minutes);
                }
            }

            // Extract break start time from active lesson
            if (activeLesson.paused_at) {
                setBreakStartedAt(activeLesson.paused_at);
            }

            console.log("🔴 ABOUT TO CALL setShowPauseModal(true)");
            setShowPauseModal(true);
            console.log("🔴 CALLED setShowPauseModal(true)");

            console.log("⏸️ StudentDataLayer: Lesson paused detected", {
                instLessonId: activeLesson.id,
                lessonId: activeLesson.lesson_id,
                lessonTitle,
                isPaused: activeLesson.is_paused,
                breakDurationMinutes,
                breakStartedAt: activeLesson.paused_at,
            });
        } else {
            // No paused lesson - hide modal
            if (showPauseModal) {
                console.log("▶️ StudentDataLayer: Lesson resumed");
            }
            setShowPauseModal(false);
        }
    }, [classroomData, showPauseModal]);

    // Detect active challenge from classroom poll
    useEffect(() => {
        if (classroomData?.data?.challenge) {
            const challenge = classroomData.data.challenge;

            // Only show if not already showing same challenge
            if (
                !activeChallenge ||
                activeChallenge.challenge_id !== challenge.challenge_id
            ) {
                console.log("🚨 Challenge detected:", challenge);
                setActiveChallenge(challenge);
            }
        } else {
            // No challenge - clear modal
            if (activeChallenge) {
                console.log("✅ Challenge cleared");
                setActiveChallenge(null);
            }
        }
    }, [classroomData]);

    // Handle challenge completion
    const handleChallengeComplete = async (
        challengeId: number,
    ): Promise<void> => {
        console.log("✅ Submitting challenge completion:", challengeId);

        try {
            const response = await fetch("/classroom/challenge-respond", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    challenge_id: challengeId,
                    completed: true,
                }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(
                    result.message || "Failed to submit challenge response",
                );
            }

            console.log("✅ Challenge completed successfully:", result);

            // Clear the modal immediately (next poll will confirm)
            setActiveChallenge(null);
        } catch (error) {
            console.error("❌ Challenge completion failed:", error);
            throw error; // Re-throw to trigger error handler
        }
    };

    // Handle challenge errors
    const handleChallengeError = (error: string): void => {
        console.error("🚨 Challenge error:", error);
        // Could show a toast notification here
        // For now, modal will handle displaying the error
    };

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
        validationsByCourseAuth:
            studentData?.data?.validations_by_course_auth || null,
        activeClassroom: studentData?.data?.active_classroom || null,
        studentExam: studentData?.data?.studentExam || null,
        studentExamsByCourseAuth:
            studentData?.data?.studentExamsByCourseAuth || null,
        lessonsByCourseAuth: studentData?.data?.lessons_by_course_auth || null,
        studentUnit: studentData?.data?.studentUnit || null,
        studentLessons: studentData?.data?.studentLessons || [],
        notifications: studentData?.data?.notifications || [],
        assignments: studentData?.data?.assignments || [],
        selectedCourseAuthId: selectedCourseAuthId,
        setSelectedCourseAuthId: handleSetSelectedCourseAuthId,
        loading: isLoading,
        error: error instanceof Error ? error.message : null,
    };

    // Create classroom context data from poll response
    const classroomContextValue: ClassroomContextType | null =
        classroomData?.data
            ? {
                  data: classroomData.data,
                  course: null, // Removed from classroom poll - use student poll
                  courseDate: classroomData.data.courseDate || null,
                  instructor: classroomData.data.instructor || null,
                  instUnit: classroomData.data.instUnit || null,
                  studentUnit: null, // Removed from classroom poll - use student poll
                  courseUnits:
                      classroomData.data.courseUnit?.course_units || [],
                  courseLessons: classroomData.data.lessons || [],
                  instLessons: classroomData.data.instUnit?.inst_lessons || [],
                  config: null, // Removed from classroom poll
                  isClassroomActive: isInstructorTeaching(classroomData.data),
                  isInstructorOnline:
                      classroomData.data.instructor?.online_status ===
                          "online" || false,
                  classroomStatus: getClassroomStatus(
                      classroomData.data,
                  ) as any,
                  loading: classroomLoading,
                  error:
                      classroomError instanceof Error
                          ? classroomError.message
                          : null,
              }
            : null;

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
                {/* Pause Modal - Shown when instructor pauses lesson */}
                <StudentLessonPauseModal
                    isVisible={showPauseModal}
                    lessonTitle={pausedLessonTitle}
                    breaksRemaining={breaksRemaining}
                    breakDurationMinutes={breakDurationMinutes}
                    breakStartedAt={breakStartedAt}
                />

                {/* Challenge Modal - Shown when participation check is active */}
                {activeChallenge && (
                    <ChallengeModal
                        challenge={activeChallenge}
                        onComplete={handleChallengeComplete}
                        onError={handleChallengeError}
                    />
                )}

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
                        <p className="mb-0">
                            Please refresh the page or contact support.
                        </p>
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
