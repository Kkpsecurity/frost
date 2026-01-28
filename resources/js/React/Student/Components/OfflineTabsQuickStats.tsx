import React from "react";

const OfflineTabsQuickStats = () => {
    return (
        <div className="row g-3 mb-4">
            <div className="col-md-3">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "none",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body text-center">
                        <div
                            style={{
                                fontSize: "2rem",
                                color: "#3498db",
                                marginBottom: "0.5rem",
                            }}
                        >
                            <i className="fas fa-book-open"></i>
                        </div>
                        <h3
                            className="mb-0"
                            style={{
                                color: "white",
                                fontSize: "2rem",
                            }}
                        >
                            {lessons.filter((l) => l.is_completed).length}/
                            {lessons.length}
                        </h3>
                        <p
                            className="mb-0"
                            style={{
                                color: "#95a5a6",
                                fontSize: "0.875rem",
                            }}
                        >
                            Lessons Complete
                        </p>
                    </div>
                </div>
            </div>
            <div className="col-md-3">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "none",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body text-center">
                        <div
                            style={{
                                fontSize: "2rem",
                                color: "#2ecc71",
                                marginBottom: "0.5rem",
                            }}
                        >
                            <i className="fas fa-chart-line"></i>
                        </div>
                        <h3
                            className="mb-0"
                            style={{
                                color: "white",
                                fontSize: "2rem",
                            }}
                        >
                            {lessons.length > 0
                                ? Math.round(
                                      (lessons.filter((l) => l.is_completed)
                                          .length /
                                          lessons.length) *
                                          100,
                                  )
                                : 0}
                            %
                        </h3>
                        <p
                            className="mb-0"
                            style={{
                                color: "#95a5a6",
                                fontSize: "0.875rem",
                            }}
                        >
                            Progress
                        </p>
                    </div>
                </div>
            </div>
            <div className="col-md-3">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "none",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body text-center">
                        <div
                            style={{
                                fontSize: "2rem",
                                color: "#f39c12",
                                marginBottom: "0.5rem",
                            }}
                        >
                            <i className="fas fa-clock"></i>
                        </div>
                        <h3
                            className="mb-0"
                            style={{
                                color: "white",
                                fontSize: "2rem",
                            }}
                        >
                            {lessons.reduce(
                                (sum, l) => sum + (l.duration_minutes || 0),
                                0,
                            )}
                        </h3>
                        <p
                            className="mb-0"
                            style={{
                                color: "#95a5a6",
                                fontSize: "0.875rem",
                            }}
                        >
                            Total Minutes
                        </p>
                    </div>
                </div>
            </div>
            <div className="col-md-3">
                <div
                    className="card"
                    style={{
                        backgroundColor: "#2c3e50",
                        border: "none",
                        borderRadius: "0.5rem",
                    }}
                >
                    <div className="card-body text-center">
                        <div
                            style={{
                                fontSize: "2rem",
                                color: "#e74c3c",
                                marginBottom: "0.5rem",
                            }}
                        >
                            <i className="fas fa-tasks"></i>
                        </div>
                        <h3
                            className="mb-0"
                            style={{
                                color: "white",
                                fontSize: "2rem",
                            }}
                        >
                            {lessons.filter((l) => !l.is_completed).length}
                        </h3>
                        <p
                            className="mb-0"
                            style={{
                                color: "#95a5a6",
                                fontSize: "0.875rem",
                            }}
                        >
                            Remaining
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default OfflineTabsQuickStats;
