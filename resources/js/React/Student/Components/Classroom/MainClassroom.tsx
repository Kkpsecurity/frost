import React, { useState } from "react";
import { useClassroom } from "../../context/ClassroomContext";
import { useStudent } from "../../context/StudentContext";
import MainOffline from "./MainOffline";
import MainOnline from "./MainOnline";
import OnboardingFlow from "./OnboardingFlow";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";

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
const MainClassroom: React.FC<MainClassroomProps> = ({ courseAuthId, student, onBackToDashboard }) => {
    const classroomContext = useClassroom();
    const studentContext = useStudent();
    const [onboardingKey, setOnboardingKey] = useState(0); // Key to force refresh after onboarding

    // Loading classroom data
    if (!classroomContext) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: "400px" }}>
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading classroom...</span>
                    </div>
                    <p className="mt-3">Loading classroom...</p>
                </div>
            </div>
        );
    }

    const { courseDate, instUnit, studentUnit, course } = classroomContext;

    // Student progress (validations) comes from the student poll, not the classroom poll.
    const validations = studentContext?.validationsByCourseAuth
        ? studentContext.validationsByCourseAuth[courseAuthId]
        : null;

    // ONBOARDING GATE: Check if student needs to complete onboarding
    // Only check when courseDate + instUnit exist (live classroom mode)
    // Agreement is ONE-TIME per course_auth (not per daily session)
    if (courseDate && instUnit && studentUnit) {
        // Get agreement status from student courses data (poll includes agreed_at)
        const courseData = studentContext?.courses?.find((c: any) => c.id === courseAuthId);
        const hasAgreedToTerms = courseData?.agreed_at !== null && courseData?.agreed_at !== undefined;

        // Check if validation (ID + headshot) is complete
        // Headshot is an object like { wednesday: url } so check if today's value exists
        const getTodayKey = () => {
            try {
                return new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
            } catch {
                return 'monday';
            }
        };
        const todayKey = getTodayKey();
        const headshotUrl = validations?.headshot?.[todayKey];
        const hasIdCard = !!validations?.idcard;
        const hasHeadshot = !!headshotUrl;

        const needsOnboarding = !hasAgreedToTerms || !hasIdCard || !hasHeadshot;

        if (needsOnboarding) {
            return (
                <OnboardingFlow
                    key={onboardingKey}
                    courseAuthId={courseAuthId}
                    courseDateId={courseDate.id}
                    studentUnitId={studentUnit.id}
                    studentUnit={studentUnit}
                    student={student}
                    course={course}
                    courseAuth={courseData} // Pass course data which includes agreed_at
                    validations={validations || null}
                    onComplete={() => {
                        // Force classroom context to refresh by incrementing key
                        setOnboardingKey(prev => prev + 1);
                        // Trigger a manual reload or re-poll here if needed
                        window.location.reload();
                    }}
                />
            );
        }
    }

    // ONLINE: Live class in session (instructor has started)
    if (courseDate && instUnit) {
        return (
            <MainOnline
                classroom={classroomContext}
                student={student}
                validations={validations || null}
                onBackToDashboard={onBackToDashboard}
            />
        );
    }

    // WAITING: Class scheduled but instructor hasn't started yet
    if (courseDate && !instUnit) {
        const courseName = course?.name || 'Class';
        const classDate = courseDate.class_date ? new Date(courseDate.class_date).toLocaleDateString() : 'Today';
        const classTime = courseDate.class_time || 'Soon';

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
                    title="Classroom"
                    subtitle={`Waiting for instructor | Student: ${student?.name || "N/A"}`}
                    icon={<i className="fas fa-clock"></i>}
                    onBackToDashboard={onBackToDashboard}
                    classroomStatus="WAITING"
                />

                {/* Waiting Room Content */}
                <div className="container-fluid" style={{ padding: "3rem 2rem", maxWidth: "900px", margin: "0 auto" }}>
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
                        <h3 style={{ color: "white", marginBottom: "1rem", fontWeight: "600" }}>
                            Waiting for Class to Start
                        </h3>

                        {/* Course Info */}
                        <div style={{ marginBottom: "2rem" }}>
                            <p style={{ color: "#95a5a6", fontSize: "1rem", marginBottom: "0.5rem" }}>
                                Your class is scheduled:
                            </p>
                            <h4 style={{ color: "#3498db", marginBottom: "0.5rem", fontWeight: "600" }}>
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
                            <div style={{ display: "flex", alignItems: "start", gap: "0.75rem" }}>
                                <i className="fas fa-info-circle" style={{ color: "#3498db", fontSize: "1.25rem", marginTop: "0.125rem" }}></i>
                                <div>
                                    <h6 style={{ color: "#3498db", marginBottom: "0.5rem", fontWeight: "600" }}>
                                        Your instructor is preparing to begin
                                    </h6>
                                    <p style={{ color: "#ecf0f1", fontSize: "0.95rem", marginBottom: "0.5rem" }}>
                                        Your class is scheduled and ready. The instructor will start the session shortly.
                                    </p>
                                    <p style={{ color: "#95a5a6", fontSize: "0.9rem", marginBottom: "0" }}>
                                        <i className="fas fa-sync-alt me-2"></i>
                                        This page will automatically update when your instructor begins the class.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Preparation Checklist */}
                        <div style={{ marginBottom: "2rem" }}>
                            <h6 style={{ color: "#ecf0f1", marginBottom: "1rem", fontWeight: "600" }}>
                                <i className="fas fa-tasks me-2" style={{ color: "#3498db" }}></i>
                                While you wait, please:
                            </h6>
                            <div style={{ textAlign: "left", maxWidth: "500px", margin: "0 auto" }}>
                                <div style={{ padding: "0.75rem", marginBottom: "0.5rem", backgroundColor: "rgba(255,255,255,0.05)", borderRadius: "0.375rem", display: "flex", alignItems: "center", gap: "0.75rem" }}>
                                    <i className="fas fa-check-circle" style={{ color: "#2ecc71", fontSize: "1.25rem" }}></i>
                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>Test your audio and video equipment</span>
                                </div>
                                <div style={{ padding: "0.75rem", marginBottom: "0.5rem", backgroundColor: "rgba(255,255,255,0.05)", borderRadius: "0.375rem", display: "flex", alignItems: "center", gap: "0.75rem" }}>
                                    <i className="fas fa-check-circle" style={{ color: "#2ecc71", fontSize: "1.25rem" }}></i>
                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>Have your course materials ready</span>
                                </div>
                                <div style={{ padding: "0.75rem", marginBottom: "0.5rem", backgroundColor: "rgba(255,255,255,0.05)", borderRadius: "0.375rem", display: "flex", alignItems: "center", gap: "0.75rem" }}>
                                    <i className="fas fa-check-circle" style={{ color: "#2ecc71", fontSize: "1.25rem" }}></i>
                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>Find a quiet environment for class</span>
                                </div>
                                <div style={{ padding: "0.75rem", backgroundColor: "rgba(255,255,255,0.05)", borderRadius: "0.375rem", display: "flex", alignItems: "center", gap: "0.75rem" }}>
                                    <i className="fas fa-check-circle" style={{ color: "#2ecc71", fontSize: "1.25rem" }}></i>
                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>Stay on this page - it updates automatically</span>
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
                                e.currentTarget.style.backgroundColor = "#3498db";
                                e.currentTarget.style.transform = "translateY(-2px)";
                            }}
                            onMouseLeave={(e) => {
                                e.currentTarget.style.backgroundColor = "#34495e";
                                e.currentTarget.style.transform = "translateY(0)";
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
    return <MainOffline courseAuthId={courseAuthId} student={student} onBackToDashboard={onBackToDashboard} />;
};

export default MainClassroom;
