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

interface StudentProfileCardProps {
    studentDisplayName: string;
    studentEmail: string;
    completedCount: number;
    lessonsTotal: number;
}

const StudentProfileCard: React.FC<StudentProfileCardProps> = ({
    studentDisplayName,
    studentEmail,
    completedCount,
    lessonsTotal,
}) => (
    <div className="card" style={cardStyle}>
        <div className="card-body">
            <h6 style={{ color: "white", fontWeight: 600 }}>
                <i
                    className="fas fa-user me-2"
                    style={{ color: "#9b59b6" }}
                ></i>
                Student Profile
            </h6>

            <ul
                className="list-group list-group-flush mt-2"
                style={{ backgroundColor: "transparent" }}
            >
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Name</span>
                    <span className="fw-semibold">{studentDisplayName}</span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>Email</span>
                    <span className="fw-semibold">{studentEmail}</span>
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
            </ul>

            <div className="mt-3" style={{ color: "#95a5a6" }}>
                Keep your ID and onboarding items up to date so you're ready
                when class is live.
            </div>
        </div>
    </div>
);

export default StudentProfileCard;
