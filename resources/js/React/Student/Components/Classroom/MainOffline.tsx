import React, { useState, useEffect } from "react";
import SchoolDashboardTitleBar from "../ShcoolDashboardTitleBar";
import { useClassroom } from "../../context/ClassroomContext";
import { LessonType } from "../../types/classroom";
import { useVideoQuota } from "../../hooks/useVideoQuota";
import { useLessonSession } from "../../hooks/useLessonSession";
import SessionInfoPanel from "./SessionInfoPanel";

interface MainOfflineProps {
    courseAuthId: number;
    student: any;
    onBackToDashboard: () => void;
}

/**
 * MainOffline - Self-study classroom mode
 * 
 * Layout:
 * - Title Bar: Student tools and information (SchoolDashboardTitleBar component)
 * - Sidebar: All lessons for selected course
 * - Content Area: Tabbed interface (Details, Self Study, Documentation)
 */
const MainOffline: React.FC<MainOfflineProps> = ({ courseAuthId, student, onBackToDashboard }) => {
    const [activeTab, setActiveTab] = useState<'details' | 'self-study' | 'documentation'>('details');
    const [lessons, setLessons] = useState<LessonType[]>([]);
    const [selectedLessonId, setSelectedLessonId] = useState<number | null>(null);
    const [isLoadingLessons, setIsLoadingLessons] = useState(true);
    const [courseName, setCourseName] = useState<string>('Loading...');
    
    // View mode: 'list' (default), 'preview' (lesson details), 'player' (video player)
    const [viewMode, setViewMode] = useState<'list' | 'preview' | 'player'>('list');
    const [previewLessonId, setPreviewLessonId] = useState<number | null>(null);
    
    // Video quota hook - manages student watch time
    const { quota, isLoading: isLoadingQuota, error: quotaError } = useVideoQuota();
    
    // Lesson session hook - manages active session with locking
    const { 
        session, 
        isActive: hasActiveSession, 
        isLocked: areLessonsLocked, 
        timeRemaining, 
        pauseRemaining,
        startSession,
        completeSession,
        terminateSession 
    } = useLessonSession();

    // Load lessons from API (offline mode gets all course lessons)
    useEffect(() => {
        const fetchLessons = async () => {
            try {
                setIsLoadingLessons(true);
                // Use correct GET endpoint with query parameter
                const response = await fetch(`/classroom/class/data?course_auth_id=${courseAuthId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to load lessons');
                }

                const data = await response.json();
                console.log('Lessons loaded:', data); // Debug log
                
                if (data.success && data.data) {
                    // Set course name from response
                    if (data.data.courseAuth?.course_name) {
                        setCourseName(data.data.courseAuth.course_name);
                    }
                    
                    // Set lessons if available
                    if (data.data.lessons) {
                        setLessons(data.data.lessons);
                        // Auto-select first incomplete or first lesson
                        const firstIncomplete = data.data.lessons.find((l: LessonType) => !l.is_completed);
                        setSelectedLessonId(firstIncomplete?.id || data.data.lessons[0]?.id || null);
                    }
                } else {
                    console.warn('No lessons found in response:', data);
                }
            } catch (error) {
                console.error('Error loading lessons:', error);
                setCourseName('Course');
            } finally {
                setIsLoadingLessons(false);
            }
        };

        fetchLessons();
    }, [courseAuthId]);

    // Get lesson status color based on status
    const getLessonStatusColor = (status: string) => {
        const colors = {
            'active_live': '#0ea5e9',    // cyan - live with instructor
            'active_fstb': '#3b82f6',    // blue - active self-study
            'completed': '#059669',      // green - completed
            'incomplete': '#1e293b',     // dark slate - not started
        };
        return colors[status as keyof typeof colors] || colors.incomplete;
    };

    // Get lesson status icon
    const getLessonStatusIcon = (lesson: LessonType) => {
        if (lesson.is_completed) return <i className="fas fa-check-circle" style={{ color: '#059669' }}></i>;
        if (lesson.is_active) return <i className="fas fa-play-circle" style={{ color: '#0ea5e9' }}></i>;
        return <i className="far fa-circle" style={{ color: '#64748b' }}></i>;
    };

    // Handle lesson click
    const handleLessonClick = (lessonId: number) => {
        setSelectedLessonId(lessonId);
        // TODO: Load lesson content in the content area
    };

    return (
        <div 
            className="offline-classroom"
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                display: "flex",
                flexDirection: "column",
                paddingTop: "60px",
                gap: 0,
            }}
        >
            {/* Title Bar - Using reusable SchoolDashboardTitleBar component */}
            <SchoolDashboardTitleBar 
                title={courseName}
                subtitle={`Self-Study Mode | Student: ${student?.name || "N/A"}`}
                icon={<i className="fas fa-book-reader"></i>}
                onBackToDashboard={onBackToDashboard}
                classroomStatus="OFFLINE"
            />

            {/* Main Layout: Sidebar + Content */}
            <div className="d-flex flex-grow-1" style={{ overflow: "hidden" }}>
                {/* Sidebar - Lessons */}
                <div 
                    className="lessons-sidebar"
                    style={{
                        width: "280px",
                        backgroundColor: "#34495e",
                        borderRight: "2px solid #2c3e50",
                        overflowY: "auto",
                        flexShrink: 0,
                    }}
                >
                    <div className="p-3">
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                <i className="fas fa-list me-2"></i>
                                Course Lessons
                            </h6>
                            <span className="badge" style={{ backgroundColor: '#3498db' }}>
                                {lessons.filter(l => l.is_completed).length} / {lessons.length}
                            </span>
                        </div>
                        
                        {/* Session Info Panel - Shows when session is active */}
                        {hasActiveSession && session && (
                            <div className="mb-3">
                                <SessionInfoPanel
                                    session={session}
                                    timeRemaining={timeRemaining}
                                    pauseRemaining={pauseRemaining}
                                    onEndSession={() => {
                                        if (confirm('Are you sure you want to end this session? Your progress will be lost.')) {
                                            terminateSession();
                                            setViewMode('list');
                                        }
                                    }}
                                />
                            </div>
                        )}
                        
                        {/* Real lesson data from API */}
                        <div className="lesson-list">
                            {isLoadingLessons ? (
                                <div className="text-center py-4">
                                    <div className="spinner-border text-light" role="status">
                                        <span className="visually-hidden">Loading lessons...</span>
                                    </div>
                                </div>
                            ) : lessons.length === 0 ? (
                                <div className="text-center py-4" style={{ color: '#95a5a6' }}>
                                    <i className="fas fa-inbox fa-2x mb-2"></i>
                                    <p className="mb-0">No lessons available</p>
                                </div>
                            ) : (
                                lessons.map((lesson) => {
                                    const isSelected = selectedLessonId === lesson.id;
                                    const baseColor = getLessonStatusColor(lesson.status);
                                    const selectedColor = '#2563eb';
                                    
                                    return (
                                        <div 
                                            key={lesson.id}
                                            className="lesson-item mb-2 p-2"
                                            onClick={() => handleLessonClick(lesson.id)}
                                            style={{
                                                backgroundColor: isSelected ? selectedColor : baseColor,
                                                borderRadius: "0.25rem",
                                                cursor: "pointer",
                                                transition: "all 0.2s",
                                                border: isSelected ? '2px solid #3b82f6' : '2px solid transparent',
                                                opacity: isSelected ? 1 : 0.85,
                                            }}
                                            onMouseEnter={(e) => {
                                                if (!isSelected) {
                                                    e.currentTarget.style.opacity = '1';
                                                    e.currentTarget.style.transform = 'translateX(4px)';
                                                }
                                            }}
                                            onMouseLeave={(e) => {
                                                if (!isSelected) {
                                                    e.currentTarget.style.opacity = '0.85';
                                                    e.currentTarget.style.transform = 'translateX(0)';
                                                }
                                            }}
                                        >
                                            <div className="d-flex align-items-start">
                                                <div className="me-2 mt-1">
                                                    {getLessonStatusIcon(lesson)}
                                                </div>
                                                <div className="flex-grow-1">
                                                    <div style={{ color: "white", fontSize: "0.875rem", fontWeight: "500" }}>
                                                        {lesson.title}
                                                    </div>
                                                    {lesson.description && (
                                                        <small style={{ color: "rgba(255,255,255,0.7)", fontSize: "0.7rem", display: 'block', marginTop: '0.25rem' }}>
                                                            {lesson.description.length > 60 
                                                                ? lesson.description.substring(0, 60) + '...' 
                                                                : lesson.description}
                                                        </small>
                                                    )}
                                                    <div className="d-flex align-items-center mt-1">
                                                        <small style={{ color: "rgba(255,255,255,0.6)", fontSize: "0.7rem" }}>
                                                            <i className="far fa-clock me-1"></i>
                                                            {lesson.duration_minutes} min
                                                        </small>
                                                        {lesson.is_completed && (
                                                            <small className="ms-2" style={{ color: '#059669', fontSize: "0.7rem" }}>
                                                                <i className="fas fa-check me-1"></i>
                                                                Complete
                                                            </small>
                                                        )}
                                                        {isSelected && (
                                                            <small className="ms-auto" style={{ color: 'white', fontSize: "0.7rem" }}>
                                                                <i className="fas fa-arrow-right"></i>
                                                            </small>
                                                        )}
                                                    </div>
                                                    
                                                    {/* Start/Resume/Locked Button - Only visible on Self Study tab */}
                                                    {activeTab === 'self-study' && (
                                                        <button
                                                            className={`btn btn-sm mt-2 w-100 ${
                                                                hasActiveSession && session?.lessonId === lesson.id
                                                                    ? 'btn-info'  // Active lesson - Resume
                                                                    : lesson.is_completed
                                                                    ? 'btn-outline-success'  // Completed - Review
                                                                    : 'btn-outline-info'  // Available - Start
                                                            }`}
                                                            style={{
                                                                padding: '0.375rem 0.75rem',
                                                                fontSize: '0.75rem',
                                                                fontWeight: '600',
                                                                borderRadius: '0.25rem',
                                                                cursor: areLessonsLocked && session?.lessonId !== lesson.id 
                                                                    ? 'not-allowed' 
                                                                    : 'pointer',
                                                                opacity: areLessonsLocked && session?.lessonId !== lesson.id 
                                                                    ? 0.5 
                                                                    : 1,
                                                                transition: 'all 0.2s',
                                                            }}
                                                            disabled={areLessonsLocked && session?.lessonId !== lesson.id}
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                
                                                                if (hasActiveSession && session?.lessonId === lesson.id) {
                                                                    // Resume active lesson - go to player
                                                                    console.log('Resume lesson:', lesson.id);
                                                                    setSelectedLessonId(lesson.id);
                                                                    setPreviewLessonId(lesson.id);
                                                                    setViewMode('player');
                                                                } else if (lesson.is_completed) {
                                                                    // Review completed lesson
                                                                    console.log('Review lesson:', lesson.id);
                                                                    setSelectedLessonId(lesson.id);
                                                                    setPreviewLessonId(lesson.id);
                                                                    setViewMode('preview');
                                                                } else if (!areLessonsLocked) {
                                                                    // Start new lesson - show preview first
                                                                    console.log('Start lesson:', lesson.id);
                                                                    setSelectedLessonId(lesson.id);
                                                                    setPreviewLessonId(lesson.id);
                                                                    setViewMode('preview');
                                                                }
                                                            }}
                                                        >
                                                            {areLessonsLocked && session?.lessonId !== lesson.id ? (
                                                                <>
                                                                    <i className="fas fa-lock me-1"></i>
                                                                    Locked
                                                                </>
                                                            ) : hasActiveSession && session?.lessonId === lesson.id ? (
                                                                <>
                                                                    <i className="fas fa-play-circle me-1"></i>
                                                                    Resume
                                                                </>
                                                            ) : lesson.is_completed ? (
                                                                <>
                                                                    <i className="fas fa-eye me-1"></i>
                                                                    Review
                                                                </>
                                                            ) : (
                                                                <>
                                                                    <i className="fas fa-play me-1"></i>
                                                                    Start Lesson
                                                                </>
                                                            )}
                                                        </button>
                                                    )}
                                                    
                                                    {/* Review Button - Only visible on Self Study tab for completed lessons */}
                                                    {activeTab === 'self-study' && lesson.is_completed && (
                                                        <button
                                                            className="btn btn-sm mt-2 w-100"
                                                            style={{
                                                                backgroundColor: '#27ae60',
                                                                color: 'white',
                                                                border: 'none',
                                                                padding: '0.375rem 0.75rem',
                                                                fontSize: '0.75rem',
                                                                fontWeight: '600',
                                                                borderRadius: '0.25rem',
                                                                transition: 'all 0.2s',
                                                            }}
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                console.log('Review lesson:', lesson.id, lesson.title);
                                                                // Update selected lesson and show preview screen
                                                                setSelectedLessonId(lesson.id);
                                                                setPreviewLessonId(lesson.id);
                                                                setViewMode('preview');
                                                            }}
                                                            onMouseEnter={(e) => {
                                                                e.currentTarget.style.backgroundColor = '#229954';
                                                                e.currentTarget.style.transform = 'translateY(-2px)';
                                                                e.currentTarget.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                                                            }}
                                                            onMouseLeave={(e) => {
                                                                e.currentTarget.style.backgroundColor = '#27ae60';
                                                                e.currentTarget.style.transform = 'translateY(0)';
                                                                e.currentTarget.style.boxShadow = 'none';
                                                            }}
                                                        >
                                                            <i className="fas fa-redo me-1"></i>
                                                            Review Lesson
                                                        </button>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    </div>
                </div>

                {/* Content Area */}
                <div className="content-area flex-grow-1 d-flex flex-column" style={{ overflow: "hidden" }}>
                    {/* Tabs Navigation */}
                    <div 
                        className="tabs-navigation"
                        style={{
                            backgroundColor: "#2c3e50",
                            borderBottom: "2px solid #34495e",
                            padding: "0 1.5rem",
                        }}
                    >
                        <div className="d-flex">
                            <button
                                className={`tab-button ${activeTab === 'details' ? 'active' : ''}`}
                                onClick={() => setActiveTab('details')}
                                style={{
                                    backgroundColor: activeTab === 'details' ? '#34495e' : 'transparent',
                                    color: activeTab === 'details' ? 'white' : '#95a5a6',
                                    border: 'none',
                                    padding: '1rem 1.5rem',
                                    cursor: 'pointer',
                                    fontWeight: activeTab === 'details' ? '600' : '400',
                                    borderBottom: activeTab === 'details' ? '3px solid #3498db' : 'none',
                                    transition: 'all 0.2s',
                                }}
                            >
                                <i className="fas fa-info-circle me-2"></i>
                                Details
                            </button>
                            <button
                                className={`tab-button ${activeTab === 'self-study' ? 'active' : ''}`}
                                onClick={() => setActiveTab('self-study')}
                                style={{
                                    backgroundColor: activeTab === 'self-study' ? '#34495e' : 'transparent',
                                    color: activeTab === 'self-study' ? 'white' : '#95a5a6',
                                    border: 'none',
                                    padding: '1rem 1.5rem',
                                    cursor: 'pointer',
                                    fontWeight: activeTab === 'self-study' ? '600' : '400',
                                    borderBottom: activeTab === 'self-study' ? '3px solid #3498db' : 'none',
                                    transition: 'all 0.2s',
                                }}
                            >
                                <i className="fas fa-graduation-cap me-2"></i>
                                Self Study
                            </button>
                            <button
                                className={`tab-button ${activeTab === 'documentation' ? 'active' : ''}`}
                                onClick={() => setActiveTab('documentation')}
                                style={{
                                    backgroundColor: activeTab === 'documentation' ? '#34495e' : 'transparent',
                                    color: activeTab === 'documentation' ? 'white' : '#95a5a6',
                                    border: 'none',
                                    padding: '1rem 1.5rem',
                                    cursor: 'pointer',
                                    fontWeight: activeTab === 'documentation' ? '600' : '400',
                                    borderBottom: activeTab === 'documentation' ? '3px solid #3498db' : 'none',
                                    transition: 'all 0.2s',
                                }}
                            >
                                <i className="fas fa-file-alt me-2"></i>
                                Documentation
                            </button>
                        </div>
                    </div>

                    {/* Tab Content */}
                    <div className="tab-content flex-grow-1 p-4" style={{ overflowY: "auto" }}>
                        {activeTab === 'details' && (
                            <div className="details-tab">
                                <h4 className="mb-4" style={{ color: "white", fontSize: "1.75rem", fontWeight: "600" }}>
                                    <i className="fas fa-tachometer-alt me-2" style={{ color: "#3498db" }}></i>
                                    Learning Dashboard
                                </h4>
                                
                                {/* Quick Stats Row */}
                                <div className="row g-3 mb-4">
                                    <div className="col-md-3">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body text-center">
                                                <div style={{ fontSize: "2rem", color: "#3498db", marginBottom: "0.5rem" }}>
                                                    <i className="fas fa-book-open"></i>
                                                </div>
                                                <h3 className="mb-0" style={{ color: "white", fontSize: "2rem" }}>
                                                    {lessons.filter(l => l.is_completed).length}/{lessons.length}
                                                </h3>
                                                <p className="mb-0" style={{ color: "#95a5a6", fontSize: "0.875rem" }}>Lessons Complete</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body text-center">
                                                <div style={{ fontSize: "2rem", color: "#2ecc71", marginBottom: "0.5rem" }}>
                                                    <i className="fas fa-chart-line"></i>
                                                </div>
                                                <h3 className="mb-0" style={{ color: "white", fontSize: "2rem" }}>
                                                    {lessons.length > 0 ? Math.round((lessons.filter(l => l.is_completed).length / lessons.length) * 100) : 0}%
                                                </h3>
                                                <p className="mb-0" style={{ color: "#95a5a6", fontSize: "0.875rem" }}>Progress</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body text-center">
                                                <div style={{ fontSize: "2rem", color: "#f39c12", marginBottom: "0.5rem" }}>
                                                    <i className="fas fa-clock"></i>
                                                </div>
                                                <h3 className="mb-0" style={{ color: "white", fontSize: "2rem" }}>
                                                    {lessons.reduce((sum, l) => sum + (l.duration_minutes || 0), 0)}
                                                </h3>
                                                <p className="mb-0" style={{ color: "#95a5a6", fontSize: "0.875rem" }}>Total Minutes</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="col-md-3">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body text-center">
                                                <div style={{ fontSize: "2rem", color: "#e74c3c", marginBottom: "0.5rem" }}>
                                                    <i className="fas fa-tasks"></i>
                                                </div>
                                                <h3 className="mb-0" style={{ color: "white", fontSize: "2rem" }}>
                                                    {lessons.filter(l => !l.is_completed).length}
                                                </h3>
                                                <p className="mb-0" style={{ color: "#95a5a6", fontSize: "0.875rem" }}>Remaining</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Main Content Row */}
                                <div className="row g-3">
                                    {/* Left Column */}
                                    <div className="col-md-8">
                                        {/* Student Progress Overview */}
                                        <div className="card mb-3" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)", borderRadius: "0.5rem 0.5rem 0 0" }}>
                                                <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                    <i className="fas fa-chart-bar me-2" style={{ color: "#3498db" }}></i>
                                                    Progress Overview
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <div className="mb-3">
                                                    <div className="d-flex justify-content-between mb-2">
                                                        <span style={{ color: "#95a5a6", fontSize: "0.875rem" }}>Overall Completion</span>
                                                        <span style={{ color: "white", fontWeight: "600" }}>
                                                            {lessons.length > 0 ? Math.round((lessons.filter(l => l.is_completed).length / lessons.length) * 100) : 0}%
                                                        </span>
                                                    </div>
                                                    <div style={{ width: "100%", height: "8px", backgroundColor: "#34495e", borderRadius: "4px", overflow: "hidden" }}>
                                                        <div style={{ 
                                                            width: `${lessons.length > 0 ? (lessons.filter(l => l.is_completed).length / lessons.length) * 100 : 0}%`, 
                                                            height: "100%", 
                                                            backgroundColor: "#3498db",
                                                            transition: "width 0.3s ease"
                                                        }}></div>
                                                    </div>
                                                </div>

                                                <div className="row g-2">
                                                    <div className="col-4">
                                                        <div className="text-center p-2" style={{ backgroundColor: "#34495e", borderRadius: "0.375rem" }}>
                                                            <div style={{ color: "#2ecc71", fontSize: "1.5rem", fontWeight: "600" }}>
                                                                {lessons.filter(l => l.is_completed).length}
                                                            </div>
                                                            <div style={{ color: "#95a5a6", fontSize: "0.75rem" }}>Completed</div>
                                                        </div>
                                                    </div>
                                                    <div className="col-4">
                                                        <div className="text-center p-2" style={{ backgroundColor: "#34495e", borderRadius: "0.375rem" }}>
                                                            <div style={{ color: "#3498db", fontSize: "1.5rem", fontWeight: "600" }}>
                                                                {lessons.filter(l => l.is_active && !l.is_completed).length}
                                                            </div>
                                                            <div style={{ color: "#95a5a6", fontSize: "0.75rem" }}>In Progress</div>
                                                        </div>
                                                    </div>
                                                    <div className="col-4">
                                                        <div className="text-center p-2" style={{ backgroundColor: "#34495e", borderRadius: "0.375rem" }}>
                                                            <div style={{ color: "#e74c3c", fontSize: "1.5rem", fontWeight: "600" }}>
                                                                {lessons.filter(l => !l.is_completed && !l.is_active).length}
                                                            </div>
                                                            <div style={{ color: "#95a5a6", fontSize: "0.75rem" }}>Not Started</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Current Enrollment Status */}
                                        <div className="card mb-3" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)", borderRadius: "0.5rem 0.5rem 0 0" }}>
                                                <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                    <i className="fas fa-certificate me-2" style={{ color: "#f39c12" }}></i>
                                                    Enrollment Details
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <table style={{ width: "100%", color: "#95a5a6" }}>
                                                    <tbody>
                                                        <tr>
                                                            <td style={{ padding: "0.75rem 0", fontWeight: "500", color: "white", width: "40%" }}>
                                                                <i className="fas fa-book me-2" style={{ color: "#3498db" }}></i>
                                                                Course Name
                                                            </td>
                                                            <td style={{ padding: "0.75rem 0", color: "#95a5a6" }}>{courseName}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style={{ padding: "0.75rem 0", fontWeight: "500", color: "white" }}>
                                                                <i className="fas fa-laptop me-2" style={{ color: "#2ecc71" }}></i>
                                                                Study Mode
                                                            </td>
                                                            <td style={{ padding: "0.75rem 0" }}>
                                                                <span className="badge" style={{ backgroundColor: "#3498db", fontSize: "0.8rem" }}>
                                                                    <i className="fas fa-book-reader me-1"></i>
                                                                    Self-Paced Learning
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style={{ padding: "0.75rem 0", fontWeight: "500", color: "white" }}>
                                                                <i className="fas fa-id-card me-2" style={{ color: "#e74c3c" }}></i>
                                                                Student ID
                                                            </td>
                                                            <td style={{ padding: "0.75rem 0", color: "#95a5a6" }}>{student?.id || "N/A"}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style={{ padding: "0.75rem 0", fontWeight: "500", color: "white" }}>
                                                                <i className="fas fa-hashtag me-2" style={{ color: "#9b59b6" }}></i>
                                                                Course Auth ID
                                                            </td>
                                                            <td style={{ padding: "0.75rem 0", color: "#95a5a6" }}>#{courseAuthId}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Right Column */}
                                    <div className="col-md-4">
                                        {/* Quick Actions */}
                                        <div className="card mb-3" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)", borderRadius: "0.5rem 0.5rem 0 0" }}>
                                                <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                    <i className="fas fa-bolt me-2" style={{ color: "#f39c12" }}></i>
                                                    Quick Actions
                                                </h6>
                                            </div>
                                            <div className="card-body d-grid gap-2">
                                                {selectedLessonId && !lessons.find(l => l.id === selectedLessonId)?.is_completed && (
                                                    <button 
                                                        className="btn btn-primary w-100"
                                                        style={{ 
                                                            backgroundColor: "#3498db", 
                                                            border: "none",
                                                            padding: "0.75rem",
                                                            fontWeight: "600"
                                                        }}
                                                        onClick={() => setActiveTab('self-study')}
                                                    >
                                                        <i className="fas fa-play me-2"></i>
                                                        Resume Current Lesson
                                                    </button>
                                                )}
                                                <button 
                                                    className="btn btn-outline-light w-100"
                                                    style={{ 
                                                        borderColor: "#3498db",
                                                        color: "#3498db",
                                                        padding: "0.75rem",
                                                        fontWeight: "500"
                                                    }}
                                                    onClick={() => setActiveTab('documentation')}
                                                >
                                                    <i className="fas fa-file-pdf me-2"></i>
                                                    View Course Materials
                                                </button>
                                            </div>
                                        </div>

                                        {/* Student Info */}
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)", borderRadius: "0.5rem 0.5rem 0 0" }}>
                                                <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                    <i className="fas fa-user me-2" style={{ color: "#2ecc71" }}></i>
                                                    Student Profile
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <div className="text-center mb-3">
                                                    <div style={{ 
                                                        width: "80px", 
                                                        height: "80px", 
                                                        margin: "0 auto",
                                                        borderRadius: "50%",
                                                        backgroundColor: "#3498db",
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent: "center",
                                                        fontSize: "2rem",
                                                        color: "white",
                                                        fontWeight: "600"
                                                    }}>
                                                        {student?.name?.charAt(0)?.toUpperCase() || "S"}
                                                    </div>
                                                </div>
                                                <table style={{ width: "100%", color: "#95a5a6", fontSize: "0.875rem" }}>
                                                    <tbody>
                                                        <tr>
                                                            <td style={{ padding: "0.5rem 0", fontWeight: "500", color: "white" }}>Name:</td>
                                                            <td style={{ padding: "0.5rem 0", textAlign: "right" }}>{student?.name || "N/A"}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style={{ padding: "0.5rem 0", fontWeight: "500", color: "white" }}>Email:</td>
                                                            <td style={{ padding: "0.5rem 0", textAlign: "right", fontSize: "0.75rem" }}>{student?.email || "N/A"}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Info Alert */}
                                <div className="alert mt-3 mb-0" style={{ 
                                    backgroundColor: "rgba(52, 152, 219, 0.1)", 
                                    border: "1px solid rgba(52, 152, 219, 0.3)",
                                    borderRadius: "0.5rem"
                                }}>
                                    <i className="fas fa-info-circle me-2" style={{ color: "#3498db" }}></i>
                                    <span style={{ color: "#3498db" }}>
                                        You are in self-study mode. Select a lesson from the sidebar to begin learning, or switch to the Self Study tab for video content.
                                    </span>
                                </div>
                            </div>
                        )}

                        {activeTab === 'self-study' && (
                            <div className="self-study-tab">
                                {viewMode === 'list' && (
                                    <>
                                        <h4 className="mb-4" style={{ color: "white", fontSize: "1.75rem", fontWeight: "600" }}>
                                            <i className="fas fa-graduation-cap me-2" style={{ color: "#3498db" }}></i>
                                            Self-Study Mode
                                        </h4>
                                
                                {/* Welcome and Instructions */}
                                <div className="row mb-4">
                                    <div className="col-12">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "2px solid #3498db", borderRadius: "0.5rem" }}>
                                            <div className="card-body" style={{ padding: "2rem" }}>
                                                <h5 style={{ color: "white", marginBottom: "1rem", fontWeight: "600" }}>
                                                    <i className="fas fa-info-circle me-2" style={{ color: "#3498db" }}></i>
                                                    What is Self-Study Mode?
                                                </h5>
                                                <p style={{ color: "#ecf0f1", fontSize: "1rem", lineHeight: "1.6", marginBottom: "1.5rem" }}>
                                                    Self-study mode allows you to watch recorded video lessons independently, outside of live instructor-led classes. 
                                                    This feature is designed to help you succeed in your coursework.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Two Main Purposes */}
                                <div className="row mb-4">
                                    {/* Purpose 1: Make Up Failed Lessons */}
                                    <div className="col-md-6 mb-3">
                                        <div className="card h-100" style={{ backgroundColor: "#27ae60", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body" style={{ padding: "1.5rem" }}>
                                                <div className="d-flex align-items-center mb-3">
                                                    <div style={{
                                                        width: "50px",
                                                        height: "50px",
                                                        borderRadius: "50%",
                                                        backgroundColor: "rgba(255,255,255,0.2)",
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent: "center",
                                                        marginRight: "1rem"
                                                    }}>
                                                        <i className="fas fa-redo-alt" style={{ fontSize: "1.5rem", color: "white" }}></i>
                                                    </div>
                                                    <h6 style={{ color: "white", margin: 0, fontWeight: "600", fontSize: "1.1rem" }}>
                                                        Purpose 1: Make Up Failed Lessons
                                                    </h6>
                                                </div>
                                                <p style={{ color: "rgba(255,255,255,0.95)", fontSize: "0.95rem", lineHeight: "1.5", marginBottom: "1rem" }}>
                                                    Use self-study to review and master content after failing a live lesson. This helps you prepare before retaking the live class.
                                                </p>
                                                <div style={{
                                                    backgroundColor: "rgba(255,255,255,0.15)",
                                                    padding: "1rem",
                                                    borderRadius: "0.375rem",
                                                    marginTop: "1rem"
                                                }}>
                                                    <div style={{ color: "white", fontWeight: "600", marginBottom: "0.5rem", display: "flex", alignItems: "center" }}>
                                                        <i className="fas fa-gift me-2"></i>
                                                        Hour Refund Policy
                                                    </div>
                                                    <p style={{ color: "rgba(255,255,255,0.95)", fontSize: "0.875rem", marginBottom: "0.5rem", lineHeight: "1.5" }}>
                                                        ✅ <strong>Your hours are refunded!</strong>
                                                    </p>
                                                    <ol style={{ color: "rgba(255,255,255,0.9)", fontSize: "0.85rem", paddingLeft: "1.25rem", marginBottom: 0, lineHeight: "1.6" }}>
                                                        <li>Fail a live lesson</li>
                                                        <li>Complete it successfully in self-study</li>
                                                        <li>Retake and pass the live class</li>
                                                        <li><strong>Result: Hours refunded to your quota!</strong></li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Purpose 2: Get a Head Start */}
                                    <div className="col-md-6 mb-3">
                                        <div className="card h-100" style={{ backgroundColor: "#3498db", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body" style={{ padding: "1.5rem" }}>
                                                <div className="d-flex align-items-center mb-3">
                                                    <div style={{
                                                        width: "50px",
                                                        height: "50px",
                                                        borderRadius: "50%",
                                                        backgroundColor: "rgba(255,255,255,0.2)",
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent: "center",
                                                        marginRight: "1rem"
                                                    }}>
                                                        <i className="fas fa-rocket" style={{ fontSize: "1.5rem", color: "white" }}></i>
                                                    </div>
                                                    <h6 style={{ color: "white", margin: 0, fontWeight: "600", fontSize: "1.1rem" }}>
                                                        Purpose 2: Get a Head Start
                                                    </h6>
                                                </div>
                                                <p style={{ color: "rgba(255,255,255,0.95)", fontSize: "0.95rem", lineHeight: "1.5", marginBottom: "1rem" }}>
                                                    Preview lessons before attending the live class. This helps you come prepared and get more value from instructor-led sessions.
                                                </p>
                                                <div style={{
                                                    backgroundColor: "rgba(255,255,255,0.15)",
                                                    padding: "1rem",
                                                    borderRadius: "0.375rem",
                                                    marginTop: "1rem"
                                                }}>
                                                    <div style={{ color: "white", fontWeight: "600", marginBottom: "0.5rem", display: "flex", alignItems: "center" }}>
                                                        <i className="fas fa-clock me-2"></i>
                                                        Hour Usage Policy
                                                    </div>
                                                    <p style={{ color: "rgba(255,255,255,0.95)", fontSize: "0.875rem", marginBottom: 0, lineHeight: "1.5" }}>
                                                        ⚠️ <strong>Video hours are consumed</strong> (no refund)
                                                    </p>
                                                    <p style={{ color: "rgba(255,255,255,0.85)", fontSize: "0.85rem", marginTop: "0.5rem", marginBottom: 0, lineHeight: "1.5" }}>
                                                        Head-start viewing uses your quota permanently. Only remediation (failed → self-study → passed) qualifies for hour refunds.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Getting Started Instructions */}
                                <div className="row mb-4">
                                    <div className="col-12">
                                        <div className="card" style={{ backgroundColor: "#34495e", border: "none", borderRadius: "0.5rem" }}>
                                            <div className="card-body" style={{ padding: "1.5rem" }}>
                                                <h6 style={{ color: "white", marginBottom: "1rem", fontWeight: "600" }}>
                                                    <i className="fas fa-play-circle me-2" style={{ color: "#3498db" }}></i>
                                                    How to Get Started
                                                </h6>
                                                <div className="row">
                                                    <div className="col-md-4 mb-3">
                                                        <div style={{ display: "flex", alignItems: "flex-start" }}>
                                                            <div style={{
                                                                width: "30px",
                                                                height: "30px",
                                                                borderRadius: "50%",
                                                                backgroundColor: "#3498db",
                                                                display: "flex",
                                                                alignItems: "center",
                                                                justifyContent: "center",
                                                                marginRight: "0.75rem",
                                                                flexShrink: 0
                                                            }}>
                                                                <strong style={{ color: "white", fontSize: "0.875rem" }}>1</strong>
                                                            </div>
                                                            <div>
                                                                <div style={{ color: "white", fontWeight: "600", fontSize: "0.9rem", marginBottom: "0.25rem" }}>
                                                                    Select a Lesson
                                                                </div>
                                                                <div style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                    Browse lessons in the sidebar and click the "Start Lesson" button
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-4 mb-3">
                                                        <div style={{ display: "flex", alignItems: "flex-start" }}>
                                                            <div style={{
                                                                width: "30px",
                                                                height: "30px",
                                                                borderRadius: "50%",
                                                                backgroundColor: "#3498db",
                                                                display: "flex",
                                                                alignItems: "center",
                                                                justifyContent: "center",
                                                                marginRight: "0.75rem",
                                                                flexShrink: 0
                                                            }}>
                                                                <strong style={{ color: "white", fontSize: "0.875rem" }}>2</strong>
                                                            </div>
                                                            <div>
                                                                <div style={{ color: "white", fontWeight: "600", fontSize: "0.9rem", marginBottom: "0.25rem" }}>
                                                                    Review Preview
                                                                </div>
                                                                <div style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                    Check lesson details and your remaining video time quota
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-4 mb-3">
                                                        <div style={{ display: "flex", alignItems: "flex-start" }}>
                                                            <div style={{
                                                                width: "30px",
                                                                height: "30px",
                                                                borderRadius: "50%",
                                                                backgroundColor: "#3498db",
                                                                display: "flex",
                                                                alignItems: "center",
                                                                justifyContent: "center",
                                                                marginRight: "0.75rem",
                                                                flexShrink: 0
                                                            }}>
                                                                <strong style={{ color: "white", fontSize: "0.875rem" }}>3</strong>
                                                            </div>
                                                            <div>
                                                                <div style={{ color: "white", fontWeight: "600", fontSize: "0.9rem", marginBottom: "0.25rem" }}>
                                                                    Begin Learning
                                                                </div>
                                                                <div style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                    Click "Begin Lesson" to start your video session
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Video Quota Reminder */}
                                <div className="alert" style={{
                                    backgroundColor: "rgba(241, 196, 15, 0.15)",
                                    border: "1px solid rgba(241, 196, 15, 0.3)",
                                    borderRadius: "0.5rem",
                                    padding: "1rem"
                                }}>
                                    <div className="d-flex align-items-start">
                                        <i className="fas fa-clock" style={{ color: "#f1c40f", fontSize: "1.25rem", marginRight: "0.75rem", marginTop: "0.125rem" }}></i>
                                        <div>
                                            <div style={{ color: "#f1c40f", fontWeight: "600", marginBottom: "0.25rem" }}>
                                                Your Video Time Quota
                                            </div>
                                            <div style={{ color: "#ecf0f1", fontSize: "0.9rem" }}>
                                                You have a total of <strong>10 hours</strong> of video watch time. Monitor your usage carefully and prioritize remediation 
                                                to earn hour refunds. Check your remaining time in the sidebar before starting each lesson.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Call to Action */}
                                <div className="text-center mt-4">
                                    <div style={{ color: "#95a5a6", fontSize: "1.1rem", marginBottom: "0.5rem" }}>
                                        <i className="fas fa-arrow-left me-2"></i>
                                        Select a lesson from the sidebar to begin your self-study session
                                    </div>
                                </div>
                                
                                <div className="row">
                                    <div className="col-12 mb-4" style={{ display: "none" }}>
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                                <h6 className="mb-0" style={{ color: "white" }}>
                                                    <i className="fas fa-video me-2"></i>
                                                    Video Lessons
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <p style={{ color: "#95a5a6" }}>
                                                    Your recorded video lessons will appear here. Select a lesson from the sidebar to begin.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-12 mb-4">
                                        <div className="card" style={{ backgroundColor: "#2c3e50", border: "none" }}>
                                            <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                                <h6 className="mb-0" style={{ color: "white" }}>
                                                    <i className="fas fa-tasks me-2"></i>
                                                    Practice Exercises
                                                </h6>
                                            </div>
                                            <div className="card-body">
                                                <p style={{ color: "#95a5a6" }}>
                                                    Complete practice exercises to reinforce your learning.
                                                </p>
                                                {/* TODO: Add exercises component */}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    </>
                                )}
                                
                                {/* Lesson Preview Screen - Shows when Start Lesson button is clicked */}
                                {viewMode === 'preview' && previewLessonId !== null && (() => {
                                    const lesson = lessons.find(l => l.id === previewLessonId);
                                    if (!lesson) return null;
                                    
                                    // Get quota from hook - use mock data as fallback
                                    const quotaTotal = quota?.total_hours || 10.0;
                                    const quotaUsed = quota?.used_hours || 0.0;
                                    const quotaRemaining = quota?.remaining_hours || (quotaTotal - quotaUsed);
                                    const lessonHours = (lesson.duration_minutes || 0) / 60;
                                    const quotaAfterLesson = quotaRemaining - lessonHours;
                                    const quotaPercentage = (quotaRemaining / quotaTotal) * 100;
                                    
                                    // Show loading state if quota is still loading
                                    if (isLoadingQuota) {
                                        return (
                                            <div className="text-center py-5">
                                                <div className="spinner-border text-light" role="status">
                                                    <span className="visually-hidden">Loading quota...</span>
                                                </div>
                                                <p className="mt-3" style={{ color: "#95a5a6" }}>Loading quota information...</p>
                                            </div>
                                        );
                                    }
                                    
                                    // Color coding for quota
                                    const getQuotaColor = (percentage: number) => {
                                        if (percentage >= 60) return '#2ecc71';
                                        if (percentage >= 30) return '#f39c12';
                                        return '#e74c3c';
                                    };
                                    
                                    return (
                                        <div className="lesson-preview">
                                            {/* Back Button */}
                                            <button
                                                className="btn btn-outline-light mb-4"
                                                onClick={() => {
                                                    setViewMode('list');
                                                    setPreviewLessonId(null);
                                                }}
                                                style={{
                                                    border: '2px solid rgba(255,255,255,0.3)',
                                                    padding: '0.5rem 1.5rem'
                                                }}
                                            >
                                                <i className="fas fa-arrow-left me-2"></i>
                                                Back to Lesson List
                                            </button>
                                            
                                            <h4 className="mb-4" style={{ color: "white", fontSize: "1.75rem", fontWeight: "600" }}>
                                                <i className="fas fa-eye me-2" style={{ color: "#3498db" }}></i>
                                                Lesson Preview
                                            </h4>
                                            
                                            {/* Quota Warning Banner - Show at top if insufficient */}
                                            {quotaAfterLesson < 0 && (
                                                <div className="alert mb-4" style={{
                                                    backgroundColor: "rgba(231, 76, 60, 0.15)",
                                                    border: "2px solid #e74c3c",
                                                    borderRadius: "0.5rem",
                                                    padding: "1.25rem"
                                                }}>
                                                    <div className="d-flex align-items-center">
                                                        <i className="fas fa-exclamation-triangle" style={{ 
                                                            color: "#e74c3c", 
                                                            fontSize: "2rem", 
                                                            marginRight: "1rem" 
                                                        }}></i>
                                                        <div>
                                                            <h6 style={{ color: "#e74c3c", fontWeight: "600", marginBottom: "0.25rem" }}>
                                                                Insufficient Video Quota
                                                            </h6>
                                                            <p style={{ color: "#ecf0f1", marginBottom: 0, fontSize: "0.95rem" }}>
                                                                You need <strong>{Math.abs(quotaAfterLesson).toFixed(2)} more hours</strong> to complete this lesson. 
                                                                Consider completing remediation lessons to earn quota refunds.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                            
                                            {/* Main Content Row */}
                                            <div className="row g-4">
                                                {/* Left Column - Lesson Details */}
                                                <div className="col-md-8">
                                                    {/* Lesson Header Card */}
                                                    <div className="card mb-4" style={{ 
                                                        backgroundColor: "#2c3e50", 
                                                        border: "none", 
                                                        borderRadius: "0.5rem",
                                                        boxShadow: "0 4px 6px rgba(0,0,0,0.2)"
                                                    }}>
                                                        <div className="card-header" style={{ 
                                                            backgroundColor: "#34495e", 
                                                            borderBottom: "2px solid rgba(52, 152, 219, 0.3)",
                                                            borderRadius: "0.5rem 0.5rem 0 0",
                                                            padding: "1.25rem"
                                                        }}>
                                                            <h5 className="mb-0" style={{ color: "white", fontWeight: "600", fontSize: "1.25rem" }}>
                                                                <i className="fas fa-book me-2" style={{ color: "#3498db" }}></i>
                                                                {lesson.title}
                                                            </h5>
                                                        </div>
                                                        <div className="card-body" style={{ padding: "1.5rem" }}>
                                                            {/* Description */}
                                                            <div className="mb-4">
                                                                <h6 style={{ 
                                                                    color: "#95a5a6", 
                                                                    fontSize: "0.75rem", 
                                                                    fontWeight: "600", 
                                                                    marginBottom: "0.75rem",
                                                                    letterSpacing: "0.05em",
                                                                    textTransform: "uppercase"
                                                                }}>
                                                                    <i className="fas fa-align-left me-2"></i>
                                                                    Description
                                                                </h6>
                                                                <p style={{ 
                                                                    color: "#ecf0f1", 
                                                                    fontSize: "1rem", 
                                                                    lineHeight: "1.7", 
                                                                    marginBottom: 0 
                                                                }}>
                                                                    {lesson.description || 'This lesson covers important concepts and skills that will help you progress in your coursework.'}
                                                                </p>
                                                            </div>
                                                            
                                                            {/* Lesson Stats Grid */}
                                                            <div className="row g-3">
                                                                <div className="col-md-4">
                                                                    <div style={{ 
                                                                        backgroundColor: "#34495e", 
                                                                        padding: "1rem", 
                                                                        borderRadius: "0.375rem",
                                                                        border: '1px solid rgba(255,255,255,0.1)',
                                                                        textAlign: "center"
                                                                    }}>
                                                                        <div style={{ 
                                                                            color: "#3498db", 
                                                                            fontSize: "1.75rem", 
                                                                            marginBottom: "0.5rem" 
                                                                        }}>
                                                                            <i className="far fa-clock"></i>
                                                                        </div>
                                                                        <div style={{ 
                                                                            color: "white", 
                                                                            fontSize: "1.25rem", 
                                                                            fontWeight: "600",
                                                                            marginBottom: "0.25rem"
                                                                        }}>
                                                                            {lesson.duration_minutes} min
                                                                        </div>
                                                                        <div style={{ color: "#95a5a6", fontSize: "0.8rem" }}>
                                                                            Duration ({lessonHours.toFixed(2)} hours)
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-4">
                                                                    <div style={{ 
                                                                        backgroundColor: "#34495e", 
                                                                        padding: "1rem", 
                                                                        borderRadius: "0.375rem",
                                                                        border: '1px solid rgba(255,255,255,0.1)',
                                                                        textAlign: "center"
                                                                    }}>
                                                                        <div style={{ 
                                                                            color: lesson.is_completed ? "#2ecc71" : "#f39c12",
                                                                            fontSize: "1.75rem", 
                                                                            marginBottom: "0.5rem" 
                                                                        }}>
                                                                            <i className={lesson.is_completed ? "fas fa-check-circle" : "fas fa-circle-notch"}></i>
                                                                        </div>
                                                                        <div style={{ 
                                                                            color: "white", 
                                                                            fontSize: "1.25rem", 
                                                                            fontWeight: "600",
                                                                            marginBottom: "0.25rem"
                                                                        }}>
                                                                            {lesson.is_completed ? 'Completed' : 'Not Started'}
                                                                        </div>
                                                                        <div style={{ color: "#95a5a6", fontSize: "0.8rem" }}>
                                                                            Status
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div className="col-md-4">
                                                                    <div style={{ 
                                                                        backgroundColor: "#34495e", 
                                                                        padding: "1rem", 
                                                                        borderRadius: "0.375rem",
                                                                        border: '1px solid rgba(255,255,255,0.1)',
                                                                        textAlign: "center"
                                                                    }}>
                                                                        <div style={{ 
                                                                            color: "#9b59b6",
                                                                            fontSize: "1.75rem", 
                                                                            marginBottom: "0.5rem" 
                                                                        }}>
                                                                            <i className="fas fa-video"></i>
                                                                        </div>
                                                                        <div style={{ 
                                                                            color: "white", 
                                                                            fontSize: "1.25rem", 
                                                                            fontWeight: "600",
                                                                            marginBottom: "0.25rem"
                                                                        }}>
                                                                            Video
                                                                        </div>
                                                                        <div style={{ color: "#95a5a6", fontSize: "0.8rem" }}>
                                                                            Format Type
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    {/* What You'll Learn */}
                                                    <div className="card mb-4" style={{ 
                                                        backgroundColor: "#2c3e50", 
                                                        border: "none", 
                                                        borderRadius: "0.5rem",
                                                        boxShadow: "0 4px 6px rgba(0,0,0,0.2)"
                                                    }}>
                                                        <div className="card-header" style={{ 
                                                            backgroundColor: "#34495e", 
                                                            borderBottom: "1px solid rgba(255,255,255,0.1)",
                                                            borderRadius: "0.5rem 0.5rem 0 0",
                                                            padding: "1rem"
                                                        }}>
                                                            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                                <i className="fas fa-graduation-cap me-2" style={{ color: "#2ecc71" }}></i>
                                                                What You'll Learn
                                                            </h6>
                                                        </div>
                                                        <div className="card-body" style={{ padding: "1.25rem" }}>
                                                            <ul style={{ 
                                                                color: "#ecf0f1", 
                                                                fontSize: "0.95rem", 
                                                                lineHeight: "1.8",
                                                                paddingLeft: "1.5rem",
                                                                marginBottom: 0
                                                            }}>
                                                                <li className="mb-2">
                                                                    <strong>Core Concepts:</strong> Understand fundamental principles and key terminology
                                                                </li>
                                                                <li className="mb-2">
                                                                    <strong>Practical Skills:</strong> Apply knowledge through real-world examples and scenarios
                                                                </li>
                                                                <li className="mb-2">
                                                                    <strong>Best Practices:</strong> Learn industry-standard approaches and techniques
                                                                </li>
                                                                <li className="mb-2">
                                                                    <strong>Assessment Prep:</strong> Prepare for quizzes and evaluations on this material
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    
                                                    {/* Prerequisites & Requirements */}
                                                    <div className="card" style={{ 
                                                        backgroundColor: "#2c3e50", 
                                                        border: "none", 
                                                        borderRadius: "0.5rem",
                                                        boxShadow: "0 4px 6px rgba(0,0,0,0.2)"
                                                    }}>
                                                        <div className="card-header" style={{ 
                                                            backgroundColor: "#34495e", 
                                                            borderBottom: "1px solid rgba(255,255,255,0.1)",
                                                            borderRadius: "0.5rem 0.5rem 0 0",
                                                            padding: "1rem"
                                                        }}>
                                                            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                                <i className="fas fa-list-check me-2" style={{ color: "#f39c12" }}></i>
                                                                Prerequisites & Requirements
                                                            </h6>
                                                        </div>
                                                        <div className="card-body" style={{ padding: "1.25rem" }}>
                                                            <div className="mb-3">
                                                                <div className="d-flex align-items-start mb-2">
                                                                    <i className="fas fa-check-circle me-2 mt-1" style={{ color: "#2ecc71", fontSize: "1rem" }}></i>
                                                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>
                                                                        No prior experience required - suitable for all skill levels
                                                                    </span>
                                                                </div>
                                                                <div className="d-flex align-items-start mb-2">
                                                                    <i className="fas fa-check-circle me-2 mt-1" style={{ color: "#2ecc71", fontSize: "1rem" }}></i>
                                                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>
                                                                        Stable internet connection for video streaming
                                                                    </span>
                                                                </div>
                                                                <div className="d-flex align-items-start mb-2">
                                                                    <i className="fas fa-check-circle me-2 mt-1" style={{ color: "#2ecc71", fontSize: "1rem" }}></i>
                                                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>
                                                                        Sufficient video quota ({lessonHours.toFixed(2)} hours required)
                                                                    </span>
                                                                </div>
                                                                <div className="d-flex align-items-start">
                                                                    <i className="fas fa-check-circle me-2 mt-1" style={{ color: "#2ecc71", fontSize: "1rem" }}></i>
                                                                    <span style={{ color: "#ecf0f1", fontSize: "0.95rem" }}>
                                                                        Quiet environment for focused learning
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {/* Right Column - Quota & Actions */}
                                                <div className="col-md-4">
                                                    {/* Quota Status Card */}
                                                    <div className="card mb-4" style={{ 
                                                        backgroundColor: "#34495e", 
                                                        border: "3px solid " + getQuotaColor(quotaPercentage),
                                                        borderRadius: "0.5rem",
                                                        boxShadow: "0 4px 6px rgba(0,0,0,0.2)"
                                                    }}>
                                                        <div className="card-header" style={{ 
                                                            backgroundColor: "#2c3e50",
                                                            borderBottom: "2px solid " + getQuotaColor(quotaPercentage),
                                                            borderRadius: "0.5rem 0.5rem 0 0",
                                                            padding: "1rem"
                                                        }}>
                                                            <h6 className="mb-0" style={{ color: "white", fontWeight: "600" }}>
                                                                <i className="fas fa-hourglass-half me-2" style={{ color: getQuotaColor(quotaPercentage) }}></i>
                                                                Video Quota
                                                            </h6>
                                                        </div>
                                                        <div className="card-body" style={{ padding: "1.25rem" }}>
                                                            {/* Current Quota */}
                                                            <div className="mb-3">
                                                                <div className="d-flex justify-content-between mb-2">
                                                                    <span style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                        Current Remaining
                                                                    </span>
                                                                    <span style={{ color: "white", fontWeight: "600", fontSize: "1rem" }}>
                                                                        {quotaRemaining.toFixed(1)}h
                                                                    </span>
                                                                </div>
                                                                <div style={{ 
                                                                    width: "100%", 
                                                                    height: "12px", 
                                                                    backgroundColor: "#1e293b", 
                                                                    borderRadius: "6px", 
                                                                    overflow: "hidden",
                                                                    border: '1px solid rgba(255,255,255,0.2)'
                                                                }}>
                                                                    <div style={{ 
                                                                        width: `${quotaPercentage}%`, 
                                                                        height: "100%", 
                                                                        backgroundColor: getQuotaColor(quotaPercentage),
                                                                        transition: "width 0.3s ease"
                                                                    }}></div>
                                                                </div>
                                                                <div className="text-center mt-2">
                                                                    <small style={{ color: "#95a5a6", fontSize: "0.75rem" }}>
                                                                        {quotaRemaining.toFixed(1)} of {quotaTotal.toFixed(1)} hours available
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            
                                                            <hr style={{ borderColor: "rgba(255,255,255,0.1)", margin: "1rem 0" }} />
                                                            
                                                            {/* Quota Breakdown */}
                                                            <div className="mb-2">
                                                                <div className="d-flex justify-content-between mb-2">
                                                                    <span style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                        <i className="fas fa-video me-1" style={{ color: "#3498db" }}></i>
                                                                        This Lesson
                                                                    </span>
                                                                    <span style={{ color: "#3498db", fontWeight: "600", fontSize: "0.95rem" }}>
                                                                        {lessonHours.toFixed(2)}h
                                                                    </span>
                                                                </div>
                                                                <div className="d-flex justify-content-between mb-2">
                                                                    <span style={{ color: "#95a5a6", fontSize: "0.85rem" }}>
                                                                        <i className="fas fa-minus-circle me-1" style={{ color: "#e74c3c" }}></i>
                                                                        After Completion
                                                                    </span>
                                                                    <span style={{ 
                                                                        color: quotaAfterLesson >= 0 ? "#2ecc71" : "#e74c3c", 
                                                                        fontWeight: "600",
                                                                        fontSize: "0.95rem"
                                                                    }}>
                                                                        {quotaAfterLesson.toFixed(2)}h
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            
                                                            {/* Quota Status Message */}
                                                            <div className="mt-3 p-2" style={{
                                                                backgroundColor: quotaAfterLesson < 0 
                                                                    ? "rgba(231, 76, 60, 0.15)" 
                                                                    : quotaPercentage < 30 
                                                                        ? "rgba(243, 156, 18, 0.15)"
                                                                        : "rgba(46, 204, 113, 0.15)",
                                                                borderRadius: "0.375rem",
                                                                textAlign: "center",
                                                                border: "1px solid " + (quotaAfterLesson < 0 ? "#e74c3c" : quotaPercentage < 30 ? "#f39c12" : "#2ecc71")
                                                            }}>
                                                                <i className={
                                                                    quotaAfterLesson < 0 
                                                                        ? "fas fa-exclamation-triangle" 
                                                                        : quotaPercentage < 30 
                                                                            ? "fas fa-exclamation-circle"
                                                                            : "fas fa-check-circle"
                                                                } style={{ 
                                                                    color: quotaAfterLesson < 0 ? "#e74c3c" : quotaPercentage < 30 ? "#f39c12" : "#2ecc71",
                                                                    fontSize: "1.5rem",
                                                                    display: "block",
                                                                    marginBottom: "0.5rem"
                                                                }}></i>
                                                                <div style={{ 
                                                                    color: quotaAfterLesson < 0 ? "#e74c3c" : quotaPercentage < 30 ? "#f39c12" : "#2ecc71",
                                                                    fontWeight: "600",
                                                                    fontSize: "0.85rem"
                                                                }}>
                                                                    {quotaAfterLesson < 0 
                                                                        ? "Insufficient Quota"
                                                                        : quotaPercentage < 30
                                                                            ? "Low Quota"
                                                                            : "Quota Available"}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    {/* Action Buttons */}
                                                    <div className="card" style={{ 
                                                        backgroundColor: "#2c3e50", 
                                                        border: "none", 
                                                        borderRadius: "0.5rem",
                                                        boxShadow: "0 4px 6px rgba(0,0,0,0.2)"
                                                    }}>
                                                        <div className="card-body" style={{ padding: "1.25rem" }}>
                                                            <button
                                                                className="btn btn-lg w-100 mb-3"
                                                                disabled={quotaAfterLesson < 0}
                                                                onClick={async () => {
                                                                    console.log('Begin lesson:', lesson.id, lesson.title);
                                                                    
                                                                    // Convert duration_minutes to seconds
                                                                    const videoDurationSeconds = lesson.duration_minutes * 60;
                                                                    
                                                                    // Start session via hook
                                                                    const result = await startSession(
                                                                        lesson.id,
                                                                        courseAuthId,
                                                                        videoDurationSeconds,
                                                                        lesson.title
                                                                    );
                                                                    
                                                                    if (result.success) {
                                                                        // Session started - go to player
                                                                        setViewMode('player');
                                                                    } else {
                                                                        // Show error
                                                                        alert(`Failed to start session: ${result.error}`);
                                                                    }
                                                                }}
                                                                style={{
                                                                    backgroundColor: quotaAfterLesson < 0 ? "#7f8c8d" : "#2ecc71",
                                                                    color: "white",
                                                                    border: "none",
                                                                    padding: "0.875rem",
                                                                    fontSize: "1.1rem",
                                                                    fontWeight: "600",
                                                                    borderRadius: "0.375rem",
                                                                    cursor: quotaAfterLesson < 0 ? 'not-allowed' : 'pointer',
                                                                    opacity: quotaAfterLesson < 0 ? 0.6 : 1,
                                                                    transition: "all 0.2s"
                                                                }}
                                                                onMouseEnter={(e) => {
                                                                    if (quotaAfterLesson >= 0) {
                                                                        e.currentTarget.style.backgroundColor = "#27ae60";
                                                                        e.currentTarget.style.transform = "translateY(-2px)";
                                                                        e.currentTarget.style.boxShadow = "0 6px 12px rgba(46,204,113,0.4)";
                                                                    }
                                                                }}
                                                                onMouseLeave={(e) => {
                                                                    if (quotaAfterLesson >= 0) {
                                                                        e.currentTarget.style.backgroundColor = "#2ecc71";
                                                                        e.currentTarget.style.transform = "translateY(0)";
                                                                        e.currentTarget.style.boxShadow = "none";
                                                                    }
                                                                }}
                                                            >
                                                                <i className="fas fa-play-circle me-2"></i>
                                                                Begin Lesson
                                                            </button>
                                                            
                                                            <button
                                                                className="btn btn-lg w-100"
                                                                onClick={() => {
                                                                    setViewMode('list');
                                                                    setPreviewLessonId(null);
                                                                }}
                                                                style={{
                                                                    backgroundColor: "transparent",
                                                                    color: "white",
                                                                    border: "2px solid rgba(255,255,255,0.3)",
                                                                    padding: "0.875rem",
                                                                    fontSize: "1rem",
                                                                    fontWeight: "600",
                                                                    borderRadius: "0.375rem",
                                                                    transition: "all 0.2s"
                                                                }}
                                                                onMouseEnter={(e) => {
                                                                    e.currentTarget.style.backgroundColor = "rgba(255,255,255,0.1)";
                                                                    e.currentTarget.style.borderColor = "white";
                                                                }}
                                                                onMouseLeave={(e) => {
                                                                    e.currentTarget.style.backgroundColor = "transparent";
                                                                    e.currentTarget.style.borderColor = "rgba(255,255,255,0.3)";
                                                                }}
                                                            >
                                                                <i className="fas fa-times me-2"></i>
                                                                Cancel
                                                            </button>
                                                            
                                                            {quotaAfterLesson < 0 && (
                                                                <div className="mt-3 text-center">
                                                                    <small style={{ 
                                                                        color: "#e74c3c", 
                                                                        fontSize: "0.8rem",
                                                                        fontStyle: "italic" 
                                                                    }}>
                                                                        <i className="fas fa-info-circle me-1"></i>
                                                                        Complete remediation lessons to earn quota refunds
                                                                    </small>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                    
                                                    {/* Tips Card */}
                                                    <div className="card mt-4" style={{ 
                                                        backgroundColor: "#34495e", 
                                                        border: "1px solid rgba(52, 152, 219, 0.3)",
                                                        borderRadius: "0.5rem"
                                                    }}>
                                                        <div className="card-body" style={{ padding: "1rem" }}>
                                                            <h6 style={{ color: "#3498db", fontWeight: "600", fontSize: "0.9rem", marginBottom: "0.75rem" }}>
                                                                <i className="fas fa-lightbulb me-2"></i>
                                                                Study Tips
                                                            </h6>
                                                            <ul style={{ 
                                                                color: "#ecf0f1", 
                                                                fontSize: "0.8rem", 
                                                                lineHeight: "1.6",
                                                                paddingLeft: "1.25rem",
                                                                marginBottom: 0
                                                            }}>
                                                                <li className="mb-2">Take notes during the video</li>
                                                                <li className="mb-2">You can rewind if you miss something</li>
                                                                <li className="mb-2">Complete in one sitting for best retention</li>
                                                                <li>Review material before live class</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })()}
                                
                                {/* Video Player Mode */}
                                {viewMode === 'player' && (
                                    <div className="video-player-mode">
                                        <h4 style={{ color: "white" }}>Video Player Coming Soon...</h4>
                                        <button
                                            className="btn btn-primary"
                                            onClick={() => {
                                                setViewMode('list');
                                                setPreviewLessonId(null);
                                            }}
                                        >
                                            Back to Lessons
                                        </button>
                                    </div>
                                )}
                            </div>
                        )}

                        {activeTab === 'documentation' && (
                            <div className="documentation-tab">
                                <h4 className="mb-4" style={{ color: "white" }}>Course Documentation</h4>
                                
                                <div className="card" style={{ backgroundColor: "#2c3e50", border: "none" }}>
                                    <div className="card-header" style={{ backgroundColor: "#34495e", borderBottom: "1px solid rgba(255,255,255,0.1)" }}>
                                        <h6 className="mb-0" style={{ color: "white" }}>
                                            <i className="fas fa-folder me-2"></i>
                                            Available Documents
                                        </h6>
                                    </div>
                                    <div className="card-body">
                                        <p style={{ color: "#95a5a6" }}>
                                            Course documents, PDFs, and supplementary materials will appear here.
                                        </p>
                                        <div className="list-group" style={{ backgroundColor: "transparent" }}>
                                            {/* TODO: Replace with real document data */}
                                            {['Course Syllabus', 'Study Guide', 'Reference Materials', 'Additional Resources'].map((doc, idx) => (
                                                <div 
                                                    key={idx}
                                                    className="list-group-item d-flex justify-content-between align-items-center"
                                                    style={{ backgroundColor: "#34495e", border: "1px solid rgba(255,255,255,0.1)", color: "white", marginBottom: "0.5rem" }}
                                                >
                                                    <div>
                                                        <i className="fas fa-file-pdf me-2" style={{ color: "#e74c3c" }}></i>
                                                        {doc}
                                                    </div>
                                                    <button className="btn btn-sm btn-outline-light">
                                                        <i className="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MainOffline;
