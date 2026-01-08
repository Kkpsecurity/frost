import React, { useState } from "react";
import { useSupportPoll } from "../Hooks/useSupportPoll";
import ActivityList from "./ActivityList";
import WeeklyAttendance from "./WeeklyAttendance";
import LessonsList from "./LessonsList";
import AttendanceHistory from "./AttendanceHistory";
import PhotoValidation from "./PhotoValidation";
import StudentDetails from "./StudentDetails";

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    avatar: string;
}

interface StudentDashboardProps {
    student: User;
    onBack: () => void;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    student,
    onBack,
}) => {
    const [selectedCourse, setSelectedCourse] = useState<string>("");
    const [activeTab, setActiveTab] = useState<string>("activity");
    const [refetchKey, setRefetchKey] = useState<number>(0);

    // Real-time polling hook
    const {
        data: pollData,
        isLoading: pollLoading,
        error: pollError,
    } = useSupportPoll({
        studentId: student.id,
        courseId: selectedCourse || null,
        enabled: true,
        pollInterval: 5000, // Poll every 5 seconds
    });

    // Debug: Log poll data when it changes
    React.useEffect(() => {
        console.log("📊 Poll data received:", pollData);
        console.log("📚 Courses array:", pollData?.courses);
    }, [pollData]);

    const handleCourseSelect = (e: React.ChangeEvent<HTMLSelectElement>) => {
        setSelectedCourse(e.target.value);
    };

    const handleRefetchData = () => {
        // Force refetch by incrementing key (will trigger new poll)
        setRefetchKey(prev => prev + 1);
    };

    const handleTabClick = (tab: string) => {
        if (selectedCourse) {
            setActiveTab(tab);
        }
    };

    return (
        <div className="container-fluid">
            <div className="row mb-3">
                <div className="col-12">
                    <button className="btn btn-primary" onClick={onBack}>
                        <i className="fas fa-arrow-left mr-2"></i>
                        New Search
                    </button>
                </div>
            </div>

            <div className="row">
                <div className="col-md-3">
                    <div className="card">
                        <div className="card-header">
                            <img
                                src={student.avatar}
                                alt={student.name}
                                className="rounded-circle mb-3"
                                style={{
                                    width: "120px",
                                    height: "120px",
                                    objectFit: "cover",
                                }}
                            />
                            <h5 className="card-title mb-1">{student.name}</h5>
                            <p className="card-text text-muted">
                                {student.email}
                            </p>
                        </div>
                        <div className="card-body">
                            {selectedCourse && pollData?.weeklyAttendance && (
                                <WeeklyAttendance attendance={pollData.weeklyAttendance} />
                            )}
                            {!selectedCourse && (
                                <div className="alert alert-warning">
                                    Select a course to view attendance
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-9">
                    <div className="card">
                        <div className="card-header">
                            <ul className="nav nav-tabs card-header-tabs">
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "activity"
                                                ? "active"
                                                : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() =>
                                            handleTabClick("activity")
                                        }
                                        disabled={!selectedCourse}
                                    >
                                        <i className="fas fa-chart-line mr-1"></i>
                                        Activity
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "lessons"
                                                ? "active"
                                                : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() =>
                                            handleTabClick("lessons")
                                        }
                                        disabled={!selectedCourse}
                                    >
                                        <i className="fas fa-book mr-1"></i>
                                        Lessons
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "class-history"
                                                ? "active"
                                                : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() =>
                                            handleTabClick("class-history")
                                        }
                                        disabled={!selectedCourse}
                                        style={{
                                            backgroundColor: selectedCourse
                                                ? "#28a745"
                                                : undefined,
                                            color: selectedCourse
                                                ? "white"
                                                : undefined,
                                        }}
                                    >
                                        <i className="fas fa-wifi mr-1"></i>
                                        History
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "photos"
                                                ? "active"
                                                : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() => handleTabClick("photos")}
                                        disabled={!selectedCourse}
                                    >
                                        <i className="fas fa-camera mr-1"></i>
                                        Photos
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "exam" ? "active" : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() => handleTabClick("exam")}
                                        disabled={!selectedCourse}
                                    >
                                        <i className="fas fa-file-alt mr-1"></i>
                                        Exam
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "details"
                                                ? "active"
                                                : ""
                                        } ${!selectedCourse ? "disabled" : ""}`}
                                        onClick={() =>
                                            handleTabClick("details")
                                        }
                                        disabled={!selectedCourse}
                                    >
                                        <i className="fas fa-user mr-1"></i>
                                        Details
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div className="card-body">
                            <div className="form-group">
                                <label>Select a Course</label>
                                <select
                                    className="form-control"
                                    value={selectedCourse}
                                    onChange={handleCourseSelect}
                                >
                                    <option value="">Select a Course</option>
                                    {pollData?.courses?.map((course: any) => (
                                        <option
                                            key={course.id}
                                            value={course.id}
                                        >
                                            {course.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {!selectedCourse && (
                                <div className="alert alert-danger">
                                    Select a course from the dropdown to view
                                    student activity.
                                </div>
                            )}

                            {selectedCourse && (
                                <div className="tab-content mt-3">
                                    {activeTab === "activity" && (
                                        <ActivityList
                                            activities={
                                                pollData?.courseActivity
                                                    ?.activities || []
                                            }
                                            studentName={student.name}
                                        />
                                    )}
                                    {activeTab === "lessons" && (
                                        <LessonsList
                                            lessons={pollData?.lessons || []}
                                            studentName={student.name}
                                        />
                                    )}
                                    {activeTab === "class-history" && (
                                        <AttendanceHistory
                                            history={pollData?.classHistory || []}
                                        />
                                    )}
                                    {activeTab === "photos" && (
                                        <PhotoValidation
                                            photos={pollData?.photos || null}
                                        />
                                    )}
                                    {activeTab === "exam" && (
                                        <div>
                                            Exam Results content for{" "}
                                            {student.name}
                                        </div>
                                    )}
                                    {activeTab === "details" && (
                                        <StudentDetails
                                            details={pollData?.studentDetails || null}
                                            onUpdate={handleRefetchData}
                                        />
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
