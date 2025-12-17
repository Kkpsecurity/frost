import React from "react";

interface ErrorStateProps {
    error: string;
    title?: string;
}

const ErrorState: React.FC<ErrorStateProps> = ({
    error,
    title = "Failed to Load Courses"
}) => {
    return (
        <div
            className="w-100 rounded p-4"
            style={{
                backgroundColor: "#f3f4f6",
                boxShadow: "var(--frost-shadow-md, 0 4px 6px rgba(0, 0, 0, 0.1))",
                border: "1px solid var(--frost-danger-color, #ef4444)",
            }}
        >
            <div className="text-center">
                <i
                    className="fas fa-exclamation-triangle fa-2x mb-3"
                    style={{
                        color: "var(--frost-danger-color, #ef4444)",
                    }}
                ></i>
                <h5
                    style={{
                        color: "var(--frost-danger-color, #ef4444)",
                    }}
                >
                    {title}
                </h5>
                <p
                    style={{
                        color: "var(--frost-base-color, #6b7280)",
                    }}
                >
                    {error}
                </p>
            </div>
        </div>
    );
};

export default ErrorState;
