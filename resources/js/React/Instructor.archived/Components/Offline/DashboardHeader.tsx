import React from "react";
import AdminButton from "./AdminButton";

interface DashboardHeaderProps {
    title?: string;
    subtitle?: string;
    onAdminAction?: () => void;
}

const DashboardHeader: React.FC<DashboardHeaderProps> = ({
    title = "Welcome to Florida Online Dashboard",
    subtitle = "Today's lesson board and class overview",
    onAdminAction,
}) => {
    return (
        <div
            className="w-100 border-bottom px-4 py-3 shadow-sm"
            style={{
                backgroundColor: "var(--frost-primary-color, #212a3e)",
                color: "var(--frost-white-color, #ffffff)",
            }}
        >
            <div className="d-flex justify-content-between align-items-center">
                <div>
                    <h2
                        className="h4 mb-1"
                        style={{
                            color: "var(--frost-white-color, #ffffff)",
                        }}
                    >
                        <i
                            className="fas fa-chalkboard-teacher me-2"
                            style={{
                                color: "var(--frost-highlight-color, #fede59)",
                                textShadow: "0 1px 2px rgba(0, 0, 0, 0.1)",
                                marginRight: "8px",
                            }}
                        ></i>
                        {title}
                    </h2>
                    <p
                        className="mb-0 small"
                        style={{
                            color: "var(--frost-light-primary-color, #e2e8f0)",
                        }}
                    >
                        {subtitle}
                    </p>
                </div>
                <div>
                    <AdminButton onClick={onAdminAction} />
                </div>
            </div>
        </div>
    );
};

export default DashboardHeader;
