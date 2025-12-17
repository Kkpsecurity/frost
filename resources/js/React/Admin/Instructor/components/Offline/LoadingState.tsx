import React from "react";

interface LoadingStateProps {
    message?: string;
}

const LoadingState: React.FC<LoadingStateProps> = ({
    message = "Loading today's courses..."
}) => {
    return (
        <div
            className="w-100 h-100 rounded d-flex align-items-center justify-content-center"
            style={{
                backgroundColor: "#f3f4f6",
                boxShadow: "var(--frost-shadow-md, 0 4px 6px rgba(0, 0, 0, 0.1))",
            }}
        >
            <div className="text-center">
                <i
                    className="fas fa-spinner fa-spin fa-2x mb-3"
                    style={{
                        color: "var(--frost-info-color, #17aac9)",
                    }}
                ></i>
                <h5
                    style={{
                        color: "var(--frost-base-color, #6b7280)",
                    }}
                >
                    {message}
                </h5>
            </div>
        </div>
    );
};

export default LoadingState;
