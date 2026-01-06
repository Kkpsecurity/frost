import React, { useState } from "react";
import { useQuery } from '@tanstack/react-query';
import LessonsPanel from "../components/LessonsPanel";
import ZoomSetupPanel from "../components/ZoomSetupPanel";
import StudentsPanel from "../Classroom/StudentsPanel";
import InstructorLessonProgressBar from "../Components/InstructorLessonProgressBar";
import FrostChatCard from "../Components/FrostChatCard";

interface ClassroomInterfaceProps {
    instructorData?: any;
    classroomData?: any;
    chatData?: any;
}

/**
 * Instructor Classroom Interface - Full-height 3-column layout
 * - NO padding, NO margin
 * - Left sidebar: 280px (collapsed 60px) - Lessons
 * - Center: flex - Teaching area with titlebar
 * - Right sidebar: 300px (collapsed 60px) - Students
 */
const ClassroomInterface: React.FC<ClassroomInterfaceProps> = ({
    instructorData,
    classroomData,
    chatData,
}) => {
    const instUnit = instructorData?.instUnit;
    const instructor = instructorData?.instructor_user;
    const courseDateId = instUnit?.course_date_id;

    const currentCourseDate = classroomData?.courseDates?.[0];
    const courseName = currentCourseDate?.course_name;

    const [leftCollapsed, setLeftCollapsed] = useState(false);
    const [rightCollapsed, setRightCollapsed] = useState(false);
    const [isZoomReady, setIsZoomReady] = useState(false);

    const studentCount = classroomData?.student_count || 0;

    // Fetch lessons for current course date
    const { data: lessonsData, isLoading: lessonsLoading, error: lessonsError } = useQuery({
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

    // Get lessons data
    const lessons = lessonsData?.lessons || [];
    const currentLesson = lessons.find((l: any) => l.status === 'in_progress') || null;

    if (!instUnit) {
        return (
            <div style={{ padding: "20px" }}>
                <div className="alert alert-warning">
                    <h4>No Active Classroom</h4>
                    <p className="mb-0">
                        Please start a class to enter the classroom interface.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <>
            <div className="classroom-container">
                {/* LEFT SIDEBAR - Lessons */}
                <LessonsPanel
                    courseDateId={courseDateId}
                    collapsed={leftCollapsed}
                    onToggle={() => setLeftCollapsed(!leftCollapsed)}
                    instUnit={instUnit}
                    zoomReady={isZoomReady}
                />

                {/* CENTER - Teaching Area */}
                <main className="main-content">
                    <div className="titlebar">
                        <div className="titlebar-left">
                            <div className="d-flex align-items-center gap-2">
                                <i className="fas fa-chalkboard-teacher text-light mr-2" />
                                <span className="text-light">
                                    Teaching Area
                                </span>
                            </div>
                        </div>
                        <div className="titlebar-right">
                            {/* End Class control intentionally not shown yet */}
                        </div>
                    </div>

                    {/* Teaching Content */}
                    <div className="teaching-area">
                        {/* Zoom Setup Card - Always visible */}
                        <div className="zoom-card-container" style={{ padding: '20px' }}>
                            <ZoomSetupPanel
                                instUnit={instUnit}
                                courseName={courseName}
                                onZoomReadyChange={setIsZoomReady}
                            />
                        </div>

                        {/* Lesson Progress Bar - Shows current lesson progress */}
                        <div className="progress-container" style={{ padding: '0 20px' }}>
                            <InstructorLessonProgressBar
                                currentLesson={currentLesson}
                                lessons={lessons}
                            />
                        </div>

                        {/* Chat Room - Instructor and student communication */}
                        <div className="chat-container" style={{ padding: '0 20px' }}>
                            <FrostChatCard
                                course_date_id={courseDateId || 0}
                                isChatEnabled={true}
                                chatUser={{
                                    id: instructor?.id || 0,
                                    name: instructor?.fname + ' ' + instructor?.lname || 'Instructor',
                                    email: instructor?.email || 'instructor@example.com',
                                    user_type: 'instructor',
                                    avatar: instructor?.avatar || '/images/default-avatar.png'
                                }}
                                darkMode={true}
                                debug={true}
                            />
                        </div>
                    </div>
                </main>

                {/* RIGHT SIDEBAR - Students */}
                <aside
                    className={`sidebar sidebar-right ${
                        rightCollapsed ? "collapsed" : ""
                    }`}
                >
                    <div className="sidebar-header">
                        <div className="sidebar-title">
                            {!rightCollapsed && (
                                <>
                                    <i className="fas fa-users" />
                                    <span>Students ({studentCount})</span>
                                </>
                            )}
                            {rightCollapsed && <i className="fas fa-users" />}
                        </div>
                        <button
                            className="btn-collapse"
                            onClick={() => setRightCollapsed(!rightCollapsed)}
                            title={rightCollapsed ? "Expand" : "Collapse"}
                        >
                            <i
                                className={`fas ${
                                    rightCollapsed
                                        ? "fa-chevron-left"
                                        : "fa-chevron-right"
                                }`}
                            />
                        </button>
                    </div>
                    <div className="sidebar-content">
                        {!rightCollapsed && (
                            <div className="p-2">
                                <StudentsPanel
                                    courseDateId={courseDateId}
                                    instUnitId={instUnit?.id}
                                />
                            </div>
                        )}
                        {rightCollapsed && (
                            <div className="collapsed-icons">
                                <i
                                    className="fas fa-user-friends"
                                    title="Students"
                                />
                            </div>
                        )}
                    </div>
                </aside>
            </div>

            {/* CSS - Clean full-height 3-column layout */}
            <style>{`
        .classroom-container {
          display: flex;
          min-height: calc(100vh - 57px); /* AdminLTE navbar height */
          margin: 0;
          padding: 0;
          overflow: visible;
        }

        .sidebar {
          display: flex;
          flex-direction: column;
          background: #343a40;
          color: white;
          transition: width 0.3s ease;
        }

        .sidebar-left {
          width: ${leftCollapsed ? "60px" : "280px"};
          border-right: 1px solid #dee2e6;
        }

        .sidebar-right {
          width: ${rightCollapsed ? "60px" : "300px"};
          border-left: 1px solid #dee2e6;
        }

        .sidebar-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 12px 15px;
          background: #212529;
          border-bottom: 1px solid #495057;
          min-height: 60px;
        }

        .sidebar-title {
          display: flex;
          align-items: center;
          gap: 10px;
          font-weight: 600;
          font-size: 16px;
          white-space: nowrap;
          overflow: hidden;
        }

        .btn-collapse {
          background: transparent;
          border: none;
          color: white;
          padding: 5px 8px;
          cursor: pointer;
          border-radius: 4px;
          transition: background 0.2s;
        }

        .btn-collapse:hover {
          background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-content {
          flex: 1;
          overflow-y: auto;
          overflow-x: hidden;
        }

        .collapsed-icons {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 20px;
          padding: 20px 0;
        }

        .collapsed-icons i {
          font-size: 24px;
          color: #adb5bd;
          cursor: pointer;
        }

        .collapsed-icons i:hover {
          color: white;
        }

        .main-content {
          flex: 1;
          display: flex;
          flex-direction: column;
          background: #343a40;
          overflow: hidden;
        }

        .titlebar {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 15px 20px;
          background: #212529;
          border-bottom: 1px solid #495057;
          min-height: 60px;
        }

        .titlebar-left {
          display: flex;
          align-items: center;
        }

        .titlebar-right {
          display: flex;
          align-items: center;
        }

        .teaching-area {
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: stretch;
          justify-content: flex-start;
          background: #343a40;
          margin: 0;
          padding: 0;
          overflow: hidden;
        }

        .zoom-setup-top {
          flex-shrink: 0;
          border-bottom: 1px solid #495057;
        }

        .video-stage {
          flex: 1;
          background: #343a40;
        }

        .placeholder-content {
          text-align: center;
          padding: 40px 20px;
        }

        /* Scrollbar styling */
        .sidebar-content::-webkit-scrollbar {
          width: 6px;
        }

        .sidebar-content::-webkit-scrollbar-track {
          background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-content::-webkit-scrollbar-thumb {
          background: rgba(255, 255, 255, 0.2);
          border-radius: 3px;
        }

        .sidebar-content::-webkit-scrollbar-thumb:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
          .classroom-container {
            flex-direction: column;
          }
          .sidebar {
            width: 100% !important;
            height: auto !important;
          }
        }
      `}</style>
        </>
    );
};

export default ClassroomInterface;
