import React, { useState, useEffect } from "react";

interface ExamViewProps {
    examAuth: any;
    onSubmitExam: (answers: Record<number, number>) => void;
    onBackToDashboard: () => void;
}

const ExamView: React.FC<ExamViewProps> = ({
    examAuth,
    onSubmitExam,
    onBackToDashboard,
}) => {
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [timeRemaining, setTimeRemaining] = useState<number>(0);
    const [showSubmitConfirm, setShowSubmitConfirm] = useState(false);

    useEffect(() => {
        // Calculate time remaining
        if (examAuth.expires_at) {
            const expiresAt = new Date(examAuth.expires_at).getTime();
            const now = new Date().getTime();
            const remaining = Math.max(0, Math.floor((expiresAt - now) / 1000));
            setTimeRemaining(remaining);
        }
    }, [examAuth]);

    useEffect(() => {
        // Countdown timer
        if (timeRemaining <= 0) {
            // Time's up - auto submit
            handleSubmit();
            return;
        }

        const timer = setInterval(() => {
            setTimeRemaining((prev) => {
                if (prev <= 1) {
                    handleSubmit();
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(timer);
    }, [timeRemaining]);

    const formatTime = (seconds: number) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
    };

    const handleAnswerChange = (questionId: number, answerValue: number) => {
        setAnswers({
            ...answers,
            [questionId]: answerValue,
        });
    };

    const handleSubmit = () => {
        // Check if all questions are answered
        const totalQuestions = examAuth.questions?.length || 0;
        const answeredQuestions = Object.keys(answers).length;

        if (answeredQuestions < totalQuestions && timeRemaining > 0) {
            if (
                !confirm(
                    `You have only answered ${answeredQuestions} out of ${totalQuestions} questions. Submit anyway?`,
                )
            ) {
                return;
            }
        }

        onSubmitExam(answers);
    };

    const getTimerColor = () => {
        if (timeRemaining < 300) return "#dc3545"; // Red - less than 5 min
        if (timeRemaining < 600) return "#ffc107"; // Yellow - less than 10 min
        return "#28a745"; // Green
    };

    return (
        <div
            style={{
                backgroundColor: "#1a1d29",
                minHeight: "100vh",
                padding: "6rem 0 2rem",
            }}
        >
            {/* Timer Bar */}
            <div
                style={{
                    position: "fixed",
                    top: 0,
                    left: 0,
                    right: 0,
                    backgroundColor: getTimerColor(),
                    color: "white",
                    padding: "1rem",
                    textAlign: "center",
                    zIndex: 1000,
                    boxShadow: "0 2px 4px rgba(0,0,0,0.2)",
                }}
            >
                <div className="container">
                    <div className="d-flex justify-content-between align-items-center">
                        <h4 className="mb-0">
                            <i className="fas fa-file-alt me-2"></i>
                            Exam: {examAuth.course?.title || "Course Exam"}
                        </h4>
                        <div>
                            <i className="fas fa-clock me-2"></i>
                            <strong style={{ fontSize: "1.5rem" }}>
                                {formatTime(timeRemaining)}
                            </strong>
                        </div>
                        <div>
                            <span className="me-3">
                                Questions: {examAuth.questions?.length || 0}
                            </span>
                            <span>Answered: {Object.keys(answers).length}</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Questions */}
            <div className="container" style={{ marginTop: "80px" }}>
                <div className="row">
                    <div className="col-12">
                        {examAuth.questions?.map(
                            (question: any, index: number) => (
                                <div
                                    key={question.id}
                                    className="card mb-3 shadow-sm"
                                    style={{ borderRadius: "8px" }}
                                >
                                    <div className="card-header bg-white">
                                        <div className="d-flex align-items-start">
                                            <div
                                                style={{
                                                    width: "40px",
                                                    height: "40px",
                                                    borderRadius: "50%",
                                                    backgroundColor: answers[
                                                        question.id
                                                    ]
                                                        ? "#28a745"
                                                        : "#ccc",
                                                    color: "white",
                                                    display: "flex",
                                                    alignItems: "center",
                                                    justifyContent: "center",
                                                    fontWeight: "bold",
                                                    fontSize: "1.1rem",
                                                    flexShrink: 0,
                                                }}
                                            >
                                                {index + 1}
                                            </div>
                                            <div
                                                className="ms-3"
                                                style={{
                                                    fontSize: "1.2rem",
                                                    fontWeight: "600",
                                                    flex: 1,
                                                }}
                                            >
                                                {question.question}
                                            </div>
                                        </div>
                                    </div>
                                    <ul className="list-group list-group-flush">
                                        {[1, 2, 3, 4, 5].map((answerNum) => {
                                            const answerText =
                                                question[`answer_${answerNum}`];
                                            if (!answerText) return null;

                                            const answerId = `answer_${question.id}_${answerNum}`;
                                            const isSelected =
                                                answers[question.id] ===
                                                answerNum;

                                            return (
                                                <li
                                                    key={answerNum}
                                                    className="list-group-item"
                                                    style={{
                                                        cursor: "pointer",
                                                        backgroundColor:
                                                            isSelected
                                                                ? "#e7f3ff"
                                                                : "white",
                                                    }}
                                                    onClick={() =>
                                                        handleAnswerChange(
                                                            question.id,
                                                            answerNum,
                                                        )
                                                    }
                                                >
                                                    <div className="form-check">
                                                        <input
                                                            className="form-check-input"
                                                            type="radio"
                                                            name={`answer_${question.id}`}
                                                            id={answerId}
                                                            value={answerNum}
                                                            checked={isSelected}
                                                            onChange={() =>
                                                                handleAnswerChange(
                                                                    question.id,
                                                                    answerNum,
                                                                )
                                                            }
                                                            style={{
                                                                width: "20px",
                                                                height: "20px",
                                                            }}
                                                        />
                                                        <label
                                                            className="form-check-label ms-2"
                                                            htmlFor={answerId}
                                                            style={{
                                                                fontSize:
                                                                    "1.1rem",
                                                                cursor: "pointer",
                                                            }}
                                                        >
                                                            {answerText}
                                                        </label>
                                                    </div>
                                                </li>
                                            );
                                        })}
                                    </ul>
                                </div>
                            ),
                        )}

                        {/* Submit Button */}
                        <div
                            className="card shadow-lg sticky-bottom mb-3"
                            style={{
                                borderRadius: "8px",
                                marginTop: "2rem",
                            }}
                        >
                            <div className="card-body p-4 text-center">
                                <button
                                    className="btn btn-primary btn-lg px-5 py-3"
                                    onClick={handleSubmit}
                                    style={{
                                        fontSize: "1.25rem",
                                        borderRadius: "8px",
                                    }}
                                >
                                    <i className="fas fa-check me-2"></i>
                                    Submit Exam
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ExamView;
