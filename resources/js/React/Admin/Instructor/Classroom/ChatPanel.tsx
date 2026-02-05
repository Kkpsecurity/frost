import React, { useEffect, useRef, useState } from "react";
import { useQuery } from "@tanstack/react-query";

interface ChatPanelProps {
    courseDateId?: number;
    instUnitId?: number;
}

interface ChatMessage {
    id: number;
    sender_id: number;
    sender_name: string;
    sender_type: "instructor" | "student";
    message: string;
    timestamp: string;
    created_at: string;
}

/**
 * ChatPanel - Bottom section showing live classroom chat
 *
 * Features:
 * - Real-time message display with timestamps
 * - Sender identification (instructor vs student)
 * - Message input field
 * - Auto-scroll to latest message
 * - Live message count
 * - Persistent connection with polling
 */
const ChatPanel: React.FC<ChatPanelProps> = ({ courseDateId, instUnitId }) => {
    const messagesEndRef = useRef<HTMLDivElement>(null);
    const [inputValue, setInputValue] = useState("");
    const [isSending, setIsSending] = useState(false);
    const [chatEnabled, setChatEnabled] = useState(true);
    const [isTogglingChat, setIsTogglingChat] = useState(false);
    const [aiMonitoringEnabled, setAiMonitoringEnabled] = useState(false);
    const [isTogglingAi, setIsTogglingAi] = useState(false);

    // Fetch chat messages
    const {
        data: chatData,
        isLoading,
        error,
        refetch,
    } = useQuery({
        queryKey: ["chat-messages", courseDateId],
        queryFn: async () => {
            if (!courseDateId) return null;

            const response = await fetch(
                `/admin/instructors/classroom/chat-messages?course_date_id=${courseDateId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                },
            );

            if (!response.ok) {
                throw new Error(
                    `Failed to fetch chat messages: ${response.statusText}`,
                );
            }

            return response.json();
        },
        staleTime: 2 * 1000, // 2 seconds - very fresh
        gcTime: 5 * 60 * 1000, // 5 minutes
        refetchInterval: 3000, // Poll every 3 seconds for live messages
        enabled: !!courseDateId,
        retry: 2,
    });

    const messages: ChatMessage[] = chatData?.messages || [];

    // Update chatEnabled state when data changes
    useEffect(() => {
        if (chatData?.enabled !== undefined) {
            setChatEnabled(chatData.enabled);
        }
    }, [chatData]);

    // Auto-scroll to latest message
    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [messages]);

    // Handle send message
    const handleSendMessage = async () => {
        if (!inputValue.trim() || isSending || !chatEnabled) return;

        setIsSending(true);
        try {
            const response = await fetch(
                "/admin/instructors/classroom/send-message",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify({
                        message: inputValue,
                        course_date_id: courseDateId,
                        inst_unit_id: instUnitId,
                    }),
                },
            );

            if (response.ok) {
                setInputValue("");
                // Refetch messages immediately
                await refetch();
            }
        } catch (err) {
            console.error("Failed to send message:", err);
        } finally {
            setIsSending(false);
        }
    };

    // Handle toggle chat enabled/disabled
    const handleToggleChat = async () => {
        console.log("💬 Chat toggle clicked", {
            courseDateId,
            chatEnabled,
            isTogglingChat,
            willSend: !courseDateId || isTogglingChat ? "NO - BLOCKED" : "YES",
        });

        if (!courseDateId || isTogglingChat) {
            console.warn("⚠️ Chat toggle blocked:", {
                courseDateId,
                isTogglingChat,
            });
            return;
        }

        const newEnabledState = !chatEnabled;
        const payload = {
            course_date_id: courseDateId,
            enabled: newEnabledState,
        };

        console.log("📤 Sending chat toggle request:", payload);

        setIsTogglingChat(true);
        try {
            const response = await fetch(
                "/admin/instructors/classroom/chat-enable",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                },
            );

            console.log("📥 Chat toggle response:", {
                ok: response.ok,
                status: response.status,
                statusText: response.statusText,
            });

            if (response.ok) {
                setChatEnabled(newEnabledState);
                await refetch();
                console.log(
                    "✅ Chat toggled successfully to:",
                    newEnabledState,
                );
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.error("❌ Chat toggle failed:", errorData);
            }
        } catch (err) {
            console.error("❌ Failed to toggle chat:", err);
        } finally {
            setIsTogglingChat(false);
        }
    };

    // Handle toggle AI monitoring
    const handleToggleAi = async () => {
        console.log("🤖 AI monitoring toggle clicked", {
            courseDateId,
            aiMonitoringEnabled,
            isTogglingAi,
        });

        if (!courseDateId || isTogglingAi) {
            console.warn("⚠️ AI toggle blocked:", {
                courseDateId,
                isTogglingAi,
            });
            return;
        }

        const newAiState = !aiMonitoringEnabled;
        const payload = {
            course_date_id: courseDateId,
            ai_enabled: newAiState,
        };

        console.log("📤 Sending AI monitoring toggle request:", payload);

        setIsTogglingAi(true);
        try {
            const response = await fetch(
                "/admin/instructors/classroom/ai-monitoring-toggle",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(payload),
                },
            );

            console.log("📥 AI monitoring toggle response:", {
                ok: response.ok,
                status: response.status,
                statusText: response.statusText,
            });

            if (response.ok) {
                setAiMonitoringEnabled(newAiState);
                console.log(
                    "✅ AI monitoring toggled successfully to:",
                    newAiState,
                );
            } else {
                const errorData = await response.json().catch(() => ({}));
                console.error("❌ AI monitoring toggle failed:", errorData);
            }
        } catch (err) {
            console.error("❌ Failed to toggle AI monitoring:", err);
        } finally {
            setIsTogglingAi(false);
        }
    };

    // Handle Enter key
    const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    };

    // Get message style
    const getMessageStyle = (senderType: string, aiSent?: boolean) => {
        // AI Assistant messages (sent by instructor but ai_sent=true)
        if (aiSent) {
            return {
                bgClass: "bg-info text-white",
                align: "flex-end",
                icon: "fas fa-robot",
            };
        }

        return senderType === "instructor"
            ? {
                  bgClass: "bg-primary text-white",
                  align: "flex-end",
                  icon: "fas fa-chalkboard-user",
              }
            : {
                  bgClass: "bg-light border",
                  align: "flex-start",
                  icon: "fas fa-user-circle",
              };
    };

    // Format time
    const formatTime = (timestamp: string) => {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    if (isLoading) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-comments me-2"></i>
                        Live Chat
                    </h5>
                </div>
                <div
                    className="card-body d-flex justify-content-center align-items-center"
                    style={{ minHeight: "150px" }}
                >
                    <div className="text-center">
                        <div
                            className="spinner-border spinner-border-sm text-primary"
                            role="status"
                        >
                            <span className="visually-hidden">
                                Loading messages...
                            </span>
                        </div>
                        <p className="mt-2 text-muted">
                            <small>Loading messages...</small>
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-comments me-2"></i>
                        Live Chat
                    </h5>
                </div>
                <div className="card-body">
                    <div className="alert alert-danger alert-sm mb-0">
                        <small>
                            <i className="fas fa-exclamation-circle me-2"></i>
                            Failed to load chat messages
                        </small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="card">
            {/* Header */}
            <div className="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 className="mb-0">
                    <i className="fas fa-comments me-2"></i>
                    💬 Live Chat
                </h5>
                <div className="d-flex align-items-center gap-2">
                    <span className="badge badge-light">
                        <i
                            className="fas fa-circle text-success me-1"
                            style={{ fontSize: "0.6rem" }}
                        ></i>
                        {messages.length} messages
                    </span>
                    {/* AI Toggle - Disabled for now, will be re-enabled with different approach later
                    <button
                        className={`btn btn-sm ${aiMonitoringEnabled ? "btn-info" : "btn-outline-info"}`}
                        onClick={handleToggleAi}
                        disabled={isTogglingAi}
                        title={
                            aiMonitoringEnabled
                                ? "Disable AI Monitoring"
                                : "Enable AI Monitoring"
                        }
                    >
                        {isTogglingAi ? (
                            <i className="fas fa-spinner fa-spin"></i>
                        ) : (
                            <>
                                <i className={`fas fa-robot me-1`}></i>
                                AI {aiMonitoringEnabled ? "On" : "Off"}
                            </>
                        )}
                    </button>
                    */}
                    <button
                        className={`btn btn-sm ${chatEnabled ? "btn-warning" : "btn-success"}`}
                        onClick={handleToggleChat}
                        disabled={isTogglingChat}
                        title={chatEnabled ? "Disable Chat" : "Enable Chat"}
                    >
                        {isTogglingChat ? (
                            <i className="fas fa-spinner fa-spin"></i>
                        ) : (
                            <>
                                <i
                                    className={`fas ${chatEnabled ? "fa-comment-slash" : "fa-comment"} me-1`}
                                ></i>
                                {chatEnabled ? "Disable" : "Enable"}
                            </>
                        )}
                    </button>
                </div>
            </div>

            {/* Messages Container */}
            <div
                className="card-body"
                style={{
                    maxHeight: "200px",
                    overflow: "auto",
                    backgroundColor: "#f8f9fa",
                }}
            >
                {messages.length === 0 ? (
                    <div className="text-center text-muted py-4">
                        <i
                            className="fas fa-comments"
                            style={{ fontSize: "2rem", opacity: 0.3 }}
                        ></i>
                        <p className="mt-2">
                            <small>No messages yet. Start chatting!</small>
                        </p>
                    </div>
                ) : (
                    <div className="messages-list">
                        {messages.map((msg) => {
                            const style = getMessageStyle(
                                msg.sender_type,
                                msg.ai_sent,
                            );
                            const isAI = msg.ai_sent === true;
                            return (
                                <div
                                    key={msg.id}
                                    className="d-flex mb-2"
                                    style={{ justifyContent: style.align }}
                                >
                                    <div
                                        className="d-flex gap-2"
                                        style={{ maxWidth: "75%" }}
                                    >
                                        {msg.sender_type === "student" && (
                                            <i
                                                className={`${style.icon} mt-1`}
                                                style={{
                                                    fontSize: "1rem",
                                                    minWidth: "1rem",
                                                }}
                                            ></i>
                                        )}
                                        <div>
                                            <div
                                                className={`p-2 rounded ${style.bgClass}`}
                                                style={{
                                                    fontSize: "0.85rem",
                                                    wordWrap: "break-word",
                                                }}
                                            >
                                                <strong>
                                                    {isAI && (
                                                        <i className="fas fa-robot me-1"></i>
                                                    )}
                                                    {msg.sender_name}
                                                    {isAI && (
                                                        <span
                                                            className="badge bg-light text-dark ms-1"
                                                            style={{
                                                                fontSize:
                                                                    "0.65rem",
                                                            }}
                                                        >
                                                            AI
                                                        </span>
                                                    )}
                                                </strong>
                                                <div>{msg.message}</div>
                                            </div>
                                            <small
                                                className="text-muted d-block mt-1"
                                                style={{ fontSize: "0.75rem" }}
                                            >
                                                {formatTime(
                                                    msg.created_at ||
                                                        msg.timestamp,
                                                )}
                                            </small>
                                        </div>
                                        {(msg.sender_type === "instructor" ||
                                            isAI) && (
                                            <i
                                                className={`${style.icon} mt-1`}
                                                style={{
                                                    fontSize: "1rem",
                                                    minWidth: "1rem",
                                                    color: "#007bff",
                                                }}
                                            ></i>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                        <div ref={messagesEndRef} />
                    </div>
                )}
            </div>

            {/* Message Input */}
            <div className="card-footer border-top">
                <div className="input-group input-group-sm">
                    <input
                        type="text"
                        className="form-control form-control-sm"
                        placeholder="Type a message..."
                        value={inputValue}
                        onChange={(e) => setInputValue(e.target.value)}
                        onKeyDown={handleKeyDown}
                        disabled={isSending}
                        style={{ fontSize: "0.85rem" }}
                    />
                    <button
                        className="btn btn-primary btn-sm"
                        onClick={handleSendMessage}
                        disabled={!inputValue.trim() || isSending}
                        title="Send message (Enter)"
                    >
                        <i
                            className={`fas ${isSending ? "fa-spinner fa-spin" : "fa-paper-plane"}`}
                        ></i>
                    </button>
                </div>
                <small className="text-muted mt-1 d-block">
                    Press <kbd>Enter</kbd> to send
                </small>
            </div>
        </div>
    );
};

export default ChatPanel;
