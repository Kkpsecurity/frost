import React from "react";
import OfflineTabsQuickStats from "../OfflineTabsQuickStats";

type OfflineLessonLike = {
    is_completed?: boolean;
    duration_minutes?: number;
};

interface TabDetailsProps {
    lessons: OfflineLessonLike[];
}

const TabDetails: React.FC<TabDetailsProps> = ({ lessons }) => (
    <div className="details-tab">
        <h4
            className="mb-4"
            style={{ color: "white", fontSize: "1.75rem", fontWeight: "600" }}
        >
            <i
                className="fas fa-tachometer-alt me-2"
                style={{ color: "#3498db" }}
            ></i>
            Learning Dashboard
        </h4>

        <p className="mb-4" style={{ color: "#95a5a6" }}>
            Course overview, progress stats, and what to do next.
        </p>

        <OfflineTabsQuickStats lessons={lessons} />

        <div className="row g-3">
            <div className="col-12 col-lg-7">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "1px solid #34495e",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body">
                        <h6 style={{ color: "white", fontWeight: 600 }}>
                            <i
                                className="fas fa-info-circle me-2"
                                style={{ color: "#3498db" }}
                            ></i>
                            How Self‑Study Works
                        </h6>
                        <ul className="mb-0" style={{ color: "#ecf0f1" }}>
                            <li>Use the left sidebar to choose any lesson.</li>
                            <li>
                                Completed lessons will show as completed in your
                                progress.
                            </li>
                            <li>
                                If you’re preparing for a retake, rewatch the
                                lesson and recheck the materials.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div className="col-12 col-lg-5">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "1px solid #34495e",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body">
                        <h6 style={{ color: "white", fontWeight: 600 }}>
                            <i
                                className="fas fa-route me-2"
                                style={{ color: "#2ecc71" }}
                            ></i>
                            Recommended Next Step
                        </h6>
                        <p className="mb-0" style={{ color: "#95a5a6" }}>
                            Pick the next incomplete lesson from the sidebar and
                            start there.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
);

export default TabDetails;
