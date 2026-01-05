import React, { useEffect, useState } from "react";
import axios from "axios";

interface Lesson {
  id: number;
  title: string;
  sort_order: number;
  lesson_type: string;
  is_completed: boolean;
  duration_minutes: number;
  description: string;
  content_url: string | null;
  objectives: string | null;
}

interface InstLesson {
  id: number;
  lesson_id: number;
  created_at: string;
  completed_at: string | null;
  is_paused: boolean;
}

interface LessonsPanelProps {
  courseDateId?: number;
  collapsed: boolean;
  onToggle: () => void;
  instUnit?: any; // Contains instLessons
  zoomReady?: boolean;
}

/**
 * LessonsPanel - Left sidebar showing today's lessons
 *
 * Rules:
 * 1. All lessons DISABLED until zoom_setup complete (zoom_started_at set)
 * 2. After zoom setup, only FIRST incomplete lesson is enabled
 * 3. Lessons enable progressively as previous lessons complete
 * 4. Prevents starting lessons out of order
 */
const LessonsPanel: React.FC<LessonsPanelProps> = ({
  courseDateId,
  collapsed,
  onToggle,
  instUnit,
  zoomReady,
}) => {
  const [lessons, setLessons] = useState<Lesson[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // Check if Zoom is setup (zoom_creds.zoom_status === enabled)
  const isZoomReady = !!zoomReady;

  // Get InstLesson records to track completion
  const instLessons = instUnit?.instLessons || [];

  useEffect(() => {
    if (!courseDateId) {
      setLessons([]);
      return;
    }

    const fetchLessons = async () => {
      setLoading(true);
      setError(null);

      try {
        console.log(`📚 Fetching lessons for courseDate: ${courseDateId}`);
        const response = await axios.get(
          `/admin/instructors/data/lessons/${courseDateId}`
        );

        console.log("✅ Lessons received:", response.data);
        setLessons(response.data.lessons || []);
      } catch (err: any) {
        console.error("❌ Error fetching lessons:", err);
        setError(err.message || "Failed to load lessons");
      } finally {
        setLoading(false);
      }
    };

    fetchLessons();
  }, [courseDateId]);

  /**
   * Check if a lesson is completed by looking at InstLesson records
   */
  const isLessonCompleted = (lessonId: number): boolean => {
    const instLesson = instLessons.find((il: InstLesson) => il.lesson_id === lessonId);
    return instLesson?.completed_at != null;
  };

  /**
   * Check if a lesson is currently active (started but not completed)
   */
  const isLessonActive = (lessonId: number): boolean => {
    const instLesson = instLessons.find((il: InstLesson) => il.lesson_id === lessonId);
    return instLesson != null && instLesson.completed_at == null;
  };

  /**
   * Determine if a lesson button should be enabled
   * Rules:
   * 1. If Zoom not ready, ALL disabled
   * 2. If lesson already started/completed, disabled
   * 3. Only first incomplete lesson is enabled
   * 4. All subsequent lessons disabled until previous completes
   */
  const isLessonEnabled = (lesson: Lesson, index: number): boolean => {
    // Rule 1: Zoom must be setup first
    if (!isZoomReady) {
      return false;
    }

    // Check if this lesson is already completed or active
    const completed = isLessonCompleted(lesson.id);
    const active = isLessonActive(lesson.id);

    // If already active or completed, disable button
    if (active || completed) {
      return false;
    }

    // Find the first incomplete lesson
    for (let i = 0; i < lessons.length; i++) {
      const currentLesson = lessons[i];
      if (!isLessonCompleted(currentLesson.id)) {
        // This is the first incomplete lesson - enable only if it's THIS lesson
        return currentLesson.id === lesson.id;
      }
    }

    return false;
  };

  const getLessonIcon = (lessonType: string) => {
    switch (lessonType) {
      case "video":
        return "fa-video";
      case "reading":
        return "fa-book";
      case "quiz":
        return "fa-clipboard-question";
      case "assignment":
        return "fa-file-pen";
      default:
        return "fa-book-open";
    }
  };

  const formatDuration = (minutes: number) => {
    if (minutes < 60) {
      return `${minutes}m`;
    }
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
  };

  return (
    <aside className={`sidebar sidebar-left ${collapsed ? "collapsed" : ""}`}>
      <div className="sidebar-header">
        <div className="sidebar-title">
          {!collapsed && (
            <>
              <i className="fas fa-book" />
              <span>Today's Lessons</span>
            </>
          )}
          {collapsed && <i className="fas fa-book" />}
        </div>
        <button
          className="btn-collapse"
          onClick={onToggle}
          title={collapsed ? "Expand" : "Collapse"}
        >
          <i
            className={`fas ${
              collapsed ? "fa-chevron-right" : "fa-chevron-left"
            }`}
          />
        </button>
      </div>

      <div className="sidebar-content">
        {collapsed && (
          <div className="collapsed-icons">
            <i className="fas fa-book-open" title="Lessons" />
            {lessons.length > 0 && (
              <div className="lesson-count-badge">{lessons.length}</div>
            )}
          </div>
        )}

        {!collapsed && (
          <>
            {loading && (
              <div className="text-center p-4">
                <i className="fas fa-spinner fa-spin fa-2x text-white-50 mb-2" />
                <p className="text-white-50 small mb-0">Loading lessons...</p>
              </div>
            )}

            {error && !loading && (
              <div className="alert alert-danger m-3">
                <i className="fas fa-exclamation-triangle mr-2" />
                {error}
              </div>
            )}

            {!loading && !error && lessons.length === 0 && (
              <div className="placeholder-content">
                <i className="fas fa-inbox fa-3x mb-3 text-muted" />
                <p className="text-muted small">No lessons scheduled for today</p>
              </div>
            )}

            {!loading && !error && lessons.length > 0 && (
              <>
                {!isZoomReady && (
                  <div className="alert alert-warning m-3">
                    <i className="fas fa-exclamation-triangle mr-2" />
                    <strong>Zoom Setup Required</strong>
                    <p className="mb-0 small">Complete Zoom setup before starting lessons</p>
                  </div>
                )}
                <div className="lessons-list">
                  {lessons.map((lesson, index) => {
                    const completed = isLessonCompleted(lesson.id);
                    const active = isLessonActive(lesson.id);
                    const enabled = isLessonEnabled(lesson, index);

                    return (
                      <div
                        key={lesson.id}
                        className={`lesson-item ${completed ? "completed" : ""} ${active ? "active" : ""}`}
                      >
                        <div className="lesson-number">{index + 1}</div>
                        <div className="lesson-content">
                          <div className="lesson-header">
                            <i className={`fas ${getLessonIcon(lesson.lesson_type)} mr-2`} />
                            <h6 className="lesson-title mb-0">{lesson.title}</h6>
                          </div>
                          <div className="lesson-meta">
                            <span className="lesson-duration">
                              <i className="far fa-clock me-1" />
                              {formatDuration(lesson.duration_minutes)}
                            </span>
                            {completed && (
                              <span className="lesson-status text-success">
                                <i className="fas fa-check-circle me-1" />
                                Completed
                              </span>
                            )}
                            {active && !completed && (
                              <span className="lesson-status text-primary">
                                <i className="fas fa-play-circle me-1" />
                                In Progress
                              </span>
                            )}
                            {!active && !completed && !enabled && (
                              <span className="lesson-status text-muted">
                                <i className="fas fa-lock me-1" />
                                Locked
                              </span>
                            )}
                          </div>
                          {!completed && !active && (
                            <button
                              className="btn btn-sm btn-primary btn-start-lesson mt-2"
                              disabled={!enabled}
                              title={
                                !isZoomReady
                                  ? "Setup Zoom first"
                                  : !enabled
                                  ? "Complete previous lesson first"
                                  : "Start this lesson"
                              }
                            >
                              <i className="fas fa-play me-1" />
                              Start Lesson
                            </button>
                          )}
                          {active && !completed && (
                            <div className="btn-group w-100 mt-2">
                              <button
                                className="btn btn-sm btn-warning"
                                title="Pause/resume handled in titlebar"
                                disabled
                              >
                                <i className="fas fa-pause me-1" />
                                Active
                              </button>
                            </div>
                          )}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </>
            )}
          </>
        )}
      </div>

      {/* Lesson Panel Specific Styles */}
      <style>{`
        .lessons-list {
          padding: 0;
        }

        .lesson-item {
          display: flex;
          gap: 12px;
          padding: 15px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.1);
          transition: background 0.2s;
        }

        .lesson-item:hover {
          background: rgba(255, 255, 255, 0.05);
        }

        .lesson-item.completed {
          opacity: 0.7;
        }

        .lesson-item.active {
          background: rgba(0, 123, 255, 0.1);
          border-left: 3px solid #007bff;
        }

        .lesson-item.active .lesson-number {
          background: #007bff;
          color: white;
        }

        .lesson-number {
          flex-shrink: 0;
          width: 32px;
          height: 32px;
          display: flex;
          align-items: center;
          justify-content: center;
          background: rgba(255, 255, 255, 0.1);
          border-radius: 50%;
          font-weight: 600;
          font-size: 14px;
          color: white;
        }

        .lesson-content {
          flex: 1;
          min-width: 0;
        }

        .lesson-header {
          display: flex;
          align-items: center;
          margin-bottom: 6px;
        }

        .lesson-title {
          font-size: 14px;
          font-weight: 500;
          color: white;
          line-height: 1.4;
        }

        .lesson-meta {
          display: flex;
          align-items: center;
          gap: 12px;
          font-size: 12px;
          color: rgba(255, 255, 255, 0.7);
          margin-bottom: 8px;
        }

        .lesson-duration {
          display: flex;
          align-items: center;
        }

        .lesson-status {
          display: flex;
          align-items: center;
        }

        .btn-start-lesson {
          width: 100%;
          font-size: 12px;
          padding: 6px 12px;
        }

        .lesson-count-badge {
          position: absolute;
          top: 8px;
          right: 8px;
          background: #007bff;
          color: white;
          border-radius: 50%;
          width: 24px;
          height: 24px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
          font-weight: 600;
        }
      `}</style>
    </aside>
  );
};

export default LessonsPanel;
