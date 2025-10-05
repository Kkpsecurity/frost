import React, { useState, useEffect } from "react";
import { CourseDate } from "../Offline/types";

interface Lesson {
    id: number;
    title: string;
    sort_order: number;
    lesson_type: string;
    is_completed: boolean;
    duration_minutes?: number;
    description?: string;
}

interface LessonsSidebarProps {
    selectedCourse: CourseDate | null;
    isVisible: boolean;
    onClose: () => void;
    className?: string;
}

const LessonsSidebar: React.FC<LessonsSidebarProps> = ({
    selectedCourse,
    isVisible,
    onClose,
    className = "",
}) => {
    const [lessons, setLessons] = useState<Lesson[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [selectedLesson, setSelectedLesson] = useState<Lesson | null>(null);

    // Fetch lessons when course changes
    useEffect(() => {
        if (selectedCourse?.id) {
            fetchLessons(selectedCourse.id);
        } else {
            setLessons([]);
            setSelectedLesson(null);
        }
    }, [selectedCourse?.id]);

    const fetchLessons = async (courseDateId: number) => {
        setLoading(true);
        setError(null);

        try {
            const response = await fetch(`/admin/instructors/data/lessons/${courseDateId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch lessons: ${response.statusText}`);
            }

            const data = await response.json();
            setLessons(data.lessons || []);

            // Auto-select first incomplete lesson
            const firstIncompleteLesson = data.lessons?.find((lesson: Lesson) => !lesson.is_completed);
            if (firstIncompleteLesson) {
                setSelectedLesson(firstIncompleteLesson);
            }
        } catch (err: any) {
            console.error('Error fetching lessons:', err);
            setError(err.message || 'Failed to load lessons');
        } finally {
            setLoading(false);
        }
    };

    const handleLessonClick = (lesson: Lesson) => {
        setSelectedLesson(lesson);
    };

    const getLessonIcon = (lessonType: string) => {
        switch (lessonType.toLowerCase()) {
            case 'video':
                return 'fas fa-play-circle';
            case 'reading':
                return 'fas fa-book-open';
            case 'quiz':
                return 'fas fa-question-circle';
            case 'assignment':
                return 'fas fa-clipboard-check';
            case 'discussion':
                return 'fas fa-comments';
            default:
                return 'fas fa-graduation-cap';
        }
    };

    const getLessonTypeColor = (lessonType: string) => {
        switch (lessonType.toLowerCase()) {
            case 'video':
                return 'text-danger';
            case 'reading':
                return 'text-primary';
            case 'quiz':
                return 'text-warning';
            case 'assignment':
                return 'text-success';
            case 'discussion':
                return 'text-info';
            default:
                return 'text-secondary';
        }
    };

    if (!isVisible) {
        return null;
    }

    return (
        <div className={`lessons-sidebar ${className}`}>
            {/* Sidebar Header */}
            <div className="sidebar-header border-bottom p-3">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 className="mb-1 fw-bold">Today's Lessons</h5>
                        {selectedCourse && (
                            <small className="text-muted">
                                {selectedCourse.course_name} - {selectedCourse.module}
                            </small>
                        )}
                    </div>
                    <button
                        type="button"
                        className="btn-close"
                        aria-label="Close"
                        onClick={onClose}
                    ></button>
                </div>
            </div>

            {/* Sidebar Content */}
            <div className="sidebar-content" style={{ height: 'calc(100vh - 120px)', overflowY: 'auto' }}>
                {loading ? (
                    <div className="p-4 text-center">
                        <div className="spinner-border spinner-border-sm text-primary me-2" role="status">
                            <span className="visually-hidden">Loading...</span>
                        </div>
                        Loading lessons...
                    </div>
                ) : error ? (
                    <div className="p-4">
                        <div className="alert alert-danger" role="alert">
                            <i className="fas fa-exclamation-triangle me-2"></i>
                            {error}
                        </div>
                    </div>
                ) : !selectedCourse ? (
                    <div className="p-4 text-center text-muted">
                        <i className="fas fa-chalkboard-teacher fa-2x mb-3"></i>
                        <p>Select a course to view lessons</p>
                    </div>
                ) : lessons.length === 0 ? (
                    <div className="p-4 text-center text-muted">
                        <i className="fas fa-book-open fa-2x mb-3"></i>
                        <p>No lessons found for this course</p>
                    </div>
                ) : (
                    <div className="lessons-list">
                        {lessons.map((lesson, index) => (
                            <div
                                key={lesson.id}
                                className={`lesson-item p-3 border-bottom cursor-pointer ${
                                    selectedLesson?.id === lesson.id ? 'bg-light border-primary' : ''
                                } ${lesson.is_completed ? 'completed' : ''}`}
                                onClick={() => handleLessonClick(lesson)}
                                style={{ cursor: 'pointer' }}
                            >
                                <div className="d-flex align-items-start">
                                    {/* Lesson Number */}
                                    <div
                                        className={`lesson-number me-3 ${
                                            lesson.is_completed ? 'bg-success text-white' : 'bg-light text-muted'
                                        }`}
                                        style={{
                                            width: '30px',
                                            height: '30px',
                                            borderRadius: '50%',
                                            display: 'flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '0.8rem',
                                            fontWeight: 'bold'
                                        }}
                                    >
                                        {lesson.is_completed ? (
                                            <i className="fas fa-check"></i>
                                        ) : (
                                            index + 1
                                        )}
                                    </div>

                                    {/* Lesson Details */}
                                    <div className="flex-grow-1">
                                        <div className="d-flex justify-content-between align-items-center mb-1">
                                            <h6 className="mb-0 fw-semibold">
                                                {lesson.title}
                                            </h6>
                                            {lesson.duration_minutes && (
                                                <small className="text-muted">
                                                    {lesson.duration_minutes}min
                                                </small>
                                            )}
                                        </div>

                                        <div className="d-flex align-items-center mb-1">
                                            <i className={`${getLessonIcon(lesson.lesson_type)} ${getLessonTypeColor(lesson.lesson_type)} me-2`}></i>
                                            <small className="text-muted text-capitalize">
                                                {lesson.lesson_type}
                                            </small>
                                        </div>

                                        {lesson.description && (
                                            <p className="mb-0 small text-muted" style={{ fontSize: '0.85rem' }}>
                                                {lesson.description.length > 80
                                                    ? `${lesson.description.substring(0, 80)}...`
                                                    : lesson.description
                                                }
                                            </p>
                                        )}

                                        {/* Progress Indicator */}
                                        {selectedLesson?.id === lesson.id && (
                                            <div className="mt-2">
                                                <div className="progress" style={{ height: '3px' }}>
                                                    <div
                                                        className={`progress-bar ${
                                                            lesson.is_completed ? 'bg-success' : 'bg-primary'
                                                        }`}
                                                        style={{ width: lesson.is_completed ? '100%' : '0%' }}
                                                    ></div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Sidebar Footer - Actions */}
            {selectedCourse && lessons.length > 0 && (
                <div className="sidebar-footer border-top p-3">
                    <div className="d-grid gap-2">
                        <div className="row g-2">
                            <div className="col-6">
                                <button
                                    className="btn btn-outline-primary btn-sm w-100"
                                    onClick={() => window.open(`/admin/courses/${selectedCourse.id}/overview`, '_blank')}
                                >
                                    <i className="fas fa-external-link-alt me-1"></i>
                                    Course View
                                </button>
                            </div>
                            <div className="col-6">
                                <button
                                    className="btn btn-primary btn-sm w-100"
                                    disabled={!selectedLesson}
                                >
                                    <i className="fas fa-play me-1"></i>
                                    Begin Lesson
                                </button>
                            </div>
                        </div>

                        {/* Progress Summary */}
                        <div className="mt-2 text-center">
                            <small className="text-muted">
                                Progress: {lessons.filter(l => l.is_completed).length} of {lessons.length} completed
                            </small>
                            <div className="progress mt-1" style={{ height: '4px' }}>
                                <div
                                    className="progress-bar bg-success"
                                    style={{
                                        width: `${(lessons.filter(l => l.is_completed).length / lessons.length) * 100}%`
                                    }}
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            )}


        </div>
    );
};

export default LessonsSidebar;
