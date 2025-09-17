import React, { useState, useEffect } from 'react';
import { Nav } from 'react-bootstrap';
import { Student, CourseAuth } from '../types/LaravelProps';

interface Lesson {
    id: number;
    title: string;
    unit_id: number;
    unit_title: string;
    unit_ordering: number;
    credit_minutes: number;
    video_seconds: number;
}

interface StudentClassroomProps {
    student: Student;
    courseAuth: CourseAuth;
    course: {
        id: number;
        title: string;
        description?: string;
        slug: string;
    };
    lessons: Lesson[];
    modality: 'online' | 'in_person' | 'offline' | 'unknown';
    current_day_only: boolean;
    onBackToDashboard?: () => void;
}

// Navigation state persistence
const NAVIGATION_STATE_KEY = 'frost_student_navigation_state';

interface NavigationState {
    view: 'DASHBOARD' | 'CLASSROOM';
    courseAuthId?: number;
    activeTab?: string;
}

const StudentClassroom: React.FC<StudentClassroomProps> = ({
    student,
    courseAuth,
    course,
    lessons,
    modality,
    current_day_only,
    onBackToDashboard
}) => {
    // Connection status management
    const [isOnline, setIsOnline] = useState(navigator.onLine);
    const [connectionStatus, setConnectionStatus] = useState<'online' | 'offline' | 'reconnecting'>('online');

    // Tab management with persistence
    const [activeTab, setActiveTab] = useState<'dashboard' | 'videos' | 'documents'>(() => {
        // Try to restore active tab from localStorage
        try {
            const saved = localStorage.getItem(NAVIGATION_STATE_KEY);
            if (saved) {
                const state: NavigationState = JSON.parse(saved);
                if (state.activeTab && ['dashboard', 'videos', 'documents'].includes(state.activeTab)) {
                    return state.activeTab as 'dashboard' | 'videos' | 'documents';
                }
            }
        } catch (error) {
            console.warn('Failed to restore active tab:', error);
        }
        return 'dashboard'; // Default tab
    });
    const [sidebarCollapsed, setSidebarCollapsed] = useState(window.innerWidth <= 1024);

    // Progress calculation (CourseAuth doesn't have progress field, so we'll calculate based on completion)
    const isCompleted = courseAuth.completed_at !== null;
    const isPassed = courseAuth.is_passed;
    const isStarted = courseAuth.start_date !== null;
    const progress = isCompleted ? 100 : isStarted ? 50 : 0; // Simple progress calculation

    // Monitor online/offline status
    useEffect(() => {
        const handleOnline = () => {
            setIsOnline(true);
            setConnectionStatus('online');
        };

        const handleOffline = () => {
            setIsOnline(false);
            setConnectionStatus('offline');
        };

        const handleResize = () => {
            setSidebarCollapsed(window.innerWidth <= 1024);
        };

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);
        window.addEventListener('resize', handleResize);

        return () => {
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    // Tab keyboard navigation with persistence
    const handleTabKeyDown = (event: React.KeyboardEvent, tabId: 'dashboard' | 'videos' | 'documents') => {
        const tabs = ['dashboard', 'videos', 'documents'];
        const currentIndex = tabs.indexOf(activeTab);

        if (event.key === 'ArrowLeft' && currentIndex > 0) {
            event.preventDefault();
            setActiveTabWithPersistence(tabs[currentIndex - 1] as typeof activeTab);
        } else if (event.key === 'ArrowRight' && currentIndex < tabs.length - 1) {
            event.preventDefault();
            setActiveTabWithPersistence(tabs[currentIndex + 1] as typeof activeTab);
        } else if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            setActiveTabWithPersistence(tabId);
        }
    };

    // Save active tab state to localStorage
    const saveActiveTabState = (tabName: string) => {
        try {
            const saved = localStorage.getItem(NAVIGATION_STATE_KEY);
            let state: NavigationState = saved ? JSON.parse(saved) : { view: 'CLASSROOM' };
            state.activeTab = tabName;
            state.courseAuthId = courseAuth.id;
            localStorage.setItem(NAVIGATION_STATE_KEY, JSON.stringify(state));
        } catch (error) {
            console.warn('Failed to save active tab state:', error);
        }
    };

    // Enhanced tab setter that persists state
    const setActiveTabWithPersistence = (tabName: 'dashboard' | 'videos' | 'documents') => {
        setActiveTab(tabName);
        saveActiveTabState(tabName);
    };

    // Navigation handler to go back to dashboard (SPA style)
    const handleBackToDashboard = () => {
        if (onBackToDashboard) {
            console.log("📋 Using SPA navigation back to dashboard");
            onBackToDashboard();
        } else {
            console.log("📋 Fallback to page navigation");
            window.location.href = '/classroom';
        }
    };

    // Tab panel components
    const DashboardPanel = () => (
        <div role="tabpanel" aria-labelledby="tab-dashboard" className="h-100">
            <div className="text-center py-5">
                <i className="fas fa-dashboard fs-1 text-muted mb-3"></i>
                <h4 className="text-muted">No dashboard yet</h4>
                <p className="text-muted">Course Dashboard will appear here when available.</p>
            </div>
        </div>
    );

    const VideosPanel = () => (
        <div role="tabpanel" aria-labelledby="tab-videos" className="h-100">
            <div className="text-center py-5">
                <i className="fas fa-video fs-1 text-muted mb-3"></i>
                <h4 className="text-muted">No videos yet</h4>
                <p className="text-muted">Course videos will appear here when available.</p>
            </div>
        </div>
    );

    const DocumentsPanel = () => (
        <div role="tabpanel" aria-labelledby="tab-documents" className="h-100">
            <div className="text-center py-5">
                <i className="fas fa-file-alt fs-1 text-muted mb-3"></i>
                <h4 className="text-muted">No documents yet</h4>
                <p className="text-muted">Course documents and materials will appear here when available.</p>
            </div>
        </div>
    );

    return (
        <div className="app d-flex vh-100 mt-3">
            {/* Sidebar - Fixed left, global nav only */}
            <div className={`sidebar bg-dark text-white ${sidebarCollapsed ? 'collapsed' : ''}`}
                 style={{
                     width: sidebarCollapsed ? '60px' : '240px',
                     minWidth: sidebarCollapsed ? '60px' : '240px',
                     transition: 'all 0.3s ease'
                 }}>
                <div className="sidebar-header p-3 border-bottom border-secondary">
                    <div className="d-flex align-items-center">
                        <i className="fas fa-graduation-cap fs-4 me-2"></i>
                        {!sidebarCollapsed && <span className="fw-bold">FROST</span>}
                    </div>
                </div>
                <nav className="sidebar-nav p-2 d-flex flex-column h-100">
                    <button
                        className="btn btn-outline-light w-100 mb-3 d-flex align-items-center"
                        onClick={handleBackToDashboard}
                        title="Back to Dashboard"
                    >
                        <i className="fas fa-arrow-left me-2"></i>
                        {!sidebarCollapsed && 'Dashboard'}
                    </button>

                    {/* Course Lessons Section */}
                    {!sidebarCollapsed && lessons && lessons.length > 0 && (
                        <div className="mb-3 flex-grow-1">
                            {/* Course Header - matches screenshot format */}
                            <div className="course-header mb-3 p-2 text-center" style={{
                                backgroundColor: 'rgba(108, 117, 125, 0.8)',
                                border: '1px solid rgba(108, 117, 125, 0.5)',
                                borderRadius: '4px'
                            }}>
                                <div className="text-white fw-bold small">
                                    {course.title.toUpperCase()}
                                    {current_day_only && (
                                        <small className="badge bg-info ms-2 d-block mt-1">Today Only</small>
                                    )}
                                </div>
                            </div>

                            <div className="lessons-list" style={{
                                maxHeight: '60vh',
                                overflowY: 'auto',
                                paddingRight: '4px'
                            }}>
                                {lessons.map((lesson, lessonIndex) => {
                                    // For now, simulate completion status (will implement proper logic later)
                                    const isCompleted = lessonIndex < 4; // First 4 lessons completed
                                    const lessonColors = [
                                        { bg: '#28a745', border: '#1e7e34' }, // Green - completed
                                        { bg: '#17a2b8', border: '#138496' }, // Teal
                                        { bg: '#6f42c1', border: '#5a3296' }, // Purple
                                        { bg: '#fd7e14', border: '#e55a00' }, // Orange
                                        { bg: '#6c757d', border: '#545b62' }  // Gray - pending
                                    ];
                                    const colorIndex = isCompleted ? 0 : (lessonIndex >= 4 ? 4 : lessonIndex);

                                    return (
                                        <div
                                            key={lesson.id}
                                            className="lesson-card mb-2 p-3 rounded"
                                            style={{
                                                backgroundColor: lessonColors[colorIndex].bg,
                                                border: `1px solid ${lessonColors[colorIndex].border}`,
                                                cursor: 'pointer',
                                                transition: 'all 0.2s ease',
                                                color: '#ffffff'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.currentTarget.style.transform = 'translateY(-1px)';
                                                e.currentTarget.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.currentTarget.style.transform = 'translateY(0)';
                                                e.currentTarget.style.boxShadow = 'none';
                                            }}
                                        >
                                            <div className="d-flex justify-content-between align-items-start">
                                                <div className="lesson-content flex-grow-1">
                                                    <h6 className="lesson-title mb-2 fw-bold text-white">
                                                        {lesson.title}
                                                    </h6>
                                                    <div className="lesson-meta small text-white-75">
                                                        Credit Minutes: <strong>{lesson.credit_minutes}</strong>
                                                    </div>
                                                </div>
                                                <div className="lesson-status">
                                                    <span className={`badge ${isCompleted ? 'bg-light text-dark' : 'bg-dark text-light'} ms-2`}>
                                                        {isCompleted ? 'Completed' : 'Pending'}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>                            {/* Course modality indicator */}
                            <div className="mt-2 p-2 rounded" style={{
                                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                                border: '1px solid rgba(52, 152, 219, 0.3)'
                            }}>
                                <small className="text-info d-flex align-items-center">
                                    <i className={`fas fa-${modality === 'online' ? 'wifi' : modality === 'in_person' ? 'users' : 'book'} me-2`}></i>
                                    {modality === 'online' ? 'Live Online' :
                                     modality === 'in_person' ? 'In-Person' :
                                     'Self-Paced'}
                                </small>
                            </div>
                        </div>
                    )}

                    {/* Collapsed state - show lesson count */}
                    {sidebarCollapsed && lessons && lessons.length > 0 && (
                        <div className="mb-3 text-center">
                            <div
                                className="p-2 rounded"
                                style={{
                                    backgroundColor: 'rgba(255,255,255,0.1)',
                                    border: '1px solid rgba(255,255,255,0.2)'
                                }}
                                title={`${lessons.length} lessons available`}
                            >
                                <i className="fas fa-book-open text-white-50"></i>
                                <div className="small text-white-50 mt-1">{lessons.length}</div>
                            </div>
                        </div>
                    )}

                    <div className="mt-auto">
                        <div className={`text-${isOnline ? 'success' : 'danger'} small p-2`}>
                            <i className={`fas fa-${isOnline ? 'wifi' : 'exclamation-triangle'} me-1`}></i>
                            {!sidebarCollapsed && connectionStatus.charAt(0).toUpperCase() + connectionStatus.slice(1)}
                        </div>
                    </div>
                </nav>
            </div>

            {/* Main - Flex column */}
            <main className="main flex-grow-1 d-flex flex-column">
                {/* Title Bar - Sticky within Main */}
                <div className="titlebar sticky-top" style={{
                    background: 'linear-gradient(135deg, #2c3e50 0%, #34495e 100%)',
                    borderBottom: '1px solid #34495e',
                    padding: '1rem 1.5rem 0 1.5rem'
                }}>
                    {/* Page title - Left aligned */}
                    <div className="mb-3">
                        <h1 className="h4 mb-1 text-white">{course.title}</h1>
                        <small className="text-white-50">Student: {student.fname} {student.lname}</small>
                    </div>

                    {/* Tabs - React Bootstrap Nav with custom styling */}
                    <Nav
                        variant="tabs"
                        activeKey={activeTab}
                        onSelect={(selectedKey) => setActiveTabWithPersistence(selectedKey as 'dashboard' | 'videos' | 'documents')}
                        style={{
                            borderBottom: 'none'
                        }}
                    >
                        <Nav.Item>
                            <Nav.Link
                                eventKey="dashboard"
                                id="tab-dashboard"
                                aria-controls="panel-dashboard"
                                onKeyDown={(e) => handleTabKeyDown(e, 'dashboard')}
                                style={{
                                    color: activeTab === 'dashboard' ? '#fff' : '#bdc3c7',
                                    backgroundColor: activeTab === 'dashboard' ? 'rgba(255,255,255,0.1)' : 'transparent',
                                    border: activeTab === 'dashboard' ? '1px solid rgba(255,255,255,0.2)' : '1px solid transparent',
                                    borderBottom: activeTab === 'dashboard' ? '2px solid #3498db' : '1px solid transparent'
                                }}
                            >
                                <i className="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                            <Nav.Link
                                eventKey="videos"
                                id="tab-videos"
                                aria-controls="panel-videos"
                                onKeyDown={(e) => handleTabKeyDown(e, 'videos')}
                                style={{
                                    color: activeTab === 'videos' ? '#fff' : '#bdc3c7',
                                    backgroundColor: activeTab === 'videos' ? 'rgba(255,255,255,0.1)' : 'transparent',
                                    border: activeTab === 'videos' ? '1px solid rgba(255,255,255,0.2)' : '1px solid transparent',
                                    borderBottom: activeTab === 'videos' ? '2px solid #3498db' : '1px solid transparent'
                                }}
                            >
                                <i className="fas fa-video me-2"></i>
                                Videos
                            </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                            <Nav.Link
                                eventKey="documents"
                                id="tab-documents"
                                aria-controls="panel-documents"
                                onKeyDown={(e) => handleTabKeyDown(e, 'documents')}
                                style={{
                                    color: activeTab === 'documents' ? '#fff' : '#bdc3c7',
                                    backgroundColor: activeTab === 'documents' ? 'rgba(255,255,255,0.1)' : 'transparent',
                                    border: activeTab === 'documents' ? '1px solid rgba(255,255,255,0.2)' : '1px solid transparent',
                                    borderBottom: activeTab === 'documents' ? '2px solid #3498db' : '1px solid transparent'
                                }}
                            >
                                <i className="fas fa-file-alt me-2"></i>
                                Documents
                            </Nav.Link>
                        </Nav.Item>
                    </Nav>
                </div>                {/* Content Area - Single scroll container */}
                <div className="content flex-grow-1 p-4 overflow-auto">
                    {activeTab === 'dashboard' && <DashboardPanel />}
                    {activeTab === 'videos' && <VideosPanel />}
                    {activeTab === 'documents' && <DocumentsPanel />}
                </div>
            </main>
        </div>
    );
};

export default StudentClassroom;
