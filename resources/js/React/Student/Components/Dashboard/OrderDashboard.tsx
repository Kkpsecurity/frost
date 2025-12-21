import React from "react";
import StudentCoursesTable from "../Tables/StudentCoursesTable";

interface OrderDashboardProps {
    data?: any;
    courseAuthId?: number | null;
}

interface Course {
    id: number;
    course_date_id: number;
    course_name: string;
    start_date: string;
    status: string;
    completion_status?: string;
}

/**
 * OrderDashboard - Shows student's purchased courses (orders)
 * This is the default view when visiting /classroom
 * Session expires after 12 hours - returns here on next visit
 */
const OrderDashboard: React.FC<OrderDashboardProps> = ({
    data,
    courseAuthId,
}) => {
    const courses: Course[] = data?.courses || [];
    const progress = data?.progress || {
        total_courses: 0,
        completed: 0,
        in_progress: 0,
    };

    const formatDate = (dateString: string) => {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    };

    return (
        <div
            className="dashboard-area"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                paddingTop: "6rem",
                paddingBottom: "3rem",
            }}
        >
            <div className="container">
                {/* Stats Cards Row */}
                <div className="row mb-5">
                    <div className="col-md-4 mb-3">
                        <div
                            className="card border-0 shadow-sm text-center"
                            style={{
                                background: "#2c3e50",
                                color: "white",
                                padding: "2rem",
                            }}
                        >
                            <i
                                className="fas fa-book mb-3"
                                style={{ fontSize: "3rem", opacity: 0.3 }}
                            ></i>
                            <div
                                style={{
                                    fontSize: "0.75rem",
                                    textTransform: "uppercase",
                                    letterSpacing: "1px",
                                    opacity: 0.7,
                                    marginBottom: "0.5rem",
                                }}
                            >
                                Total Courses
                            </div>
                            <div
                                style={{ fontSize: "3rem", fontWeight: "700" }}
                            >
                                {progress.total_courses}
                            </div>
                        </div>
                    </div>

                    <div className="col-md-4 mb-3">
                        <div
                            className="card border-0 shadow-sm text-center"
                            style={{
                                background: "#17a2b8",
                                color: "white",
                                padding: "2rem",
                            }}
                        >
                            <i
                                className="fas fa-clock mb-3"
                                style={{ fontSize: "3rem", opacity: 0.3 }}
                            ></i>
                            <div
                                style={{
                                    fontSize: "0.75rem",
                                    textTransform: "uppercase",
                                    letterSpacing: "1px",
                                    opacity: 0.7,
                                    marginBottom: "0.5rem",
                                }}
                            >
                                In Progress
                            </div>
                            <div
                                style={{ fontSize: "3rem", fontWeight: "700" }}
                            >
                                {progress.in_progress}
                            </div>
                        </div>
                    </div>

                    <div className="col-md-4 mb-3">
                        <div
                            className="card border-0 shadow-sm text-center"
                            style={{
                                background: "#28a745",
                                color: "white",
                                padding: "2rem",
                            }}
                        >
                            <i
                                className="fas fa-check-circle mb-3"
                                style={{ fontSize: "3rem", opacity: 0.3 }}
                            ></i>
                            <div
                                style={{
                                    fontSize: "0.75rem",
                                    textTransform: "uppercase",
                                    letterSpacing: "1px",
                                    opacity: 0.7,
                                    marginBottom: "0.5rem",
                                }}
                            >
                                Completed
                            </div>
                            <div
                                style={{ fontSize: "3rem", fontWeight: "700" }}
                            >
                                {progress.completed}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Courses Table Section */}
                <div className="row">
                    <div className="col-12">
                        <div
                            className="card border-0 shadow-sm"
                            style={{ backgroundColor: "#2c3e50" }}
                        >
                            <div className="card-body">
                                <h5
                                    className="mb-4"
                                    style={{
                                        fontWeight: "600",
                                        display: "flex",
                                        alignItems: "center",
                                        color: "white",
                                    }}
                                >
                                    <i className="fas fa-graduation-cap me-2"></i>
                                    My Courses
                                </h5>

                                {courses && courses.length > 0 ? (
                                    <div className="table-responsive">
                                        <StudentCoursesTable
                                            courses={courses}
                                            formatDate={formatDate}
                                        />
                                    </div>
                                ) : (
                                    <div className="text-center py-5">
                                        <i
                                            className="fas fa-inbox mb-3"
                                            style={{
                                                fontSize: "4rem",
                                                color: "#dee2e6",
                                            }}
                                        ></i>
                                        <h5 style={{ color: "#6c757d" }}>
                                            No Courses Found
                                        </h5>
                                        <p style={{ color: "#6c757d" }}>
                                            You don't have any courses enrolled
                                            yet. Please contact support if you
                                            believe this is an error.
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default OrderDashboard;
