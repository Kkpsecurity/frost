import React, { useMemo, useState } from "react";
import {
    ClassroomSessionMode,
    useAskInstructorMyQueue,
    useAskInstructorSubmit,
} from "../../../Hooks/ClassroomAskInstructorHooks";
import { toast } from "react-toastify";

interface AskInstructorCardProps {
    courseDateId: number | null;
    mode: ClassroomSessionMode;
}

const AskInstructorCard: React.FC<AskInstructorCardProps> = ({ courseDateId, mode }) => {
    const [topic, setTopic] = useState<string>("");
    const [urgency, setUrgency] = useState<"Normal" | "Urgent">("Normal");
    const [question, setQuestion] = useState<string>("");

    const { data, isLoading, isError } = useAskInstructorMyQueue(courseDateId);
    const { mutateAsync, isPending } = useAskInstructorSubmit(courseDateId);

    const questions = useMemo(() => data?.questions ?? [], [data]);

    const helperText =
        mode === "BREAK"
            ? "Class is on break. Submit a question for the instructor."
            : "Chat is disabled during teaching. Submit a question for the instructor.";

    const onSubmit = async () => {
        if (!courseDateId) return;

        const t = topic.trim();
        const q = question.trim();
        if (!t || !q) {
            toast.error("Please fill in topic and question.");
            return;
        }

        try {
            const res = await mutateAsync({
                course_date_id: courseDateId,
                topic: t,
                urgency,
                question: q,
            });

            if (!res?.success) {
                toast.error(res?.message || "Failed to submit question");
                return;
            }

            toast.success(res?.message || "Received. Instructor will respond.");
            setTopic("");
            setUrgency("Normal");
            setQuestion("");
        } catch (e: any) {
            toast.error("Failed to submit question");
        }
    };

    return (
        <div
            className="card"
            style={{
                backgroundColor: "#34495e",
                border: "none",
                marginTop: "1rem",
            }}
        >
            <div
                className="card-header"
                style={{
                    backgroundColor: "#2c3e50",
                    borderBottom: "1px solid rgba(255,255,255,0.1)",
                }}
            >
                <h6 className="mb-0" style={{ color: "white" }}>
                    <i className="fas fa-question-circle me-2"></i>
                    Ask Instructor
                </h6>
            </div>

            <div className="card-body" style={{ padding: "0.75rem" }}>
                <div
                    style={{
                        color: "#95a5a6",
                        fontSize: "0.9rem",
                        backgroundColor: "rgba(0,0,0,0.15)",
                        borderRadius: "0.5rem",
                        padding: "0.75rem",
                        marginBottom: "0.75rem",
                    }}
                >
                    {helperText}
                </div>

                <div style={{ display: "flex", gap: "0.5rem", marginBottom: "0.5rem" }}>
                    <input
                        className="form-control"
                        value={topic}
                        onChange={(e) => setTopic(e.target.value)}
                        placeholder="Topic (e.g. Module 3)"
                        disabled={isPending}
                        style={{
                            backgroundColor: "rgba(0,0,0,0.15)",
                            border: "1px solid rgba(255,255,255,0.12)",
                            color: "#ecf0f1",
                        }}
                    />

                    <select
                        className="form-select"
                        value={urgency}
                        onChange={(e) => setUrgency(e.target.value as any)}
                        disabled={isPending}
                        style={{
                            backgroundColor: "rgba(0,0,0,0.15)",
                            border: "1px solid rgba(255,255,255,0.12)",
                            color: "#ecf0f1",
                            maxWidth: "135px",
                        }}
                    >
                        <option value="Normal">Normal</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>

                <textarea
                    className="form-control"
                    value={question}
                    onChange={(e) => setQuestion(e.target.value)}
                    placeholder="Your question..."
                    disabled={isPending}
                    rows={3}
                    style={{
                        backgroundColor: "rgba(0,0,0,0.15)",
                        border: "1px solid rgba(255,255,255,0.12)",
                        color: "#ecf0f1",
                        marginBottom: "0.5rem",
                    }}
                />

                <button
                    className="btn btn-primary"
                    type="button"
                    onClick={onSubmit}
                    disabled={isPending || !topic.trim() || !question.trim()}
                    style={{ width: "100%" }}
                >
                    {isPending ? "Submitting..." : "Submit"}
                </button>

                <div style={{ marginTop: "0.75rem" }}>
                    <div style={{ color: "#ecf0f1", fontWeight: 600, fontSize: "0.9rem" }}>
                        Your recent questions
                    </div>

                    {isLoading ? (
                        <div style={{ color: "#95a5a6", fontSize: "0.85rem", marginTop: "0.25rem" }}>
                            Loading...
                        </div>
                    ) : isError ? (
                        <div style={{ color: "#95a5a6", fontSize: "0.85rem", marginTop: "0.25rem" }}>
                            Queue temporarily unavailable
                        </div>
                    ) : questions.length === 0 ? (
                        <div style={{ color: "#95a5a6", fontSize: "0.85rem", marginTop: "0.25rem" }}>
                            No questions yet.
                        </div>
                    ) : (
                        <div style={{ marginTop: "0.5rem" }}>
                            {questions.slice(0, 5).map((q) => (
                                <div
                                    key={q.id}
                                    style={{
                                        padding: "0.5rem",
                                        borderRadius: "0.5rem",
                                        backgroundColor: "rgba(0,0,0,0.08)",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    <div
                                        style={{
                                            display: "flex",
                                            justifyContent: "space-between",
                                            gap: "0.5rem",
                                        }}
                                    >
                                        <div style={{ color: "#ecf0f1", fontWeight: 600, fontSize: "0.85rem" }}>
                                            {q.topic}
                                        </div>
                                        <div style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                                            {q.status}
                                        </div>
                                    </div>

                                    <div
                                        style={{
                                            color: "#ecf0f1",
                                            fontSize: "0.85rem",
                                            marginTop: "0.25rem",
                                            wordBreak: "break-word",
                                        }}
                                    >
                                        {q.question}
                                    </div>

                                    {q.answer_text ? (
                                        <div
                                            style={{
                                                marginTop: "0.35rem",
                                                padding: "0.5rem",
                                                borderRadius: "0.5rem",
                                                backgroundColor: "rgba(0,0,0,0.12)",
                                            }}
                                        >
                                            <div style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                                                Instructor reply
                                            </div>
                                            <div
                                                style={{
                                                    color: "#ecf0f1",
                                                    fontSize: "0.85rem",
                                                    marginTop: "0.15rem",
                                                    wordBreak: "break-word",
                                                }}
                                            >
                                                {q.answer_text}
                                            </div>
                                        </div>
                                    ) : null}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default AskInstructorCard;
