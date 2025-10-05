import React from 'react';
import type {
    BulletinBoardData,
    Announcement,
    InstructorResource,
    AvailableCourse,
} from "./useInstructorBulletinBoard";

interface InstructorBulletinBoardProps {
    data: BulletinBoardData;
    onRefresh: () => void;
}

const InstructorBulletinBoard: React.FC<InstructorBulletinBoardProps> = ({
    data,
    onRefresh
}) => {
    const { announcements, available_courses, instructor_resources, quick_stats } = data;

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getAnnouncementIcon = (type: string) => {
        switch (type) {
            case 'urgent': return 'fas fa-exclamation-triangle text-danger';
            case 'training': return 'fas fa-graduation-cap text-info';
            case 'policy': return 'fas fa-file-contract text-warning';
            default: return 'fas fa-bullhorn text-primary';
        }
    };

    const getResourceIcon = (type: string) => {
        switch (type) {
            case 'video': return 'fas fa-play-circle text-danger';
            case 'document': return 'fas fa-file-pdf text-danger';
            case 'link': return 'fas fa-external-link-alt text-info';
            case 'training': return 'fas fa-chalkboard-teacher text-success';
            default: return 'fas fa-file text-secondary';
        }
    };

    return (
        <div className="instructor-bulletin-board">
            {/* Welcome Header */}
            <div className="row mb-4">
                <div className="col-md-12">
                    <div className="card card-primary card-outline">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between">
                                <div className="d-flex align-items-center">
                                    <div className="me-3">
                                        <i className="fas fa-chalkboard-teacher text-primary fa-3x"></i>
                                    </div>
                                    <div>
                                        <h3 className="mb-1 text-primary">Instructor Dashboard</h3>
                                        <p className="text-muted mb-0">
                                            Welcome to your instructor dashboard. Here you'll find announcements, resources, and quick actions.
                                        </p>
                                    </div>
                                </div>
                                <button
                                    className="btn btn-outline-primary"
                                    onClick={onRefresh}
                                    title="Refresh bulletin board"
                                >
                                    <i className="fas fa-sync-alt me-2"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* AdminLTE Info Boxes */}
            <div className="row mb-4">
                <div className="col-md-4">
                    <div className="info-box bg-info">
                        <span className="info-box-icon"><i className="fas fa-users"></i></span>
                        <div className="info-box-content">
                            <span className="info-box-text">Total Instructors</span>
                            <span className="info-box-number">{quick_stats.total_instructors}</span>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="info-box bg-success">
                        <span className="info-box-icon"><i className="fas fa-calendar-day"></i></span>
                        <div className="info-box-content">
                            <span className="info-box-text">Active Courses Today</span>
                            <span className="info-box-number">{quick_stats.active_courses_today}</span>
                        </div>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="info-box bg-warning">
                        <span className="info-box-icon"><i className="fas fa-user-graduate"></i></span>
                        <div className="info-box-content">
                            <span className="info-box-text">Students in System</span>
                            <span className="info-box-number">{quick_stats.students_in_system}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row">
                {/* Announcements */}
                <div className="col-lg-6 mb-4">
                    <div className="card card-primary h-100">
                        <div className="card-header">
                            <div className="d-flex justify-content-between align-items-center">
                                <h3 className="card-title">
                                    <i className="fas fa-bullhorn me-2"></i>
                                    Latest Announcements
                                </h3>
                                <span className="badge badge-light">{announcements.length} items</span>
                            </div>
                        </div>
                        <div className="card-body">
                            {announcements.length === 0 ? (
                                <div className="text-center text-muted py-5">
                                    <i className="fas fa-inbox fa-4x mb-3 opacity-50"></i>
                                    <h6>No announcements at this time</h6>
                                    <p className="mb-0">Check back later for updates</p>
                                </div>
                            ) : (
                                <div className="announcement-list">
                                    {announcements.slice(0, 5).map((announcement) => (
                                        <div key={announcement.id} className="announcement-item border-bottom pb-3 mb-3">
                                            <div className="d-flex align-items-start">
                                                <i className={`${getAnnouncementIcon(announcement.type)} me-3 mt-1`}></i>
                                                <div className="flex-grow-1">
                                                    <h6 className="mb-2 fw-semibold">{announcement.title}</h6>
                                                    <p className="text-muted mb-2" style={{ fontSize: "0.9rem" }}>
                                                        {announcement.content}
                                                    </p>
                                                    <div className="d-flex justify-content-between align-items-center">
                                                        <small className="text-muted">
                                                            <i className="fas fa-user me-1"></i>
                                                            {announcement.author} • {formatDate(announcement.created_at)}
                                                        </small>
                                                        {announcement.expires_at && (
                                                            <small className="text-warning">
                                                                <i className="fas fa-clock me-1"></i>
                                                                Expires {formatDate(announcement.expires_at)}
                                                            </small>
                                                        )}
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

                {/* Resources & Available Courses */}
                <div className="col-lg-6">
                    {/* Instructor Resources */}
                    <div className="card card-success mb-4">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-toolbox me-2"></i>
                                Instructor Resources
                            </h3>
                        </div>
                        <div className="card-body">
                            {instructor_resources.length === 0 ? (
                                <div className="text-center text-muted py-4">
                                    <i className="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                                    <p className="mb-0">No resources available</p>
                                </div>
                            ) : (
                                <div className="resource-list">
                                    {instructor_resources.slice(0, 4).map((resource) => (
                                        <div key={resource.id} className="resource-item d-flex align-items-center mb-3 p-3 border rounded bg-light">
                                            <i className={`${getResourceIcon(resource.type)} me-3 fa-lg`}></i>
                                            <div className="flex-grow-1">
                                                <h6 className="mb-1">
                                                    <a
                                                        href={resource.url}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-decoration-none text-primary"
                                                    >
                                                        {resource.title}
                                                    </a>
                                                </h6>
                                                <p className="text-muted mb-2 small">{resource.description}</p>
                                                <span className="badge badge-secondary small">{resource.category}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Available Courses */}
                    <div className="card card-warning">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-book-open me-2"></i>
                                Available Courses
                            </h3>
                        </div>
                        <div className="card-body">
                            {available_courses.length === 0 ? (
                                <div className="text-center text-muted py-4">
                                    <i className="fas fa-calendar-plus fa-3x mb-3 opacity-50"></i>
                                    <p className="mb-0">No courses available for scheduling</p>
                                </div>
                            ) : (
                                <div className="course-list">
                                    {available_courses.slice(0, 4).map((course) => (
                                        <div key={course.id} className="course-item border rounded p-3 mb-3 bg-light">
                                            <div className="d-flex justify-content-between align-items-start">
                                                <div className="flex-grow-1 me-3">
                                                    <h6 className="mb-2 fw-semibold">{course.title}</h6>
                                                    <p className="text-muted mb-2 small">
                                                        {course.description || 'No description available'}
                                                    </p>
                                                    <div className="d-flex gap-3">
                                                        <small className="text-muted">
                                                            <i className="fas fa-clock me-1"></i>
                                                            {course.total_minutes} min
                                                        </small>
                                                        <small className="text-success fw-semibold">
                                                            <i className="fas fa-dollar-sign me-1"></i>
                                                            {course.price}
                                                        </small>
                                                    </div>
                                                </div>
                                                <span className={`badge ${course.is_active ? 'badge-success' : 'badge-secondary'}`}>
                                                    {course.is_active ? 'Active' : 'Inactive'}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="row mt-4">
                <div className="col-12">
                    <div className="card card-info">
                        <div className="card-header">
                            <h3 className="card-title">
                                <i className="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div className="card-body">
                            <div className="row">
                                <div className="col-md-3 mb-2">
                                    <button className="btn btn-outline-primary w-100">
                                        <i className="fas fa-calendar-plus me-2"></i>
                                        Schedule Course
                                    </button>
                                </div>
                                <div className="col-md-3 mb-2">
                                    <button className="btn btn-outline-success w-100">
                                        <i className="fas fa-book me-2"></i>
                                        View All Courses
                                    </button>
                                </div>
                                <div className="col-md-3 mb-2">
                                    <button className="btn btn-outline-info w-100">
                                        <i className="fas fa-toolbox me-2"></i>
                                        Resources Library
                                    </button>
                                </div>
                                <div className="col-md-3 mb-2">
                                    <button className="btn btn-outline-warning w-100">
                                        <i className="fas fa-user-cog me-2"></i>
                                        Profile Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default InstructorBulletinBoard;
