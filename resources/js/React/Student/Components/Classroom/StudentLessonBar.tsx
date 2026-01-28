import React from "react";

interface StudentLessonBarProps {
    lessons: {
        id: number;
        title: string;
        description: string;
        duration_minutes: number;
        order: number;
        status: "incomplete" | "completed" | "active_live" | "active_fstb";
        is_completed: boolean;
        is_active: boolean;
        is_paused?: boolean;
        paused_at?: string | null;
    }[];
}

const StudentLessonBar = ({ lessons }: StudentLessonBarProps) => {
    return (
        <div className="d-flex justify-content-between align-items-center mb-3">
            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                <i className="fas fa-list me-2"></i>
                Course Lessons
            </h6>
            <span className="badge" style={{ backgroundColor: "#3498db" }}>
                {lessons.filter((l) => l.is_completed).length} /{" "}
                {lessons.length}
            </span>
        </div>
    );
};

export default StudentLessonBar;
