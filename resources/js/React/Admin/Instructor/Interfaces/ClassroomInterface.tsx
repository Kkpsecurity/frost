import React, { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import LessonsPanel from "../components/LessonsPanel";
import ZoomSetupPanel from "../components/ZoomSetupPanel";
import StudentsPanel from "../Classroom/StudentsPanel";
import InstructorLessonProgressBar from "../Components/InstructorLessonProgressBar";
import FrostChatCard from "../Components/FrostChatCard";
import StudentIdentityPanel from "../components/StudentIdentityPanel";

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
    const instructor = instructorData?.instructor; // Fixed: was instructor_user, should be instructor
    const zoomStatus = instructorData?.zoom; // NEW: Get zoom status from instructor poll
    const courseDateId = instUnit?.course_date_id;

    console.log("🔍 ClassroomInterface: courseDateId debug", {
        instUnit,
        courseDateId,
        hasInstUnit: !!instUnit,
        instUnitKeys: instUnit ? Object.keys(instUnit) : [],
    });

    const currentCourseDate = classroomData?.courseDates?.[0];
    const courseName = currentCourseDate?.course_name;

    // Determine if current user is assistant (not the instructor)
    const currentUserId = instructorData?.instructor?.id;
    const instructorId = instUnit?.created_by;
    const assistantId = instUnit?.assistant_id;
    const isAssistant =
        currentUserId === assistantId && currentUserId !== instructorId;

    console.log("🎨 ClassroomInterface: Role detection:", {
        currentUserId,
        instructorId,
        assistantId,
        isAssistant: isAssistant
            ? "YES - READ ONLY MODE"
            : "NO - FULL CONTROLS",
    });

    const [leftCollapsed, setLeftCollapsed] = useState(false);
    const [rightCollapsed, setRightCollapsed] = useState(false);
    const [isZoomReady, setIsZoomReady] = useState(false);
    const [selectedStudent, setSelectedStudent] = useState<{
        id: number;
        name: string;
        email: string;
        course_auth_id: number;
        student_unit_id?: number;
    } | null>(null);

    const studentCount = classroomData?.student_count || 0;

    // Fetch lessons for current course date
    const {
        data: lessonsData,
        isLoading: lessonsLoading,
        error: lessonsError,
    } = useQuery({
        queryKey: ["lessons", courseDateId],
        queryFn: async () => {
            if (!courseDateId) return null;

            const response = await fetch(
                `/admin/instructors/data/lessons/${courseDateId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                },
            );

            if (!response.ok) {
                throw new Error(
                    `Failed to fetch lessons: ${response.statusText}`,
                );
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

    // Build currentLesson from instUnitLesson (real-time lesson tracking)
    const instUnitLesson = instUnit?.instUnitLesson;
    let currentLesson = null;

    if (instUnitLesson && instUnitLesson.lesson_id) {
        // Find the lesson details from lessons array
        const lessonDetails = lessons.find(
            (l: any) => l.id === instUnitLesson.lesson_id,
        );

        if (lessonDetails) {
            currentLesson = {
                id: lessonDetails.id,
                lesson_name: lessonDetails.title,
                lesson_description: lessonDetails.description,
                duration_minutes: lessonDetails.duration_minutes,
                status: "in_progress",
                start_time: instUnitLesson.started_at,
                progress_minutes: 0, // Will be calculated by progress bar
            };
        }
    }

    // Handle Leave Class action for assistants
    const handleLeaveClass = async () => {
        if (!confirm("Are you sure you want to leave this class?")) return;

        try {
            // Clear assistant_id from InstUnit
            const response = await fetch(
                `/admin/instructors/classroom/leave-assist/${instUnit.id}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN":
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content") || "",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                },
            );

            const result = await response.json();
            if (result.success) {
                // Redirect back to bulletin board
                window.location.href = "/admin/instructors";
            } else {
                alert(`Failed to leave class: ${result.message}`);
            }
        } catch (error: any) {
            console.error("Error leaving class:", error);
            alert(`Error leaving class: ${error.message}`);
        }
    };

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
                {/* LEFT SIDEBAR - Lessons (Hidden for assistants) */}
                {!isAssistant && (
                    <LessonsPanel
                        courseDateId={courseDateId}
                        collapsed={leftCollapsed}
                        onToggle={() => setLeftCollapsed(!leftCollapsed)}
                        instUnit={instUnit}
                        zoomReady={isZoomReady}
                    />
                )}

                {/* CENTER - Teaching Area */}
                <main
                    className={`main-content ${
                        isAssistant ? "assistant-no-left-sidebar" : ""
                    }`}
                >
                    <div className="titlebar">
                        <div className="titlebar-left">
                            <div className="d-flex align-items-center gap-2">
                                <i
                                    className={`fas ${
                                        isAssistant
                                            ? "fa-hands-helping"
                                            : "fa-chalkboard-teacher"
                                    } text-light mr-2`}
                                />
                                <span className="text-light">
                                    {isAssistant
                                        ? "Assistant View"
                                        : "Teaching Area"}
                                </span>
                            </div>
                        </div>
                        <div className="titlebar-right">
                            {isAssistant && (
                                <button
                                    className="btn btn-danger btn-sm"
                                    onClick={handleLeaveClass}
                                >
                                    <i className="fas fa-sign-out-alt mr-1"></i>
                                    Leave Class
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Teaching Content */}
                    <div className="teaching-area">
                        {/* Zoom Setup Card - Always visible */}
                        <div
                            className="zoom-card-container"
                            style={{ padding: "20px" }}
                        >
                            <ZoomSetupPanel
                                instUnit={instUnit}
                                courseName={courseName}
                                zoomStatusFromPoll={zoomStatus}
                                onZoomReadyChange={setIsZoomReady}
                                isAssistant={isAssistant}
                            />
                        </div>

                        {/* Student Identity Validation Panel - Shows when clicking student name */}
                        {selectedStudent && courseDateId && (
                            <div
                                className="student-identity-container"
                                style={{ padding: "0 20px 20px 20px" }}
                            >
                                <StudentIdentityPanel
                                    studentId={selectedStudent.id}
                                    courseDateId={courseDateId}
                                    onClose={() => setSelectedStudent(null)}
                                />
                            </div>
                        )}

                        {/* Lesson Progress Bar - Shows current lesson progress */}
                        <div
                            className="progress-container"
                            style={{ padding: "0 20px" }}
                        >
                            <InstructorLessonProgressBar
                                currentLesson={currentLesson}
                                lessons={lessons}
                            />
                        </div>

                        {/* Chat Room - Instructor and student communication */}
                        <div
                            className="chat-container"
                            style={{ padding: "0 20px" }}
                        >
                            <FrostChatCard
                                course_date_id={courseDateId || 0}
                                chatUser={{
                                    id: instructor?.id || 0,
                                    name:
                                        instructor?.fname +
                                            " " +
                                            instructor?.lname || "Instructor",
                                    email:
                                        instructor?.email ||
                                        "instructor@example.com",
                                    user_type: "instructor",
                                    avatar:
                                        instructor?.avatar ||
                                        "/images/default-avatar.png",
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
                            <div className="p-2 h-100">
                                <StudentsPanel
                                    courseDateId={courseDateId}
                                    instUnitId={instUnit?.id}
                                    onStudentClick={setSelectedStudent}
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

        /* Assistant mode - No left sidebar */
        .main-content.assistant-no-left-sidebar {
          margin-left: 0;
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
