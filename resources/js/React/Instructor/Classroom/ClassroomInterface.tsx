import React from 'react';
import { useQuery } from '@tanstack/react-query';
import BulletinBoard from './BulletinBoard';
import ClassDashboard from './ClassDashboard';
import type { DashboardState, LoadingStates } from '../../types/global';

interface ClassroomInterfaceProps {
  instructorId?: number;
  className?: string;
}

interface DashboardResponse {
  has_scheduled_courses: boolean;
  active_courses: any[];
  upcoming_courses: any[];
  recent_courses: any[];
  bulletin_content: any;
  instructor_profile: any;
}

interface InstructorValidationResponse {
  instructor: any;
  course_date: any;
  status: string;
}

interface ClassDataResponse {
  course_dates: any[];
  instructor: any;
  students: any[];
  announcements?: any[];
  resources?: any[];
}

const ClassroomInterface: React.FC<ClassroomInterfaceProps> = ({
  instructorId,
  className = ''
}) => {
  // Fetch dashboard state to determine which interface to show
  const {
    data: dashboardData,
    isLoading,
    error,
    refetch
  } = useQuery({
    queryKey: ['instructor', 'dashboard', instructorId],
    queryFn: async (): Promise<DashboardResponse> => {
      try {
          // Try existing admin endpoints for instructor validation
          const validateResponse = await fetch("/admin/instructors/validate", {
              method: "GET",
              headers: {
                  Accept: "application/json",
                  "Content-Type": "application/json",
                  "X-Requested-With": "XMLHttpRequest",
                  "X-CSRF-TOKEN": (window as any).csrfToken || "",
              },
              credentials: "include",
          });

          if (!validateResponse.ok) {
              throw new Error(
                  `Instructor validation failed: ${validateResponse.status}`
              );
          }

          const validationData = await validateResponse.json();
          console.log("✅ Instructor validation successful:", validationData);

          // Then get classroom data using new organized endpoint
          const classDataResponse = await fetch(
              "/admin/instructors/data/classroom",
              {
                  method: "GET",
                  headers: {
                      Accept: "application/json",
                      "Content-Type": "application/json",
                      "X-Requested-With": "XMLHttpRequest",
                      "X-CSRF-TOKEN": (window as any).csrfToken || "",
                  },
                  credentials: "include",
              }
          );

          if (!classDataResponse.ok) {
              throw new Error(
                  `Class data fetch failed: ${classDataResponse.status}`
              );
          }

          const classData = await classDataResponse.json();
          console.log("✅ Classroom data fetch successful:", classData);

          // Transform new classroom data structure to DashboardResponse format
          const hasScheduledCourses =
              classData.classroom?.status === "admin_access";

          return {
              has_scheduled_courses: hasScheduledCourses,
              active_courses: hasScheduledCourses ? [classData.classroom] : [],
              upcoming_courses: [],
              recent_courses: [],
              bulletin_content: {
                  announcements: [],
                  available_courses: [],
                  instructor_resources: [],
                  quick_stats: {
                      total_instructors: 1,
                      active_courses_today: hasScheduledCourses ? 1 : 0,
                      students_in_system:
                          classData.classroom?.current_enrollment || 0,
                  },
              },
              instructor_profile: validationData.instructor || {},
          };
      } catch (error) {
        console.error('Dashboard fetch error:', error);
        throw error;
      }
    },
    staleTime: 5 * 60 * 1000, // 5 minutes
    refetchInterval: 30 * 1000, // Refetch every 30 seconds for real-time updates
  });

  // Transform API response to our internal state format
  const dashboardState: DashboardState | null = dashboardData ? {
    hasScheduledCourses: dashboardData.has_scheduled_courses,
    activeCourses: dashboardData.active_courses || [],
    upcomingCourses: dashboardData.upcoming_courses || [],
    recentCourses: dashboardData.recent_courses || [],
    bulletinContent: dashboardData.bulletin_content || {
      announcements: [],
      available_courses: [],
      instructor_resources: [],
      quick_stats: {
        total_instructors: 0,
        active_courses_today: 0,
        students_in_system: 0
      }
    },
    instructorProfile: dashboardData.instructor_profile || {}
  } : null;

  // Loading state
  if (isLoading) {
    return (
      <div className={`classroom-interface ${className}`}>
        <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
          <div className="text-center">
            <div className="spinner-border text-primary mb-3" role="status">
              <span className="visually-hidden">Loading...</span>
            </div>
            <h5 className="text-muted">Loading Your Classroom...</h5>
            <p className="text-muted">Preparing your instructor interface</p>
          </div>
        </div>
      </div>
    );
  }

  // Error state
  if (error) {
    return (
      <div className={`classroom-interface ${className}`}>
        <div className="alert alert-danger mx-3 mt-3">
          <h5 className="alert-heading">
            <i className="fas fa-exclamation-triangle me-2"></i>
            Unable to Load Classroom
          </h5>
          <p className="mb-3">
            {(error as Error).message.includes('validation failed')
              ? 'Authentication failed. Please ensure you are logged in as an instructor and have the proper permissions.'
              : 'We\'re having trouble loading your instructor dashboard. This could be due to a network issue or a temporary problem with our servers.'
            }
          </p>
          <div className="d-flex gap-2">
            <button
              className="btn btn-outline-danger btn-sm"
              onClick={() => refetch()}
            >
              <i className="fas fa-redo me-1"></i>
              Try Again
            </button>
            <button
              className="btn btn-outline-secondary btn-sm"
              onClick={() => window.location.reload()}
            >
              <i className="fas fa-refresh me-1"></i>
              Reload Page
            </button>
          </div>
        </div>
      </div>
    );
  }

  // No data state (shouldn't happen normally)
  if (!dashboardState) {
    return (
      <div className={`classroom-interface ${className}`}>
        <div className="alert alert-warning mx-3 mt-3">
          <h5 className="alert-heading">
            <i className="fas fa-info-circle me-2"></i>
            Setting Up Your Classroom
          </h5>
          <p className="mb-0">
            We're preparing your instructor interface. Please wait a moment...
          </p>
        </div>
      </div>
    );
  }

  // Main routing logic: Show BulletinBoard if no scheduled courses, otherwise show ClassDashboard
  return (
    <div className={`classroom-interface ${className}`}>
      {!dashboardState.hasScheduledCourses ? (
        <BulletinBoard
          data={dashboardState.bulletinContent}
          instructor={dashboardState.instructorProfile}
          onRefresh={() => refetch()}
        />
      ) : (
        <ClassDashboard
          activeCourses={dashboardState.activeCourses}
          upcomingCourses={dashboardState.upcomingCourses}
          recentCourses={dashboardState.recentCourses}
          instructor={dashboardState.instructorProfile}
          onRefresh={() => refetch()}
        />
      )}
    </div>
  );
};

export default ClassroomInterface;
