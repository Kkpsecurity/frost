import React from "react";
import { SchoolDashboardTitleBarProps } from "../types/props/classroom.props";
import DynamicTitleBar from "@/React/Components/Widgets/DynamicTitleBar";

const SchoolDashboardTitleBar = ({
    title,
    subtitle,
    icon,
    onBackToDashboard,
}: SchoolDashboardTitleBarProps) => {
    return (
        <div
            className="section-title"
            style={{
                background:
                    "linear-gradient(135deg, var(--frost-primary-color), var(--frost-secondary-color))",
                color: "white",
                padding: "20px 30px",
                boxShadow: "0 4px 15px rgba(0,0,0,0.1)",
            }}
        >
            <DynamicTitleBar
                title={title}
                subtitle={subtitle}
                icon={icon}
                onBackToDashboard={onBackToDashboard}
            />
        </div>
    );
};

export default SchoolDashboardTitleBar;

