import React, { useEffect, useState, useMemo } from "react";
import { Alert } from "react-bootstrap";
import MainDashboard from "../Student/Components/Dashboard/MainDashboard";
import PageLoader from "../Shared/Components/Widgets/PageLoader";
import StudentLessonPauseModal from "../Student/Components/Classroom/StudentLessonPauseModal";
import ChallengeModal, { ChallengeData } from "../Student/Components/Classroom/ChallengeModal";
import {
    StudentContextProvider,
    StudentContextType,
} from "./context/StudentContext";
import {
    ClassroomContextProvider,
    ClassroomContextType,
} from "./context/ClassroomContext";
import { isInstructorTeaching, getClassroomStatus } from "./services/classroomService";
import { useStudentPoll, useClassroomPoll } from "./hooks";

interface StudentDataLayerProps {
    courseAuthId?: number | null;
}

// ---------------------------------------------------------------------
// SAFE HELPERS (no validation, no throwing)
// ---------------------------------------------------------------------
const safeJsonFromElement = (id: string): any | null => {
    const el = document.getElementById(id);
    if (!el?.textContent) return null;
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
};

const getOkData = <T,>(res: any): T | null => {
    if (!res) return null;
    if (typeof res.success === "boolean" && res.success === false) return null;
    return (res.data ?? res) as T;
};

const getLessonId = (lesson: any): number | null => {
    const raw = lesson?.lesson_id ?? lesson?.id;
    const n = Number(raw);
    return Number.isFinite(n) ? n : null;
};

const StudentDataLayer: React.FC<StudentDataLayerProps> = ({
    courseAuthId: initialCourseAuthId,
}) => {
    // Session expiration: 12 hours
    const SESSION_DURATION_MS = 12 * 60 * 60 * 1000;

    const isSessionExpired = (): boolean => {
        const sessionTimestamp = localStorage.getItem("frost_session_timestamp");
        if (!sessionTimestamp) return true;
        const sessionTime = parseInt(sessionTimestamp, 10);
        return Date.now() - sessionTime > SESSION_DURATION_MS;
    };

    // OPTIONAL bootstrap from DOM props (never required, never validated)
    const domBootstrapCourseAuthId = useMemo(() => {
        const props = safeJsonFromElement("student-props");
        const candidate =
            props?.course_auth_id ??
            props?.selected_course_auth_id ??
            props?.selectedCourseAuthId ??
            null;

        const n = Number(candidate);
        return Number.isFinite(n) && n > 0 ? n : null;
    }, []);

    // Selected course routing state
    const [selectedCourseAuthId, setSelectedCourseAuthId] = useState<number | null>(() => {
        // expire session
        if (isSessionExpired()) {
            localStorage.removeItem("frost_selected_course_auth_id");
            localStorage.removeItem("frost_session_timestamp");
            return null;
        }

        // restore persisted selection
        const saved = localStorage.getItem("frost_selected_course_auth_id");
        if (saved) {
            const parsedId = parseInt(saved, 10);
            if (!isNaN(parsedId)) return parsedId;
        }

        // fallback priority: prop -> DOM -> null
        return initialCourseAuthId ?? domBootstrapCourseAuthId ?? null;
    });

    // Dashboard lock flag
    const [userExplicitlySelectedDashboard, setUserExplicitlySelectedDashboard] = useState(() => {
        return localStorage.getItem("frost_user_on_dashboard") === "true";
    });

    // Pause modal state
    const [showPauseModal, setShowPauseModal] = useState(false);
    const [pausedLessonTitle, setPausedLessonTitle] = useState<string>("");
    const [breaksRemaining, setBreaksRemaining] = useState<number | undefined>(undefined);
    const [breakDurationMinutes, setBreakDurationMinutes] = useState<number>(15);
    const [breakStartedAt, setBreakStartedAt] = useState<string | undefined>(undefined);

    // Challenge state
    const [activeChallenge, setActiveChallenge] = useState<ChallengeData | null>(null);

    // Persist selection
    useEffect(() => {
        if (selectedCourseAuthId !== null) {
            localStorage.setItem("frost_selected_course_auth_id", String(selectedCourseAuthId));
            localStorage.setItem("frost_session_timestamp", String(Date.now()));
        } else {
            localStorage.removeItem("frost_selected_course_auth_id");
            localStorage.removeItem("frost_session_timestamp");
        }
    }, [selectedCourseAuthId]);

    // Setter wrapper
    const handleSetSelectedCourseAuthId = (id: number | null) => {
        // ---------------------------------------------------------------
        // Course-switch session close
        //
        // Rules:
        //   - Switching from course A → course B (both non-null, different):
        //     fire a background leave for the active StudentUnit so the
        //     old session is properly closed before the new one opens.
        //   - Navigating to the order dashboard (id === null):
        //     the session stays alive — student may return to the same course.
        // ---------------------------------------------------------------
        if (id !== null && selectedCourseAuthId !== null && id !== selectedCourseAuthId) {
            const activeStudentUnitId = studentPoll?.studentUnit?.id;
            if (activeStudentUnitId) {
                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ?? "";
                fetch("/classroom/session/leave", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        student_unit_id: activeStudentUnitId,
                        reason: "course_switch",
                    }),
                    keepalive: true, // completes even if component re-renders mid-flight
                }).catch(() => {
                    // Fire-and-forget: if the request fails the session will be
                    // cleaned up by the next heartbeat timeout on the server side.
                });
            }
        }

        if (id === null) {
            setUserExplicitlySelectedDashboard(true);
            localStorage.setItem("frost_user_on_dashboard", "true");
        } else {
            setUserExplicitlySelectedDashboard(false);
            localStorage.removeItem("frost_user_on_dashboard");
        }
        setSelectedCourseAuthId(id);
    };

    // ---------------------------------------------------------------------
    // POLLING (SOURCE OF TRUTH)
    // ---------------------------------------------------------------------
    const {
        data: studentPollRes,
        isLoading: studentLoading,
        error: studentError,
    } = useStudentPoll() as any;

    const studentPoll = getOkData<any>(studentPollRes);

    // Prefer the per-course map (new API) so each enrollment is evaluated
    // independently. Fall back to the legacy single-entry field if the map
    // is not yet present (old backend / cache).
    const activeClassroomsByAuth: Record<string, any> =
        studentPoll?.active_classrooms_by_course_auth ?? {};
    const activeClassroom: any =
        selectedCourseAuthId
            ? (activeClassroomsByAuth[String(selectedCourseAuthId)] ??
               activeClassroomsByAuth[selectedCourseAuthId] ??
               null)
            : (studentPoll?.active_classroom ?? null);

    // Only activate the classroom poll for the selected course, and only when the
    // class hasn't expired. A class is considered expired (stale/leftover) only when:
    // - It has no instructor (never started), AND
    // - The starts_at date is from a PREVIOUS calendar day (not today).
    // Using a previous-day check instead of a 2-hour check prevents incorrectly
    // expiring today's class when the instructor is simply late to start.
    const _acStartsAt = activeClassroom?.starts_at
        ? new Date(activeClassroom.starts_at).getTime()
        : null;
    const _acIsFromToday =
        _acStartsAt !== null &&
        !isNaN(_acStartsAt) &&
        new Date(_acStartsAt).toDateString() === new Date().toDateString();
    const _acExpired =
        _acStartsAt !== null &&
        !isNaN(_acStartsAt) &&
        !activeClassroom?.inst_unit_id &&
        !_acIsFromToday;

    const courseDateId =
        activeClassroom &&
            selectedCourseAuthId &&
            !_acExpired
            ? activeClassroom.course_date_id
            : null;

    const {
        data: classroomPollRes,
        isLoading: classroomLoading,
        error: classroomError,
    } = useClassroomPoll(courseDateId) as any;

    const classroomPoll = getOkData<any>(classroomPollRes);

    // ---------------------------------------------------------------------
    // RECONCILIATION: validate persisted selectedCourseAuthId against live
    // enrollment list from the poll.
    //
    // Problem: localStorage preserves the last-selected course_auth_id across
    // page loads. If the student now has different enrollments (e.g. added G
    // course, or a stale session for a removed course), the stored ID will not
    // match anything in courses[] — but the auto-select below bails early
    // because the value is non-null, freezing the UI on the wrong course.
    //
    // Fix: once we have a non-empty courses[] from the poll, verify the stored
    // ID is still valid. If not, clear it (and the dashboard-lock flag) so
    // auto-select can run cleanly on the next render.
    // ---------------------------------------------------------------------
    useEffect(() => {
        const courses = studentPoll?.courses ?? [];

        // Wait until the poll has returned at least one enrolled course.
        if (courses.length === 0) return;

        // Nothing stored — nothing to validate.
        if (selectedCourseAuthId === null) return;

        const isValid = courses.some(
            (c: any) => Number(c?.id) === selectedCourseAuthId,
        );

        if (!isValid) {
            // Stale or orphaned ID: wipe it so auto-select re-runs.
            localStorage.removeItem("frost_selected_course_auth_id");
            localStorage.removeItem("frost_user_on_dashboard");
            setUserExplicitlySelectedDashboard(false);
            setSelectedCourseAuthId(null);
        }
    }, [studentPoll, selectedCourseAuthId]);

    // ---------------------------------------------------------------------
    // AUTO-SELECT COURSE
    // ---------------------------------------------------------------------
    useEffect(() => {
        if (userExplicitlySelectedDashboard) return;
        if (selectedCourseAuthId) return;

        if (!activeClassroom?.course_id) return;

        const courses = studentPoll?.courses ?? [];
        const match = courses.find(
            (c: any) => Number(c?.course_id) === Number(activeClassroom.course_id),
        );

        const nextId = Number(match?.id);
        if (!Number.isNaN(nextId) && nextId > 0) {
            setSelectedCourseAuthId(nextId);
        }
    }, [activeClassroom, selectedCourseAuthId, studentPoll, userExplicitlySelectedDashboard]);

    // ---------------------------------------------------------------------
    // PAUSE DETECTION
    // ---------------------------------------------------------------------
    useEffect(() => {
        const activeLesson = classroomPoll?.activeLesson;

        if (!activeLesson) {
            setShowPauseModal(false);
            return;
        }

        if (activeLesson.is_paused) {
            const lessonTitle =
                classroomPoll?.lessons?.find((l: any) => {
                    const lid = getLessonId(l);
                    return lid !== null && Number(lid) === Number(activeLesson.lesson_id);
                })?.title || "Current Lesson";

            setPausedLessonTitle(lessonTitle);

            const breaks = classroomPoll?.breaks;
            if (breaks) {
                setBreaksRemaining(breaks.breaks_remaining);
                if (breaks.break_duration_minutes) {
                    setBreakDurationMinutes(breaks.break_duration_minutes);
                }
            }

            if (activeLesson.paused_at) setBreakStartedAt(activeLesson.paused_at);

            setShowPauseModal(true);
        } else {
            setShowPauseModal(false);
        }
    }, [classroomPoll]);

    // ---------------------------------------------------------------------
    // CHALLENGE DETECTION
    // ---------------------------------------------------------------------
    useEffect(() => {
        const challenge = classroomPoll?.challenge ?? null;

        if (challenge?.challenge_id) {
            if (!activeChallenge || activeChallenge.challenge_id !== challenge.challenge_id) {
                setActiveChallenge(challenge);
            }
        } else {
            if (activeChallenge) setActiveChallenge(null);
        }
    }, [classroomPoll, activeChallenge]);

    const handleChallengeComplete = async (challengeId: number): Promise<void> => {
        const response = await fetch("/classroom/challenge-respond", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
            },
            body: JSON.stringify({ challenge_id: challengeId, completed: true }),
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const result = await response.json();
        if (!result.success) throw new Error(result.message || "Failed to submit challenge response");

        setActiveChallenge(null);
    };

    const handleChallengeError = (error: string): void => {
        console.error("🚨 Challenge error:", error);
    };

    // Loading & error
    const isInitialStudentLoad = studentLoading && !studentPollRes;
    // When courseDateId is null the classroom poll is disabled — never block on it.
    const isInitialClassroomLoad =
        !!courseDateId &&
        !!selectedCourseAuthId &&
        classroomLoading &&
        !classroomPollRes;
    const isInitialLoading = isInitialStudentLoad || isInitialClassroomLoad;

    const isLoading = studentLoading || classroomLoading;
    const error = studentError || classroomError;

    // Build contexts
    const studentContextValue: StudentContextType = {
        student: studentPoll?.student || null,
        courses: studentPoll?.courses || [],
        progress: studentPoll?.progress || null,
        validationsByCourseAuth: studentPoll?.validations_by_course_auth || null,
        activeClassroom: studentPoll?.active_classroom || null,
        studentExam: studentPoll?.studentExam || null,
        studentExamsByCourseAuth: studentPoll?.studentExamsByCourseAuth || null,
        lessonsByCourseAuth: studentPoll?.lessons_by_course_auth || null,
        studentUnit: studentPoll?.studentUnit || null,
        studentLessons: studentPoll?.studentLessons || [],
        challenges: studentPoll?.challenges || [],
        notifications: studentPoll?.notifications || [],
        assignments: studentPoll?.assignments || [],
        selectedCourseAuthId,
        setSelectedCourseAuthId: handleSetSelectedCourseAuthId,
        loading: isLoading,
        error: error instanceof Error ? error.message : null,
    };

    const classroomContextValue: ClassroomContextType | null =
        classroomPoll
            ? {
                data: classroomPoll,
                course: null,
                courseDate: classroomPoll.courseDate || null,
                instructor: classroomPoll.instructor || null,
                instUnit: classroomPoll.instUnit || null,
                studentUnit: null,
                courseUnits: classroomPoll.courseUnit?.course_units || [],
                courseLessons: classroomPoll.lessons || [],
                instLessons: classroomPoll.instUnit?.inst_lessons || [],
                config: null,
                isClassroomActive: isInstructorTeaching(classroomPoll),
                isInstructorOnline: classroomPoll.instructor?.online_status === "online" || false,
                classroomStatus: getClassroomStatus(classroomPoll) as any,
                loading: classroomLoading,
                error: classroomError instanceof Error ? classroomError.message : null,
            }
            : null;

    return (
        <StudentContextProvider value={studentContextValue}>
            <ClassroomContextProvider value={classroomContextValue}>
                <StudentLessonPauseModal
                    isVisible={showPauseModal}
                    lessonTitle={pausedLessonTitle}
                    breaksRemaining={breaksRemaining}
                    breakDurationMinutes={breakDurationMinutes}
                    breakStartedAt={breakStartedAt}
                />

                {activeChallenge && (
                    <ChallengeModal
                        challenge={activeChallenge}
                        onComplete={handleChallengeComplete}
                        onError={handleChallengeError}
                    />
                )}

                {isInitialLoading ? (
                    <PageLoader />
                ) : error && !studentPollRes ? (
                    <Alert variant="danger" className="m-4">
                        <Alert.Heading>⚠️ Data Loading Error</Alert.Heading>
                        <p>{error instanceof Error ? error.message : "Unable to load student data"}</p>
                        <p className="mb-0">Please refresh the page or contact support.</p>
                    </Alert>
                ) : (
                    <MainDashboard courseAuthId={selectedCourseAuthId} />
                )}
            </ClassroomContextProvider>
        </StudentContextProvider>
    );
};

export default StudentDataLayer;
