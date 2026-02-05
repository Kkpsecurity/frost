import React, { useState } from "react";
import styled from "styled-components";

interface Challenge {
    id: number;
    student_lesson_id: number;
    lesson_name: string;
    type: string;
    created_at: string;
    completed_at: string | null;
    failed_at: string | null;
    expired_at: string | null;
    is_final: boolean;
    is_eol: boolean;
}

interface ChallengeHistoryProps {
    challenges?: Challenge[];
}

const CollapsibleCard = styled.div`
    background-color: #34495e;
    border: none;
    border-radius: 0.25rem;
`;

const CardHeader = styled.div<{ $isOpen: boolean }>`
    background-color: #2c3e50;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 0.75rem 1rem;
    cursor: pointer;
    user-select: none;
    transition: background-color 0.2s;

    &:hover {
        background-color: #34495e;
    }
`;

const HeaderContent = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
`;

const HeaderTitle = styled.h6`
    margin: 0;
    color: white;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
`;

const ChevronIcon = styled.i<{ $isOpen: boolean }>`
    transition: transform 0.3s ease;
    transform: ${(props) => (props.$isOpen ? "rotate(90deg)" : "rotate(0)")};
    color: #95a5a6;
`;

const CardBody = styled.div<{ $isOpen: boolean }>`
    max-height: ${(props) => (props.$isOpen ? "400px" : "0")};
    overflow: hidden;
    transition: max-height 0.3s ease;
`;

const HistoryList = styled.div`
    padding: 1rem;
    max-height: 400px;
    overflow-y: auto;

    &::-webkit-scrollbar {
        width: 6px;
    }

    &::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 3px;
    }

    &::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;

        &:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    }
`;

const ChallengeItem = styled.div<{
    $status: "completed" | "failed" | "expired";
}>`
    background-color: ${(props) => {
        switch (props.$status) {
            case "completed":
                return "rgba(39, 174, 96, 0.1)";
            case "failed":
                return "rgba(231, 76, 60, 0.1)";
            case "expired":
                return "rgba(243, 156, 18, 0.1)";
            default:
                return "rgba(255, 255, 255, 0.05)";
        }
    }};
    border-left: 3px solid
        ${(props) => {
            switch (props.$status) {
                case "completed":
                    return "#27ae60";
                case "failed":
                    return "#e74c3c";
                case "expired":
                    return "#f39c12";
                default:
                    return "#95a5a6";
            }
        }};
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    border-radius: 0.25rem;
    transition: background-color 0.2s;

    &:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }

    &:last-child {
        margin-bottom: 0;
    }
`;

const ChallengeHeader = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
`;

const LessonName = styled.div`
    color: white;
    font-weight: 500;
    font-size: 0.875rem;
`;

const StatusBadge = styled.span<{
    $status: "completed" | "failed" | "expired";
}>`
    background-color: ${(props) => {
        switch (props.$status) {
            case "completed":
                return "#27ae60";
            case "failed":
                return "#e74c3c";
            case "expired":
                return "#f39c12";
            default:
                return "#95a5a6";
        }
    }};
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
`;

const ChallengeDetails = styled.div`
    color: #95a5a6;
    font-size: 0.75rem;
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
`;

const DetailItem = styled.div`
    display: flex;
    align-items: center;
    gap: 0.25rem;
`;

const EmptyState = styled.div`
    color: #95a5a6;
    font-size: 0.875rem;
    text-align: center;
    padding: 2rem 1rem;
`;

const LoadingState = styled.div`
    color: #95a5a6;
    font-size: 0.875rem;
    text-align: center;
    padding: 2rem 1rem;
`;

const ErrorState = styled.div`
    color: #e74c3c;
    font-size: 0.875rem;
    text-align: center;
    padding: 2rem 1rem;
`;

const BadgeGroup = styled.div`
    display: flex;
    gap: 0.25rem;
    align-items: center;
`;

const SmallBadge = styled.span`
    background-color: rgba(52, 152, 219, 0.2);
    color: #3498db;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
`;

const formatDateTime = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
    });
};

const ChallengeHistory: React.FC<ChallengeHistoryProps> = ({
    challenges = [],
}) => {
    const [isOpen, setIsOpen] = useState(false);

    const completedCount = challenges.filter((c) => c.completed_at).length;
    const failedCount = challenges.filter((c) => c.failed_at).length;
    const expiredCount = challenges.filter((c) => c.expired_at).length;

    const getStatus = (
        challenge: Challenge,
    ): "completed" | "failed" | "expired" => {
        if (challenge.completed_at) return "completed";
        if (challenge.failed_at) return "failed";
        if (challenge.expired_at) return "expired";
        return "expired"; // Default fallback
    };

    return (
        <CollapsibleCard className="card">
            <CardHeader $isOpen={isOpen} onClick={() => setIsOpen(!isOpen)}>
                <HeaderContent>
                    <HeaderTitle>
                        <i className="fas fa-history"></i>
                        Challenge History
                        {completedCount + failedCount > 0 && (
                            <span
                                style={{
                                    backgroundColor: "rgba(52, 152, 219, 0.2)",
                                    color: "#3498db",
                                    padding: "0.125rem 0.5rem",
                                    borderRadius: "1rem",
                                    fontSize: "0.75rem",
                                    fontWeight: "600",
                                }}
                            >
                                {completedCount + failedCount}
                            </span>
                        )}
                    </HeaderTitle>
                    <ChevronIcon
                        className="fas fa-chevron-right"
                        $isOpen={isOpen}
                    />
                </HeaderContent>
            </CardHeader>

            <CardBody $isOpen={isOpen}>
                <HistoryList>
                    {challenges.length === 0 && (
                        <EmptyState>
                            <i className="fas fa-clipboard-list fa-2x mb-2"></i>
                            <p className="mb-0">No challenge history yet</p>
                        </EmptyState>
                    )}

                    {challenges.length > 0 && (
                        <>
                            {challenges.map((challenge) => {
                                const status = getStatus(challenge);
                                return (
                                    <ChallengeItem
                                        key={challenge.id}
                                        $status={status}
                                    >
                                        <ChallengeHeader>
                                            <div>
                                                <LessonName>
                                                    {challenge.lesson_name}
                                                </LessonName>
                                                <BadgeGroup
                                                    style={{
                                                        marginTop: "0.25rem",
                                                    }}
                                                >
                                                    {challenge.is_final && (
                                                        <SmallBadge
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(231, 76, 60, 0.2)",
                                                                color: "#e74c3c",
                                                            }}
                                                        >
                                                            Final
                                                        </SmallBadge>
                                                    )}
                                                    {challenge.is_eol && (
                                                        <SmallBadge
                                                            style={{
                                                                backgroundColor:
                                                                    "rgba(155, 89, 182, 0.2)",
                                                                color: "#9b59b6",
                                                            }}
                                                        >
                                                            EOL
                                                        </SmallBadge>
                                                    )}
                                                </BadgeGroup>
                                            </div>
                                            <StatusBadge $status={status}>
                                                {status === "completed" && (
                                                    <i className="fas fa-check me-1"></i>
                                                )}
                                                {status === "failed" && (
                                                    <i className="fas fa-times me-1"></i>
                                                )}
                                                {status === "expired" && (
                                                    <i className="fas fa-clock me-1"></i>
                                                )}
                                                {status}
                                            </StatusBadge>
                                        </ChallengeHeader>

                                        <ChallengeDetails>
                                            <DetailItem>
                                                <i className="fas fa-play-circle"></i>
                                                <span>
                                                    {formatDateTime(
                                                        challenge.created_at,
                                                    )}
                                                </span>
                                            </DetailItem>
                                            {challenge.completed_at && (
                                                <DetailItem>
                                                    <i className="fas fa-check-circle"></i>
                                                    <span>
                                                        {formatDateTime(
                                                            challenge.completed_at,
                                                        )}
                                                    </span>
                                                </DetailItem>
                                            )}
                                            {challenge.failed_at && (
                                                <DetailItem>
                                                    <i className="fas fa-times-circle"></i>
                                                    <span>
                                                        {formatDateTime(
                                                            challenge.failed_at,
                                                        )}
                                                    </span>
                                                </DetailItem>
                                            )}
                                        </ChallengeDetails>
                                    </ChallengeItem>
                                );
                            })}
                        </>
                    )}
                </HistoryList>
            </CardBody>
        </CollapsibleCard>
    );
};

export default ChallengeHistory;
