import React, { useState, useEffect } from "react";
import { useStudent } from "../../context/StudentContext";
import ExamAcknowledgement from "./ExamAcknowledgement";
import ExamView from "./ExamView";
import ExamResult from "./ExamResult";

interface ExamRoomProps {
    courseAuthId: number;
    onBackToDashboard: () => void;
}

type ExamStage = "acknowledgement" | "taking" | "result";

const EXAM_SESSION_KEY = "exam_session";

const ExamRoom: React.FC<ExamRoomProps> = ({
    courseAuthId,
    onBackToDashboard,
}) => {
    const studentContext = useStudent();
    const [stage, setStage] = useState<ExamStage>("acknowledgement");
    const [examAuth, setExamAuth] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    // Get exam data from student context
    const studentExam =
        studentContext?.studentExamsByCourseAuth?.[courseAuthId];

    useEffect(() => {
        // Check for existing exam session
        const savedSession = sessionStorage.getItem(EXAM_SESSION_KEY);
        if (savedSession) {
            try {
                const session = JSON.parse(savedSession);
                if (session.courseAuthId === courseAuthId && session.examAuth) {
                    console.log("🔄 Restoring exam session:", session);
                    setExamAuth(session.examAuth);
                    setStage(session.stage || "taking");
                    setLoading(false);
                    return;
                }
            } catch (e) {
                console.error("Failed to restore exam session:", e);
                sessionStorage.removeItem(EXAM_SESSION_KEY);
            }
        }

        console.log("🎓 ExamRoom: Loading exam data", {
            courseAuthId,
            studentExam,
            has_active_attempt: studentExam?.has_active_attempt,
            is_ready: studentExam?.is_ready,
        });

        // Check if there's an active exam attempt
        if (studentExam?.has_active_attempt && studentExam?.exam_auth_id) {
            console.log("📝 Loading active exam:", studentExam.exam_auth_id);
            loadExamAuth(studentExam.exam_auth_id);
        } else if (studentExam?.is_ready) {
            console.log("✅ Exam ready - loading exam config");
            // Load exam configuration before showing acknowledgement
            loadExamConfig();
        } else {
            console.log("❌ No exam available");
            alert("No exam is currently available for this course.");
            onBackToDashboard();
        }
    }, [courseAuthId]);

    const saveSession = (newStage: ExamStage, newExamAuth: any) => {
        sessionStorage.setItem(
            EXAM_SESSION_KEY,
            JSON.stringify({
                courseAuthId,
                stage: newStage,
                examAuth: newExamAuth,
                timestamp: Date.now(),
            }),
        );
    };

    const clearSession = () => {
        sessionStorage.removeItem(EXAM_SESSION_KEY);
    };

    const loadExamConfig = async () => {
        try {
            console.log("🔍 Fetching exam config for course:", courseAuthId);
            console.log("📊 StudentExam data:", studentExam);

            const missingFields = [];

            // Check studentExam availability
            if (!studentExam) {
                missingFields.push("studentExam (entire object is null/undefined)");
            }

            // Fetch exam configuration from the course
            const courseData = studentContext?.courses?.find(
                (c: any) => c.id === courseAuthId,
            );

            console.log("🎓 Course data:", courseData);

            if (!courseData) {
                missingFields.push("courseData (course not found in student context)");
            }

            // Check required exam fields
            if (!studentExam?.exam_id) {
                missingFields.push("exam_id");
            }
            if (!studentExam?.num_questions) {
                missingFields.push("num_questions");
            }
            if (!studentExam?.num_to_pass) {
                missingFields.push("num_to_pass");
            }
            if (!studentExam?.policy_expire_seconds) {
                missingFields.push("policy_expire_seconds");
            }

            if (missingFields.length > 0) {
                const errorMsg = `Missing exam configuration fields: ${missingFields.join(", ")}`;
                console.error("❌ " + errorMsg);
                console.log("Full studentExam object:", JSON.stringify(studentExam, null, 2));
                console.log("Full studentContext:", studentContext);
                throw new Error(errorMsg);
            }

            // Use data from studentExam context (polling data)
            const examData = {
                exam: {
                    id: studentExam.exam_id,
                    num_questions: studentExam.num_questions,
                    num_to_pass: studentExam.num_to_pass,
                    policy_expire_seconds: studentExam.policy_expire_seconds,
                },
                course: courseData,
            };

            console.log("📚 Constructed exam data:", examData);

            setExamAuth(examData);
            setStage("acknowledgement");
            setLoading(false);
        } catch (error) {
            console.error("❌ Failed to load exam config:", error);
            const errorMessage = error.message || "Unknown error";
            setExamAuth({ error: errorMessage });
            setStage("acknowledgement");
            setLoading(false);
        }
    };

    const loadExamAuth = async (examAuthId: number) => {
        try {
            const response = await fetch(`/api/exam/auth/${examAuthId}`);
            const data = await response.json();

            if (data.success) {
                setExamAuth(data.exam_auth);

                // Determine stage based on exam state
                const newStage = data.exam_auth.completed_at
                    ? "result"
                    : "taking";
                setStage(newStage);
                saveSession(newStage, data.exam_auth);
            } else {
                alert("Failed to load exam: " + data.error);
                onBackToDashboard();
            }
            setLoading(false);
        } catch (error) {
            console.error("Failed to load exam:", error);
            alert("Error loading exam");
            onBackToDashboard();
            setLoading(false);
        }
    };

    const handleBeginExam = async () => {
        try {
            setLoading(true);
            const response = await fetch("/api/exam/begin", {
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
                setExamAuth(data.exam_auth);
                setStage("taking");
                saveSession("taking", data.exam_auth);
            } else {
                alert("Failed to begin exam: " + data.error);
            }
            setLoading(false);
        } catch (error) {
            console.error("Failed to begin exam:", error);
            alert("Error starting exam");
            setLoading(false);
        }
    };

    const handleSubmitExam = async (answers: Record<number, number>) => {
        try {
            setLoading(true);
            const response = await fetch(`/api/exam/submit/${examAuth.id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({
                    answers: answers,
                }),
            });

            const data = await response.json();

            if (data.success) {
                setExamAuth(data.exam_auth);
                setStage("result");
                clearSession(); // Clear session after completion
            } else {
                alert("Failed to submit exam: " + data.error);
            }
            setLoading(false);
        } catch (error) {
            console.error("Failed to submit exam:", error);
            alert("Error submitting exam");
            setLoading(false);
        }
    };

    const handleBackToDashboard = () => {
        clearSession();
        onBackToDashboard();
    };

    if (loading) {
        return (
            <div
                className="d-flex justify-content-center align-items-center"
                style={{ minHeight: "400px" }}
            >
                <div className="text-center">
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading exam...</span>
                    </div>
                    <p className="mt-3">Loading exam...</p>
                </div>
            </div>
        );
    }

    if (stage === "acknowledgement") {
        return (
            <ExamAcknowledgement
                studentExam={studentExam}
                onBeginExam={handleBeginExam}
                onBackToDashboard={handleBackToDashboard}
            />
        );
    }

    if (stage === "taking" && examAuth) {
        return (
            <ExamView
                examAuth={examAuth}
                onSubmitExam={handleSubmitExam}
                onBackToDashboard={handleBackToDashboard}
            />
        );
    }

    if (stage === "result" && examAuth) {
        return (
            <ExamResult
                examAuth={examAuth}
                onBackToDashboard={handleBackToDashboard}
            />
        );
    }

    return null;
};

export default ExamRoom;
