import React, { useState } from "react";

interface ExamReviewProps {
    attempt: any;
    attemptNumber: number;
    exam: any;
}

/**
 * ExamReview Component - Display detailed review of a completed exam attempt
 * Shows questions, student answers, correct answers, and highlights mistakes
 */
const ExamReview: React.FC<ExamReviewProps> = ({
    attempt,
    attemptNumber,
    exam,
}) => {
    const [expanded, setExpanded] = useState(false);

    if (!attempt) return null;

    const questions = attempt.questions || [];
    const studentAnswers = attempt.answers || {};
    const incorrectQuestionIds = attempt.incorrect || [];

    // Parse score (e.g., "45 / 50")
    let score = 0;
    let total = 0;
    let percentage = 0;

    if (attempt.score) {
        const parts = attempt.score.split(" / ");
        if (parts.length === 2) {
            score = parseInt(parts[0]);
            total = parseInt(parts[1]);
            percentage = total > 0 ? Math.round((score / total) * 100) : 0;
        }
    }

    const passed = attempt.is_passed;
    const completedDate = new Date(attempt.completed_at).toLocaleString();

    return (
        <div
            className="card shadow-sm mb-3"
            style={{
                backgroundColor: "#252d3d",
                border: `2px solid ${passed ? "#27ae60" : "#e74c3c"}`,
            }}
        >
            {/* Attempt Header */}
            <div
                className="card-header d-flex justify-content-between align-items-center"
                style={{
                    backgroundColor: passed
                        ? "rgba(39, 174, 96, 0.1)"
                        : "rgba(231, 76, 60, 0.1)",
                    borderBottom: `1px solid ${passed ? "#27ae60" : "#e74c3c"}`,
                    cursor: "pointer",
                }}
                onClick={() => setExpanded(!expanded)}
            >
                <div className="d-flex align-items-center">
                    <div
                        className="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style={{
                            width: "50px",
                            height: "50px",
                            backgroundColor: passed ? "#27ae60" : "#e74c3c",
                        }}
                    >
                        <i
                            className={`fas ${passed ? "fa-check" : "fa-times"} text-white fa-lg`}
                        ></i>
                    </div>
                    <div>
                        <h5 className="mb-0" style={{ color: "#fff" }}>
                            Attempt {attemptNumber}:
                            {passed ? " Passed ✓" : " Failed ✗"}
                        </h5>
                        <small style={{ color: "#b8c5d6" }}>
                            {completedDate}
                        </small>
                    </div>
                </div>
                <div className="d-flex align-items-center">
                    <div className="text-end me-3">
                        <div
                            style={{
                                fontSize: "1.5rem",
                                fontWeight: "bold",
                                color: passed ? "#2ecc71" : "#e74c3c",
                            }}
                        >
                            {percentage}%
                        </div>
                        <div style={{ fontSize: "0.9rem", color: "#b8c5d6" }}>
                            {score} / {total}
                        </div>
                    </div>
                    <i
                        className={`fas fa-chevron-${expanded ? "up" : "down"}`}
                        style={{ color: "#b8c5d6" }}
                    ></i>
                </div>
            </div>

            {/* Expanded Review Content */}
            {expanded && (
                <div className="card-body p-4">
                    {/* Summary Stats */}
                    <div className="row mb-4">
                        <div className="col-md-3 text-center">
                            <div
                                className="p-3 rounded"
                                style={{
                                    backgroundColor: "rgba(46, 204, 113, 0.1)",
                                    border: "1px solid #2ecc71",
                                }}
                            >
                                <h3
                                    style={{
                                        color: "#2ecc71",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    {score}
                                </h3>
                                <small style={{ color: "#b8c5d6" }}>
                                    Correct
                                </small>
                            </div>
                        </div>
                        <div className="col-md-3 text-center">
                            <div
                                className="p-3 rounded"
                                style={{
                                    backgroundColor: "rgba(231, 76, 60, 0.1)",
                                    border: "1px solid #e74c3c",
                                }}
                            >
                                <h3
                                    style={{
                                        color: "#e74c3c",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    {total - score}
                                </h3>
                                <small style={{ color: "#b8c5d6" }}>
                                    Incorrect
                                </small>
                            </div>
                        </div>
                        <div className="col-md-3 text-center">
                            <div
                                className="p-3 rounded"
                                style={{
                                    backgroundColor: "rgba(52, 152, 219, 0.1)",
                                    border: "1px solid #3498db",
                                }}
                            >
                                <h3
                                    style={{
                                        color: "#3498db",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    {exam?.num_to_pass || "N/A"}
                                </h3>
                                <small style={{ color: "#b8c5d6" }}>
                                    Required to Pass
                                </small>
                            </div>
                        </div>
                        <div className="col-md-3 text-center">
                            <div
                                className="p-3 rounded"
                                style={{
                                    backgroundColor: passed
                                        ? "rgba(39, 174, 96, 0.1)"
                                        : "rgba(231, 76, 60, 0.1)",
                                    border: `1px solid ${passed ? "#27ae60" : "#e74c3c"}`,
                                }}
                            >
                                <h3
                                    style={{
                                        color: passed ? "#2ecc71" : "#e74c3c",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    {passed ? "PASS" : "FAIL"}
                                </h3>
                                <small style={{ color: "#b8c5d6" }}>
                                    Result
                                </small>
                            </div>
                        </div>
                    </div>

                    {/* Question Review */}
                    <h5 style={{ color: "#fff", marginBottom: "1.5rem" }}>
                        <i className="fas fa-clipboard-list me-2"></i>
                        Question Review
                    </h5>

                    {questions.length === 0 ? (
                        <div
                            className="alert"
                            style={{
                                backgroundColor: "#34495e",
                                border: "1px solid #3a4456",
                                color: "#b8c5d6",
                            }}
                        >
                            <i className="fas fa-info-circle me-2"></i>
                            No question details available for this attempt.
                        </div>
                    ) : (
                        <div
                            className="accordion"
                            id={`examReview${attempt.id}`}
                        >
                            {questions.map((question: any, idx: number) => {
                                const studentAnswerNum =
                                    studentAnswers[question.id];
                                const isIncorrect =
                                    incorrectQuestionIds.includes(question.id);
                                const isCorrect = !isIncorrect;

                                // Get answer text
                                const studentAnswerText = studentAnswerNum
                                    ? question[`answer_${studentAnswerNum}`]
                                    : "Not answered";
                                const correctAnswerText = question.correct
                                    ? question[`answer_${question.correct}`]
                                    : "Unknown";

                                return (
                                    <div
                                        key={question.id}
                                        className="card mb-2"
                                        style={{
                                            backgroundColor: "#1a1f2e",
                                            border: `1px solid ${isCorrect ? "#2ecc71" : "#e74c3c"}`,
                                        }}
                                    >
                                        <div
                                            className="card-header d-flex justify-content-between align-items-center"
                                            style={{
                                                backgroundColor: isCorrect
                                                    ? "rgba(46, 204, 113, 0.05)"
                                                    : "rgba(231, 76, 60, 0.05)",
                                                border: "none",
                                            }}
                                            data-bs-toggle="collapse"
                                            data-bs-target={`#question${attempt.id}_${question.id}`}
                                            role="button"
                                        >
                                            <div
                                                className="d-flex align-items-center"
                                                style={{ flex: 1 }}
                                            >
                                                <span
                                                    className="badge me-3"
                                                    style={{
                                                        backgroundColor:
                                                            isCorrect
                                                                ? "#2ecc71"
                                                                : "#e74c3c",
                                                        minWidth: "30px",
                                                    }}
                                                >
                                                    {idx + 1}
                                                </span>
                                                <span
                                                    style={{
                                                        color: "#fff",
                                                        flex: 1,
                                                    }}
                                                >
                                                    {question.question}
                                                </span>
                                                <i
                                                    className={`fas ${isCorrect ? "fa-check-circle" : "fa-times-circle"} ms-3`}
                                                    style={{
                                                        color: isCorrect
                                                            ? "#2ecc71"
                                                            : "#e74c3c",
                                                        fontSize: "1.2rem",
                                                    }}
                                                ></i>
                                            </div>
                                        </div>
                                        <div
                                            id={`question${attempt.id}_${question.id}`}
                                            className="collapse"
                                            data-bs-parent={`#examReview${attempt.id}`}
                                        >
                                            <div className="card-body">
                                                {/* Your Answer */}
                                                <div className="mb-3">
                                                    <strong
                                                        style={{
                                                            color: isCorrect
                                                                ? "#2ecc71"
                                                                : "#e74c3c",
                                                        }}
                                                    >
                                                        <i
                                                            className={`fas ${isCorrect ? "fa-check" : "fa-times"} me-2`}
                                                        ></i>
                                                        Your Answer:
                                                    </strong>
                                                    <div
                                                        className="mt-2 p-3 rounded"
                                                        style={{
                                                            backgroundColor:
                                                                isCorrect
                                                                    ? "rgba(46, 204, 113, 0.1)"
                                                                    : "rgba(231, 76, 60, 0.1)",
                                                            border: `1px solid ${isCorrect ? "#2ecc71" : "#e74c3c"}`,
                                                            color: "#fff",
                                                        }}
                                                    >
                                                        {studentAnswerText}
                                                    </div>
                                                </div>

                                                {/* Correct Answer (only show if wrong) */}
                                                {!isCorrect && (
                                                    <div>
                                                        <strong
                                                            style={{
                                                                color: "#2ecc71",
                                                            }}
                                                        >
                                                            <i className="fas fa-check-circle me-2"></i>
                                                            Correct Answer:
                                                        </strong>
                                                        <div
                                                            className="mt-2 p-3 rounded"
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(46, 204, 113, 0.1)",
                                                                border: "1px solid #2ecc71",
                                                                color: "#fff",
                                                            }}
                                                        >
                                                            {correctAnswerText}
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

export default ExamReview;
