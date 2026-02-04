import React, { useMemo, useState } from "react";
import {
    useClassroomChat,
    usePostClassroomChatMessage,
    ClassroomChatMessage,
} from "../../../Hooks/ClassroomChatHooks";

interface ClassroomChatCardProps {
    courseDateId: number | null;
}

const ClassroomChatCard: React.FC<ClassroomChatCardProps> = ({
    courseDateId,
}) => {
    const [draft, setDraft] = useState<string>("");

    const { data, isLoading, isError } = useClassroomChat(courseDateId);
    const { mutateAsync, isPending } =
        usePostClassroomChatMessage(courseDateId);

    const enabled = data?.enabled ?? false;
    const messages: ClassroomChatMessage[] = useMemo(
        () => data?.messages ?? [],
        [data],
    );

    const onSend = async () => {
        if (!courseDateId || !enabled) return;
        const message = draft.trim();
        if (!message) return;

        await mutateAsync({ course_date_id: courseDateId, message });
        setDraft("");
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
                    <i className="fas fa-comments me-2"></i>
                    Chat Room
                </h6>
            </div>

            <div className="card-body" style={{ padding: "0.75rem" }}>
                {isLoading ? (
                    <div style={{ color: "#95a5a6", fontSize: "0.9rem" }}>
                        Loading chat...
                    </div>
                ) : isError ? (
                    <div style={{ color: "#95a5a6", fontSize: "0.9rem" }}>
                        Chat temporarily unavailable
                    </div>
                ) : !enabled ? (
                    <div
                        style={{
                            color: "#95a5a6",
                            fontSize: "0.9rem",
                            backgroundColor: "rgba(0,0,0,0.15)",
                            borderRadius: "0.5rem",
                            padding: "0.75rem",
                        }}
                    >
                        Chat is disabled.
                    </div>
                ) : (
                    <>
                        <div
                            style={{
                                height: "260px",
                                overflowY: "auto",
                                backgroundColor: "rgba(0,0,0,0.15)",
                                borderRadius: "0.5rem",
                                padding: "0.5rem",
                                marginBottom: "0.75rem",
                            }}
                        >
                            {messages.length === 0 ? (
                                <div
                                    style={{
                                        color: "#95a5a6",
                                        fontSize: "0.9rem",
                                        textAlign: "center",
                                        padding: "1rem 0.5rem",
                                    }}
                                >
                                    No messages yet. Say hi!
                                </div>
                            ) : (
                                messages.map((m) => (
                                    <div
                                        key={m.id}
                                        style={{
                                            display: "flex",
                                            gap: "0.5rem",
                                            padding: "0.5rem",
                                            borderRadius: "0.5rem",
                                            backgroundColor: "rgba(0,0,0,0.08)",
                                            marginBottom: "0.5rem",
                                        }}
                                    >
                                        <img
                                            src={
                                                m.user.user_avatar ||
                                                "/images/default-avatar.png"
                                            }
                                            alt={m.user.user_name}
                                            style={{
                                                width: "28px",
                                                height: "28px",
                                                borderRadius: "50%",
                                                objectFit: "cover",
                                                flex: "0 0 auto",
                                                marginTop: "2px",
                                                backgroundColor:
                                                    "rgba(0,0,0,0.25)",
                                            }}
                                        />
                                        <div style={{ minWidth: 0, flex: 1 }}>
                                            <div
                                                style={{
                                                    display: "flex",
                                                    alignItems: "baseline",
                                                    justifyContent:
                                                        "space-between",
                                                    gap: "0.5rem",
                                                }}
                                            >
                                                <div
                                                    style={{
                                                        color: "#ecf0f1",
                                                        fontWeight: 600,
                                                        fontSize: "0.85rem",
                                                        overflow: "hidden",
                                                        textOverflow:
                                                            "ellipsis",
                                                        whiteSpace: "nowrap",
                                                    }}
                                                    title={m.user.user_name}
                                                >
                                                    {m.user.user_name}
                                                </div>
                                                <div
                                                    style={{
                                                        color: "#95a5a6",
                                                        fontSize: "0.75rem",
                                                        flex: "0 0 auto",
                                                    }}
                                                >
                                                    {m.created_at || ""}
                                                </div>
                                            </div>
                                            <div
                                                style={{
                                                    color: "#ecf0f1",
                                                    fontSize: "0.9rem",
                                                    lineHeight: 1.25,
                                                    marginTop: "0.25rem",
                                                    wordBreak: "break-word",
                                                }}
                                            >
                                                {m.body}
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        <div style={{ display: "flex", gap: "0.5rem" }}>
                            <input
                                className="form-control"
                                value={draft}
                                onChange={(e) => setDraft(e.target.value)}
                                onKeyDown={(e) => {
                                    if (e.key === "Enter" && enabled) {
                                        e.preventDefault();
                                        onSend();
                                    }
                                }}
                                placeholder={
                                    enabled
                                        ? "Type a message..."
                                        : "Chat is disabled"
                                }
                                disabled={isPending || !enabled}
                                style={{
                                    backgroundColor: "rgba(0,0,0,0.15)",
                                    border: "1px solid rgba(255,255,255,0.12)",
                                    color: "#ecf0f1",
                                    opacity: enabled ? 1 : 0.5,
                                }}
                            />
                            <button
                                className="btn btn-primary"
                                type="button"
                                onClick={onSend}
                                disabled={
                                    isPending || !draft.trim() || !enabled
                                }
                                style={{
                                    whiteSpace: "nowrap",
                                    opacity: enabled ? 1 : 0.5,
                                }}
                            >
                                Send
                            </button>
                        </div>
                    </>
                )}
            </div>
        </div>
    );
};

export default ClassroomChatCard;
