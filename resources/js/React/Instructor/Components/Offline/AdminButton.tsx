import React from "react";
import { useUser } from "./useUser";

interface AdminButtonProps {
    onClick?: () => void;
    text?: string;
    icon?: string;
    className?: string;
}

const AdminButton: React.FC<AdminButtonProps> = ({
    onClick,
    text = "Create Test Course",
    icon = "fas fa-plus-circle",
    className = "",
}) => {
    const { isSysAdmin, isLoading } = useUser();

    // Don't render if user is not sys_admin or still loading
    if (isLoading || !isSysAdmin) {
        return null;
    }

    const handleClick = () => {
        if (onClick) {
            onClick();
        }
    };

    return (
        <button
            type="button"
            className={`btn btn-sm ${className}`}
            onClick={handleClick}
            style={{
                backgroundColor: "var(--frost-highlight-color, #fede59)",
                borderColor: "var(--frost-highlight-color, #fede59)",
                color: "var(--frost-primary-color, #212a3e)",
                fontWeight: "500",
                fontSize: "0.875rem",
                padding: "0.375rem 0.75rem",
                borderRadius: "0.375rem",
                transition: "var(--frost-transition-base, 0.2s ease-in-out)",
                boxShadow: "0 1px 3px rgba(0, 0, 0, 0.1)",
            }}
            onMouseEnter={(e) => {
                const target = e.target as HTMLButtonElement;
                target.style.backgroundColor =
                    "var(--frost-info-color, #17aac9)";
                target.style.borderColor = "var(--frost-info-color, #17aac9)";
                target.style.color = "var(--frost-white-color, #ffffff)";
                target.style.transform = "translateY(-1px)";
                target.style.boxShadow = "0 2px 6px rgba(0, 0, 0, 0.15)";
            }}
            onMouseLeave={(e) => {
                const target = e.target as HTMLButtonElement;
                target.style.backgroundColor =
                    "var(--frost-highlight-color, #fede59)";
                target.style.borderColor =
                    "var(--frost-highlight-color, #fede59)";
                target.style.color = "var(--frost-primary-color, #212a3e)";
                target.style.transform = "translateY(0)";
                target.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)";
            }}
        >
            <i className={`${icon} mr-2`}></i> {text}
        </button>
    );
};

export default AdminButton;
