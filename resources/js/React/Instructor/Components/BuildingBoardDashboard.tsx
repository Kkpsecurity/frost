import React from "react";
import { useQuery } from "@tanstack/react-query";

// API endpoints for building board data
const fetchTodaysLessons = async () => {
    const response = await fetch("/admin/instructors/data/lessons/today");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
};

const fetchUpcomingLessons = async () => {
    const response = await fetch("/admin/instructors/data/lessons/upcoming");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
};

const fetchPreviousLessons = async () => {
    const response = await fetch("/admin/instructors/data/lessons/previous");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
};

const fetchOverviewStats = async () => {
    const response = await fetch("/admin/instructors/data/stats/overview");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    return response.json();
};

// Course Card Component for Current/Previous/Upcoming
interface CourseCardProps {
    title: string;
    lesson: any;
    cardType: 'current' | 'previous' | 'upcoming';
    isEmpty: boolean;
}

const CourseCard: React.FC<CourseCardProps> = ({ title, lesson, cardType, isEmpty }) => {
    const cardColors = {
        current: 'bg-success',
        previous: 'bg-secondary', 
        upcoming: 'bg-primary'
    };

    const iconColors = {
        current: 'fas fa-play-circle text-white',
        previous: 'fas fa-check-circle text-white',
        upcoming: 'fas fa-clock text-white'
    };

    if (isEmpty) {
        return (
            <div className="col-md-4 mb-4">
                <div className="card">
                    <div className="card-header d-flex align-items-center">
                        <i className={iconColors[cardType]} style={{ marginRight: '8px' }}></i>
                        <h5 className="mb-0">{title}</h5>
                    </div>
                    <div className="card-body text-center">
                        <div className="py-4">
                            <i className="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                            <p className="text-muted mb-0">
                                {cardType === 'current' && 'No courses for the day'}
                                {cardType === 'previous' && 'No recent courses'}
                                {cardType === 'upcoming' && 'No upcoming courses'}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="col-md-4 mb-4">
            <div className="card">
                <div className="card-header d-flex align-items-center">
                    <i className={iconColors[cardType]} style={{ marginRight: '8px' }}></i>
                    <h5 className="mb-0">{title}</h5>
                </div>
                <div className="card-body">
                    <h6 className="card-title">{lesson.course_name}</h6>
                    <p className="card-subtitle mb-2 text-muted">{lesson.lesson_name}</p>
                    <div className="row">
                        <div className="col-6">
                            <small className="text-muted">Time:</small><br/>
                            <strong>{lesson.time}</strong>
                        </div>
                        <div className="col-6">
                            <small className="text-muted">Students:</small><br/>
                            <strong>{lesson.student_count}</strong>
                        </div>
                    </div>
                    <div className="mt-3">
                        <span className={`badge ${cardColors[cardType]}`}>
                            {lesson.status || cardType}
                        </span>
                    </div>
                    {cardType === 'current' && (
                        <div className="mt-3">
                            <button className="btn btn-success btn-sm mr-2">Take Over</button>
                            <button className="btn btn-outline-success btn-sm">Assist</button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

// Main Building Board Dashboard Component
const BuildingBoardDashboard: React.FC = () => {
    // Fetch all data for building board
    const { data: todaysData, isLoading: todaysLoading } = useQuery({
        queryKey: ['todays-lessons'],
        queryFn: fetchTodaysLessons,
        refetchInterval: 30000 // Refresh every 30 seconds
    });

    const { data: upcomingData, isLoading: upcomingLoading } = useQuery({
        queryKey: ['upcoming-lessons'],
        queryFn: fetchUpcomingLessons,
        refetchInterval: 60000 // Refresh every minute
    });

    const { data: previousData, isLoading: previousLoading } = useQuery({
        queryKey: ['previous-lessons'],
        queryFn: fetchPreviousLessons,
        refetchInterval: 300000 // Refresh every 5 minutes
    });

    const { data: statsData, isLoading: statsLoading } = useQuery({
        queryKey: ['overview-stats'],
        queryFn: fetchOverviewStats,
        refetchInterval: 60000 // Refresh every minute
    });

    const isLoading = todaysLoading || upcomingLoading || previousLoading || statsLoading;

    if (isLoading) {
        return (
            <div className="container-fluid">
                <div className="row">
                    <div className="col-12 text-center p-4">
                        <i className="fas fa-spinner fa-spin fa-2x text-primary"></i>
                        <p className="mt-2"><strong>Loading instructor dashboard...</strong></p>
                    </div>
                </div>
            </div>
        );
    }

    // Extract lessons for cards
    const currentLesson = todaysData?.lessons?.find((l: any) => l.status === 'in-progress') || null;
    const previousLesson = previousData?.lessons?.[0] || null;
    const upcomingLesson = upcomingData?.lessons?.[0] || null;

    return (
        <div className="container-fluid">
            {/* Header */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 className="mb-0">Instructor Building Board</h2>
                            <p className="text-muted mb-0">School activity overview and management</p>
                        </div>
                        <div>
                            <span className="badge badge-info">
                                Last updated: {new Date().toLocaleTimeString()}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {/* Row 1: Course Cards (Current, Previous, Upcoming) */}
            <div className="row mb-4">
                <div className="col-12">
                    <h4 className="mb-3">
                        <i className="fas fa-chalkboard-teacher mr-2"></i>
                        Current Session Status
                    </h4>
                </div>
                <CourseCard 
                    title="Current Class"
                    lesson={currentLesson}
                    cardType="current"
                    isEmpty={!currentLesson}
                />
                <CourseCard 
                    title="Previous Class" 
                    lesson={previousLesson}
                    cardType="previous"
                    isEmpty={!previousLesson}
                />
                <CourseCard 
                    title="Upcoming Class"
                    lesson={upcomingLesson}
                    cardType="upcoming"
                    isEmpty={!upcomingLesson}
                />
            </div>

            {/* Row 2: Today's Lessons Table */}
            <div className="row mb-4">
                <div className="col-12">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">
                                <i className="fas fa-calendar-day mr-2"></i>
                                Today's Lessons
                            </h4>
                        </div>
                        <div className="card-body">
                            {!todaysData?.has_lessons ? (
                                <div className="text-center py-4">
                                    <i className="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 className="text-muted">No courses scheduled for today</h5>
                                    <p className="text-muted">Check the upcoming lessons or previous lessons sections.</p>
                                </div>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Course</th>
                                                <th>Lesson</th>
                                                <th>Students</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {todaysData.lessons.map((lesson: any) => (
                                                <tr key={lesson.id}>
                                                    <td><strong>{lesson.time}</strong></td>
                                                    <td>{lesson.course_name}</td>
                                                    <td>{lesson.lesson_name}</td>
                                                    <td>
                                                        <span className="badge badge-secondary">
                                                            {lesson.student_count}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span className={`badge ${
                                                            lesson.status === 'in-progress' ? 'badge-success' :
                                                            lesson.status === 'completed' ? 'badge-secondary' :
                                                            'badge-primary'
                                                        }`}>
                                                            {lesson.status}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {lesson.status === 'in-progress' && (
                                                            <>
                                                                <button className="btn btn-sm btn-success mr-1">Join</button>
                                                                <button className="btn btn-sm btn-outline-success">Assist</button>
                                                            </>
                                                        )}
                                                        {lesson.status === 'scheduled' && (
                                                            <button className="btn btn-sm btn-primary">Start</button>
                                                        )}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Row 3: Calendar View (Placeholder) */}
            <div className="row mb-4">
                <div className="col-md-8">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">
                                <i className="fas fa-calendar-alt mr-2"></i>
                                Upcoming Calendar
                            </h4>
                        </div>
                        <div className="card-body">
                            {!upcomingData?.has_lessons ? (
                                <div className="text-center py-4">
                                    <i className="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                    <h5 className="text-muted">No upcoming classes</h5>
                                    <p className="text-muted">New course schedules will appear here.</p>
                                </div>
                            ) : (
                                <div className="row">
                                    {upcomingData.lessons.slice(0, 6).map((lesson: any) => (
                                        <div key={lesson.id} className="col-md-6 mb-3">
                                            <div className="card border-left-primary">
                                                <div className="card-body py-2">
                                                    <div className="d-flex justify-content-between">
                                                        <div>
                                                            <h6 className="mb-1">{lesson.course_name}</h6>
                                                            <small className="text-muted">{lesson.date} at {lesson.time}</small>
                                                        </div>
                                                        <div className="text-right">
                                                            <small className="text-muted">
                                                                {lesson.days_until} days
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="card">
                        <div className="card-header">
                            <h4 className="mb-0">
                                <i className="fas fa-history mr-2"></i>
                                Recent Activity
                            </h4>
                        </div>
                        <div className="card-body">
                            <div className="text-center py-4">
                                <i className="fas fa-clock fa-2x text-muted mb-3"></i>
                                <p className="text-muted">Activity feed coming soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Row 4: Overview Stats */}
            <div className="row mb-4">
                <div className="col-12">
                    <h4 className="mb-3">
                        <i className="fas fa-chart-bar mr-2"></i>
                        School Overview
                    </h4>
                </div>
                <div className="col-lg-3 col-6">
                    <div className="small-box bg-info">
                        <div className="inner">
                            <h3>{statsData?.stats?.total_students || 0}</h3>
                            <p>Total Students</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div className="col-lg-3 col-6">
                    <div className="small-box bg-success">
                        <div className="inner">
                            <h3>{statsData?.stats?.active_courses || 0}</h3>
                            <p>Active Courses</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-book"></i>
                        </div>
                    </div>
                </div>
                <div className="col-lg-3 col-6">
                    <div className="small-box bg-warning">
                        <div className="inner">
                            <h3>{upcomingData?.metadata?.count || 0}</h3>
                            <p>Upcoming Classes</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-calendar-plus"></i>
                        </div>
                    </div>
                </div>
                <div className="col-lg-3 col-6">
                    <div className="small-box bg-danger">
                        <div className="inner">
                            <h3>{previousData?.metadata?.count || 0}</h3>
                            <p>Completed This Week</p>
                        </div>
                        <div className="icon">
                            <i className="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BuildingBoardDashboard;