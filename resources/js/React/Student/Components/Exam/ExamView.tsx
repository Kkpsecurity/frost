import React, { useEffect, useState } from "react";

interface ExamViewProps {
    examAuth: any;
    onSubmitExam: (answers: Record<number, number>) => void;
    onBackToDashboard: () => void;
    showDevTools?: boolean;
}

const ExamView: React.FC<ExamViewProps> = ({
    examAuth,
    onSubmitExam,
    onBackToDashboard,
    showDevTools,
}) => {
    const devEnabled = (() => {
        if (showDevTools) return true;
        try {
            if (new URLSearchParams(window.location.search).has("dev")) {
                return true;
            }
            return window.localStorage.getItem("frost_dev_tools") === "1";
        } catch {
            return false;
        }
    })();

    const [answers, setAnswers] = useState<Record<number, number>>(() => {
        const examAuthId = examAuth?.id;
        if (!examAuthId) return {};
        try {
            const raw = window.localStorage.getItem(
                `frost_exam_answers_${examAuthId}`,
            );
            return raw ? JSON.parse(raw) : {};
        } catch {
            return {};
        }
    });
    const [showSubmitConfirm, setShowSubmitConfirm] = useState(false);

    const totalQuestions = examAuth.questions?.length || 0;
    const answeredQuestions = Object.keys(answers).length;
    const allAnswered =
        totalQuestions > 0 && answeredQuestions >= totalQuestions;

    const handleAnswerChange = (questionId: number, answerValue: number) => {
        const examAuthId = examAuth?.id;
        setAnswers((prev) => {
            const next = {
                ...prev,
                [questionId]: answerValue,
            };
            if (examAuthId) {
                try {
                    window.localStorage.setItem(
                        `frost_exam_answers_${examAuthId}`,
                        JSON.stringify(next),
                    );
                } catch {
                    // ignore
                }
            }
            return next;
        });
    };

    useEffect(() => {
        const examAuthId = examAuth?.id;
        if (!examAuthId) {
            setAnswers({});
            return;
        }

        try {
            const raw = window.localStorage.getItem(
                `frost_exam_answers_${examAuthId}`,
            );
            setAnswers(raw ? JSON.parse(raw) : {});
        } catch {
            setAnswers({});
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [examAuth?.id]);

    const handleSubmit = () => {
        // Prevent premature submits
        if (!allAnswered) {
            alert(
                `Please answer all questions before submitting. (${answeredQuestions}/${totalQuestions})`,
            );
            return;
        }

        onSubmitExam(answers);
    };

    const handleDevAutofill = () => {
        const examAuthId = examAuth?.id;
        const questions = Array.isArray(examAuth?.questions)
            ? examAuth.questions
            : [];

        if (!examAuthId || questions.length === 0) {
            alert("No exam questions available to auto-fill.");
            return;
        }

        const next: Record<number, number> = {};
        const randomSeed = Math.random().toString(36).substring(7);

        console.log(
            `🎲 DEV Auto-fill: Generating random answers (seed: ${randomSeed})...`,
        );

        for (const question of questions) {
            const questionId = Number(question?.id);
            if (!questionId) continue;

            // Collect all available answers
            const availableAnswers: number[] = [];
            for (let answerNum = 1; answerNum <= 5; answerNum++) {
                const answerText = question[`answer_${answerNum}`];
                if (answerText) {
                    availableAnswers.push(answerNum);
                }
            }

            // Pick a random answer from available answers
            if (availableAnswers.length > 0) {
                const randomIndex = Math.floor(
                    Math.random() * availableAnswers.length,
                );
                next[questionId] = availableAnswers[randomIndex];
                console.log(
                    `  Q${questionId}: Selected answer ${availableAnswers[randomIndex]} from [${availableAnswers.join(", ")}]`,
                );
            }
        }

        console.log(
            `🎲 DEV Auto-fill complete: ${Object.keys(next).length} questions answered (ExamAuth ID: ${examAuthId})`,
        );

        setAnswers(next);
        try {
            window.localStorage.setItem(
                `frost_exam_answers_${examAuthId}`,
                JSON.stringify(next),
            );
            console.log(
                `💾 Saved answers to localStorage for exam ${examAuthId}`,
            );
        } catch {
            console.error("Failed to save answers to localStorage");
        }

        alert(
            `✅ Randomly filled ${Object.keys(next).length} answers! (Seed: ${randomSeed})\nCheck console for details.`,
        );
    };

    const getTimerColor = () => "#6c757d";

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
                                Timer disabled
                            </strong>
                        </div>
                        <div>
                            <span className="me-3">
                                Questions: {examAuth.questions?.length || 0}
                            </span>
                            <span>Answered: {Object.keys(answers).length}</span>
                        </div>
                    </div>

                    {devEnabled && (
                        <div className="mt-2 text-end">
                            <button
                                type="button"
                                className="btn btn-sm"
                                style={{
                                    backgroundColor: "#6f42c1",
                                    border: "none",
                                    color: "white",
                                }}
                                onClick={handleDevAutofill}
                            >
                                DEV: Auto Fill Answers
                            </button>
                        </div>
                    )}
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
                                {devEnabled && (
                                    <div className="mb-3">
                                        <button
                                            type="button"
                                            className="btn btn-sm me-2"
                                            style={{
                                                backgroundColor: "#6f42c1",
                                                border: "none",
                                                color: "white",
                                            }}
                                            onClick={handleDevAutofill}
                                        >
                                            DEV: Auto Fill Answers
                                        </button>
                                    </div>
                                )}

                                <button
                                    className="btn btn-primary btn-lg px-5 py-3"
                                    onClick={handleSubmit}
                                    disabled={!allAnswered}
                                    style={{
                                        fontSize: "1.25rem",
                                        borderRadius: "8px",
                                        opacity: allAnswered ? 1 : 0.6,
                                    }}
                                >
                                    <i className="fas fa-check me-2"></i>
                                    Submit Exam
                                </button>

                                {!allAnswered && (
                                    <div className="mt-3 text-muted">
                                        Answer all questions to enable submit. (
                                        {answeredQuestions}/{totalQuestions})
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ExamView;
