import React, { useState } from "react";
import axios from "axios";

// ─── Types ────────────────────────────────────────────────────────────────────

interface ExamAttempt {
    id: number;
    created_at: string;
    completed_at: string | null;
    expires_at: string | null;
    next_attempt_at: string | null;
    score: string | null;
    score_percent: number | null;
    is_passed: boolean;
    is_expired: boolean;
    is_completed: boolean;
    is_in_progress: boolean;
    can_review: boolean;
    has_answers: boolean;
}

interface ExamResultsData {
    status: string;
    exam_ready: boolean;
    readiness_reason: { reason: string; next_attempt_at?: string } | null;
    exam_passed: boolean;
    exam_admin_override: boolean;
    max_attempts: number;
    attempts_used: number;
    attempts_remaining: number;
    next_attempt_at: string | null;
    attempts: ExamAttempt[];
}

interface ReviewQuestion {
    id: number;
    question: string;
    answer_options: Record<number, string>;
    correct_answer: number;
    student_answer: number | null;
    is_correct: boolean;
}

interface ExamReview {
    exam_auth_id: number;
    score: string;
    score_percent: number;
    is_passed: boolean;
    completed_at: string;
    total_questions: number;
    questions: ReviewQuestion[];
}

interface ExamResultsProps {
    examResults: ExamResultsData | null;
    studentName: string;
    onRefresh: () => void;
}

// ─── Status Badge ─────────────────────────────────────────────────────────────

const StatusBadge: React.FC<{ status: string }> = ({ status }) => {
    const map: Record<string, { label: string; cls: string }> = {
        passed: { label: "✓ Passed", cls: "badge-success" },
        ready: { label: "● Ready to Take", cls: "badge-primary" },
        cooldown: { label: "⏳ Cooldown – Waiting", cls: "badge-warning" },
        lessons_incomplete: {
            label: "✗ Lessons Incomplete",
            cls: "badge-secondary",
        },
        no_attempts: { label: "✗ No Attempts Left", cls: "badge-danger" },
        not_ready: { label: "✗ Not Ready", cls: "badge-secondary" },
    };
    const { label, cls } = map[status] ?? {
        label: status,
        cls: "badge-secondary",
    };
    return (
        <span
            className={`badge ${cls}`}
            style={{ fontSize: "0.85rem", padding: "6px 10px" }}
        >
            {label}
        </span>
    );
};

// ─── Review Modal ─────────────────────────────────────────────────────────────

const ReviewModal: React.FC<{
    review: ExamReview | null;
    loading: boolean;
    onClose: () => void;
}> = ({ review, loading, onClose }) => (
    <div
        className="modal fade show d-block"
        style={{ backgroundColor: "rgba(0,0,0,0.5)" }}
        onClick={onClose}
    >
        <div
            className="modal-dialog modal-xl modal-dialog-scrollable"
            onClick={(e) => e.stopPropagation()}
        >
            <div className="modal-content">
                <div className="modal-header">
                    <h5 className="modal-title">
                        <i className="fas fa-search mr-2"></i>
                        Exam Review
                        {review && (
                            <span className="ml-3">
                                <span
                                    className={`badge ${review.is_passed ? "badge-success" : "badge-danger"} mr-2`}
                                >
                                    {review.is_passed ? "Passed" : "Failed"}
                                </span>
                                <span className="badge badge-info">
                                    {review.score} ({review.score_percent}%)
                                </span>
                            </span>
                        )}
                    </h5>
                    <button type="button" className="close" onClick={onClose}>
                        <span>&times;</span>
                    </button>
                </div>
                <div className="modal-body">
                    {loading && (
                        <div className="text-center py-5">
                            <i className="fas fa-spinner fa-spin fa-2x"></i>
                            <p className="mt-2">Loading exam review…</p>
                        </div>
                    )}
                    {!loading && !review && (
                        <div className="alert alert-danger">
                            Failed to load exam review.
                        </div>
                    )}
                    {!loading && review && (
                        <>
                            <div className="alert alert-info mb-3">
                                <strong>
                                    {review.total_questions} Questions
                                </strong>{" "}
                                — Completed {review.completed_at}
                            </div>
                            {review.questions.map((q, idx) => (
                                <div
                                    key={q.id}
                                    className="card mb-3"
                                    style={{
                                        borderLeft: `4px solid ${q.is_correct ? "#28a745" : "#dc3545"}`,
                                    }}
                                >
                                    <div className="card-body">
                                        <div className="d-flex justify-content-between align-items-start">
                                            <h6 className="mb-2">
                                                <span className="text-muted mr-2">
                                                    Q{idx + 1}.
                                                </span>
                                                {q.question}
                                            </h6>
                                            <span
                                                className={`badge ml-3 flex-shrink-0 ${
                                                    q.is_correct
                                                        ? "badge-success"
                                                        : "badge-danger"
                                                }`}
                                            >
                                                {q.is_correct
                                                    ? "Correct"
                                                    : "Incorrect"}
                                            </span>
                                        </div>
                                        <div className="mt-2">
                                            {Object.entries(
                                                q.answer_options,
                                            ).map(([num, text]) => {
                                                const n = parseInt(num);
                                                const isCorrect =
                                                    n === q.correct_answer;
                                                const isStudentChoice =
                                                    n === q.student_answer;
                                                let bg = "transparent";
                                                if (isCorrect)
                                                    bg = "rgba(40,167,69,0.15)";
                                                if (
                                                    isStudentChoice &&
                                                    !isCorrect
                                                )
                                                    bg = "rgba(220,53,69,0.15)";
                                                return (
                                                    <div
                                                        key={n}
                                                        className="d-flex align-items-center py-1 px-2 mb-1 rounded"
                                                        style={{
                                                            background: bg,
                                                        }}
                                                    >
                                                        <span
                                                            className="mr-2 font-weight-bold"
                                                            style={{
                                                                minWidth:
                                                                    "18px",
                                                            }}
                                                        >
                                                            {n}.
                                                        </span>
                                                        <span>{text}</span>
                                                        {isCorrect && (
                                                            <span className="ml-auto badge badge-success">
                                                                ✓ Correct
                                                            </span>
                                                        )}
                                                        {isStudentChoice &&
                                                            !isCorrect && (
                                                                <span className="ml-auto badge badge-danger">
                                                                    ✗ Student's
                                                                    Answer
                                                                </span>
                                                            )}
                                                        {isStudentChoice &&
                                                            isCorrect && (
                                                                <span className="ml-2 badge badge-info">
                                                                    Student's
                                                                    Answer
                                                                </span>
                                                            )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </>
                    )}
                </div>
                <div className="modal-footer">
                    <button className="btn btn-secondary" onClick={onClose}>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
);

// ─── Main Component ───────────────────────────────────────────────────────────

const ExamResults: React.FC<ExamResultsProps> = ({
    examResults,
    studentName,
    onRefresh,
}) => {
    const [resettingId, setResettingId] = useState<number | null>(null);
    const [reviewData, setReviewData] = useState<ExamReview | null>(null);
    const [reviewLoading, setReviewLoading] = useState(false);
    const [showReview, setShowReview] = useState(false);
    const [actionError, setActionError] = useState<string | null>(null);

    if (!examResults) {
        return (
            <div className="alert alert-warning">
                <i className="fas fa-exclamation-triangle mr-2"></i>
                No exam data available for this course enrollment.
            </div>
        );
    }

    const {
        status,
        exam_ready,
        readiness_reason,
        exam_passed,
        exam_admin_override,
        max_attempts,
        attempts_used,
        attempts_remaining,
        next_attempt_at,
        attempts,
    } = examResults;

    const handleReset = async (attemptId: number) => {
        if (
            !confirm(
                "Reset this exam attempt? The student will be able to retake the exam.",
            )
        )
            return;
        setResettingId(attemptId);
        setActionError(null);
        try {
            const response = await axios.post(
                `/admin/support/reset-exam/${attemptId}`,
            );
            if (response.data.success) {
                onRefresh();
            } else {
                setActionError(response.data.message || "Reset failed.");
            }
        } catch (err: any) {
            setActionError(err.response?.data?.message || "Reset failed.");
        } finally {
            setResettingId(null);
        }
    };

    const handleReview = async (attemptId: number) => {
        setReviewLoading(true);
        setReviewData(null);
        setShowReview(true);
        setActionError(null);
        try {
            const response = await axios.get(
                `/admin/support/exam-review/${attemptId}`,
            );
            if (response.data.success) {
                setReviewData(response.data.data);
            } else {
                setActionError(
                    response.data.message || "Could not load review.",
                );
                setShowReview(false);
            }
        } catch (err: any) {
            setActionError(
                err.response?.data?.message || "Could not load review.",
            );
            setShowReview(false);
        } finally {
            setReviewLoading(false);
        }
    };

    return (
        <div className="exam-results">
            {/* ── Status Overview ── */}
            <div className="row mb-3">
                <div className="col-12">
                    <div className="card">
                        <div className="card-body">
                            <div className="d-flex flex-wrap align-items-center justify-content-between">
                                <div>
                                    <h6 className="mb-1">
                                        <i className="fas fa-file-alt mr-2"></i>
                                        Exam Status — <em>{studentName}</em>
                                    </h6>
                                    <StatusBadge status={status} />
                                    {exam_admin_override && (
                                        <span className="badge badge-warning ml-2">
                                            Admin Override Active
                                        </span>
                                    )}
                                </div>
                                <div className="text-right mt-2 mt-sm-0">
                                    <div className="text-muted small">
                                        Attempts Used:{" "}
                                        <strong>
                                            {attempts_used} / {max_attempts}
                                        </strong>
                                    </div>
                                    <div className="text-muted small">
                                        Remaining:{" "}
                                        <strong>{attempts_remaining}</strong>
                                    </div>
                                    {next_attempt_at && (
                                        <div className="text-warning small">
                                            Next attempt available:{" "}
                                            {next_attempt_at}
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Readiness explanation */}
                            {!exam_ready && readiness_reason && (
                                <div
                                    className="alert alert-warning mt-3 mb-0"
                                    style={{ fontSize: "0.85rem" }}
                                >
                                    {readiness_reason.reason === "lessons" && (
                                        <>
                                            <i className="fas fa-book mr-1"></i>{" "}
                                            Student has not completed all
                                            required lessons.
                                        </>
                                    )}
                                    {readiness_reason.reason === "cooldown" && (
                                        <>
                                            <i className="fas fa-clock mr-1"></i>{" "}
                                            Student is in cooldown. Next attempt
                                            allowed:{" "}
                                            <strong>
                                                {
                                                    readiness_reason.next_attempt_at
                                                }
                                            </strong>
                                        </>
                                    )}
                                    {readiness_reason.reason ===
                                        "already_passed" && (
                                        <>
                                            <i className="fas fa-check-circle mr-1"></i>{" "}
                                            Student has already passed this
                                            exam.
                                        </>
                                    )}
                                    {readiness_reason.reason === "inactive" && (
                                        <>
                                            <i className="fas fa-ban mr-1"></i>{" "}
                                            Course enrollment is not active.
                                        </>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* ── Error ── */}
            {actionError && (
                <div className="alert alert-danger alert-dismissible">
                    <button
                        className="close"
                        onClick={() => setActionError(null)}
                    >
                        ×
                    </button>
                    {actionError}
                </div>
            )}

            {/* ── Attempts Table ── */}
            {attempts.length === 0 ? (
                <div className="alert alert-info">
                    <i className="fas fa-info-circle mr-2"></i>
                    No exam attempts found for this enrollment.
                </div>
            ) : (
                <div className="card">
                    <div className="card-header">
                        <h6 className="mb-0">
                            <i className="fas fa-history mr-2"></i>
                            Exam Attempts ({attempts.length})
                        </h6>
                    </div>
                    <div className="card-body p-0">
                        <div className="table-responsive">
                            <table className="table table-sm table-hover mb-0">
                                <thead className="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Started</th>
                                        <th>Completed</th>
                                        <th>Score</th>
                                        <th>Result</th>
                                        <th>Status</th>
                                        <th>Next Attempt</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {attempts.map((attempt, idx) => (
                                        <tr key={attempt.id}>
                                            <td className="text-muted">
                                                {idx + 1}
                                            </td>
                                            <td
                                                style={{ whiteSpace: "nowrap" }}
                                            >
                                                {attempt.created_at}
                                            </td>
                                            <td
                                                style={{ whiteSpace: "nowrap" }}
                                            >
                                                {attempt.completed_at ?? (
                                                    <span className="text-muted">
                                                        —
                                                    </span>
                                                )}
                                            </td>
                                            <td>
                                                {attempt.score ? (
                                                    <span>
                                                        {attempt.score}
                                                        {attempt.score_percent !==
                                                            null && (
                                                            <span className="text-muted ml-1">
                                                                (
                                                                {
                                                                    attempt.score_percent
                                                                }
                                                                %)
                                                            </span>
                                                        )}
                                                    </span>
                                                ) : (
                                                    <span className="text-muted">
                                                        —
                                                    </span>
                                                )}
                                            </td>
                                            <td>
                                                {attempt.is_completed ? (
                                                    <span
                                                        className={`badge ${
                                                            attempt.is_passed
                                                                ? "badge-success"
                                                                : "badge-danger"
                                                        }`}
                                                    >
                                                        {attempt.is_passed
                                                            ? "Passed"
                                                            : "Failed"}
                                                    </span>
                                                ) : (
                                                    <span className="text-muted">
                                                        —
                                                    </span>
                                                )}
                                            </td>
                                            <td>
                                                {attempt.is_in_progress && (
                                                    <span className="badge badge-primary">
                                                        In Progress
                                                    </span>
                                                )}
                                                {attempt.is_expired &&
                                                    !attempt.is_completed && (
                                                        <span className="badge badge-secondary">
                                                            Expired
                                                        </span>
                                                    )}
                                                {attempt.is_completed && (
                                                    <span className="badge badge-light text-muted">
                                                        Completed
                                                    </span>
                                                )}
                                            </td>
                                            <td
                                                style={{ whiteSpace: "nowrap" }}
                                            >
                                                {attempt.next_attempt_at ? (
                                                    <span className="text-muted small">
                                                        {
                                                            attempt.next_attempt_at
                                                        }
                                                    </span>
                                                ) : (
                                                    <span className="text-muted">
                                                        —
                                                    </span>
                                                )}
                                            </td>
                                            <td
                                                style={{ whiteSpace: "nowrap" }}
                                            >
                                                <div className="btn-group btn-group-sm">
                                                    {attempt.can_review &&
                                                        attempt.has_answers && (
                                                            <button
                                                                className="btn btn-info btn-sm"
                                                                onClick={() =>
                                                                    handleReview(
                                                                        attempt.id,
                                                                    )
                                                                }
                                                                title="Review exam answers"
                                                            >
                                                                <i className="fas fa-search"></i>{" "}
                                                                Review
                                                            </button>
                                                        )}
                                                    <button
                                                        className="btn btn-warning btn-sm"
                                                        onClick={() =>
                                                            handleReset(
                                                                attempt.id,
                                                            )
                                                        }
                                                        disabled={
                                                            resettingId ===
                                                            attempt.id
                                                        }
                                                        title="Reset this attempt (student can retake)"
                                                    >
                                                        {resettingId ===
                                                        attempt.id ? (
                                                            <i className="fas fa-spinner fa-spin"></i>
                                                        ) : (
                                                            <>
                                                                <i className="fas fa-undo"></i>{" "}
                                                                Reset
                                                            </>
                                                        )}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {/* ── Review Modal ── */}
            {showReview && (
                <ReviewModal
                    review={reviewData}
                    loading={reviewLoading}
                    onClose={() => {
                        setShowReview(false);
                        setReviewData(null);
                    }}
                />
            )}
        </div>
    );
};

export default ExamResults;
