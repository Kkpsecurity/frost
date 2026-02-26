import React from "react";

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

const liStyle: React.CSSProperties = {
    backgroundColor: "transparent",
    borderColor: "#34495e",
    color: "#ecf0f1",
    padding: "0.5rem 0",
};

interface CourseDetailsCardProps {
    courseName: string;
    completedCount: number;
    lessonsTotal: number;
}

const CourseDetailsCard: React.FC<CourseDetailsCardProps> = ({
    courseName,
    completedCount,
    lessonsTotal,
}) => (
    <div className="card" style={cardStyle}>
        <div className="card-body">
            <h6 style={{ color: "white", fontWeight: 600 }}>
                <i
                    className="fas fa-book me-2"
                    style={{ color: "#3498db" }}
                ></i>
                Course Details
            </h6>

            <ul
                className="list-group list-group-flush mt-2"
                style={{ backgroundColor: "transparent" }}
            >
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Course</span>
                    <span className="fw-semibold">{courseName}</span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Mode</span>
                    <span className="fw-semibold">Self‑Study (Offline)</span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Progress</span>
                    <span className="fw-semibold">
                        {completedCount} / {lessonsTotal} lessons
                    </span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Remaining</span>
                    <span className="fw-semibold">
                        {Math.max(0, lessonsTotal - completedCount)} lessons
                    </span>
                </li>
            </ul>

            <div className="mt-3" style={{ color: "#95a5a6" }}>
                Choose any lesson from the left sidebar to continue.
            </div>
        </div>
    </div>
);

export default CourseDetailsCard;
