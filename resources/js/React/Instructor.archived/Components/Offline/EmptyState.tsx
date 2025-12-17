import React from "react";

interface EmptyStateProps {
    title?: string;
    message?: string;
    icon?: string;
}

const EmptyState: React.FC<EmptyStateProps> = ({
    title = "No Courses Scheduled",
    message = "There are no courses scheduled for today.",
    icon = "fas fa-calendar-times"
}) => {
    return (
        <div
            className="w-100 rounded p-5"
            style={{
                backgroundColor: "#f3f4f6",
                boxShadow: "var(--frost-shadow-md, 0 4px 6px rgba(0, 0, 0, 0.1))",
                border: "1px solid var(--frost-light-primary-color, #e2e8f0)",
            }}
        >
            <div className="text-center">
                <i
                    className={`${icon} fa-3x mb-3`}
                    style={{
                        color: "var(--frost-base-color, #6b7280)",
                    }}
                ></i>
                <h5
                    style={{
                        color: "var(--frost-primary-color, #212a3e)",
                    }}
                >
                    {title}
                </h5>
                <p
                    style={{
                        color: "var(--frost-base-color, #6b7280)",
                    }}
                >
                    {message}
                </p>
            </div>
        </div>
    );
};

export default EmptyState;
