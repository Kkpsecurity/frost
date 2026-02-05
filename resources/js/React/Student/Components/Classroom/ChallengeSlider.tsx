import React, { useState, useRef, useEffect } from "react";
import styled from "styled-components";

/**
 * ChallengeSlider Component
 *
 * Interactive slider that requires user to drag from left to right
 * to confirm their participation in the lesson.
 *
 * Features:
 * - Must drag (can't click directly on right side)
 * - 80% completion threshold to trigger
 * - Smooth animations
 * - Touch and mouse support
 * - Visual feedback on completion
 */

interface ChallengeSliderProps {
    onComplete: () => void;
    disabled?: boolean;
}

const SliderContainer = styled.div`
    position: relative;
    width: 100%;
    height: 60px;
    background: linear-gradient(90deg, #e9ecef 0%, #dee2e6 100%);
    border-radius: 30px;
    overflow: hidden;
    user-select: none;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
`;

const SliderTrack = styled.div<{ progress: number }>`
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: ${(props) => props.progress}%;
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    transition: width 0.1s ease-out;
    border-radius: 30px 0 0 30px;
`;

const SliderButton = styled.div<{ position: number; isDragging: boolean }>`
    position: absolute;
    top: 50%;
    left: ${(props) => props.position}%;
    transform: translate(-50%, -50%);
    width: 56px;
    height: 56px;
    background: ${(props) =>
        props.isDragging
            ? "linear-gradient(135deg, #20c997 0%, #28a745 100%)"
            : "linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%)"};
    border-radius: 50%;
    cursor: ${(props) => (props.isDragging ? "grabbing" : "grab")};
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;

    &:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        transform: translate(-50%, -50%) scale(1.05);
    }

    &:active {
        transform: translate(-50%, -50%) scale(0.95);
    }
`;

const ArrowIcon = styled.div<{ completed: boolean }>`
    color: ${(props) => (props.completed ? "#ffffff" : "#28a745")};
    font-size: 24px;
    font-weight: bold;
    transition: color 0.2s ease;
`;

const SliderText = styled.div<{ visible: boolean }>`
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #6c757d;
    font-size: 16px;
    font-weight: 600;
    opacity: ${(props) => (props.visible ? 1 : 0)};
    transition: opacity 0.3s ease;
    pointer-events: none;
    white-space: nowrap;
`;

const CompletionCheckmark = styled.div`
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 32px;
    color: #ffffff;
    animation: checkmarkPop 0.4s ease;

    @keyframes checkmarkPop {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.2);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }
`;

const ChallengeSlider: React.FC<ChallengeSliderProps> = ({
    onComplete,
    disabled = false,
}) => {
    const [isDragging, setIsDragging] = useState(false);
    const [position, setPosition] = useState(5); // Start at 5% (left side with padding)
    const [isCompleted, setIsCompleted] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);
    const startPositionRef = useRef<number>(0);

    const COMPLETION_THRESHOLD = 80; // Must drag to 80% to complete
    const MIN_POSITION = 5; // Minimum position (left padding)
    const MAX_POSITION = 95; // Maximum position (right padding)

    useEffect(() => {
        const handleMouseMove = (e: MouseEvent) => {
            if (!isDragging || disabled || isCompleted) return;
            updatePosition(e.clientX);
        };

        const handleTouchMove = (e: TouchEvent) => {
            if (!isDragging || disabled || isCompleted) return;
            e.preventDefault();
            updatePosition(e.touches[0].clientX);
        };

        const handleMouseUp = () => {
            if (isDragging) {
                handleDragEnd();
            }
        };

        const handleTouchEnd = () => {
            if (isDragging) {
                handleDragEnd();
            }
        };

        if (isDragging) {
            document.addEventListener("mousemove", handleMouseMove);
            document.addEventListener("mouseup", handleMouseUp);
            document.addEventListener("touchmove", handleTouchMove, {
                passive: false,
            });
            document.addEventListener("touchend", handleTouchEnd);
        }

        return () => {
            document.removeEventListener("mousemove", handleMouseMove);
            document.removeEventListener("mouseup", handleMouseUp);
            document.removeEventListener("touchmove", handleTouchMove);
            document.removeEventListener("touchend", handleTouchEnd);
        };
    }, [isDragging, disabled, isCompleted]);

    const updatePosition = (clientX: number) => {
        if (!containerRef.current) return;

        const rect = containerRef.current.getBoundingClientRect();
        const x = clientX - rect.left;
        const percentage = (x / rect.width) * 100;

        // Constrain to min/max bounds
        const newPosition = Math.max(
            MIN_POSITION,
            Math.min(MAX_POSITION, percentage),
        );
        setPosition(newPosition);

        // Check if crossed completion threshold
        if (newPosition >= COMPLETION_THRESHOLD && !isCompleted) {
            handleComplete();
        }
    };

    const handleDragStart = (clientX: number) => {
        if (disabled || isCompleted) return;

        // Must start dragging from near the button (prevent clicking far right)
        if (!containerRef.current) return;
        const rect = containerRef.current.getBoundingClientRect();
        const clickX = clientX - rect.left;
        const clickPercentage = (clickX / rect.width) * 100;

        // Only allow starting drag if clicking near current position (±15%)
        if (Math.abs(clickPercentage - position) < 15) {
            setIsDragging(true);
            startPositionRef.current = position;
        }
    };

    const handleDragEnd = () => {
        setIsDragging(false);

        // If not completed, snap back to start
        if (!isCompleted && position < COMPLETION_THRESHOLD) {
            setPosition(MIN_POSITION);
        }
    };

    const handleComplete = () => {
        setIsCompleted(true);
        setPosition(100);
        setIsDragging(false);

        // Call completion callback after animation
        setTimeout(() => {
            onComplete();
        }, 500);
    };

    const handleMouseDown = (e: React.MouseEvent) => {
        e.preventDefault();
        handleDragStart(e.clientX);
    };

    const handleTouchStart = (e: React.TouchEvent) => {
        e.preventDefault();
        handleDragStart(e.touches[0].clientX);
    };

    return (
        <SliderContainer ref={containerRef}>
            <SliderTrack progress={position} />

            <SliderText visible={!isDragging && !isCompleted && position < 30}>
                Slide to confirm you are present →
            </SliderText>

            {isCompleted ? (
                <CompletionCheckmark>✓</CompletionCheckmark>
            ) : (
                <SliderButton
                    position={position}
                    isDragging={isDragging}
                    onMouseDown={handleMouseDown}
                    onTouchStart={handleTouchStart}
                >
                    <ArrowIcon completed={position >= COMPLETION_THRESHOLD}>
                        {position >= COMPLETION_THRESHOLD ? "✓" : "→"}
                    </ArrowIcon>
                </SliderButton>
            )}
        </SliderContainer>
    );
};

export default ChallengeSlider;
