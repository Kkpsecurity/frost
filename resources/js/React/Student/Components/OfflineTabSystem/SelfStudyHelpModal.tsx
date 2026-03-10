import React from "react";

interface SelfStudyHelpModalProps {
    isOpen: boolean;
    onClose: () => void;
}

const overlayStyle: React.CSSProperties = {
    position: "fixed",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    background: "rgba(0, 0, 0, 0.75)",
    zIndex: 9000,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    padding: "1rem",
};

const modalStyle: React.CSSProperties = {
    background: "#1e2d3d",
    border: "1px solid #34495e",
    borderRadius: "0.75rem",
    width: "100%",
    maxWidth: "640px",
    maxHeight: "85vh",
    display: "flex",
    flexDirection: "column",
    boxShadow: "0 20px 60px rgba(0,0,0,0.6)",
};

const headerStyle: React.CSSProperties = {
    display: "flex",
    alignItems: "center",
    justifyContent: "space-between",
    padding: "1.25rem 1.5rem 1rem",
    borderBottom: "1px solid #34495e",
    flexShrink: 0,
};

const bodyStyle: React.CSSProperties = {
    overflowY: "auto",
    padding: "1.25rem 1.5rem 1.5rem",
};

const sectionStyle: React.CSSProperties = {
    marginBottom: "1.5rem",
};

const sectionTitleStyle: React.CSSProperties = {
    color: "#3498db",
    fontWeight: 700,
    fontSize: "0.95rem",
    marginBottom: "0.5rem",
    display: "flex",
    alignItems: "center",
    gap: "0.5rem",
};

const bodyTextStyle: React.CSSProperties = {
    color: "#bdc3c7",
    fontSize: "0.875rem",
    lineHeight: 1.6,
};

const listStyle: React.CSSProperties = {
    color: "#bdc3c7",
    fontSize: "0.875rem",
    lineHeight: 1.8,
    paddingLeft: "1.25rem",
    margin: 0,
};

const dividerStyle: React.CSSProperties = {
    borderColor: "#2c3e50",
    margin: "0 0 1.5rem",
};

const SelfStudyHelpModal: React.FC<SelfStudyHelpModalProps> = ({
    isOpen,
    onClose,
}) => {
    if (!isOpen) return null;

    const handleOverlayClick = (e: React.MouseEvent<HTMLDivElement>) => {
        if (e.target === e.currentTarget) onClose();
    };

    return (
        <div style={overlayStyle} onClick={handleOverlayClick}>
            <div style={modalStyle} role="dialog" aria-modal="true" aria-labelledby="help-modal-title">

                {/* Header */}
                <div style={headerStyle}>
                    <h5
                        id="help-modal-title"
                        style={{ color: "white", margin: 0, fontWeight: 700 }}
                    >
                        <i className="fas fa-question-circle me-2" style={{ color: "#3498db" }}></i>
                        Self Study — Help &amp; Rules
                    </h5>
                    <button
                        onClick={onClose}
                        style={{
                            background: "none",
                            border: "none",
                            color: "#95a5a6",
                            fontSize: "1.25rem",
                            cursor: "pointer",
                            lineHeight: 1,
                            padding: "0.25rem",
                        }}
                        aria-label="Close help"
                    >
                        ×
                    </button>
                </div>

                {/* Body */}
                <div style={bodyStyle}>

                    {/* What is Self Study */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-graduation-cap"></i>
                            What is Self Study Mode?
                        </div>
                        <p style={bodyTextStyle}>
                            Self Study Mode lets you watch lesson videos on your own schedule,
                            outside of a live scheduled class. You work through each lesson
                            independently. Once every lesson is completed, the Exam Room unlocks
                            and you can take your final exam.
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Video Quota */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-hourglass-half" style={{ color: "#f39c12" }}></i>
                            Video Quota
                        </div>
                        <p style={{ ...bodyTextStyle, marginBottom: "0.5rem" }}>
                            Each enrollment comes with a fixed amount of video watch time (shown as hours).
                        </p>
                        <ul style={listStyle}>
                            <li><strong style={{ color: "#ecf0f1" }}>Total</strong> — your full hour budget for this course enrollment.</li>
                            <li><strong style={{ color: "#ecf0f1" }}>Used</strong> — hours consumed by completed lesson sessions.</li>
                            <li><strong style={{ color: "#ecf0f1" }}>Remaining</strong> — hours left to spend on lessons.</li>
                            <li><strong style={{ color: "#ecf0f1" }}>Refunded</strong> — hours returned if you later completed the same lesson in a live class.</li>
                        </ul>
                        <p style={{ ...bodyTextStyle, marginTop: "0.75rem" }}>
                            You cannot start a new session if your remaining quota is less than
                            the time required for the selected lesson. Manage your time carefully.
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* How Sessions Work */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-film" style={{ color: "#3498db" }}></i>
                            How Lesson Sessions Work
                        </div>
                        <ol style={{ ...listStyle, listStyleType: "decimal" }}>
                            <li>Select a lesson from the left sidebar.</li>
                            <li>Click <strong style={{ color: "#ecf0f1" }}>Start Session</strong> — this opens a timed session window and reserves your quota.</li>
                            <li>Click <strong style={{ color: "#ecf0f1" }}>Open Player</strong>, then press Play when you're ready to watch.</li>
                            <li>Watch the video to the required completion percentage.</li>
                            <li>The session completes automatically, or click <strong style={{ color: "#ecf0f1" }}>Complete Session</strong> when done.</li>
                        </ol>
                        <p style={{ ...bodyTextStyle, marginTop: "0.75rem" }}>
                            <i className="fas fa-info-circle me-1" style={{ color: "#3498db" }}></i>
                            Your progress is saved automatically. If you refresh or leave the page,
                            your active session is restored when you return — as long as it hasn't expired.
                        </p>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Breaks */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-coffee" style={{ color: "#e67e22" }}></i>
                            Breaks &amp; Pause Time
                        </div>
                        <p style={{ ...bodyTextStyle, marginBottom: "0.5rem" }}>
                            Each session includes a break allowance based on the lesson's length.
                        </p>
                        <ul style={listStyle}>
                            <li>Shorter lessons receive one break; longer lessons receive multiple (e.g., Break 1 of 3).</li>
                            <li>Each break has a time limit — a warning plays when your break is nearly over.</li>
                            <li>Pause time is tracked against your budget. If you exceed your allowed pause time, your session window may expire.</li>
                        </ul>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Completion */}
                    <div style={sectionStyle}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-check-circle" style={{ color: "#2ecc71" }}></i>
                            Completing a Lesson
                        </div>
                        <ul style={listStyle}>
                            <li>A lesson is marked <strong style={{ color: "#2ecc71" }}>Complete</strong> when you watch the minimum required percentage of the video.</li>
                            <li>Completed lessons show a <strong style={{ color: "#2ecc71" }}>✓</strong> checkmark in the left sidebar.</li>
                            <li>Quota is deducted from your balance once a session is completed — not when it starts.</li>
                            <li>If a session expires before you finish, it will be marked as incomplete and you will need to start a new session.</li>
                        </ul>
                    </div>

                    <hr style={dividerStyle} />

                    {/* Exam Eligibility */}
                    <div style={{ ...sectionStyle, marginBottom: 0 }}>
                        <div style={sectionTitleStyle}>
                            <i className="fas fa-file-alt" style={{ color: "#9b59b6" }}></i>
                            Exam Eligibility
                        </div>
                        <ul style={listStyle}>
                            <li>The Exam Room only unlocks after <strong style={{ color: "#ecf0f1" }}>all required lessons</strong> are completed.</li>
                            <li>You can track your progress in the sidebar — all lessons must show the ✓ checkmark.</li>
                            <li>Your instructor can grant early exam access if needed.</li>
                            <li>If you complete lessons in a live class, they count toward your self-study requirements.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    );
};

export default SelfStudyHelpModal;
