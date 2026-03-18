import React from "react";
import { t } from "@/i18n";

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
                {t("offlineTab.courseDetails")}
            </h6>

            <ul
                className="list-group list-group-flush mt-2"
                style={{ backgroundColor: "transparent" }}
            >
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>{t("offlineTab.labelCourse")}</span>
                    <span className="fw-semibold">{courseName}</span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>{t("offlineTab.labelMode")}</span>
                    <span className="fw-semibold">{t("offlineTab.offlineMode")}</span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>{t("offlineTab.labelProgress")}</span>
                    <span className="fw-semibold">
                        {t("offlineTab.progressValue", { completed: completedCount, total: lessonsTotal })}
                    </span>
                </li>
                <li
                    className="list-group-item d-flex justify-content-between align-items-center"
                    style={liStyle}
                >
                    <span style={{ color: "#95a5a6" }}>{t("offlineTab.labelRemaining")}</span>
                    <span className="fw-semibold">
                        {t("offlineTab.lessonsCount", { count: Math.max(0, lessonsTotal - completedCount) })}
                    </span>
                </li>
            </ul>

            <div className="mt-3" style={{ color: "#95a5a6" }}>
                {t("offlineTab.courseDetailsFooter")}
            </div>
        </div>
    </div>
);

export default CourseDetailsCard;
