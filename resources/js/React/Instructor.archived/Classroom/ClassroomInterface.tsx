import React from 'react';
import { useQuery } from "@tanstack/react-query";

interface BulletinBoardData {
    bulletin_board: {
        stats: {
            total_course_dates: number;
            active_course_dates: number;
            upcoming_course_dates: number;
            this_week_course_dates: number;
            this_month_course_dates: number;
        };
        upcoming_classes: Array<{
            id: number;
            course_unit_id: number;
            starts_at: string;
            ends_at: string;
            formatted_date: string;
        }>;
        recent_history: Array<{
            id: number;
            course_unit_id: number;
            starts_at: string;
            ends_at: string;
            formatted_date: string;
        }>;
        charts: {
            course_activity: {
                labels: string[];
                data: number[];
            };
        };
    };
    metadata: {
        generated_at: string;
        view_type: string;
        has_course_dates: boolean;
    };
}

// API function to fetch bulletin board data
const fetchBulletinBoardData = async (): Promise<BulletinBoardData> => {
    console.log(
        "🔄 Fetching bulletin board data from /admin/instructors/data/bulletin-board"
    );

    const response = await fetch("/admin/instructors/data/bulletin-board", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
        credentials: "same-origin",
    });

    console.log("📡 Response status:", response.status);

    if (!response.ok) {
        console.error("❌ HTTP error:", response.status, response.statusText);
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    console.log("✅ Bulletin board data received:", data);
    return data;
};

const ClassroomInterface: React.FC = () => {
    const {
        data: bulletinData,
        isLoading,
        error,
        refetch,
    } = useQuery<BulletinBoardData>({
        queryKey: ["bulletinBoard"],
        queryFn: fetchBulletinBoardData,
        staleTime: 5 * 60 * 1000, // 5 minutes
        gcTime: 10 * 60 * 1000, // 10 minutes
        retry: 3,
        retryDelay: 1000,
    });

    if (isLoading) {
        return (
            <div className="classroom-interface">
                <div className="d-flex justify-content-center p-4">
                    <div className="spinner-border text-primary" role="status">
                        <span className="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="classroom-interface">
                <div className="alert alert-danger m-3">
                    <h5>Error Loading Dashboard</h5>
                    <p>
                        Failed to load classroom data:{" "}
                        {(error as Error).message}
                    </p>
                    <button
                        className="btn btn-outline-danger btn-sm"
                        onClick={() => refetch()}
                    >
                        Retry
                    </button>
                </div>
            </div>
        );
    }

    if (!bulletinData) {
        return (
            <div className="classroom-interface">
                <div className="alert alert-warning m-3">No data available</div>
            </div>
        );
    }

    return (
        <div className="classroom-interface">
            {/* Debug Info - Show Raw API Response */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header">
                            <h5 className="card-title mb-0">
                                <i className="fas fa-bug mr-2"></i>
                                Debug: API Response Data
                            </h5>
                        </div>
                        <div className="card-body">
                            <pre
                                style={{
                                    fontSize: "12px",
                                    maxHeight: "300px",
                                    overflow: "auto",
                                    backgroundColor: "#f8f9fa",
                                }}
                            >
                                {JSON.stringify(bulletinData, null, 2)}
                            </pre>
                        </div>
                    </div>
                </div>
            </div>

            {/* Course Date Statistics */}
            <div className="row mb-4">
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-info">
                            <i className="far fa-calendar-alt"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">
                                Total Course Dates
                            </span>
                            <span className="info-box-number">
                                {
                                    bulletinData.bulletin_board.stats
                                        .total_course_dates
                                }
                            </span>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-success">
                            <i className="far fa-calendar-check"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">
                                Active Courses
                            </span>
                            <span className="info-box-number">
                                {
                                    bulletinData.bulletin_board.stats
                                        .active_course_dates
                                }
                            </span>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-warning">
                            <i className="far fa-clock"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">Upcoming</span>
                            <span className="info-box-number">
                                {
                                    bulletinData.bulletin_board.stats
                                        .upcoming_course_dates
                                }
                            </span>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-primary">
                            <i className="fas fa-chart-line"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">This Week</span>
                            <span className="info-box-number">
                                {
                                    bulletinData.bulletin_board.stats
                                        .this_week_course_dates
                                }
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Upcoming Classes */}
            {bulletinData.bulletin_board.upcoming_classes?.length > 0 && (
                <div className="row mb-4">
                    <div className="col-12">
                        <div className="card">
                            <div className="card-header">
                                <h3 className="card-title">
                                    <i className="fas fa-calendar-plus mr-2"></i>
                                    Upcoming Classes
                                </h3>
                            </div>
                            <div className="card-body">
                                <div className="table-responsive">
                                    <table className="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Course Unit ID</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Formatted Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {bulletinData.bulletin_board.upcoming_classes.map(
                                                (class_item) => (
                                                    <tr key={class_item.id}>
                                                        <td>
                                                            <strong>
                                                                Unit{" "}
                                                                {
                                                                    class_item.course_unit_id
                                                                }
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            {
                                                                class_item.starts_at
                                                            }
                                                        </td>
                                                        <td>
                                                            {class_item.ends_at}
                                                        </td>
                                                        <td>
                                                            {
                                                                class_item.formatted_date
                                                            }
                                                        </td>
                                                    </tr>
                                                )
                                            )}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Course Activity Chart */}
            <div className="row">
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-chart-bar mr-2"></i>
                                Course Statistics
                            </h3>
                        </div>
                        <div className="card-body">
                            <div className="chart-container">
                                {bulletinData.bulletin_board.charts.course_activity.labels.map(
                                    (label, index) => (
                                        <div
                                            key={label}
                                            className="progress-group"
                                        >
                                            <span className="float-right">
                                                <b>
                                                    {
                                                        bulletinData
                                                            .bulletin_board
                                                            .charts
                                                            .course_activity
                                                            .data[index]
                                                    }
                                                </b>
                                            </span>
                                            <span className="progress-text">
                                                {label}
                                            </span>
                                            <div className="progress progress-sm">
                                                <div
                                                    className="progress-bar bg-primary"
                                                    style={{
                                                        width: `${
                                                            (bulletinData
                                                                .bulletin_board
                                                                .charts
                                                                .course_activity
                                                                .data[index] /
                                                                bulletinData
                                                                    .bulletin_board
                                                                    .stats
                                                                    .total_course_dates) *
                                                            100
                                                        }%`,
                                                    }}
                                                ></div>
                                            </div>
                                        </div>
                                    )
                                )}
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="card">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-info-circle mr-2"></i>
                                Quick Stats
                            </h3>
                        </div>
                        <div className="card-body">
                            <div className="info-box mb-3">
                                <span className="info-box-icon bg-success elevation-1">
                                    <i className="fas fa-percentage"></i>
                                </span>
                                <div className="info-box-content">
                                    <span className="info-box-text">
                                        Active Percentage
                                    </span>
                                    <span className="info-box-number">
                                        {bulletinData.bulletin_board.stats
                                            .total_course_dates > 0
                                            ? Math.round(
                                                  (bulletinData.bulletin_board
                                                      .stats
                                                      .active_course_dates /
                                                      bulletinData
                                                          .bulletin_board.stats
                                                          .total_course_dates) *
                                                      100
                                              )
                                            : 0}
                                        %
                                    </span>
                                </div>
                            </div>
                            <div className="info-box">
                                <span className="info-box-icon bg-info elevation-1">
                                    <i className="fas fa-calendar-week"></i>
                                </span>
                                <div className="info-box-content">
                                    <span className="info-box-text">
                                        This Month
                                    </span>
                                    <span className="info-box-number">
                                        {
                                            bulletinData.bulletin_board.stats
                                                .this_month_course_dates
                                        }
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ClassroomInterface;
