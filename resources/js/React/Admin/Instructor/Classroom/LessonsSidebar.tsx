import React from 'react';
import { useQuery } from '@tanstack/react-query';

interface LessonsSidebarProps {
    courseDateId?: number;
    instUnitId?: number;
}

interface Lesson {
    id: number;
    course_unit_lesson_id: number;
    lesson_name: string;
    lesson_description?: string;
    order_index: number;
    progress_minutes: number;
    status: 'not_started' | 'in_progress' | 'completed';
    start_time?: string;
    end_time?: string;
}

/**
 * LessonsSidebar - Left sidebar panel showing course lessons with progress
 *
 * Features:
 * - Displays lessons for current day's class
 * - Shows progression status (not_started, in_progress, completed)
 * - Auto-highlights current lesson
 * - Progress bars for each lesson
 * - Click to navigate between lessons
 */
const LessonsSidebar: React.FC<LessonsSidebarProps> = ({ courseDateId, instUnitId }) => {
    // Fetch lessons for current course date
    const { data: lessonsData, isLoading, error } = useQuery({
        queryKey: ['lessons', courseDateId],
        queryFn: async () => {
            if (!courseDateId) return null;

            const response = await fetch(`/admin/instructors/data/lessons/${courseDateId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch lessons: ${response.statusText}`);
            }

            return response.json();
        },
        staleTime: 30 * 1000, // 30 seconds
        gcTime: 5 * 60 * 1000, // 5 minutes
        enabled: !!courseDateId,
        retry: 2,
    });

    // Get current active lesson (in_progress)
    const currentLesson = lessonsData?.lessons?.find((l: Lesson) => l.status === 'in_progress');
    const lessons: Lesson[] = lessonsData?.lessons || [];

    // Calculate completion percentage
    const completedCount = lessons.filter(l => l.status === 'completed').length;
    const progressPercent = lessons.length > 0 ? (completedCount / lessons.length) * 100 : 0;

    // Get status badge style
    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'completed':
                return { bg: 'bg-success', icon: 'fas fa-check-circle', text: 'Completed' };
            case 'in_progress':
                return { bg: 'bg-primary', icon: 'fas fa-circle-notch fa-spin', text: 'In Progress' };
            case 'not_started':
            default:
                return { bg: 'bg-secondary', icon: 'fas fa-circle', text: 'Pending' };
        }
    };

    if (isLoading) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-book me-2"></i>
                        Lessons
                    </h5>
                </div>
                <div className="card-body d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
                    <div className="text-center">
                        <div className="spinner-border text-primary" role="status">
                            <span className="visually-hidden">Loading lessons...</span>
                        </div>
                        <p className="mt-2 text-muted">Loading lessons...</p>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-book me-2"></i>
                        Lessons
                    </h5>
                </div>
                <div className="card-body">
                    <div className="alert alert-danger alert-sm">
                        <small>
                            <i className="fas fa-exclamation-circle me-2"></i>
                            Failed to load lessons
                        </small>
                    </div>
                </div>
            </div>
        );
    }

    if (!lessons || lessons.length === 0) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-book me-2"></i>
                        Lessons
                    </h5>
                </div>
                <div className="card-body">
                    <div className="alert alert-info alert-sm">
                        <small>No lessons scheduled for this class</small>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="card h-100">
            <div className="card-header bg-secondary text-white">
                <h5 className="mb-0">
                    <i className="fas fa-book me-2"></i>
                    📚 Lessons
                </h5>
                <small className="text-white-50">
                    {completedCount} / {lessons.length} completed
                </small>
            </div>

            {/* Overall Progress Bar */}
            <div className="card-body pb-2">
                <div className="progress" style={{ height: '6px' }}>
                    <div
                        className="progress-bar bg-success"
                        style={{ width: `${progressPercent}%` }}
                        role="progressbar"
                        aria-valuenow={progressPercent}
                        aria-valuemin={0}
                        aria-valuemax={100}
                    ></div>
                </div>
                <small className="text-muted d-block mt-2">
                    {Math.round(progressPercent)}% Complete
                </small>
            </div>

            {/* Lessons List */}
            <div className="card-body" style={{ maxHeight: '500px', overflow: 'auto', paddingTop: '0.5rem' }}>
                <div className="lessons-list">
                    {lessons.map((lesson, index) => {
                        const badge = getStatusBadge(lesson.status);
                        const isCurrentLesson = lesson.id === currentLesson?.id;

                        return (
                            <div
                                key={lesson.id}
                                className={`lesson-item p-2 mb-2 border rounded cursor-pointer transition ${
                                    isCurrentLesson ? 'bg-light border-primary border-2' : 'bg-white'
                                }`}
                                style={{
                                    cursor: 'pointer',
                                    borderLeft: isCurrentLesson ? '4px solid #0d6efd' : '4px solid #ddd',
                                    transition: 'all 0.3s ease'
                                }}
                            >
                                {/* Lesson Number & Status */}
                                <div className="d-flex justify-content-between align-items-start mb-2">
                                    <div className="d-flex align-items-center gap-2">
                                        <small className="badge bg-secondary">{index + 1}</small>
                                        <small className={`badge ${badge.bg}`}>
                                            <i className={`${badge.icon} me-1`}></i>
                                            {badge.text}
                                        </small>
                                    </div>
                                    {isCurrentLesson && (
                                        <small className="badge bg-info">
                                            <i className="fas fa-star me-1"></i>
                                            Current
                                        </small>
                                    )}
                                </div>

                                {/* Lesson Name */}
                                <h6 className="mb-1">
                                    <strong>{lesson.lesson_name}</strong>
                                </h6>

                                {/* Lesson Description */}
                                {lesson.lesson_description && (
                                    <small className="text-muted d-block mb-2">
                                        {lesson.lesson_description}
                                    </small>
                                )}

                                {/* Progress Minutes */}
                                {lesson.progress_minutes > 0 && (
                                    <small className="text-muted d-block mb-2">
                                        <i className="fas fa-clock me-1"></i>
                                        {lesson.progress_minutes} minutes
                                    </small>
                                )}

                                {/* Time Info */}
                                {(lesson.start_time || lesson.end_time) && (
                                    <small className="text-muted d-block">
                                        <i className="fas fa-calendar me-1"></i>
                                        {lesson.start_time && new Date(lesson.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                        {lesson.start_time && lesson.end_time && ' - '}
                                        {lesson.end_time && new Date(lesson.end_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </small>
                                )}

                                {/* Action Buttons */}
                                <div className="mt-2 d-flex gap-1">
                                    {lesson.status === 'not_started' && (
                                        <button className="btn btn-sm btn-outline-primary" title="Start this lesson">
                                            <i className="fas fa-play me-1"></i>
                                            <small>Start</small>
                                        </button>
                                    )}
                                    {lesson.status === 'in_progress' && (
                                        <button className="btn btn-sm btn-outline-danger" title="Complete this lesson">
                                            <i className="fas fa-check me-1"></i>
                                            <small>Complete</small>
                                        </button>
                                    )}
                                    {lesson.status === 'completed' && (
                                        <button className="btn btn-sm btn-outline-secondary" disabled title="Lesson completed">
                                            <i className="fas fa-check-circle me-1"></i>
                                            <small>Done</small>
                                        </button>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Footer Stats */}
            <div className="card-footer bg-light text-muted small">
                <div className="row text-center">
                    <div className="col-4">
                        <strong>{lessons.filter(l => l.status === 'completed').length}</strong>
                        <br/>
                        Completed
                    </div>
                    <div className="col-4">
                        <strong>{lessons.filter(l => l.status === 'in_progress').length}</strong>
                        <br/>
                        In Progress
                    </div>
                    <div className="col-4">
                        <strong>{lessons.filter(l => l.status === 'not_started').length}</strong>
                        <br/>
                        Pending
                    </div>
                </div>
            </div>
        </div>
    );
};

export default LessonsSidebar;
