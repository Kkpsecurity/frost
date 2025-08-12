import React, { useState } from 'react';
import type { CourseDate, Instructor } from '../../types/global';

interface ClassDashboardProps {
  activeCourses: CourseDate[];
  upcomingCourses: CourseDate[];
  recentCourses: CourseDate[];
  instructor: Instructor;
  onRefresh: () => void;
}

const ClassDashboard: React.FC<ClassDashboardProps> = ({
  activeCourses,
  upcomingCourses,
  recentCourses,
  instructor,
  onRefresh
}) => {
  const [activeTab, setActiveTab] = useState<'today' | 'upcoming' | 'recent'>('today');

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const formatTime = (dateString: string) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getStatusBadge = (course: CourseDate) => {
    const now = new Date();
    const startTime = new Date(course.starts_at);
    const endTime = new Date(course.ends_at);

    if (now >= startTime && now <= endTime) {
      return <span className="badge bg-success">In Progress</span>;
    } else if (now < startTime) {
      return <span className="badge bg-warning">Upcoming</span>;
    } else {
      return <span className="badge bg-secondary">Completed</span>;
    }
  };

  const renderCourseCard = (course: CourseDate, showActions = true) => (
    <div key={course.id} className="course-card card mb-3">
      <div className="card-body">
        <div className="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h5 className="card-title mb-1">{course.course.title}</h5>
            <h6 className="card-subtitle text-muted">{course.course_unit.title}</h6>
          </div>
          {getStatusBadge(course)}
        </div>

        <div className="row mb-3">
          <div className="col-sm-6">
            <small className="text-muted d-block">
              <i className="fas fa-calendar me-1"></i>
              {formatDate(course.starts_at)}
            </small>
            <small className="text-muted d-block">
              <i className="fas fa-clock me-1"></i>
              {formatTime(course.starts_at)} - {formatTime(course.ends_at)}
            </small>
          </div>
          <div className="col-sm-6">
            <small className="text-muted d-block">
              <i className="fas fa-users me-1"></i>
              {course.student_count} Student{course.student_count !== 1 ? 's' : ''}
            </small>
            <small className="text-muted d-block">
              <i className="fas fa-check-circle me-1"></i>
              {course.attendance_records?.length || 0} Attendance Records
            </small>
          </div>
        </div>

        {course.notes && (
          <div className="mb-3">
            <small className="text-muted">
              <i className="fas fa-sticky-note me-1"></i>
              <strong>Notes:</strong> {course.notes}
            </small>
          </div>
        )}

        {showActions && (
          <div className="d-flex gap-2 flex-wrap">
            <a
              href={`/instructor/courses/${course.id}`}
              className="btn btn-primary btn-sm"
            >
              <i className="fas fa-chalkboard me-1"></i>
              Enter Class
            </a>
            <a
              href={`/instructor/courses/${course.id}/attendance`}
              className="btn btn-outline-secondary btn-sm"
            >
              <i className="fas fa-user-check me-1"></i>
              Attendance
            </a>
            <a
              href={`/instructor/courses/${course.id}/students`}
              className="btn btn-outline-info btn-sm"
            >
              <i className="fas fa-users me-1"></i>
              Students
            </a>
            <button
              className="btn btn-outline-warning btn-sm"
              onClick={() => {
                // TODO: Implement quick notes/messaging
                alert('Quick notes feature coming soon!');
              }}
            >
              <i className="fas fa-comment me-1"></i>
              Notes
            </button>
          </div>
        )}
      </div>
    </div>
  );

  const getTabCounts = () => ({
    today: activeCourses.length,
    upcoming: upcomingCourses.length,
    recent: recentCourses.length
  });

  const tabCounts = getTabCounts();

  return (
    <div className="class-dashboard">
      {/* Header */}
      <div className="row mb-4">
        <div className="col-md-8">
          <div className="d-flex align-items-center">
            <div className="me-3">
              <i className="fas fa-chalkboard text-success fs-2"></i>
            </div>
            <div>
              <h3 className="mb-1">Welcome back, {instructor.fname}!</h3>
              <p className="text-muted mb-0">
                You have {activeCourses.length} active course{activeCourses.length !== 1 ? 's' : ''} today
                {upcomingCourses.length > 0 && ` and ${upcomingCourses.length} upcoming`}.
              </p>
            </div>
          </div>
        </div>
        <div className="col-md-4 text-md-end">
          <button
            className="btn btn-outline-primary me-2"
            onClick={onRefresh}
            title="Refresh dashboard"
          >
            <i className="fas fa-sync-alt me-2"></i>
            Refresh
          </button>
          <a href="/instructor/schedule" className="btn btn-primary">
            <i className="fas fa-plus me-2"></i>
            Schedule Course
          </a>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="row mb-4">
        <div className="col-md-3">
          <div className="card bg-primary text-white">
            <div className="card-body text-center">
              <i className="fas fa-play-circle fa-2x mb-2"></i>
              <h4 className="mb-1">{activeCourses.length}</h4>
              <small>Active Today</small>
            </div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="card bg-warning text-white">
            <div className="card-body text-center">
              <i className="fas fa-clock fa-2x mb-2"></i>
              <h4 className="mb-1">{upcomingCourses.length}</h4>
              <small>Upcoming</small>
            </div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="card bg-success text-white">
            <div className="card-body text-center">
              <i className="fas fa-check-circle fa-2x mb-2"></i>
              <h4 className="mb-1">{recentCourses.length}</h4>
              <small>Recently Completed</small>
            </div>
          </div>
        </div>
        <div className="col-md-3">
          <div className="card bg-info text-white">
            <div className="card-body text-center">
              <i className="fas fa-users fa-2x mb-2"></i>
              <h4 className="mb-1">
                {[...activeCourses, ...upcomingCourses].reduce((total, course) => total + course.student_count, 0)}
              </h4>
              <small>Total Students</small>
            </div>
          </div>
        </div>
      </div>

      {/* Course Tabs */}
      <div className="card">
        <div className="card-header p-0">
          <ul className="nav nav-tabs card-header-tabs">
            <li className="nav-item">
              <button
                className={`nav-link ${activeTab === 'today' ? 'active' : ''}`}
                onClick={() => setActiveTab('today')}
              >
                <i className="fas fa-calendar-day me-2"></i>
                Today's Classes
                {tabCounts.today > 0 && (
                  <span className="badge bg-primary ms-2">{tabCounts.today}</span>
                )}
              </button>
            </li>
            <li className="nav-item">
              <button
                className={`nav-link ${activeTab === 'upcoming' ? 'active' : ''}`}
                onClick={() => setActiveTab('upcoming')}
              >
                <i className="fas fa-clock me-2"></i>
                Upcoming Classes
                {tabCounts.upcoming > 0 && (
                  <span className="badge bg-warning ms-2">{tabCounts.upcoming}</span>
                )}
              </button>
            </li>
            <li className="nav-item">
              <button
                className={`nav-link ${activeTab === 'recent' ? 'active' : ''}`}
                onClick={() => setActiveTab('recent')}
              >
                <i className="fas fa-history me-2"></i>
                Recent Classes
                {tabCounts.recent > 0 && (
                  <span className="badge bg-secondary ms-2">{tabCounts.recent}</span>
                )}
              </button>
            </li>
          </ul>
        </div>
        <div className="card-body">
          {/* Today's Classes */}
          {activeTab === 'today' && (
            <div className="today-classes">
              {activeCourses.length === 0 ? (
                <div className="text-center text-muted py-5">
                  <i className="fas fa-calendar-check fa-3x mb-3 opacity-50"></i>
                  <h5>No Classes Scheduled Today</h5>
                  <p>You don't have any classes scheduled for today. Enjoy your day off!</p>
                  <a href="/instructor/schedule" className="btn btn-primary">
                    <i className="fas fa-plus me-2"></i>
                    Schedule a Course
                  </a>
                </div>
              ) : (
                <div className="courses-list">
                  <div className="row">
                    {activeCourses.map((course) => (
                      <div key={course.id} className="col-lg-6">
                        {renderCourseCard(course)}
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Upcoming Classes */}
          {activeTab === 'upcoming' && (
            <div className="upcoming-classes">
              {upcomingCourses.length === 0 ? (
                <div className="text-center text-muted py-5">
                  <i className="fas fa-calendar-plus fa-3x mb-3 opacity-50"></i>
                  <h5>No Upcoming Classes</h5>
                  <p>You don't have any upcoming classes scheduled.</p>
                  <a href="/instructor/schedule" className="btn btn-primary">
                    <i className="fas fa-plus me-2"></i>
                    Schedule a Course
                  </a>
                </div>
              ) : (
                <div className="courses-list">
                  <div className="row">
                    {upcomingCourses.map((course) => (
                      <div key={course.id} className="col-lg-6">
                        {renderCourseCard(course)}
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Recent Classes */}
          {activeTab === 'recent' && (
            <div className="recent-classes">
              {recentCourses.length === 0 ? (
                <div className="text-center text-muted py-5">
                  <i className="fas fa-history fa-3x mb-3 opacity-50"></i>
                  <h5>No Recent Classes</h5>
                  <p>You don't have any recently completed classes to show.</p>
                </div>
              ) : (
                <div className="courses-list">
                  <div className="row">
                    {recentCourses.map((course) => (
                      <div key={course.id} className="col-lg-6">
                        {renderCourseCard(course, false)}
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Quick Actions Footer */}
      <div className="row mt-4">
        <div className="col-12">
          <div className="card bg-light">
            <div className="card-body">
              <h6 className="card-title">
                <i className="fas fa-bolt me-2 text-primary"></i>
                Quick Actions
              </h6>
              <div className="row">
                <div className="col-md-2">
                  <a href="/instructor/attendance/bulk" className="btn btn-outline-primary w-100 btn-sm">
                    <i className="fas fa-check-double me-1"></i>
                    Bulk Attendance
                  </a>
                </div>
                <div className="col-md-2">
                  <a href="/instructor/students" className="btn btn-outline-success w-100 btn-sm">
                    <i className="fas fa-users me-1"></i>
                    All Students
                  </a>
                </div>
                <div className="col-md-2">
                  <a href="/instructor/reports" className="btn btn-outline-info w-100 btn-sm">
                    <i className="fas fa-chart-bar me-1"></i>
                    Reports
                  </a>
                </div>
                <div className="col-md-2">
                  <a href="/instructor/messages" className="btn btn-outline-warning w-100 btn-sm">
                    <i className="fas fa-envelope me-1"></i>
                    Messages
                  </a>
                </div>
                <div className="col-md-2">
                  <a href="/instructor/resources" className="btn btn-outline-secondary w-100 btn-sm">
                    <i className="fas fa-toolbox me-1"></i>
                    Resources
                  </a>
                </div>
                <div className="col-md-2">
                  <a href="/instructor/profile" className="btn btn-outline-dark w-100 btn-sm">
                    <i className="fas fa-user-cog me-1"></i>
                    Profile
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

export default ClassDashboard;
