import React, { useEffect, useState } from "react";
import { useQueryClient } from "@tanstack/react-query";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";
import MainOffline from "./MainOffline";
import MainOnline from "./MainOnline";
import OnboardingFlow from "./OnboardingFlow";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import ExamRoom from "../Exam/ExamRoom";

interface MainClassroomProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
}

/**
 * MainClassroom - Orchestrator for classroom experience
 *
 * Determines whether student is in:
 * - MainOnline: Live classroom with instructor (courseDate + instUnit)
 * - WaitingRoom: Class scheduled, waiting for instructor (courseDate, NO instUnit)
 * - MainOffline: Self-study mode (no courseDate)
 *
 * Decision based on classroom poll data:
 * - CourseDate exists + InstUnit exists = Online (live class)
 * - CourseDate exists + NO InstUnit = Waiting (scheduled, pending instructor)
 * - No CourseDate = Offline (self-study)
 */
const MainClassroom: React.FC<MainClassroomProps> = ({
    courseAuthId,
    student,
    onBackToDashboard,
}) => {
    const classroomContext = useClassroom();
    const studentContext = useStudent();
    const queryClient = useQueryClient();
    const [onboardingKey, setOnboardingKey] = useState(0); // Key to force refresh after onboarding
    const [showExamRoom, setShowExamRoom] = useState(false); // Exam room state
    const [hasExitedExamRoom, setHasExitedExamRoom] = useState(false); // Track if user explicitly exited

    // Resume exam immediately on refresh (before polls finish)
    useEffect(() => {
        try {
            const raw = window.localStorage.getItem(
                `frost_exam_active_exam_auth_id_${courseAuthId}`,
            );
            if (raw) {
                setShowExamRoom(true);
            }
        } catch {
            // ignore
        }
    }, [courseAuthId]);

    // 🎨 DEV MODE: Toggle between online/offline views for design testing
    const [devMode, setDevMode] = useState<"auto" | "online" | "offline">(
        "auto",
    );
    // Show toggle in all modes for now (remove this line later to restrict to dev only)
    const showToggle = true; // TODO: Change to import.meta.env.DEV when done testing

    // 🛠️ DEV TOOLS: Lesson management functions
    const handleCompleteAllLessons = async (mode: "online" | "offline") => {
        const modeLabel =
            mode === "online" ? "ONLINE (Live Class)" : "OFFLINE (Self-Study)";
        if (
            !confirm(
                `Mark ALL lessons as complete in ${modeLabel} mode?\n\nThis will ${mode === "online" ? "create StudentLesson records" : "create SelfStudyLesson records"}.`,
            )
        )
            return;

        try {
            const response = await fetch(
                "/classroom/dev/complete-all-lessons",
                {
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
                        mode: mode,
                    }),
                },
            );

            const data = await response.json();

            if (data.success) {
                alert(`${data.message}\n\nPolling will refresh automatically.`);
            } else {
                alert(`Failed: ${data.error || "Unknown error"}`);
            }
        } catch (error) {
            console.error("Error completing lessons:", error);
            alert("Error completing lessons: " + error);
        }
    };

    const handleResetProgress = async () => {
        if (
            !confirm(
                "Reset ALL lesson progress? This will clear all completion data (StudentLessons, SelfStudyLessons, Challenges, StudentUnits).\n\nThis cannot be undone!",
            )
        )
            return;

        try {
            const response = await fetch("/classroom/dev/reset-all-lessons", {
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
                // Reset exam room state to return to normal classroom
                setShowExamRoom(false);
                setHasExitedExamRoom(false);

                // Clear any localStorage/sessionStorage that might cache exam state
                try {
                    Object.keys(localStorage).forEach((key) => {
                        if (
                            key.includes("exam") ||
                            key.includes("student") ||
                            key.includes("classroom")
                        ) {
                            localStorage.removeItem(key);
                        }
                    });
                    Object.keys(sessionStorage).forEach((key) => {
                        if (
                            key.includes("exam") ||
                            key.includes("student") ||
                            key.includes("classroom")
                        ) {
                            sessionStorage.removeItem(key);
                        }
                    });
                } catch (e) {
                    console.warn("Could not clear storage:", e);
                }

                alert(
                    `✅ Reset Complete!\n\nDeleted:\n` +
                    `- ${data.counts.student_lessons} StudentLessons\n` +
                    `- ${data.counts.self_study_lessons} SelfStudyLessons\n` +
                    `- ${data.counts.challenges} Challenges\n` +
                    `- ${data.counts.validations} Validations\n` +
                    `- ${data.counts.student_units} StudentUnits\n` +
                    `- ${data.counts.exam_auths} ExamAuths\n\n` +
                    `Page will reload in 1 second...`,
                );

                // Force page reload to clear all cached polling data
                setTimeout(() => {
                    console.log("🔄 Forcing page reload after reset...");
                    // Use hard reload to bypass all caches
                    window.location.href =
                        window.location.href.split("?")[0] +
                        "?_reload=" +
                        Date.now();
                }, 1000);
            } else {
                alert(`❌ Failed: ${data.error || "Unknown error"}`);
            }
        } catch (error) {
            console.error("Error resetting lessons:", error);
            alert("❌ Error resetting lessons: " + error);
        }
    };

    const handleResetVideoQuota = async () => {
        if (!confirm("Reset video quota back to default (10 hours)?")) return;

        try {
            const response = await fetch("/classroom/dev/reset-video-quota", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                body: JSON.stringify({}),
            });

            const data = await response.json();

            if (!response.ok || !data?.success) {
                alert(`❌ Failed: ${data?.error || "Unknown error"}`);
                return;
            }

            // Refresh quota everywhere (Self-Study tab uses this query).
            queryClient.invalidateQueries({ queryKey: ["video-quota"] });

            alert("✅ Video quota reset.");
        } catch (error) {
            console.error("Error resetting video quota:", error);
            alert("❌ Error resetting video quota: " + error);
        }
    };

    // 🎓 EXAM: Handler for exam button click
    const handleExamClick = () => {
        setShowExamRoom(true);
        setHasExitedExamRoom(false); // Reset exit flag when entering exam room
    };

    const handleExamExit = () => {
        setShowExamRoom(false);
        setHasExitedExamRoom(true); // Mark that user explicitly exited
    };

    // � DEV TOGGLE UI (defined early to avoid hoisting issues)
    const DevModeToggle = showToggle ? (
        <>
            <div
                className="btn-group me-2"
                role="group"
                aria-label="View mode toggle"
            >
                <button
                    type="button"
                    onClick={() => setDevMode("auto")}
                    className="btn btn-sm"
                    title="Auto mode"
                    style={{
                        backgroundColor:
                            devMode === "auto"
                                ? "#3498db"
                                : "rgba(255,255,255,0.2)",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    Auto
                </button>
                <button
                    type="button"
                    onClick={() => setDevMode("online")}
                    className="btn btn-sm"
                    title="Force online view"
                    style={{
                        backgroundColor:
                            devMode === "online"
                                ? "#27ae60"
                                : "rgba(255,255,255,0.2)",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    Online
                </button>
                <button
                    type="button"
                    onClick={() => setDevMode("offline")}
                    className="btn btn-sm"
                    title="Force offline view"
                    style={{
                        backgroundColor:
                            devMode === "offline"
                                ? "#e67e22"
                                : "rgba(255,255,255,0.2)",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    Offline
                </button>
            </div>
            <div className="btn-group" role="group" aria-label="Lesson tools">
                <button
                    type="button"
                    onClick={() => handleCompleteAllLessons("online")}
                    className="btn btn-sm"
                    title="Complete all lessons (Online/Live Class mode)"
                    style={{
                        backgroundColor: "#27ae60",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    <i className="fas fa-check-double me-1"></i>
                    Complete Online
                </button>
                <button
                    type="button"
                    onClick={() => handleCompleteAllLessons("offline")}
                    className="btn btn-sm"
                    title="Complete all lessons (Offline/Self-Study mode)"
                    style={{
                        backgroundColor: "#3498db",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    <i className="fas fa-check-double me-1"></i>
                    Complete Offline
                </button>
                <button
                    type="button"
                    onClick={handleResetProgress}
                    className="btn btn-sm"
                    title="Reset all lesson progress"
                    style={{
                        backgroundColor: "#e74c3c",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    <i className="fas fa-redo me-1"></i>
                    Reset
                </button>

                <button
                    type="button"
                    onClick={handleResetVideoQuota}
                    className="btn btn-sm"
                    title="Reset video quota"
                    style={{
                        backgroundColor: "#8e44ad",
                        color: "#fff",
                        border: "1px solid rgba(255,255,255,0.3)",
                        padding: "6px 12px",
                        fontSize: "0.85rem",
                        fontWeight: "600",
                    }}
                >
                    <i className="fas fa-stopwatch me-1"></i>
                    Reset Quota
                </button>
            </div>
        </>
    ) : null;

    // �🎓 AUTO-DETECT: Check if all lessons are complete for auto-redirect to Exam Room
    const studentExam =
        studentContext?.studentExamsByCourseAuth?.[courseAuthId];

    // Check if exam is actually ready (not just if the key exists)
    // Only auto-route to ExamRoom if:
    // 1. Student genuinely completed all lessons (NOT affected by exam_admin_id bypass)
    // 2. OR student has an active exam attempt in progress
    // NOTE: is_ready intentionally NOT used here — it can be true via admin override
    // even when lessons are incomplete, which would wrongly suppress the offline classroom.
    const allLessonsComplete =
        studentExam?.all_lessons_completed || studentExam?.has_active_attempt;

    console.log("🎓 ExamRoom Auto-Detection:", {
        courseAuthId,
        studentExam,
        allLessonsComplete,
        devMode,
        showExamRoom,
        hasExitedExamRoom,
    });

    // Auto-redirect to Exam Room if all lessons complete (unless dev mode overrides or user explicitly exited)
    const shouldAutoShowExamRoom =
        devMode === "auto" &&
        allLessonsComplete &&
        !showExamRoom &&
        !hasExitedExamRoom;

    // Show Exam Room if manually triggered OR auto-detected
    if (showExamRoom || shouldAutoShowExamRoom) {
        return (
            <ExamRoom
                courseAuthId={courseAuthId}
                onBackToDashboard={handleExamExit}
                devModeToggle={DevModeToggle}
            />
        );
    }

    // When classroomContext is null there is no active classroom poll (no class scheduled today).
    // Fall through to the OFFLINE rendering below — do NOT show a spinner here.

    const { courseDate, instUnit, studentUnit, course } = classroomContext ?? {
        courseDate: null, instUnit: null, studentUnit: null, course: null,
    };

    // When the instructor ends the day, backend may still provide an InstUnit with completed_at/status.
    // Treat that as ENDED so the student does not remain in WAITING.
    const isEnded = !!(instUnit?.completed_at || instUnit?.status === "ended");

    // Student progress (validations) comes from the student poll, not the classroom poll.
    const validations = studentContext?.validationsByCourseAuth
        ? studentContext.validationsByCourseAuth[courseAuthId]
        : null;

    // ONBOARDING GATE: Check if student needs to complete onboarding
    // Show onboarding when:
    // 1. Class is live (courseDate + instUnit exist)
    // 2. AND (studentUnit doesn't exist YET OR onboarding is not complete)
    //
    // studentUnit is NULL when it's a new day and student hasn't joined yet.
    // Once they complete onboarding, a StudentUnit will be created.
    if (courseDate && instUnit && !isEnded) {
        // Get agreement status from student courses data (poll includes agreed_at)
        const courseData = studentContext?.courses?.find(
            (c: any) => c.id === courseAuthId,
        );

        // Determine if onboarding is needed
        // Check validations.onboarding_completed instead of studentUnit.onboarding_completed
        // Validations object manages all onboarding requirements (terms, rules, identity)
        const needsOnboarding = !validations?.onboarding_completed;

        console.log("🔍 Onboarding check:", {
            hasStudentUnit: !!studentUnit,
            studentUnitId: studentUnit?.id,
            hasValidations: !!validations,
            onboarding_completed: validations?.onboarding_completed,
            needsOnboarding: needsOnboarding,
        });

        if (needsOnboarding) {
            return (
                <OnboardingFlow
                    key={onboardingKey}
                    courseAuthId={courseAuthId}
                    courseDateId={courseDate.id}
                    studentUnitId={studentUnit?.id || 0} // 0 means "create new"
                    studentUnit={studentUnit || null}
                    student={student}
                    course={course}
                    courseAuth={courseData} // Pass course data which includes agreed_at
                    validations={validations || null}
                    onComplete={() => {
                        // Force classroom context to refresh by incrementing key
                        setOnboardingKey((prev) => prev + 1);
                        console.log(
                            "✅ Onboarding complete - polling will refresh automatically",
                        );
                    }}
                />
            );
        }
    }

    // 🎨 DEV MODE: Simplified view override for layout testing
    // When devMode is set, bypass normal logic and show the selected view
    let shouldShowOnline = false;
    let shouldShowOffline = false;

    if (devMode === "online") {
        shouldShowOnline = true;
    } else if (devMode === "offline") {
        shouldShowOffline = true;
    } else {
        // Auto mode - use actual classroom state
        shouldShowOnline = !!(courseDate && instUnit && !isEnded);
        shouldShowOffline = !courseDate || isEnded;
    }

    // DevModeToggle already defined above (moved to avoid hoisting issues)

    // ONLINE: Live class in session (instructor has started)
    if (shouldShowOnline) {
        return (
            <MainOnline
                classroom={classroomContext}
                student={student}
                validations={validations || null}
                onBackToDashboard={onBackToDashboard}
                onExamClick={handleExamClick}
                devModeToggle={DevModeToggle}
                courseAuthId={courseAuthId}
            />
        );
    }

    // OFFLINE: Self-study mode (no class today)
    if (shouldShowOffline) {
        return (
            <MainOffline
                courseAuthId={courseAuthId}
                student={student}
                onBackToDashboard={onBackToDashboard}
                onExamClick={handleExamClick}
                devModeToggle={DevModeToggle}
            />
        );
    }

    // WAITING: Class scheduled but instructor hasn't started yet.
    // If the scheduled start time is more than 2 hours in the past with no
    // instructor, the class never happened — fall through to OFFLINE.
    const courseStartTime = courseDate?.starts_at
        ? new Date(courseDate.starts_at)
        : null;
    const courseExpired =
        courseStartTime !== null &&
        Date.now() - courseStartTime.getTime() > 2 * 60 * 60 * 1000;

    if (courseDate && !instUnit && !courseExpired) {
        // Course name: classroom poll has course=null in WAITING state, so fall
        // back to the student poll's courses[] which always has enrollment data.
        const courseAuth = studentContext?.courses?.find(
            (c: any) => c.id === courseAuthId,
        );
        const courseName =
            courseAuth?.course_name ||
            courseAuth?.course?.title_long ||
            courseAuth?.course?.title ||
            course?.name ||
            "Class";

        const classDate = courseDate.starts_at
            ? new Date(courseDate.starts_at).toLocaleDateString()
            : courseDate.class_date
                ? new Date(courseDate.class_date).toLocaleDateString()
                : "Today";
        const classTime = courseDate.starts_at
            ? new Date(courseDate.starts_at).toLocaleTimeString()
            : courseDate.class_time || "Soon";

        return (
            <div
                style={{
                    backgroundColor: "#1a1f2e",
                    minHeight: "100vh",
                    display: "flex",
                    flexDirection: "column",
                    paddingTop: "60px",
                }}
            >
                {/* Title Bar */}
                <SchoolDashboardTitleBar
                    title={courseName !== "Class" ? courseName : "Classroom"}
                    subtitle={`Waiting for instructor | Student: ${student?.name || "N/A"}`}
                    icon={<i className="fas fa-clock"></i>}
                    onBackToDashboard={onBackToDashboard}
                    onExamClick={handleExamClick}
                    classroomStatus="WAITING"
                    devModeToggle={DevModeToggle}
                />
                {/* Waiting Room Content */}
                <div
                    className="container-fluid"
                    style={{
                        padding: "3rem 2rem",
                        maxWidth: "900px",
                        margin: "0 auto",
                    }}
                >
                    {/* Main Waiting Card */}
                    <div
                        className="card"
                        style={{
                            backgroundColor: "#2c3e50",
                            border: "2px solid #3498db",
                            borderRadius: "0.75rem",
                            padding: "3rem",
                            textAlign: "center",
                            boxShadow: "0 4px 6px rgba(0,0,0,0.3)",
                        }}
                    >
                        {/* Icon */}
                        <div style={{ marginBottom: "2rem" }}>
                            <i
                                className="fas fa-hourglass-half"
                                style={{
                                    fontSize: "4rem",
                                    color: "#3498db",
                                    animation: "pulse 2s infinite",
                                }}
                            ></i>
                        </div>

                        {/* Title */}
                        <h3
                            style={{
                                color: "white",
                                marginBottom: "1rem",
                                fontWeight: "600",
                            }}
                        >
                            Waiting for Class to Start
                        </h3>

                        {/* Course Info */}
                        <div style={{ marginBottom: "2rem" }}>
                            <p
                                style={{
                                    color: "#95a5a6",
                                    fontSize: "1rem",
                                    marginBottom: "0.5rem",
                                }}
                            >
                                Your class is scheduled:
                            </p>
                            <h4
                                style={{
                                    color: "#3498db",
                                    marginBottom: "0.5rem",
                                    fontWeight: "600",
                                }}
                            >
                                {courseName}
                            </h4>
                            <p style={{ color: "#ecf0f1", fontSize: "1.1rem" }}>
                                {classDate} at {classTime}
                            </p>
                        </div>

                        {/* Info Alert */}
                        <div
                            className="alert"
                            style={{
                                backgroundColor: "rgba(52, 152, 219, 0.15)",
                                border: "1px solid rgba(52, 152, 219, 0.4)",
                                borderRadius: "0.5rem",
                                padding: "1.25rem",
                                textAlign: "left",
                                marginBottom: "2rem",
                            }}
                        >
                            <div
                                style={{
                                    display: "flex",
                                    alignItems: "start",
                                    gap: "0.75rem",
                                }}
                            >
                                <i
                                    className="fas fa-info-circle"
                                    style={{
                                        color: "#3498db",
                                        fontSize: "1.25rem",
                                        marginTop: "0.125rem",
                                    }}
                                ></i>
                                <div>
                                    <h6
                                        style={{
                                            color: "#3498db",
                                            marginBottom: "0.5rem",
                                            fontWeight: "600",
                                        }}
                                    >
                                        Your instructor is preparing to begin
                                    </h6>
                                    <p
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.95rem",
                                            marginBottom: "0.5rem",
                                        }}
                                    >
                                        Your class is scheduled and ready. The
                                        instructor will start the session
                                        shortly.
                                    </p>
                                    <p
                                        style={{
                                            color: "#95a5a6",
                                            fontSize: "0.9rem",
                                            marginBottom: "0",
                                        }}
                                    >
                                        <i className="fas fa-sync-alt me-2"></i>
                                        This page will automatically update when
                                        your instructor begins the class.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Preparation Checklist */}
                        <div style={{ marginBottom: "2rem" }}>
                            <h6
                                style={{
                                    color: "#ecf0f1",
                                    marginBottom: "1rem",
                                    fontWeight: "600",
                                }}
                            >
                                <i
                                    className="fas fa-tasks me-2"
                                    style={{ color: "#3498db" }}
                                ></i>
                                While you wait, please:
                            </h6>
                            <div
                                style={{
                                    textAlign: "left",
                                    maxWidth: "500px",
                                    margin: "0 auto",
                                }}
                            >
                                <div
                                    style={{
                                        padding: "0.75rem",
                                        marginBottom: "0.5rem",
                                        backgroundColor:
                                            "rgba(255,255,255,0.05)",
                                        borderRadius: "0.375rem",
                                        display: "flex",
                                        alignItems: "center",
                                        gap: "0.75rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle"
                                        style={{
                                            color: "#2ecc71",
                                            fontSize: "1.25rem",
                                        }}
                                    ></i>
                                    <span
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.95rem",
                                        }}
                                    >
                                        Test your audio and video equipment
                                    </span>
                                </div>
                                <div
                                    style={{
                                        padding: "0.75rem",
                                        marginBottom: "0.5rem",
                                        backgroundColor:
                                            "rgba(255,255,255,0.05)",
                                        borderRadius: "0.375rem",
                                        display: "flex",
                                        alignItems: "center",
                                        gap: "0.75rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle"
                                        style={{
                                            color: "#2ecc71",
                                            fontSize: "1.25rem",
                                        }}
                                    ></i>
                                    <span
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.95rem",
                                        }}
                                    >
                                        Have your course materials ready
                                    </span>
                                </div>
                                <div
                                    style={{
                                        padding: "0.75rem",
                                        marginBottom: "0.5rem",
                                        backgroundColor:
                                            "rgba(255,255,255,0.05)",
                                        borderRadius: "0.375rem",
                                        display: "flex",
                                        alignItems: "center",
                                        gap: "0.75rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle"
                                        style={{
                                            color: "#2ecc71",
                                            fontSize: "1.25rem",
                                        }}
                                    ></i>
                                    <span
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.95rem",
                                        }}
                                    >
                                        Find a quiet environment for class
                                    </span>
                                </div>
                                <div
                                    style={{
                                        padding: "0.75rem",
                                        backgroundColor:
                                            "rgba(255,255,255,0.05)",
                                        borderRadius: "0.375rem",
                                        display: "flex",
                                        alignItems: "center",
                                        gap: "0.75rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle"
                                        style={{
                                            color: "#2ecc71",
                                            fontSize: "1.25rem",
                                        }}
                                    ></i>
                                    <span
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.95rem",
                                        }}
                                    >
                                        Stay on this page - it updates
                                        automatically
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Back Button */}
                        <button
                            className="btn"
                            onClick={onBackToDashboard}
                            style={{
                                backgroundColor: "#34495e",
                                color: "white",
                                border: "2px solid #3498db",
                                borderRadius: "0.5rem",
                                padding: "0.75rem 2rem",
                                fontSize: "1rem",
                                fontWeight: "600",
                                cursor: "pointer",
                                transition: "all 0.3s",
                            }}
                            onMouseEnter={(e) => {
                                e.currentTarget.style.backgroundColor =
                                    "#3498db";
                                e.currentTarget.style.transform =
                                    "translateY(-2px)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.backgroundColor =
                                    "#34495e";
                                e.currentTarget.style.transform =
                                    "translateY(0)";
                            }}
                        >
                            <i className="fas fa-arrow-left me-2"></i>
                            Back to Dashboard
                        </button>
                    </div>
                </div>

                {/* CSS Animation for pulse effect */}
                <style>{`
                    @keyframes pulse {
                        0%, 100% {
                            opacity: 1;
                            transform: scale(1);
                        }
                        50% {
                            opacity: 0.7;
                            transform: scale(1.05);
                        }
                    }
                `}</style>
            </div>
        );
    }

    // OFFLINE: No scheduled class, self-study mode
    return (
        <MainOffline
            courseAuthId={courseAuthId}
            student={student}
            onBackToDashboard={onBackToDashboard}
            onExamClick={handleExamClick}
            devModeToggle={DevModeToggle}
        />
    );
};

export default MainClassroom;
