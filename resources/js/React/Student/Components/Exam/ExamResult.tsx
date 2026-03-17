import React from "react";
import { t } from "@/i18n";

interface ExamResultProps {
    examAuth: any;
    onBackToDashboard: () => void;
}

const ExamResult: React.FC<ExamResultProps> = ({
    examAuth,
    onBackToDashboard,
}) => {
    const passed = examAuth.passed;
    const score = examAuth.score || 0;
    const totalQuestions = examAuth.questions?.length || 0;
    const numToPass = examAuth.exam?.num_to_pass || 0;

    const formatNextAttemptTime = (dateString: string) => {
        if (!dateString) return null;
        const date = new Date(dateString);
        const label = (() => {
            try {
                const parts = new Intl.DateTimeFormat("en-US", {
                    timeZone: "America/New_York",
                    timeZoneName: "short",
                }).formatToParts(date);
                return (
                    parts.find((p) => p.type === "timeZoneName")?.value ?? "ET"
                );
            } catch {
                return "ET";
            }
        })();
        return (
            date.toLocaleString("en-US", {
                timeZone: "America/New_York",
                month: "long",
                day: "numeric",
                year: "numeric",
                hour: "numeric",
                minute: "2-digit",
                hour12: true,
            }) +
            " " +
            label
        );
    };

    const getMissedQuestionsByLesson = () => {
        if (!examAuth.questions) return {};

        const missedByLesson: Record<string, any[]> = {};

        examAuth.questions.forEach((question: any) => {
            const userAnswer = examAuth.answers?.[question.id];
            if (userAnswer !== question.correct_answer) {
                const lessonTitle = question.lesson?.title || "Unknown Lesson";
                if (!missedByLesson[lessonTitle]) {
                    missedByLesson[lessonTitle] = [];
                }
                missedByLesson[lessonTitle].push(question);
            }
        });

        return missedByLesson;
    };

    const missedByLesson = getMissedQuestionsByLesson();

    return (
        <div
            style={{
                backgroundColor: "#1a1d29",
                minHeight: "100vh",
                padding: "6rem 0 3rem",
            }}
        >
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-10">
                        {/* Result Header */}
                        <div
                            className={`card shadow-lg mb-4 ${passed ? "border-success" : "border-danger"}`}
                            style={{ borderWidth: "3px", borderRadius: "12px" }}
                        >
                            <div
                                className={`card-header text-center text-white ${passed ? "bg-success" : "bg-danger"}`}
                                style={{ padding: "2rem" }}
                            >
                                <div style={{ fontSize: "4rem" }}>
                                    {passed ? (
                                        <i className="fas fa-check-circle"></i>
                                    ) : (
                                        <i className="fas fa-times-circle"></i>
                                    )}
                                </div>
                                <h2 className="mb-0 mt-3">
                                    {passed
                                        ? t("examResult.congratsPassed")
                                        : t("examResult.didNotPass")}
                                </h2>
                            </div>
                            <div className="card-body p-4">
                                <div className="row text-center">
                                    <div className="col-md-4">
                                        <div className="mb-3">
                                            <h5 className="text-muted">
                                                {t("examResult.yourScore")}
                                            </h5>
                                            <div
                                                style={{
                                                    fontSize: "3rem",
                                                    fontWeight: "bold",
                                                    color: passed
                                                        ? "#28a745"
                                                        : "#dc3545",
                                                }}
                                            >
                                                {score}
                                            </div>
                                            <div className="text-muted">
                                                {t("examResult.outOf", { total: totalQuestions })}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="mb-3">
                                            <h5 className="text-muted">
                                                {t("examResult.percentage")}
                                            </h5>
                                            <div
                                                style={{
                                                    fontSize: "3rem",
                                                    fontWeight: "bold",
                                                    color: passed
                                                        ? "#28a745"
                                                        : "#dc3545",
                                                }}
                                            >
                                                {totalQuestions > 0
                                                    ? Math.round(
                                                        (score /
                                                            totalQuestions) *
                                                        100,
                                                    )
                                                    : 0}
                                                %
                                            </div>
                                            <div className="text-muted">
                                                {t("examResult.requiredPct", { pct: Math.round((numToPass / totalQuestions) * 100) })}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                        <div className="mb-3">
                                            <h5 className="text-muted">
                                                {t("examResult.questionsCorrect")}
                                            </h5>
                                            <div
                                                style={{
                                                    fontSize: "3rem",
                                                    fontWeight: "bold",
                                                    color: passed
                                                        ? "#28a745"
                                                        : "#dc3545",
                                                }}
                                            >
                                                {score}/{totalQuestions}
                                            </div>
                                            <div className="text-muted">
                                                {t("examResult.requiredFraction", { pass: numToPass, total: totalQuestions })}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {!passed && examAuth.next_attempt_at && (
                                    <div className="alert alert-warning mt-3 text-center">
                                        <i className="fas fa-clock me-2"></i>
                                        <strong>
                                            {t("examResult.nextAttemptAvailable")}
                                        </strong>{" "}
                                        {formatNextAttemptTime(
                                            examAuth.next_attempt_at,
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Missed Questions Breakdown */}
                        {!passed && Object.keys(missedByLesson).length > 0 && (
                            <div className="card shadow mb-4">
                                <div className="card-header bg-white">
                                    <h4 className="mb-0">
                                        <i className="fas fa-exclamation-triangle text-warning me-2"></i>
                                        {t("examResult.questionsMissedByLesson")}
                                    </h4>
                                    <p className="text-muted mb-0 mt-2">
                                        {t("examResult.reviewLessons")}
                                    </p>
                                </div>
                                <div className="card-body">
                                    {Object.entries(missedByLesson).map(
                                        ([lessonTitle, questions]) => (
                                            <div
                                                key={lessonTitle}
                                                className="mb-3"
                                            >
                                                <h5 className="text-primary">
                                                    <i className="fas fa-book me-2"></i>
                                                    {lessonTitle}
                                                </h5>
                                                <div className="ms-4">
                                                    <div className="badge bg-danger mb-2">
                                                        {questions.length > 1
                                                            ? t("examResult.questionMissedPlural", { count: questions.length })
                                                            : t("examResult.questionMissedSingular", { count: questions.length })}
                                                    </div>
                                                    <ul className="list-unstyled">
                                                        {questions.map(
                                                            (question: any) => (
                                                                <li
                                                                    key={
                                                                        question.id
                                                                    }
                                                                    className="text-muted mb-1"
                                                                >
                                                                    <i className="fas fa-circle-notch fa-xs me-2"></i>
                                                                    {
                                                                        question.question
                                                                    }
                                                                </li>
                                                            ),
                                                        )}
                                                    </ul>
                                                </div>
                                            </div>
                                        ),
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Actions */}
                        <div className="text-center">
                            <button
                                className="btn btn-primary btn-lg px-5 py-3"
                                onClick={onBackToDashboard}
                                style={{
                                    fontSize: "1.25rem",
                                    borderRadius: "8px",
                                }}
                            >
                                <i className="fas fa-home me-2"></i>
                                {t("examResult.backToDashboard")}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ExamResult;
