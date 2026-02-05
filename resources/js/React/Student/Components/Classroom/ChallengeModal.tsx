import React, { useEffect, useState, useCallback } from "react";
import styled from "styled-components";
import ChallengeSlider from "./ChallengeSlider";

/**
 * ChallengeModal Component
 *
 * Full-screen modal that appears when a participation challenge is active.
 * Requires the student to interact with the slider to confirm presence.
 *
 * Features:
 * - Full-screen overlay (cannot dismiss)
 * - Timer countdown display
 * - Different styling for final challenges (urgent)
 * - Audio alert on mount
 * - Auto-submit on slider completion
 */

export interface ChallengeData {
    challenge_id: number;
    student_lesson_id: number;
    is_final: boolean;
    is_eol: boolean;
    expires_at: string; // ISO timestamp
    time_remaining: number; // seconds
    created_at: string; // ISO timestamp
}

interface ChallengeModalProps {
    challenge: ChallengeData;
    onComplete: (challengeId: number) => Promise<void>;
    onError?: (error: string) => void;
}

const ModalOverlay = styled.div<{ isFinal: boolean }>`
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: ${(props) =>
        props.isFinal
            ? "rgba(220, 53, 69, 0.95)" // Red overlay for final challenge
            : "rgba(0, 0, 0, 0.85)"}; // Dark overlay for regular challenge
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
`;

const ModalContent = styled.div<{ isFinal: boolean }>`
    background: white;
    border-radius: 16px;
    padding: 48px;
    max-width: 600px;
    width: 90%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.4s ease;
    border: ${(props) => (props.isFinal ? "4px solid #dc3545" : "none")};

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
`;

const Header = styled.div`
    text-align: center;
    margin-bottom: 32px;
`;

const Icon = styled.div<{ isFinal: boolean }>`
    font-size: 64px;
    margin-bottom: 16px;
    animation: ${(props) => (props.isFinal ? "pulse 1s infinite" : "none")};

    @keyframes pulse {
        0%,
        100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
`;

const Title = styled.h2<{ isFinal: boolean }>`
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 12px;
    color: ${(props) => (props.isFinal ? "#dc3545" : "#212529")};
`;

const Subtitle = styled.p`
    font-size: 16px;
    color: #6c757d;
    line-height: 1.5;
`;

const TimerContainer = styled.div`
    text-align: center;
    margin-bottom: 32px;
`;

const TimerLabel = styled.div`
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
`;

const TimerDisplay = styled.div<{ isUrgent: boolean }>`
    font-size: 48px;
    font-weight: 700;
    color: ${(props) => (props.isUrgent ? "#dc3545" : "#28a745")};
    font-family: "Courier New", monospace;
    transition: color 0.3s ease;
`;

const SliderContainer = styled.div`
    margin-bottom: 24px;
`;

const InfoText = styled.div<{ isFinal: boolean }>`
    text-align: center;
    font-size: 14px;
    color: ${(props) => (props.isFinal ? "#dc3545" : "#6c757d")};
    font-weight: ${(props) => (props.isFinal ? "600" : "400")};
    line-height: 1.5;
`;

const WarningBox = styled.div`
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 16px;
    margin-top: 24px;
    text-align: center;
`;

const WarningText = styled.p`
    margin: 0;
    color: #856404;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.5;
`;

const ChallengeModal: React.FC<ChallengeModalProps> = ({
    challenge,
    onComplete,
    onError,
}) => {
    const [timeRemaining, setTimeRemaining] = useState(
        challenge.time_remaining,
    );
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Format time as MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
    };

    // Countdown timer
    useEffect(() => {
        const interval = setInterval(() => {
            setTimeRemaining((prev) => {
                if (prev <= 1) {
                    clearInterval(interval);
                    // Challenge expired - will be handled by backend on next poll
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, []);

    // Play audio alert on mount
    useEffect(() => {
        try {
            const audio = new Audio("/sounds/challenge-alert.mp3");
            audio.volume = 0.5;
            audio.play().catch((err) => {
                console.warn("Could not play challenge alert sound:", err);
            });
        } catch (err) {
            console.warn("Audio not supported or file missing:", err);
        }
    }, []);

    const handleSliderComplete = useCallback(async () => {
        if (isSubmitting) return;

        setIsSubmitting(true);

        try {
            await onComplete(challenge.challenge_id);
            // Modal will be closed by parent component after successful response
        } catch (error) {
            console.error("Failed to complete challenge:", error);
            setIsSubmitting(false);

            if (onError) {
                onError(
                    error instanceof Error
                        ? error.message
                        : "Failed to submit response",
                );
            }
        }
    }, [challenge.challenge_id, onComplete, onError, isSubmitting]);

    const isUrgent = timeRemaining <= 60; // Last minute is urgent
    const isFinal = challenge.is_final;

    return (
        <ModalOverlay isFinal={isFinal}>
            <ModalContent isFinal={isFinal}>
                <Header>
                    <Icon isFinal={isFinal}>{isFinal ? "⚠️" : "👋"}</Icon>
                    <Title isFinal={isFinal}>
                        {isFinal
                            ? "FINAL PARTICIPATION CHECK"
                            : "Participation Check"}
                    </Title>
                    <Subtitle>
                        {isFinal
                            ? "This is your last chance to confirm you are actively participating in the lesson."
                            : "Please confirm that you are present and following along with the lesson."}
                    </Subtitle>
                </Header>

                <TimerContainer>
                    <TimerLabel>Time Remaining</TimerLabel>
                    <TimerDisplay isUrgent={isUrgent}>
                        {formatTime(timeRemaining)}
                    </TimerDisplay>
                </TimerContainer>

                <SliderContainer>
                    <ChallengeSlider
                        onComplete={handleSliderComplete}
                        disabled={isSubmitting || timeRemaining === 0}
                    />
                </SliderContainer>

                <InfoText isFinal={isFinal}>
                    {isSubmitting
                        ? "Submitting your response..."
                        : timeRemaining === 0
                          ? "Time expired - waiting for next poll..."
                          : "Slide the button all the way to the right to confirm your presence."}
                </InfoText>

                {isFinal && (
                    <WarningBox>
                        <WarningText>
                            ⚠️ Warning: Missing this challenge will mark the
                            lesson as "Did Not Complete" and you may need to
                            retake it.
                        </WarningText>
                    </WarningBox>
                )}
            </ModalContent>
        </ModalOverlay>
    );
};

export default ChallengeModal;
