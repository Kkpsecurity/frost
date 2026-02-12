import React from "react";

interface ExamAcknowledgementProps {
    studentExam: any;
    onBeginExam: () => void;
    onBackToDashboard: () => void;
}

const ExamAcknowledgement: React.FC<ExamAcknowledgementProps> = ({
    studentExam,
    onBeginExam,
    onBackToDashboard,
}) => {
    console.log("📋 ExamAcknowledgement received:", studentExam);

    // Check if studentExam has the flat fields directly (from polling)
    // or nested exam object (from examAuth state)
    const exam =
        studentExam?.exam ||
        (studentExam?.exam_id
            ? {
                  id: studentExam.exam_id,
                  num_questions: studentExam.num_questions,
                  num_to_pass: studentExam.num_to_pass,
                  policy_expire_seconds: studentExam.policy_expire_seconds,
              }
            : null);
    const errorMessage = studentExam?.error;

    if (errorMessage) {
        return (
            <div
                style={{
                    backgroundColor: "#1a1d29",
                    minHeight: "100vh",
                    padding: "6rem 2rem 2rem",
                }}
            >
                <div className="container">
                    <div
                        className="card shadow-lg"
                        style={{
                            backgroundColor: "#2c3e50",
                            border: "2px solid #e74c3c",
                            borderRadius: "12px",
                            maxWidth: "700px",
                            margin: "2rem auto",
                        }}
                    >
                        <div className="card-body p-5">
                            <h4
                                style={{
                                    color: "#e74c3c",
                                    marginBottom: "1rem",
                                }}
                            >
                                <i className="fas fa-exclamation-triangle me-2"></i>
                                Exam Configuration Error
                            </h4>
                            <p
                                style={{
                                    color: "#ecf0f1",
                                    marginBottom: "1.5rem",
                                }}
                            >
                                {errorMessage}
                            </p>
                            <div
                                className="alert"
                                style={{
                                    backgroundColor: "#34495e",
                                    border: "1px solid #e74c3c",
                                    color: "#bdc3c7",
                                    marginBottom: "1.5rem",
                                }}
                            >
                                <strong>Debug Information:</strong>
                                <div
                                    style={{
                                        marginTop: "0.5rem",
                                        fontFamily: "monospace",
                                        fontSize: "0.9rem",
                                    }}
                                >
                                    <div>
                                        studentExam available:{" "}
                                        {studentExam ? "Yes" : "No"}
                                    </div>
                                    <div>
                                        exam object: {exam ? "Yes" : "No"}
                                    </div>
                                    {studentExam && (
                                        <div style={{ marginTop: "0.5rem" }}>
                                            Raw data:{" "}
                                            {JSON.stringify(
                                                studentExam,
                                                null,
                                                2,
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                            <button
                                className="btn btn-lg mt-3"
                                onClick={onBackToDashboard}
                                style={{
                                    backgroundColor: "#34495e",
                                    border: "1px solid #3498db",
                                    color: "#ecf0f1",
                                }}
                            >
                                <i className="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    if (!exam) {
        console.error("❌ No exam data available:", { studentExam });
        return (
            <div
                style={{
                    backgroundColor: "#1a1d29",
                    minHeight: "100vh",
                    padding: "6rem 2rem 2rem",
                }}
            >
                <div className="container">
                    <div
                        className="card shadow-lg"
                        style={{
                            backgroundColor: "#2c3e50",
                            border: "2px solid #e74c3c",
                            borderRadius: "12px",
                            maxWidth: "600px",
                            margin: "2rem auto",
                        }}
                    >
                        <div className="card-body p-5">
                            <h4
                                style={{
                                    color: "#e74c3c",
                                    marginBottom: "1rem",
                                }}
                            >
                                <i className="fas fa-exclamation-triangle me-2"></i>
                                Exam Configuration Missing
                            </h4>
                            <p style={{ color: "#ecf0f1" }}>
                                The exam configuration could not be loaded. The
                                exam object is missing.
                            </p>
                            <button
                                className="btn btn-lg mt-3"
                                onClick={onBackToDashboard}
                                style={{
                                    backgroundColor: "#34495e",
                                    border: "1px solid #3498db",
                                    color: "#ecf0f1",
                                }}
                            >
                                <i className="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    const formatTime = (seconds: number) => {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        if (hours > 0) {
            return `${hours} hour${hours > 1 ? "s" : ""}${minutes > 0 ? ` ${minutes} minutes` : ""}`;
        }
        return `${minutes} minutes`;
    };

    return (
        <div
            style={{
                backgroundColor: "#1a1d29",
                minHeight: "100vh",
                padding: "6rem 2rem 2rem",
            }}
        >
            <div className="container">
                <div
                    className="card shadow-lg"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "2px solid #3498db",
                        maxWidth: "800px",
                        margin: "2rem auto",
                        borderRadius: "10px",
                    }}
                >
                    <div
                        className="card-body p-5"
                        style={{ backgroundColor: "#2c3e50" }}
                    >
                        <h2
                            className="fw-bold mb-3"
                            style={{ color: "#ecf0f1" }}
                        >
                            Beginning the Exam
                        </h2>
                        <h4
                            className="lead fw-bold mb-4"
                            style={{ color: "#bdc3c7" }}
                        >
                            When you click Begin Exam below, you will begin your
                            exam.
                        </h4>

                        <div
                            className="alert"
                            role="alert"
                            style={{
                                backgroundColor: "#e74c3c",
                                color: "white",
                                border: "1px solid #c0392b",
                            }}
                        >
                            <i className="fas fa-clock me-2"></i>
                            You will have{" "}
                            <strong>
                                {formatTime(exam.policy_expire_seconds || 7200)}
                            </strong>{" "}
                            to complete your exam. If you do not submit your
                            answers in that time, you will automatically fail
                            the exam.
                        </div>

                        <ul className="list-group mb-4">
                            <li
                                className="list-group-item d-flex justify-content-between"
                                style={{
                                    backgroundColor: "#34495e",
                                    border: "1px solid #3498db",
                                    color: "#ecf0f1",
                                }}
                            >
                                <span>Total Questions:</span>
                                <strong style={{ color: "#3498db" }}>
                                    {exam.num_questions}
                                </strong>
                            </li>
                            <li
                                className="list-group-item d-flex justify-content-between"
                                style={{
                                    backgroundColor: "#34495e",
                                    border: "1px solid #3498db",
                                    color: "#ecf0f1",
                                }}
                            >
                                <span>Required to Pass:</span>
                                <strong style={{ color: "#3498db" }}>
                                    {exam.num_to_pass}
                                </strong>
                            </li>
                        </ul>

                        <div className="d-flex justify-content-center gap-3">
                            <button
                                type="button"
                                className="btn btn-lg px-5 py-3"
                                onClick={onBeginExam}
                                style={{
                                    fontSize: "1.25rem",
                                    borderRadius: "20px",
                                    backgroundColor: "#27ae60",
                                    border: "none",
                                    color: "white",
                                }}
                            >
                                <i className="fas fa-play me-2"></i>
                                Begin Exam
                            </button>
                            <button
                                type="button"
                                className="btn btn-lg px-4 py-3"
                                onClick={onBackToDashboard}
                                style={{
                                    borderRadius: "20px",
                                    backgroundColor: "#34495e",
                                    border: "1px solid #3498db",
                                    color: "#ecf0f1",
                                }}
                            >
                                <i className="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ExamAcknowledgement;
