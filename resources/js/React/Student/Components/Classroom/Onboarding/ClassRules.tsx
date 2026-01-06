import React, { useState } from "react";

interface ClassRulesProps {
    onAgree: () => void;
    loading?: boolean;
}

/**
 * ClassRules - Classroom procedures and challenge system explanation
 *
 * Adapted from archived component:
 * - Kept all 7 FAQ items explaining procedures
 * - Kept audio alert test functionality
 * - Changed onAgree to be a prop that calls API
 * - Added loading state support
 */
const ClassRules: React.FC<ClassRulesProps> = ({ onAgree, loading = false }) => {
    const challengeSound = window.location.origin + "/assets/sound/challenge.mp3";
    const audio = new Audio(challengeSound);

    const playSound = () => {
        audio.play().catch((err) => {
            console.error("Error playing sound:", err);
        });
    };

    const handleAgreement = () => {
        window.scrollTo(0, 0);
        onAgree();
    };

    return (
        <div>
            {/* Header */}
            <div
                style={{
                    backgroundColor: "#34495e",
                    padding: "1rem",
                    borderRadius: "0.5rem",
                    marginBottom: "1.5rem",
                    textAlign: "center",
                }}
            >
                <i
                    className="fas fa-book-reader"
                    style={{ fontSize: "2.5rem", color: "#3498db", marginBottom: "0.5rem" }}
                ></i>
                <h5 style={{ color: "white", marginBottom: "0.25rem" }}>
                    Classroom Procedures
                </h5>
                <p style={{ color: "#95a5a6", fontSize: "0.875rem", marginBottom: 0 }}>
                    Please review these important guidelines for today's session
                </p>
            </div>

            {/* Rules List */}
            <ul className="list-group list-group-flush" style={{ marginBottom: "1.5rem" }}>
                {/* Rule 1: Check-in */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-user-check"
                            style={{
                                color: "#3498db",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Check-In Procedure
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                You must check in within the first 10 minutes of class. If you arrive late and the instructor has started the lesson, you'll see a "Lesson in Progress" message.
                            </p>
                        </div>
                    </div>
                </li>

                {/* Rule 2: Late Arrival */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-clock"
                            style={{
                                color: "#e74c3c",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Late Arrival
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                If you see the "Lesson in Progress" screen, you cannot join the current lesson block. You must wait until the next lesson starts to check in.
                            </p>
                        </div>
                    </div>
                </li>

                {/* Rule 3: Lesson Blocks */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-lock"
                            style={{
                                color: "#f39c12",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Lesson Progress Blocks
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                Once a lesson starts, students who haven't checked in are blocked from joining. This ensures all participants start together and prevents disruption.
                            </p>
                        </div>
                    </div>
                </li>

                {/* Rule 4: Rejoining */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-redo"
                            style={{
                                color: "#2ecc71",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Rejoining After Being Blocked
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                If you're blocked from a lesson, you can rejoin when the instructor starts the next lesson. Make sure to check in promptly to avoid missing multiple lessons.
                            </p>
                        </div>
                    </div>
                </li>

                {/* Rule 5: Challenges */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-trophy"
                            style={{
                                color: "#9b59b6",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Lesson Challenges
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                During lessons, you may receive random verification challenges (4-digit codes). You must enter the code displayed by your instructor to prove you're actively participating. This ensures you're paying attention and present in class.
                            </p>
                        </div>
                    </div>
                </li>

                {/* Rule 6: Challenge Alerts */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        marginBottom: "0.5rem",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-bell"
                            style={{
                                color: "#e67e22",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Challenge Audio Alerts
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: "0.5rem", fontSize: "0.875rem" }}>
                                When a challenge is issued, you'll hear an audio alert (if your browser is in fullscreen mode). This helps ensure you don't miss any challenges.
                            </p>
                            <button
                                type="button"
                                className="btn btn-sm btn-outline-warning"
                                onClick={playSound}
                            >
                                <i className="fas fa-volume-up me-2"></i>
                                Test Alert Sound
                            </button>
                        </div>
                    </div>
                </li>

                {/* Rule 7: Consequences */}
                <li
                    className="list-group-item"
                    style={{
                        backgroundColor: "#34495e",
                        border: "1px solid rgba(255, 255, 255, 0.1)",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div style={{ display: "flex", alignItems: "start" }}>
                        <i
                            className="fas fa-exclamation-triangle"
                            style={{
                                color: "#e74c3c",
                                fontSize: "1.5rem",
                                marginRight: "1rem",
                                marginTop: "0.25rem",
                            }}
                        ></i>
                        <div>
                            <h6 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Challenge Failure Consequences
                            </h6>
                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.875rem" }}>
                                If you fail 2 challenges during a lesson, that lesson will be marked as incomplete. Your instructor has discretion to excuse failures for valid reasons (technical issues, emergency, etc.). Make sure to stay engaged!
                            </p>
                        </div>
                    </div>
                </li>
            </ul>

            {/* Agreement Button */}
            <button
                type="button"
                className="btn btn-primary w-100"
                onClick={handleAgreement}
                disabled={loading}
                style={{
                    fontSize: "1.1rem",
                    padding: "0.75rem",
                    fontWeight: "bold",
                }}
            >
                {loading ? (
                    <>
                        <i className="fas fa-spinner fa-spin me-2"></i>
                        Processing...
                    </>
                ) : (
                    <>
                        <i className="fas fa-check me-2"></i>
                        I Understand the Rules
                    </>
                )}
            </button>
        </div>
    );
};

export default ClassRules;
