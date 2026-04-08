import React, { useEffect, useState } from "react";
import axios from "axios";

interface LessonRow {
    inst_lesson_id: number;
    lesson_id: number;
    lesson_title: string;
    started_at: string | null;
    student_lesson_id: number | null;
    dnc_at: string | null;
    completed_at: string | null;
    can_reverse_dnc: boolean;
}

interface DayLessonsPanelProps {
    studentUnitId: number;
    onRefresh: () => void;
    toolPermissions: Record<string, boolean>;
}

const statusBadge = (row: LessonRow) => {
    if (!row.student_lesson_id) {
        return (
            <span className="badge badge-secondary">
                <i className="fas fa-minus mr-1"></i>Missing
            </span>
        );
    }
    if (row.completed_at) {
        return (
            <span className="badge badge-success">
                <i className="fas fa-check mr-1"></i>Completed
            </span>
        );
    }
    if (row.dnc_at) {
        return (
            <span className="badge badge-danger">
                <i className="fas fa-times mr-1"></i>DNC
            </span>
        );
    }
    return (
        <span className="badge badge-warning">
            <i className="fas fa-hourglass-half mr-1"></i>In Progress
        </span>
    );
};

const DayLessonsPanel: React.FC<DayLessonsPanelProps> = ({ studentUnitId, onRefresh, toolPermissions }) => {
    const [lessons, setLessons] = useState<LessonRow[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [grantingId, setGrantingId] = useState<number | null>(null);
    const [grantMessages, setGrantMessages] = useState<Record<number, { text: string; type: "success" | "danger" }>>({});
    const [reversingId, setReversingId] = useState<number | null>(null);
    const [reverseReasons, setReverseReasons] = useState<Record<number, string>>({});
    const [reverseMessages, setReverseMessages] = useState<Record<number, { text: string; type: "success" | "danger" }>>({});

    const fetchLessons = async () => {
        setLoading(true);
        setError(null);
        try {
            const res = await axios.get(
                `/admin/support/student-tools/lessons-for-day/${studentUnitId}`
            );
            setLessons(res.data.lessons || []);
        } catch (err: any) {
            setError(err?.response?.data?.message || "Failed to load lessons.");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLessons();
    }, [studentUnitId]);

    const handleGrant = async (row: LessonRow) => {
        setGrantingId(row.inst_lesson_id);
        try {
            await axios.post(
                `/admin/support/student-tools/grant-lesson/${studentUnitId}`,
                { inst_lesson_id: row.inst_lesson_id }
            );
            setGrantMessages((prev) => ({
                ...prev,
                [row.inst_lesson_id]: { text: "Lesson granted.", type: "success" },
            }));
            fetchLessons();
            onRefresh();
        } catch (err: any) {
            const msg = err?.response?.data?.message || "Failed to grant lesson.";
            setGrantMessages((prev) => ({
                ...prev,
                [row.inst_lesson_id]: { text: msg, type: "danger" },
            }));
        } finally {
            setGrantingId(null);
        }
    };

    const handleReverseDnc = async (row: LessonRow) => {
        setReversingId(row.inst_lesson_id);
        try {
            await axios.post(
                `/admin/support/student-tools/reverse-lesson-dnc/${row.student_lesson_id}`,
                { reason: reverseReasons[row.inst_lesson_id] ?? "" }
            );
            setReverseMessages((prev) => ({
                ...prev,
                [row.inst_lesson_id]: { text: "DNC reversed.", type: "success" },
            }));
            fetchLessons();
            onRefresh();
        } catch (err: any) {
            const msg = err?.response?.data?.message || "Failed to reverse DNC.";
            setReverseMessages((prev) => ({
                ...prev,
                [row.inst_lesson_id]: { text: msg, type: "danger" },
            }));
        } finally {
            setReversingId(null);
        }
    };

    if (loading) {
        return (
            <div className="text-center py-2">
                <i className="fas fa-spinner fa-spin mr-2"></i>
                <small>Loading lessons...</small>
            </div>
        );
    }

    if (error) {
        return (
            <div className="alert alert-danger alert-sm py-1 px-2 mb-0">
                <small>{error}</small>
            </div>
        );
    }

    if (lessons.length === 0) {
        return (
            <div className="alert alert-info alert-sm py-1 px-2 mb-0">
                <small>No lessons found for this class day.</small>
            </div>
        );
    }

    return (
        <div className="table-responsive mt-2">
            <table className="table table-sm table-bordered mb-0">
                <thead className="thead-dark">
                    <tr>
                        <th>Lesson</th>
                        <th>Started</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {lessons.map((row) => {
                        const msg = grantMessages[row.inst_lesson_id];
                        return (
                            <tr key={row.inst_lesson_id}>
                                <td>{row.lesson_title}</td>
                                <td>
                                    <small className="text-muted">
                                        {row.started_at
                                            ? new Date(row.started_at).toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })
                                            : "—"}
                                    </small>
                                </td>
                                <td>{statusBadge(row)}</td>
                                <td>
                                    {!row.student_lesson_id && toolPermissions['grant-lesson'] && (
                                        <>
                                            <button
                                                className="btn btn-xs btn-primary"
                                                disabled={grantingId === row.inst_lesson_id}
                                                onClick={() => handleGrant(row)}
                                            >
                                                {grantingId === row.inst_lesson_id ? (
                                                    <><i className="fas fa-spinner fa-spin mr-1"></i>Granting...</>
                                                ) : (
                                                    <><i className="fas fa-plus mr-1"></i>Grant Lesson</>
                                                )}
                                            </button>
                                            {msg && (
                                                <span className={`ml-2 text-${msg.type} small`}>{msg.text}</span>
                                            )}
                                        </>
                                    )}
                                    {row.can_reverse_dnc && toolPermissions['reverse-dnc'] && (
                                        <div className="d-flex align-items-center mt-1" style={{ gap: "4px" }}>
                                            <input
                                                type="text"
                                                className="form-control form-control-sm"
                                                placeholder="Note (optional)"
                                                style={{ maxWidth: "160px" }}
                                                value={reverseReasons[row.inst_lesson_id] ?? ""}
                                                onChange={(e) =>
                                                    setReverseReasons((prev) => ({ ...prev, [row.inst_lesson_id]: e.target.value }))
                                                }
                                                disabled={reversingId === row.inst_lesson_id}
                                            />
                                            <button
                                                className="btn btn-xs btn-warning"
                                                disabled={reversingId === row.inst_lesson_id}
                                                onClick={() => handleReverseDnc(row)}
                                            >
                                                {reversingId === row.inst_lesson_id ? (
                                                    <><i className="fas fa-spinner fa-spin mr-1"></i>Reversing...</>
                                                ) : (
                                                    <><i className="fas fa-undo mr-1"></i>Reverse DNC</>
                                                )}
                                            </button>
                                            {reverseMessages[row.inst_lesson_id] && (
                                                <span className={`text-${reverseMessages[row.inst_lesson_id].type} small`}>
                                                    {reverseMessages[row.inst_lesson_id].text}
                                                </span>
                                            )}
                                        </div>
                                    )}
                                    {row.student_lesson_id && !row.can_reverse_dnc && msg && (
                                        <span className={`text-${msg.type} small`}>{msg.text}</span>
                                    )}
                                </td>
                            </tr>
                        );
                    })}
                </tbody>
            </table>
        </div>
    );
};

export default DayLessonsPanel;
