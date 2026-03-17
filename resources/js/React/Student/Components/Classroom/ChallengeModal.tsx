import React, { useEffect, useState, useCallback } from "react";
import styled from "styled-components";
import ChallengeSlider from "./ChallengeSlider";
import { t } from "@/i18n";

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
    warning_before_seconds: number; // seconds before expiry to play warning sound
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
    const [sliderKey, setSliderKey] = useState(0);
    const warningPlayedRef = React.useRef(false);
    const warningThreshold = challenge.warning_before_seconds ?? 30;

    // Format time as MM:SS
    const formatTime = (seconds: number): string => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, "0")}:${secs.toString().padStart(2, "0")}`;
    };

    // Countdown timer + warning audio trigger
    useEffect(() => {
        const interval = setInterval(() => {
            setTimeRemaining((prev) => {
                if (prev <= 1) {
                    clearInterval(interval);
                    // Challenge expired - will be handled by backend on next poll
                    return 0;
                }

                const next = prev - 1;

                // Fire warning audio the moment we cross the warning threshold (once only)
                if (next <= warningThreshold && !warningPlayedRef.current) {
                    warningPlayedRef.current = true;
                    try {
                        const warningAudio = new Audio("/sounds/challenge-warning.mp3");
                        warningAudio.volume = 0.7;
                        warningAudio.play().catch((err) => {
                            console.warn("Could not play challenge warning sound:", err);
                        });
                    } catch (err) {
                        console.warn("Warning audio not supported or file missing:", err);
                    }
                }

                return next;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [warningThreshold]);

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
            // Reset the slider so the student can try again
            setSliderKey((k) => k + 1);

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
    const isEol = challenge.is_eol;

    return (
        <ModalOverlay isFinal={isFinal || isEol}>
            <ModalContent isFinal={isFinal || isEol}>
                <Header>
                    <Icon isFinal={isFinal || isEol}>
                        {isEol ? "🏁" : isFinal ? "⚠️" : "👋"}
                    </Icon>
                    <Title isFinal={isFinal || isEol}>
                        {isEol
                            ? t("challenge.eolTitle")
                            : isFinal
                                ? t("challenge.finalTitle")
                                : t("challenge.regularTitle")}
                    </Title>
                    <Subtitle>
                        {isEol
                            ? t("challenge.eolSubtitle")
                            : isFinal
                                ? t("challenge.finalSubtitle")
                                : t("challenge.regularSubtitle")}
                    </Subtitle>
                </Header>

                <TimerContainer>
                    <TimerLabel>{t("challenge.timeRemaining")}</TimerLabel>
                    <TimerDisplay isUrgent={isUrgent}>
                        {formatTime(timeRemaining)}
                    </TimerDisplay>
                </TimerContainer>

                <SliderContainer>
                    <ChallengeSlider
                        key={sliderKey}
                        onComplete={handleSliderComplete}
                        disabled={isSubmitting || timeRemaining === 0}
                    />
                </SliderContainer>

                <InfoText isFinal={isFinal}>
                    {isSubmitting
                        ? t("challenge.submitting")
                        : timeRemaining === 0
                            ? t("challenge.timeExpired")
                            : t("challenge.slideInstruction")}
                </InfoText>

                {(isFinal || isEol) && (
                    <WarningBox>
                        <WarningText>
                            {isEol
                                ? t("challenge.eolWarning")
                                : t("challenge.finalWarning")}
                        </WarningText>
                    </WarningBox>
                )}
            </ModalContent>
        </ModalOverlay>
    );
};

export default ChallengeModal;
