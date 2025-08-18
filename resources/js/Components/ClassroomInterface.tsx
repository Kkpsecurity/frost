import React, { useEffect, useState } from 'react';
import { useQuery } from '@tanstack/react-query';

interface BulletinBoardData {
    totalCourseDates: number;
    activeCourseDates: number;
    upcomingCourseDates: number;
    weeklyBreakdown: Array<{ week_start: string; count: number }>;
    monthlyBreakdown: Array<{ month: string; count: number }>;
    upcomingClasses: Array<{
        id: number;
        course_code: string;
        course_name: string;
        session_date: string;
        start_time: string;
        end_time: string;
        location: string;
        instructor_name: string;
    }>;
    recentHistory: Array<{
        id: number;
        course_code: string;
        course_name: string;
        session_date: string;
        start_time: string;
        end_time: string;
        location: string;
        instructor_name: string;
    }>;
}

// API function to fetch bulletin board data
const fetchBulletinBoardData = async (): Promise<BulletinBoardData> => {
    const response = await fetch('/admin/instructors/api/bulletin-board', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin'
    });

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    return response.json();
};

export const ClassroomInterface: React.FC = () => {
    const {
        data: bulletinData,
        isLoading,
        error,
        refetch
    } = useQuery<BulletinBoardData>({
        queryKey: ['bulletinBoard'],
        queryFn: fetchBulletinBoardData,
        staleTime: 5 * 60 * 1000, // 5 minutes
        cacheTime: 10 * 60 * 1000, // 10 minutes
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
                    <p>Failed to load classroom data: {(error as Error).message}</p>
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
                <div className="alert alert-warning m-3">
                    No data available
                </div>
            </div>
        );
    }

    return (
        <div className="classroom-interface">
            {/* Course Date Statistics */}
            <div className="row mb-4">
                <div className="col-md-3">
                    <div className="info-box">
                        <span className="info-box-icon bg-info">
                            <i className="far fa-calendar-alt"></i>
                        </span>
                        <div className="info-box-content">
                            <span className="info-box-text">Total Course Dates</span>
                            <span className="info-box-number">{bulletinData.totalCourseDates}</span>
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
                            <span className="info-box-number">{bulletinData.activeCourseDates}</span>
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
                            <span className="info-box-number">{bulletinData.upcomingCourseDates}</span>
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
                                {bulletinData.weeklyBreakdown?.[0]?.count || 0}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Upcoming Classes */}
            {bulletinData.upcomingClasses?.length > 0 && (
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
                                                <th>Course</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Location</th>
                                                <th>Instructor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {bulletinData.upcomingClasses.map((class_item) => (
                                                <tr key={class_item.id}>
                                                    <td>
                                                        <strong>{class_item.course_code}</strong>
                                                        <br />
                                                        <small className="text-muted">
                                                            {class_item.course_name}
                                                        </small>
                                                    </td>
                                                    <td>{class_item.session_date}</td>
                                                    <td>{class_item.start_time} - {class_item.end_time}</td>
                                                    <td>{class_item.location}</td>
                                                    <td>{class_item.instructor_name}</td>
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

            {/* Monthly Breakdown Chart */}
            {bulletinData.monthlyBreakdown?.length > 0 && (
                <div className="row">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header">
                                <h3 className="card-title">
                                    <i className="fas fa-chart-bar mr-2"></i>
                                    Monthly Distribution
                                </h3>
                            </div>
                            <div className="card-body">
                                <div className="chart-container">
                                    {bulletinData.monthlyBreakdown.map((month) => (
                                        <div key={month.month} className="progress-group">
                                            <span className="float-right">
                                                <b>{month.count}</b>/
                                                {bulletinData.totalCourseDates}
                                            </span>
                                            <span className="progress-text">{month.month}</span>
                                            <div className="progress progress-sm">
                                                <div
                                                    className="progress-bar bg-primary"
                                                    style={{
                                                        width: `${(month.count / bulletinData.totalCourseDates) * 100}%`
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
                                        <span className="info-box-text">Course Completion Rate</span>
                                        <span className="info-box-number">
                                            {bulletinData.totalCourseDates > 0
                                                ? Math.round((bulletinData.activeCourseDates / bulletinData.totalCourseDates) * 100)
                                                : 0
                                            }%
                                        </span>
                                    </div>
                                </div>
                                <div className="info-box">
                                    <span className="info-box-icon bg-info elevation-1">
                                        <i className="fas fa-calendar-week"></i>
                                    </span>
                                    <div className="info-box-content">
                                        <span className="info-box-text">Average per Week</span>
                                        <span className="info-box-number">
                                            {bulletinData.weeklyBreakdown?.length > 0
                                                ? Math.round(
                                                    bulletinData.weeklyBreakdown.reduce((sum, week) => sum + week.count, 0) /
                                                    bulletinData.weeklyBreakdown.length
                                                  )
                                                : 0
                                            }
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ClassroomInterface;
