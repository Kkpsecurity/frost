import React, { useState, useEffect } from "react";
import { useStudent } from "../../context/StudentContext";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import FrostDashboardWrapper from "../../Styles/FrostDashboardWrapper.styled";
import ExamAcknowledgement from "./ExamAcknowledgement";
import ExamView from "./ExamView";
import ExamReview from "./ExamReview";

interface ExamRoomProps {
    courseAuthId: number;
    onBackToDashboard: () => void;
    devModeToggle?: React.ReactNode;
}

/**
 * 🎓 EXAM ROOM COMPONENT
 *
 * Purpose: Main exam room dashboard for students who have completed all lessons
 * This serves as the exam hub where students can:
 * - View exam availability and requirements
 * - Start new exam attempts
 * - Resume in-progress exams
 * - View past exam results
 *
 * Similar to MainOffline and MainOnline, but focused on exam experience
 */
const ExamRoom: React.FC<ExamRoomProps> = ({
    courseAuthId,
    onBackToDashboard,
    devModeToggle,
}) => {
    console.log(
        "🎓 ExamRoom component rendering - VERSION 2.0 - HOISTING FIXED",
    );

    const studentContext = useStudent();
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [lessonStats, setLessonStats] = useState({
        total: 0,
        completed: 0,
    });
    const [startingExam, setStartingExam] = useState(false);
    const [resettingExam, setResettingExam] = useState(false);

    // Exam attempt tracking
    const [examAttempts, setExamAttempts] = useState<any[]>([]);
    const [attemptStats, setAttemptStats] = useState({
        total: 0,
        max: 2,
        remaining: 2,
    });
    const [loadingAttempts, setLoadingAttempts] = useState(false);

    // Exam flow state
    const [examView, setExamView] = useState<
        "dashboard" | "acknowledgement" | "exam"
    >("dashboard");
    const [examAuth, setExamAuth] = useState<any>(null);
    const [loadingExam, setLoadingExam] = useState(false);

    const getExamStartedKey = (examAuthId: number) =>
        `frost_exam_started_${examAuthId}`;
    const getExamAnswersKey = (examAuthId: number) =>
        `frost_exam_answers_${examAuthId}`;
    const getActiveExamAuthIdKey = (courseAuthId: number) =>
        `frost_exam_active_exam_auth_id_${courseAuthId}`;

    const hasExamBeenStarted = (examAuthId: number) => {
        try {
            return (
                window.localStorage.getItem(getExamStartedKey(examAuthId)) ===
                "1"
            );
        } catch {
            return false;
        }
    };

    const markExamStarted = (examAuthId: number) => {
        try {
            window.localStorage.setItem(getExamStartedKey(examAuthId), "1");
        } catch {
            // ignore
        }
    };

    const clearExamLocalState = (examAuthId: number) => {
        try {
            window.localStorage.removeItem(getExamStartedKey(examAuthId));
            window.localStorage.removeItem(getExamAnswersKey(examAuthId));
        } catch {
            // ignore
        }
    };

    const getStoredActiveExamAuthId = () => {
        try {
            const raw = window.localStorage.getItem(
                getActiveExamAuthIdKey(courseAuthId),
            );
            if (!raw) return null;
            const parsed = Number(raw);
            return Number.isFinite(parsed) && parsed > 0 ? parsed : null;
        } catch {
            return null;
        }
    };

    const setStoredActiveExamAuthId = (examAuthId: number) => {
        try {
            window.localStorage.setItem(
                getActiveExamAuthIdKey(courseAuthId),
                String(examAuthId),
            );
        } catch {
            // ignore
        }
    };

    const clearStoredActiveExamAuthId = () => {
        try {
            window.localStorage.removeItem(
                getActiveExamAuthIdKey(courseAuthId),
            );
        } catch {
            // ignore
        }
    };

    // Get course and exam data from student context
    const studentExam =
        studentContext?.studentExamsByCourseAuth?.[courseAuthId];
    const course = studentContext?.courses?.find(
        (c: any) => c.id === courseAuthId || c.course_auth_id === courseAuthId,
    );
    const student = studentContext?.student;

    // Get course name from multiple possible sources
    const courseName =
        course?.name ||
        course?.course_name ||
        (studentContext as any)?.selectedCourse?.name ||
        "Course";

    const lessonPayload =
        studentContext?.lessonsByCourseAuth?.[courseAuthId] ?? null;

    const handleResetExam = async () => {
        if (resettingExam) return;

        if (
            !confirm(
                "⚠️ DEV MODE: This will delete all exam attempts for this course. Are you sure?",
            )
        ) {
            return;
        }

        setResettingExam(true);

        try {
            const response = await fetch("/classroom/exam/reset", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    course_auth_id: courseAuthId,
                }),
            });

            const data = await response.json();

            if (data.success) {
                if (examAuth?.id) {
                    clearExamLocalState(examAuth.id);
                }
                alert(
                    `✅ ${data.message || "Exam reset successful"} - Reloading...`,
                );
                window.location.reload();
            } else {
                alert(
                    "❌ Failed to reset exam: " +
                        (data.error || "Unknown error"),
                );
                setResettingExam(false);
            }
        } catch (error) {
            console.error("Failed to reset exam:", error);
            alert("❌ Error resetting exam. Check console for details.");
            setResettingExam(false);
        }
    };

    const fetchExamAttempts = async () => {
        setLoadingAttempts(true);
        console.log(
            "🔍 Fetching exam attempts for courseAuthId:",
            courseAuthId,
        );
        try {
            const response = await fetch(
                `/classroom/exam/attempts?course_auth_id=${courseAuthId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                    },
                },
            );

            const data = await response.json();
            console.log("📊 Exam attempts response:", data);

            if (data.success) {
                console.log("✅ Loaded exam attempts:", {
                    attempts: data.attempts,
                    total: data.total_attempts,
                    max: data.max_attempts,
                    remaining: data.remaining_attempts,
                });
                setExamAttempts(data.attempts || []);
                setAttemptStats({
                    total: data.total_attempts ?? 0,
                    max: data.max_attempts ?? 2,
                    remaining: data.remaining_attempts ?? 2,
                });
            } else {
                console.error("❌ Failed to fetch exam attempts:", data.error);
            }
        } catch (error) {
            console.error("❌ Error fetching exam attempts:", error);
        } finally {
            setLoadingAttempts(false);
        }
    };

    const fetchActiveExam = async (examAuthId: number) => {
        setLoadingExam(true);
        try {
            const response = await fetch(`/classroom/exam/auth/${examAuthId}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
            });

            const data = await response.json();

            if (data.success && data.exam_auth) {
                console.log("🎓 Fetched active exam:", data.exam_auth);
                const started = hasExamBeenStarted(examAuthId);
                setStoredActiveExamAuthId(examAuthId);
                setExamAuth({ ...data.exam_auth, expires_at: null });
                setExamView(started ? "exam" : "acknowledgement");
            } else {
                console.error("Failed to fetch exam:", data.error);
                clearStoredActiveExamAuthId();
                alert(
                    "Failed to load exam: " + (data.error || "Unknown error"),
                );
            }
        } catch (error) {
            console.error("Error fetching exam:", error);
            clearStoredActiveExamAuthId();
            alert("Error loading exam. Please refresh the page.");
        } finally {
            setLoadingExam(false);
        }
    };

    const handleResumeExam = async () => {
        const activeExamAuthId =
            studentExam?.active_exam_auth_id ?? getStoredActiveExamAuthId();
        if (!activeExamAuthId) {
            alert("No active exam attempt found.");
            return;
        }
        await fetchActiveExam(activeExamAuthId);
    };

    const handleBeginExam = async () => {
        // User clicked "I Understand, Begin Exam" in acknowledgement
        // TEMP: timer disabled. Just load the exam and show questions.
        if (!examAuth?.id) {
            alert("Error: No exam data found");
            return;
        }

        const examAuthId = examAuth.id;
        markExamStarted(examAuthId);
        setStoredActiveExamAuthId(examAuthId);

        try {
            const refreshed = await fetch(
                `/classroom/exam/auth/${examAuthId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                    },
                },
            );

            const refreshedData = await refreshed.json();

            if (refreshedData.success && refreshedData.exam_auth) {
                // Ensure timer stays disabled client-side
                setExamAuth({ ...refreshedData.exam_auth, expires_at: null });
                setExamView("exam");
            } else {
                alert(
                    "Failed to load exam: " +
                        (refreshedData.error || "Unknown error"),
                );
            }
        } catch (error) {
            console.error("Failed to load exam:", error);
            alert("Error loading exam. Please try again.");
        }
    };

    const handleBackToDashboard = () => {
        // Reset exam view state and go back to dashboard
        setExamView("dashboard");
        setExamAuth(null);
        onBackToDashboard();
    };

    const handleSubmitExam = async (answers: Record<number, number>) => {
        if (!examAuth?.id) {
            alert("Error: No exam data found");
            return;
        }

        try {
            const response = await fetch(
                `/classroom/exam/submit/${examAuth.id}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    body: JSON.stringify({ answers }),
                },
            );

            const data = await response.json();

            if (data.success) {
                if (examAuth?.id) {
                    clearExamLocalState(examAuth.id);
                }
                clearStoredActiveExamAuthId();
                // Reload to show results
                window.location.reload();
            } else {
                alert(
                    "Failed to submit exam: " + (data.error || "Unknown error"),
                );
            }
        } catch (error) {
            console.error("Failed to submit exam:", error);
            alert("Error submitting exam. Please try again.");
        }
    };

    const handleStartExam = async () => {
        if (startingExam) return;

        // Check if attempts are exhausted
        if (attemptStats.remaining <= 0) {
            alert(
                `❌ No more attempts available. You have used all ${attemptStats.max} attempts for this exam.`,
            );
            return;
        }

        setStartingExam(true);

        try {
            const response = await fetch("/classroom/exam/begin", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    course_auth_id: courseAuthId,
                }),
            });

            const data = await response.json();

            if (data.success && data.exam_auth) {
                // Set exam data and show acknowledgement screen
                console.log(
                    "🎓 Exam created, showing acknowledgement:",
                    data.exam_auth,
                );
                if (data.exam_auth?.id) {
                    clearExamLocalState(data.exam_auth.id);
                }
                clearStoredActiveExamAuthId();
                if (data.exam_auth?.id) {
                    setStoredActiveExamAuthId(data.exam_auth.id);
                }
                setExamAuth(data.exam_auth);
                setExamView("acknowledgement");
                setStartingExam(false);
            } else {
                alert(
                    "Failed to start exam: " +
                        (data.error || data.message || "Unknown error"),
                );
                setStartingExam(false);
            }
        } catch (error) {
            console.error("Failed to start exam:", error);
            alert("Error starting exam. Please try again.");
            setStartingExam(false);
        }
    };

    useEffect(() => {
        console.log("🎓 ExamRoom mounted", {
            courseAuthId,
            courseName,
            course,
            allCourses: studentContext?.courses,
            selectedCourse: (studentContext as any)?.selectedCourse,
            studentExam,
            has_active_attempt: studentExam?.has_active_attempt,
            active_exam_auth_id: studentExam?.active_exam_auth_id,
            is_ready: studentExam?.is_ready,
            has_previous_attempt: studentExam?.has_previous_attempt,
            previous_exam_score: studentExam?.previous_exam_score,
            previous_exam_passed: studentExam?.previous_exam_passed,
            lessonPayload,
        });

        // Fetch exam attempts to show history
        fetchExamAttempts();

        // Resume path on refresh:
        // 1) Prefer poll-provided active attempt
        // 2) Fallback to localStorage (so we don't briefly render dashboard)
        if (!examAuth && examView === "dashboard") {
            const pollActiveId = studentExam?.active_exam_auth_id;
            const storedActiveId = getStoredActiveExamAuthId();
            const targetId = pollActiveId ?? storedActiveId;

            if (targetId) {
                console.log(
                    "🎓 Detected active exam attempt, fetching exam data...",
                );
                fetchActiveExam(targetId);
            }
        }

        if (!lessonPayload) {
            console.log("🎓 ExamRoom: lesson payload not yet available");
            setLessonStats({ total: 0, completed: 0 });
            setLoading(false);
            return;
        }

        const lessons = Array.isArray(lessonPayload.lessons)
            ? lessonPayload.lessons
            : [];
        const completed = lessons.filter(
            (lesson: any) =>
                lesson.is_completed ||
                lesson.status === "completed" ||
                lesson.completed_at,
        ).length;

        setLessonStats({
            total: lessons.length,
            completed,
        });
        setLoading(false);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [
        courseAuthId,
        lessonPayload,
        studentExam?.has_active_attempt,
        studentExam?.active_exam_auth_id,
    ]);

    // Determine if student is eligible to take exam
    // If studentExam exists with is_ready=true, or if we're in ExamRoom at all (means backend allowed it)
    const isExamEligible =
        studentExam?.is_ready || lessonStats.completed === lessonStats.total;
    const hasActiveAttempt = studentExam?.has_active_attempt;
    const shouldAutoResume =
        studentExam?.has_active_attempt &&
        studentExam?.active_exam_auth_id &&
        !examAuth &&
        examView === "dashboard";

    // Loading state
    if (loading || loadingExam || shouldAutoResume) {
        return (
            <div
                className="d-flex justify-content-center align-items-center"
                style={{ minHeight: "400px" }}
            >
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">
                            Loading exam room...
                        </span>
                    </div>
                    <p className="mt-3">Loading exam room...</p>
                </div>
            </div>
        );
    }

    // Show ExamView if user has started the exam
    if (examView === "exam" && examAuth) {
        return (
            <ExamView
                examAuth={examAuth}
                onSubmitExam={handleSubmitExam}
                onBackToDashboard={handleBackToDashboard}
                showDevTools={!!devModeToggle}
            />
        );
    }

    // Show ExamAcknowledgement if user has active exam but hasn't started yet
    if (examView === "acknowledgement" && examAuth) {
        return (
            <ExamAcknowledgement
                studentExam={examAuth}
                onBeginExam={handleBeginExam}
                onBackToDashboard={handleBackToDashboard}
            />
        );
    }

    return (
        <FrostDashboardWrapper>
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar
                title={courseName}
                subtitle="Complete your final exam"
                icon={<i className="fas fa-graduation-cap"></i>}
                onBackToDashboard={handleBackToDashboard}
                devModeToggle={devModeToggle}
                classroomStatus={null}
                courseAuthId={courseAuthId}
            />

            {/* Main Content */}
            <div
                className="container-fluid py-4"
                style={{
                    backgroundColor: "#1a1f2e",
                    minHeight: "calc(100vh - 60px)",
                }}
            >
                <div className="row">
                    <div className="col-12">
                        {/* Welcome Section */}
                        <div
                            className="card mb-4 shadow-sm"
                            style={{
                                backgroundColor: "#252d3d",
                                border: "1px solid #3a4456",
                            }}
                        >
                            <div className="card-body p-4">
                                <div className="d-flex align-items-center mb-3">
                                    <div
                                        className="rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style={{
                                            width: "60px",
                                            height: "60px",
                                            backgroundColor: "#3498db",
                                        }}
                                    >
                                        <i className="fas fa-graduation-cap text-white fa-2x"></i>
                                    </div>
                                    <div>
                                        <h3
                                            className="mb-0"
                                            style={{ color: "#fff" }}
                                        >
                                            {courseName}
                                        </h3>
                                        <p
                                            className="mb-0"
                                            style={{ color: "#b8c5d6" }}
                                        >
                                            🎉 Congratulations! You've completed
                                            all {lessonStats.completed} lessons.
                                            You're ready for the final exam!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="row">
                            {/* Exam Status Card */}
                            <div className="col-md-6 mb-4">
                                <div
                                    className="card h-100 shadow-sm"
                                    style={{
                                        backgroundColor: "#252d3d",
                                        border: "1px solid #3a4456",
                                    }}
                                >
                                    <div
                                        className="card-header text-white"
                                        style={{
                                            backgroundColor: "#3498db",
                                            borderBottom: "1px solid #3a4456",
                                        }}
                                    >
                                        <h5 className="mb-0">
                                            <i className="fas fa-clipboard-check me-2"></i>
                                            Exam Status
                                            {attemptStats.total > 0 && (
                                                <span
                                                    className="badge ms-2"
                                                    style={{
                                                        backgroundColor:
                                                            attemptStats.remaining >
                                                            0
                                                                ? "#27ae60"
                                                                : "#e74c3c",
                                                        fontSize: "0.75rem",
                                                    }}
                                                >
                                                    Attempts:{" "}
                                                    {attemptStats.total} /{" "}
                                                    {attemptStats.max}
                                                </span>
                                            )}
                                        </h5>
                                    </div>
                                    <div className="card-body">
                                        {/* Show Exam Attempts History */}
                                        {examAttempts.length > 0 && (
                                            <div className="mb-3">
                                                <div className="list-group">
                                                    {examAttempts.map(
                                                        (attempt, index) => {
                                                            const attemptNumber =
                                                                examAttempts.length -
                                                                index;
                                                            return (
                                                                <div
                                                                    key={
                                                                        attempt.id
                                                                    }
                                                                    className="list-group-item d-flex justify-content-between align-items-center"
                                                                    style={{
                                                                        backgroundColor:
                                                                            "#1e2533",
                                                                        border: "1px solid #3a4456",
                                                                        color: "#fff",
                                                                    }}
                                                                >
                                                                    <span>
                                                                        Attempt{" "}
                                                                        {
                                                                            attemptNumber
                                                                        }
                                                                        :
                                                                    </span>
                                                                    <span
                                                                        style={{
                                                                            color: attempt.is_passed
                                                                                ? "#27ae60"
                                                                                : "#e74c3c",
                                                                            fontWeight:
                                                                                "bold",
                                                                        }}
                                                                    >
                                                                        {
                                                                            attempt.score
                                                                        }{" "}
                                                                        /{" "}
                                                                        {
                                                                            attempt.total_points
                                                                        }
                                                                    </span>
                                                                </div>
                                                            );
                                                        },
                                                    )}
                                                </div>
                                            </div>
                                        )}

                                        {hasActiveAttempt ? (
                                            <>
                                                <div
                                                    className="alert"
                                                    style={{
                                                        backgroundColor:
                                                            "#f39c12",
                                                        border: "1px solid #e67e22",
                                                        color: "#fff",
                                                    }}
                                                >
                                                    <i className="fas fa-clock me-2"></i>
                                                    You have an exam in progress
                                                </div>
                                                <button
                                                    className="btn btn-lg w-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#f39c12",
                                                        border: "none",
                                                        color: "#fff",
                                                    }}
                                                    onClick={handleResumeExam}
                                                >
                                                    <i className="fas fa-redo me-2"></i>
                                                    Resume Exam
                                                </button>
                                            </>
                                        ) : isExamEligible &&
                                          attemptStats.remaining > 0 ? (
                                            <>
                                                {attemptStats.remaining ===
                                                    1 && (
                                                    <div
                                                        className="alert mb-3"
                                                        style={{
                                                            backgroundColor:
                                                                "rgba(241, 196, 15, 0.1)",
                                                            border: "1px solid #f1c40f",
                                                            color: "#fff",
                                                        }}
                                                    >
                                                        <i className="fas fa-exclamation-triangle me-2"></i>
                                                        ⚠️ This is your final
                                                        attempt!
                                                    </div>
                                                )}
                                                <button
                                                    className="btn btn-lg w-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#3498db",
                                                        border: "none",
                                                        color: "#fff",
                                                    }}
                                                    onClick={handleStartExam}
                                                    disabled={startingExam}
                                                >
                                                    {startingExam ? (
                                                        <>
                                                            <span
                                                                className="spinner-border spinner-border-sm me-2"
                                                                role="status"
                                                                aria-hidden="true"
                                                            ></span>
                                                            Starting Exam...
                                                        </>
                                                    ) : (
                                                        <>
                                                            <i className="fas fa-play-circle me-2"></i>
                                                            {attemptStats.total >
                                                            0
                                                                ? "Retake Exam"
                                                                : "Begin Exam"}
                                                        </>
                                                    )}
                                                </button>
                                            </>
                                        ) : attemptStats.remaining === 0 ? (
                                            <>
                                                <div
                                                    className="alert mb-3"
                                                    style={{
                                                        backgroundColor:
                                                            "#e74c3c",
                                                        border: "1px solid #c0392b",
                                                        color: "#fff",
                                                    }}
                                                >
                                                    <i className="fas fa-exclamation-triangle me-2"></i>
                                                    <strong>
                                                        No More Attempts
                                                        Available
                                                    </strong>
                                                    <p className="mb-0 mt-2">
                                                        You have used all{" "}
                                                        {attemptStats.max}{" "}
                                                        attempts for this exam.
                                                        Please contact your
                                                        instructor for
                                                        assistance.
                                                    </p>
                                                </div>
                                                <button
                                                    className="btn btn-lg w-100 mb-2"
                                                    style={{
                                                        backgroundColor:
                                                            "#6c757d",
                                                        border: "none",
                                                        color: "#fff",
                                                        opacity: 0.6,
                                                        cursor: "not-allowed",
                                                    }}
                                                    disabled
                                                >
                                                    <i className="fas fa-ban me-2"></i>
                                                    Retake Exam (No Attempts
                                                    Left)
                                                </button>
                                                <button
                                                    className="btn btn-sm w-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#f39c12",
                                                        border: "1px solid #e67e22",
                                                        color: "#fff",
                                                    }}
                                                    onClick={handleResetExam}
                                                    disabled={resettingExam}
                                                >
                                                    {resettingExam ? (
                                                        <>
                                                            <span
                                                                className="spinner-border spinner-border-sm me-2"
                                                                role="status"
                                                                aria-hidden="true"
                                                            ></span>
                                                            Resetting...
                                                        </>
                                                    ) : (
                                                        <>
                                                            <i className="fas fa-undo me-2"></i>
                                                            DEV: Reset All Exam
                                                            Attempts
                                                        </>
                                                    )}
                                                </button>
                                            </>
                                        ) : isExamEligible ? (
                                            <>
                                                {studentExam && (
                                                    <div
                                                        className="mb-3"
                                                        style={{
                                                            color: "#b8c5d6",
                                                        }}
                                                    >
                                                        <strong
                                                            style={{
                                                                color: "#fff",
                                                            }}
                                                        >
                                                            Exam Details:
                                                        </strong>
                                                        <ul className="mt-2">
                                                            <li>
                                                                Questions:{" "}
                                                                {studentExam.num_questions ||
                                                                    "N/A"}
                                                            </li>
                                                            <li>
                                                                Passing Score:{" "}
                                                                {studentExam.num_to_pass ||
                                                                    "N/A"}
                                                            </li>
                                                            <li>
                                                                Time Limit:{" "}
                                                                {studentExam.policy_expire_seconds
                                                                    ? `${Math.floor(studentExam.policy_expire_seconds / 60)} minutes`
                                                                    : "N/A"}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                )}
                                                <button
                                                    className="btn btn-lg w-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#3498db",
                                                        border: "none",
                                                        color: "#fff",
                                                    }}
                                                    onClick={handleStartExam}
                                                    disabled={startingExam}
                                                >
                                                    {startingExam ? (
                                                        <>
                                                            <span
                                                                className="spinner-border spinner-border-sm me-2"
                                                                role="status"
                                                                aria-hidden="true"
                                                            ></span>
                                                            Starting Exam...
                                                        </>
                                                    ) : (
                                                        <>
                                                            <i className="fas fa-play-circle me-2"></i>
                                                            Begin Exam
                                                        </>
                                                    )}
                                                </button>
                                            </>
                                        ) : studentExam?.has_active_attempt ? (
                                            <>
                                                <div
                                                    className="alert"
                                                    style={{
                                                        backgroundColor:
                                                            "#f39c12",
                                                        border: "1px solid #e67e22",
                                                        color: "#fff",
                                                    }}
                                                >
                                                    <i className="fas fa-clock me-2"></i>
                                                    You have an exam in progress
                                                </div>
                                                <button
                                                    className="btn btn-lg w-100"
                                                    style={{
                                                        backgroundColor:
                                                            "#f39c12",
                                                        border: "none",
                                                        color: "#fff",
                                                    }}
                                                    onClick={handleResumeExam}
                                                >
                                                    <i className="fas fa-redo me-2"></i>
                                                    Resume Exam
                                                </button>
                                            </>
                                        ) : (
                                            <div
                                                className="alert"
                                                style={{
                                                    backgroundColor: "#34495e",
                                                    border: "1px solid #3a4456",
                                                    color: "#b8c5d6",
                                                }}
                                            >
                                                <i className="fas fa-info-circle me-2"></i>
                                                Complete all lessons to unlock
                                                the exam
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Course Progress Card */}
                            <div className="col-md-6 mb-4">
                                <div
                                    className="card h-100 shadow-sm"
                                    style={{
                                        backgroundColor: "#252d3d",
                                        border: "1px solid #3a4456",
                                    }}
                                >
                                    <div
                                        className="card-header text-white"
                                        style={{
                                            backgroundColor: "#27ae60",
                                            borderBottom: "1px solid #3a4456",
                                        }}
                                    >
                                        <h5 className="mb-0">
                                            <i className="fas fa-trophy me-2"></i>
                                            Course Progress
                                        </h5>
                                    </div>
                                    <div className="card-body">
                                        <div className="mb-4">
                                            <div className="d-flex justify-content-between mb-2">
                                                <span
                                                    style={{
                                                        color: "#fff",
                                                        fontWeight: "bold",
                                                    }}
                                                >
                                                    Lessons Completed
                                                </span>
                                                <span
                                                    style={{
                                                        color: "#2ecc71",
                                                        fontWeight: "bold",
                                                    }}
                                                >
                                                    {lessonStats.completed} /{" "}
                                                    {lessonStats.total}
                                                </span>
                                            </div>
                                            <div
                                                className="progress"
                                                style={{
                                                    height: "25px",
                                                    backgroundColor: "#34495e",
                                                }}
                                            >
                                                <div
                                                    className="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar"
                                                    style={{
                                                        width: `${lessonStats.total > 0 ? (lessonStats.completed / lessonStats.total) * 100 : 0}%`,
                                                        backgroundColor:
                                                            "#27ae60",
                                                    }}
                                                    aria-valuenow={
                                                        lessonStats.completed
                                                    }
                                                    aria-valuemin={0}
                                                    aria-valuemax={
                                                        lessonStats.total
                                                    }
                                                >
                                                    {lessonStats.total > 0
                                                        ? Math.round(
                                                              (lessonStats.completed /
                                                                  lessonStats.total) *
                                                                  100,
                                                          )
                                                        : 0}
                                                    %
                                                </div>
                                            </div>
                                        </div>

                                        <div className="text-center">
                                            <div
                                                className="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style={{
                                                    width: "80px",
                                                    height: "80px",
                                                    backgroundColor: "#27ae60",
                                                }}
                                            >
                                                <i className="fas fa-check text-white fa-3x"></i>
                                            </div>
                                            <h4
                                                className="mb-2"
                                                style={{ color: "#2ecc71" }}
                                            >
                                                Ready for Exam!
                                            </h4>
                                            <p style={{ color: "#b8c5d6" }}>
                                                You've successfully completed
                                                all required lessons for{" "}
                                                {course?.name || "this course"}.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Exam Results Section - Show ONLY if PASSED */}
                        {(() => {
                            const showPassedResults =
                                studentExam?.has_previous_attempt &&
                                !studentExam?.has_active_attempt &&
                                studentExam?.previous_exam_passed === true;
                            console.log("🎓 ExamRoom: Exam Results Check", {
                                studentExam,
                                has_previous_attempt:
                                    studentExam?.has_previous_attempt,
                                has_active_attempt:
                                    studentExam?.has_active_attempt,
                                previous_exam_passed:
                                    studentExam?.previous_exam_passed,
                                previous_exam_score:
                                    studentExam?.previous_exam_score,
                                willRender: showPassedResults,
                            });
                            return showPassedResults ? (
                                <div className="row mb-4">
                                    <div className="col-12">
                                        <div
                                            className="card shadow-lg"
                                            style={{
                                                backgroundColor: "#27ae60",
                                                border: "none",
                                            }}
                                        >
                                            <div className="card-body p-4 text-center text-white">
                                                <div
                                                    className="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                    style={{
                                                        width: "100px",
                                                        height: "100px",
                                                        backgroundColor:
                                                            "rgba(255,255,255,0.3)",
                                                    }}
                                                >
                                                    <i className="fas fa-check-circle fa-4x"></i>
                                                </div>
                                                <h2 className="mb-3">
                                                    🎉 Congratulations! You
                                                    Passed!
                                                </h2>
                                                <h3 className="mb-4">
                                                    Score:{" "}
                                                    {studentExam.previous_exam_score ||
                                                        "N/A"}
                                                </h3>
                                                {studentExam.previous_exam_completed_at && (
                                                    <p
                                                        className="mb-0"
                                                        style={{ opacity: 0.9 }}
                                                    >
                                                        Completed on{" "}
                                                        {new Date(
                                                            Number(
                                                                studentExam.previous_exam_completed_at,
                                                            ) * 1000,
                                                        ).toLocaleString()}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : null;
                        })()}

                        {/* Previous Exam Attempts Details Section - DEPRECATED - Kept for reference */}
                        {false &&
                            (() => {
                                console.log(
                                    "🎓 ExamRoom: Previous Exam Details Check",
                                    {
                                        studentExam,
                                        has_previous_attempt:
                                            studentExam?.has_previous_attempt,
                                        willRender:
                                            studentExam?.has_previous_attempt ===
                                            true,
                                    },
                                );
                                return studentExam?.has_previous_attempt ? (
                                    <div
                                        className="card shadow-sm mb-4"
                                        style={{
                                            backgroundColor: "#252d3d",
                                            border: "1px solid #3a4456",
                                        }}
                                    >
                                        <div
                                            className="card-header text-white"
                                            style={{
                                                backgroundColor:
                                                    studentExam.previous_exam_passed
                                                        ? "#27ae60"
                                                        : "#95a5a6",
                                                borderBottom:
                                                    "1px solid #3a4456",
                                            }}
                                        >
                                            <h5 className="mb-0">
                                                <i className="fas fa-history me-2"></i>
                                                Previous Exam Attempt
                                            </h5>
                                        </div>
                                        <div className="card-body">
                                            <div className="row">
                                                <div className="col-md-4 mb-3 mb-md-0">
                                                    <div className="text-center">
                                                        <div
                                                            className="rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                                            style={{
                                                                width: "60px",
                                                                height: "60px",
                                                                backgroundColor:
                                                                    studentExam.previous_exam_passed
                                                                        ? "#27ae60"
                                                                        : "#e74c3c",
                                                            }}
                                                        >
                                                            <i
                                                                className={`fas ${
                                                                    studentExam.previous_exam_passed
                                                                        ? "fa-check"
                                                                        : "fa-times"
                                                                } text-white fa-2x`}
                                                            ></i>
                                                        </div>
                                                        <h5
                                                            style={{
                                                                color: studentExam.previous_exam_passed
                                                                    ? "#2ecc71"
                                                                    : "#e74c3c",
                                                            }}
                                                        >
                                                            {studentExam.previous_exam_passed
                                                                ? "Passed"
                                                                : "Not Passed"}
                                                        </h5>
                                                    </div>
                                                </div>
                                                <div className="col-md-4 mb-3 mb-md-0">
                                                    <div className="text-center">
                                                        <p
                                                            className="mb-1"
                                                            style={{
                                                                color: "#b8c5d6",
                                                                fontSize:
                                                                    "0.9rem",
                                                            }}
                                                        >
                                                            Score
                                                        </p>
                                                        <h4
                                                            style={{
                                                                color: "#fff",
                                                            }}
                                                        >
                                                            {studentExam.previous_exam_score ||
                                                                "N/A"}
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div className="col-md-4">
                                                    <div className="text-center">
                                                        <p
                                                            className="mb-1"
                                                            style={{
                                                                color: "#b8c5d6",
                                                                fontSize:
                                                                    "0.9rem",
                                                            }}
                                                        >
                                                            Completed
                                                        </p>
                                                        <h6
                                                            style={{
                                                                color: "#fff",
                                                            }}
                                                        >
                                                            {studentExam.previous_exam_completed_at
                                                                ? new Date(
                                                                      Number(
                                                                          studentExam.previous_exam_completed_at,
                                                                      ) * 1000,
                                                                  ).toLocaleDateString(
                                                                      undefined,
                                                                      {
                                                                          year: "numeric",
                                                                          month: "short",
                                                                          day: "numeric",
                                                                      },
                                                                  )
                                                                : "N/A"}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            {devModeToggle && (
                                                <div className="mt-3 text-center">
                                                    <button
                                                        className="btn btn-sm"
                                                        style={{
                                                            backgroundColor:
                                                                "#e74c3c",
                                                            border: "none",
                                                            color: "#fff",
                                                        }}
                                                        onClick={
                                                            handleResetExam
                                                        }
                                                        disabled={resettingExam}
                                                    >
                                                        {resettingExam ? (
                                                            <>
                                                                <span
                                                                    className="spinner-border spinner-border-sm me-2"
                                                                    role="status"
                                                                    aria-hidden="true"
                                                                ></span>
                                                                Resetting...
                                                            </>
                                                        ) : (
                                                            <>
                                                                <i className="fas fa-trash me-2"></i>
                                                                🔧 DEV: Reset
                                                                All Exam
                                                                Attempts
                                                            </>
                                                        )}
                                                    </button>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ) : null;
                            })()}

                        {/* Instructions Card */}
                        <div
                            className="card shadow-sm"
                            style={{
                                backgroundColor: "#252d3d",
                                border: "1px solid #3a4456",
                            }}
                        >
                            <div
                                className="card-header text-white"
                                style={{
                                    backgroundColor: "#3498db",
                                    borderBottom: "1px solid #3a4456",
                                }}
                            >
                                <h5 className="mb-0">
                                    <i className="fas fa-lightbulb me-2"></i>
                                    Exam Instructions
                                </h5>
                            </div>
                            <div className="card-body">
                                <div className="row">
                                    <div className="col-md-6">
                                        <h6 style={{ color: "#3498db" }}>
                                            Before You Begin:
                                        </h6>
                                        <ul style={{ color: "#b8c5d6" }}>
                                            <li>
                                                Find a quiet environment free
                                                from distractions
                                            </li>
                                            <li>
                                                Ensure you have a stable
                                                internet connection
                                            </li>
                                            <li>
                                                Have your ID ready for
                                                verification if required
                                            </li>
                                            <li>
                                                Read all questions carefully
                                                before answering
                                            </li>
                                        </ul>
                                    </div>
                                    <div className="col-md-6">
                                        <h6 style={{ color: "#3498db" }}>
                                            During the Exam:
                                        </h6>
                                        <ul style={{ color: "#b8c5d6" }}>
                                            <li>
                                                You cannot pause once you start
                                            </li>
                                            <li>
                                                All questions must be answered
                                            </li>
                                            <li>
                                                Double-check your answers before
                                                submitting
                                            </li>
                                            <li>
                                                The timer will count down
                                                automatically
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </FrostDashboardWrapper>
    );
};

export default ExamRoom;
