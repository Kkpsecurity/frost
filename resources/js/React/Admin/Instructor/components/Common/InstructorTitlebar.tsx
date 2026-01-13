import { InstructorData } from "@/React/Admin/Instructor/types";
import React from "react";

interface InstructorTitlebarProps {
    instructor?: InstructorData;
    title?: string;
    isAssistant?: boolean;
    onLeaveClass?: () => void;
}

const InstructorTitlebar: React.FC<InstructorTitlebarProps> = ({
    instructor,
    title = "Welcome: Florida Online Bulletin Board",
    isAssistant = false,
    onLeaveClass,
}) => {
    // Get instructor name from various possible property names
    console.log("🏫 InstructorTitlebar: instructor data", instructor);
    console.log("🏫 InstructorTitlebar: isAssistant mode", isAssistant);

    const getInstructorName = (
        instructor: InstructorData | undefined
    ): string => {
        if (!instructor) return isAssistant ? "Assistant" : "Instructor";
        if (instructor.fname && instructor.lname)
            return `${instructor.fname} ${instructor.lname}`;

        return isAssistant ? "Assistant" : "Instructor";
    };
    return (
        <div
            className="w-100 border-bottom shadow-sm"
            style={{
                backgroundColor: "var(--frost-primary-color, #212a3e)",
                color: "var(--frost-white-color, #ffffff)",
                margin: "0",
                padding: "0",
                borderRadius: "0",
            }}
        >
            <div
                className="d-flex justify-content-between align-items-center"
                style={{ padding: "1rem" }}
            >
                <div>
                    <h3
                        className="h4 mb-1"
                        style={{ color: "var(--frost-white-color, #ffffff)" }}
                    >
                        <i
                            className="fas fa-bullhorn mr-1"
                            style={{
                                color: "var(--frost-highlight-color, #fede59)",
                            }}
                        ></i>
                        {title}
                    </h3>
                    <p className="mb-0 text-light opacity-75">
                        <i
                            className={`fas ${
                                isAssistant
                                    ? "fa-hands-helping"
                                    : "fa-chalkboard-teacher"
                            } mr-1`}
                        ></i>
                        {isAssistant ? "Assistant" : "Instructor"}:{" "}
                        <strong>{getInstructorName(instructor)}</strong>
                    </p>
                </div>
                <div className="text-end">
                    {isAssistant && onLeaveClass ? (
                        <button
                            className="btn btn-danger"
                            onClick={onLeaveClass}
                            style={{ padding: "8px 16px", fontSize: "0.9rem" }}
                        >
                            <i className="fas fa-sign-out-alt mr-1"></i>
                            Leave Class
                        </button>
                    ) : (
                        <span
                            className="badge bg-primary"
                            style={{ padding: "8px 12px", fontSize: "0.9rem" }}
                        >
                            <i className="fas fa-calendar-alt mr-1"></i>
                            {new Date().toLocaleDateString("en-US", {
                                weekday: "short",
                                month: "short",
                                day: "numeric",
                            })}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
};

export default InstructorTitlebar;
