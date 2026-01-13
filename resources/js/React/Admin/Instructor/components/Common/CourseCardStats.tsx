import React from "react";

interface CourseCardStatsProps {
    lessonCount: number;
    studentCount: number;
    time: string | null;
}

const CourseCardStats: React.FC<CourseCardStatsProps> = ({ 
    lessonCount = 0, 
    studentCount = 0, 
    time 
}) => {
    return (
        <div
            className="row text-center g-0 mb-3 rounded p-2"
            style={{ background: "#2b3743", color: "#e5e7eb" }}
        >
            <div className="col-4">
                <div className="py-1">
                    <div className="fw-semibold h4 text-white mb-0">
                        {lessonCount}
                    </div>
                    <div className="small text-secondary">
                        Lesson
                        {lessonCount === 1 ? "" : "s"}
                    </div>
                </div>
            </div>
            <div className="col-4 border-start border-end">
                <div className="py-1">
                    <div className="fw-semibold h4 text-white mb-0">
                        {studentCount}
                    </div>
                    <div className="small text-secondary">
                        Student
                        {studentCount === 1 ? "" : "s"}
                    </div>
                </div>
            </div>
            <div className="col-4">
                <div className="py-1">
                    <div className="fw-semibold h5 text-white mb-0">
                        {time || "—"}
                    </div>
                    <div className="small text-secondary">Start</div>
                </div>
            </div>
        </div>
    );
};

export default CourseCardStats;
