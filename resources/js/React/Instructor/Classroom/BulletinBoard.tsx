import React from 'react';
import type { BulletinBoardData, Instructor, Announcement, InstructorResource } from '../../types/global';

interface BulletinBoardProps {
  data: BulletinBoardData;
  instructor: Instructor;
  onRefresh: () => void;
}

const BulletinBoard: React.FC<BulletinBoardProps> = ({ data, instructor, onRefresh }) => {
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
    <div className="bulletin-board">
      {/* Header */}
      <div className="row mb-4">
        <div className="col-md-8">
          <div className="d-flex align-items-center">
            <div className="me-3">
              <i className="fas fa-chalkboard-teacher text-primary fs-2"></i>
            </div>
            <div>
              <h3 className="mb-1">Welcome, {instructor.fname}!</h3>
              <p className="text-muted mb-0">
                You don't have any scheduled courses right now. Check out the latest updates and resources below.
              </p>
            </div>
          </div>
        </div>
        <div className="col-md-4 text-md-end">
          <button
            className="btn btn-outline-primary"
            onClick={onRefresh}
            title="Refresh dashboard"
          >
            <i className="fas fa-sync-alt me-2"></i>
            Refresh
          </button>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="row mb-4">
        <div className="col-md-4">
          <div className="card bg-primary text-white">
            <div className="card-body text-center">
              <i className="fas fa-users fa-2x mb-2"></i>
              <h4 className="mb-1">{quick_stats.total_instructors}</h4>
              <small>Total Instructors</small>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card bg-success text-white">
            <div className="card-body text-center">
              <i className="fas fa-calendar-day fa-2x mb-2"></i>
              <h4 className="mb-1">{quick_stats.active_courses_today}</h4>
              <small>Active Courses Today</small>
            </div>
          </div>
        </div>
        <div className="col-md-4">
          <div className="card bg-info text-white">
            <div className="card-body text-center">
              <i className="fas fa-user-graduate fa-2x mb-2"></i>
              <h4 className="mb-1">{quick_stats.students_in_system}</h4>
              <small>Students in System</small>
            </div>
          </div>
        </div>
      </div>

      <div className="row">
        {/* Announcements */}
        <div className="col-lg-6">
          <div className="card h-100">
            <div className="card-header d-flex justify-content-between align-items-center">
              <h5 className="mb-0">
                <i className="fas fa-bullhorn me-2 text-primary"></i>
                Latest Announcements
              </h5>
              <small className="text-muted">{announcements.length} items</small>
            </div>
            <div className="card-body">
              {announcements.length === 0 ? (
                <div className="text-center text-muted py-4">
                  <i className="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                  <p>No announcements at this time</p>
                </div>
              ) : (
                <div className="announcement-list">
                  {announcements.slice(0, 5).map((announcement) => (
                    <div key={announcement.id} className="announcement-item border-bottom pb-3 mb-3">
                      <div className="d-flex align-items-start">
                        <i className={`${getAnnouncementIcon(announcement.type)} me-3 mt-1`}></i>
                        <div className="flex-grow-1">
                          <h6 className="mb-1">{announcement.title}</h6>
                          <p className="text-muted small mb-2">{announcement.content}</p>
                          <div className="d-flex justify-content-between align-items-center">
                            <small className="text-muted">
                              By {announcement.author} • {formatDate(announcement.created_at)}
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
          <div className="card mb-4">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-toolbox me-2 text-success"></i>
                Instructor Resources
              </h5>
            </div>
            <div className="card-body">
              {instructor_resources.length === 0 ? (
                <div className="text-center text-muted py-3">
                  <i className="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                  <p className="mb-0">No resources available</p>
                </div>
              ) : (
                <div className="resource-list">
                  {instructor_resources.slice(0, 4).map((resource) => (
                    <div key={resource.id} className="resource-item d-flex align-items-center mb-3 p-2 border rounded">
                      <i className={`${getResourceIcon(resource.type)} me-3`}></i>
                      <div className="flex-grow-1">
                        <h6 className="mb-1">
                          <a
                            href={resource.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-decoration-none"
                          >
                            {resource.title}
                          </a>
                        </h6>
                        <small className="text-muted">{resource.description}</small>
                        <div className="mt-1">
                          <span className="badge bg-secondary small">{resource.category}</span>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Available Courses */}
          <div className="card">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-book-open me-2 text-warning"></i>
                Available Courses
              </h5>
            </div>
            <div className="card-body">
              {available_courses.length === 0 ? (
                <div className="text-center text-muted py-3">
                  <i className="fas fa-calendar-plus fa-2x mb-2 opacity-50"></i>
                  <p className="mb-0">No courses available for scheduling</p>
                </div>
              ) : (
                <div className="course-list">
                  {available_courses.slice(0, 4).map((course) => (
                    <div key={course.id} className="course-item border rounded p-3 mb-3">
                      <div className="d-flex justify-content-between align-items-start">
                        <div>
                          <h6 className="mb-1">{course.title}</h6>
                          <p className="text-muted small mb-2">
                            {course.description || 'No description available'}
                          </p>
                          <div className="d-flex gap-3">
                            <small className="text-muted">
                              <i className="fas fa-clock me-1"></i>
                              {course.total_minutes} minutes
                            </small>
                            <small className="text-muted">
                              <i className="fas fa-tag me-1"></i>
                              ${course.price}
                            </small>
                          </div>
                        </div>
                        <span className={`badge ${course.is_active ? 'bg-success' : 'bg-secondary'}`}>
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
          <div className="card">
            <div className="card-header">
              <h5 className="mb-0">
                <i className="fas fa-bolt me-2 text-primary"></i>
                Quick Actions
              </h5>
            </div>
            <div className="card-body">
              <div className="row">
                <div className="col-md-3">
                  <a href="/instructor/schedule" className="btn btn-outline-primary w-100 mb-2">
                    <i className="fas fa-calendar-plus me-2"></i>
                    Schedule Course
                  </a>
                </div>
                <div className="col-md-3">
                  <a href="/instructor/courses" className="btn btn-outline-success w-100 mb-2">
                    <i className="fas fa-book me-2"></i>
                    View All Courses
                  </a>
                </div>
                <div className="col-md-3">
                  <a href="/instructor/resources" className="btn btn-outline-info w-100 mb-2">
                    <i className="fas fa-toolbox me-2"></i>
                    Resources Library
                  </a>
                </div>
                <div className="col-md-3">
                  <a href="/instructor/profile" className="btn btn-outline-warning w-100 mb-2">
                    <i className="fas fa-user-cog me-2"></i>
                    Profile Settings
                  </a>
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
