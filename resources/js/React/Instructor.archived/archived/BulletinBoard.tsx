import React from 'react';

interface BulletinBoardProps {
    bulletinData: {
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
    };
    onCourseSelect?: (course: any) => void;
}

const BulletinBoard: React.FC<BulletinBoardProps> = ({ bulletinData, onCourseSelect }) => {
    if (!bulletinData?.bulletin_board) {
        return (
            <div className="bulletin-board">
                <div className="alert alert-info">
                    <i className="fas fa-info-circle mr-2"></i>
                    No bulletin board data available
                </div>
            </div>
        );
    }

    const { stats, upcoming_classes, charts } = bulletinData.bulletin_board;

    // Handle course selection for teaching
    const handleStartTeaching = (classItem: any) => {
        if (onCourseSelect) {
            const courseData = {
                id: classItem.id,
                name: `Course Unit ${classItem.course_unit_id}`,
                course_unit_id: classItem.course_unit_id,
                starts_at: classItem.starts_at,
                ends_at: classItem.ends_at,
                formatted_date: classItem.formatted_date
            };
            onCourseSelect(courseData);
        }
    };

    return (
        <div className="bulletin-board">
            {/* Course Date Statistics Cards */}
            <div className="row mb-4">
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-info">
                            <i className="far fa-calendar-alt"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">Total Course Dates</span>
                            <span className="info-box-number">{stats.total_course_dates}</span>
                        </div>
                    </div>
                </div>
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-success">
                            <i className="far fa-calendar-check"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">Active Courses</span>
                            <span className="info-box-number">{stats.active_course_dates}</span>
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
                            <span className="info-box-number">{stats.upcoming_course_dates}</span>
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
                            <span className="info-box-number">{stats.this_week_course_dates}</span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Upcoming Classes Table */}
            {upcoming_classes?.length > 0 && (
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
                                                <th>Course Unit</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Formatted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {upcoming_classes.map((classItem) => (
                                                <tr key={classItem.id}>
                                                    <td>
                                                        <strong>Unit {classItem.course_unit_id}</strong>
                                                    </td>
                                                    <td>{classItem.starts_at}</td>
                                                    <td>{classItem.ends_at}</td>
                                                    <td>
                                                        <span className="badge badge-info">
                                                            {classItem.formatted_date}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button
                                                            onClick={() => handleStartTeaching(classItem)}
                                                            className="btn btn-sm btn-success"
                                                            type="button"
                                                        >
                                                            <i className="fas fa-play me-1"></i>
                                                            Select Course
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Statistics Charts */}
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
                                {charts.course_activity.labels.map((label, index) => (
                                    <div key={label} className="progress-group">
                                        <span className="float-right">
                                            <b>{charts.course_activity.data[index]}</b>
                                        </span>
                                        <span className="progress-text">{label}</span>
                                        <div className="progress progress-sm">
                                            <div
                                                className="progress-bar bg-primary"
                                                style={{
                                                    width: `${
                                                        stats.total_course_dates > 0
                                                            ? (charts.course_activity.data[index] / stats.total_course_dates) * 100
                                                            : 0
                                                    }%`,
                                                }}
                                            ></div>
                                        </div>
                                    </div>
                                ))}
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
                                    <span className="info-box-text">Active Percentage</span>
                                    <span className="info-box-number">
                                        {stats.total_course_dates > 0
                                            ? Math.round((stats.active_course_dates / stats.total_course_dates) * 100)
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
                                    <span className="info-box-text">This Month</span>
                                    <span className="info-box-number">{stats.this_month_course_dates}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BulletinBoard;
