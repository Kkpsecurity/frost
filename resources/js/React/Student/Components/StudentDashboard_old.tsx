import React from "react";
import { Student, CourseAuth, LessonsData } from "../types/LaravelProps";
import SchoolDashboard from "./SchoolDashboard";
import StudentPurchaseTable from "./StudentPurchaseTable";
import StudentStatsBlock from "./StudentStatsBlock";
import { useStudentDashboard } from "../hooks/useStudentDashboard";

interface StudentDashboardProps {
    student: Student;
    courseAuths: CourseAuth[];
    lessons?: LessonsData;
    hasLessons?: boolean;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    student,
    courseAuths = [],
    lessons,
    hasLessons = false,
}) => {
    // Use the custom hook for all dashboard logic
    const {
        currentView,
        selectedCourse,
        stats,
        table,
        globalFilter,
        setGlobalFilter,
        handleBackToDashboard,
        flexRender,
    } = useStudentDashboard({
        student,
        courseAuths,
        lessons,
        hasLessons,
    });

    // Render classroom dashboard when a course is selected
    };

    const getStatusBadge = (auth: any) => {
        if (auth.completed_at) {
            return auth.is_passed ? (
                <span className="badge bg-success">Completed</span>
            ) : (
                <span className="badge bg-danger">Failed</span>
            );
        }
        if (auth.start_date) {
            return <span className="badge bg-primary">In Progress</span>;
        }
        return <span className="badge bg-secondary">Not Started</span>;
    };

    /**
     * Calculate real progress based on lesson completion
     * Progress = (completed_lessons / total_lessons) * 100
     */
    const calculateProgress = (
        auth: any
    ): { percentage: number; completed: number; total: number } => {
        try {
            // Get total lessons from course structure
            let totalLessons = 0;
            if (auth.course?.course_units) {
                auth.course.course_units.forEach((unit: any) => {
                    if (unit.course_unit_lessons) {
                        totalLessons += unit.course_unit_lessons.length;
                    }
                });
            }

            // Get completed lessons from student progress
            let completedLessons = 0;
            if (auth.student_units) {
                auth.student_units.forEach((studentUnit: any) => {
                    if (studentUnit.student_lessons) {
                        completedLessons += studentUnit.student_lessons.filter(
                            (lesson: any) =>
                                lesson.is_completed || lesson.completed_at
                        ).length;
                    }
                });
            }

            // Calculate percentage
            const percentage =
                totalLessons > 0
                    ? Math.round((completedLessons / totalLessons) * 100)
                    : 0;

            console.log(`🎓 Progress for course ${auth.course_id}:`, {
                completed: completedLessons,
                total: totalLessons,
                percentage: percentage,
            });

            return {
                percentage,
                completed: completedLessons,
                total: totalLessons,
            };
        } catch (error) {
            console.error("🎓 Error calculating progress:", error);
            return { percentage: 0, completed: 0, total: 0 };
        }
    };

    // TanStack Table column definitions
    const columnHelper = createColumnHelper<CourseAuth>();

    const columns = useMemo(
        () => [
            columnHelper.accessor(
                (row) => row.course?.title || "Unknown Course",
                {
                    id: "course",
                    header: () => (
                        <div
                            className="d-flex align-items-center"
                            style={{ color: "#f1f5f9" }}
                        >
                            <i className="fas fa-book me-2"></i>
                            Course
                        </div>
                    ),
                    cell: (info) => (
                        <div>
                            <div
                                className="fw-bold mb-1"
                                style={{ fontSize: "1rem", color: "#f1f5f9" }}
                            >
                                <i
                                    className="fas fa-certificate me-2"
                                    style={{ color: "#3b82f6" }}
                                ></i>
                                {info.getValue()}
                            </div>
                            {info.row.original.course?.description && (
                                <div
                                    className="small fw-medium"
                                    style={{
                                        color: "#94a3b8",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    {info.row.original.course.description}
                                </div>
                            )}
                        </div>
                    ),
                    enableSorting: true,
                    enableColumnFilter: true,
                }
            ),
            columnHelper.accessor("created_at", {
                id: "purchase_date",
                header: () => (
                    <div
                        className="d-flex align-items-center"
                        style={{ color: "#f1f5f9" }}
                    >
                        <i className="fas fa-calendar-plus me-2"></i>
                        Purchase Date
                    </div>
                ),
                cell: (info) => (
                    <div
                        className="fw-semibold"
                        style={{ color: "#e2e8f0", fontSize: "0.9rem" }}
                    >
                        <i
                            className="fas fa-calendar me-2"
                            style={{ color: "#64748b" }}
                        ></i>
                        {formatDate(info.getValue())}
                    </div>
                ),
                enableSorting: true,
            }),
            columnHelper.accessor("start_date", {
                id: "start_date",
                header: () => (
                    <div
                        className="d-flex align-items-center"
                        style={{ color: "#f1f5f9" }}
                    >
                        <i className="fas fa-calendar-day me-2"></i>
                        Start Date
                    </div>
                ),
                cell: (info) => (
                    <div
                        className="fw-semibold"
                        style={{ color: "#e2e8f0", fontSize: "0.9rem" }}
                    >
                        <i
                            className="fas fa-play-circle me-2"
                            style={{ color: "#64748b" }}
                        ></i>
                        {formatDate(info.getValue())}
                    </div>
                ),
                enableSorting: true,
            }),
            columnHelper.display({
                id: "status",
                header: () => (
                    <div
                        className="d-flex align-items-center"
                        style={{ color: "#f1f5f9" }}
                    >
                        <i className="fas fa-info-circle me-2"></i>
                        Status
                    </div>
                ),
                cell: (info) => getStatusBadge(info.row.original),
                enableSorting: false,
            }),
            columnHelper.display({
                id: "progress",
                header: () => (
                    <div
                        className="d-flex align-items-center"
                        style={{ color: "#f1f5f9" }}
                    >
                        <i className="fas fa-chart-line me-2"></i>
                        Progress
                    </div>
                ),
                cell: (info) => {
                    const progress = calculateProgress(info.row.original);
                    return (
                        <div>
                            <div className="d-flex align-items-center mb-2">
                                <small
                                    className="fw-bold me-2"
                                    style={{
                                        color: "#f1f5f9",
                                        fontSize: "0.8rem",
                                    }}
                                >
                                    {progress.percentage}% Complete
                                </small>
                                {progress.total > 0 && (
                                    <small
                                        style={{
                                            color: "#94a3b8",
                                            fontSize: "0.75rem",
                                        }}
                                    >
                                        ({progress.completed}/{progress.total}{" "}
                                        lessons)
                                    </small>
                                )}
                            </div>
                            <div
                                className="progress shadow-sm"
                                style={{ height: "8px", borderRadius: "10px" }}
                            >
                                <div
                                    className="progress-bar progress-bar-striped"
                                    style={{
                                        width: `${progress.percentage}%`,
                                        background:
                                            progress.percentage > 75
                                                ? "linear-gradient(45deg, #28a745, #20c997)"
                                                : progress.percentage > 50
                                                ? "linear-gradient(45deg, #007bff, #6f42c1)"
                                                : progress.percentage > 25
                                                ? "linear-gradient(45deg, #ffc107, #fd7e14)"
                                                : "linear-gradient(45deg, #dc3545, #e83e8c)",
                                        borderRadius: "10px",
                                        transition: "width 0.6s ease",
                                    }}
                                ></div>
                            </div>
                        </div>
                    );
                },
                enableSorting: false,
            }),
            columnHelper.display({
                id: "actions",
                header: () => (
                    <div
                        className="d-flex align-items-center justify-content-center"
                        style={{ color: "#f1f5f9" }}
                    >
                        <i className="fas fa-cogs me-2"></i>
                        Actions
                    </div>
                ),
                cell: (info) => {
                    const auth = info.row.original;

                    // Handle course navigation - SPA style (no page reload)
                    const handleCourseAction = (
                        action: "continue" | "start" | "review"
                    ) => {
                        console.log(`🎓 ${action} button clicked for course:`, {
                            courseAuth: auth.id,
                            courseId: auth.course_id,
                            action: action,
                        });

                        // Switch to classroom view (SPA style)
                        setSelectedCourseAuthId(auth.id);
                        setCurrentView("course");
                        console.log(
                            `🎓 Switching to classroom view for course auth: ${auth.id}`
                        );
                    };

                    return (
                        <div className="text-center">
                            {auth.start_date && !auth.completed_at && (
                                <button
                                    className="btn btn-primary btn-sm shadow-sm fw-bold px-3 py-2"
                                    style={{
                                        borderRadius: "8px",
                                        background:
                                            "linear-gradient(45deg, #007bff, #0056b3)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() =>
                                        handleCourseAction("continue")
                                    }
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(-2px)";
                                        e.currentTarget.style.boxShadow =
                                            "0 6px 16px rgba(0,123,255,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "0 2px 4px rgba(0,0,0,0.1)";
                                    }}
                                >
                                    <i className="fas fa-graduation-cap me-2"></i>
                                    Take Course
                                </button>
                            )}
                            {!auth.start_date && (
                                <button
                                    className="btn btn-success btn-sm shadow-sm fw-bold px-3 py-2"
                                    style={{
                                        borderRadius: "8px",
                                        background:
                                            "linear-gradient(45deg, #28a745, #1e7e34)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() => handleCourseAction("start")}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(-2px)";
                                        e.currentTarget.style.boxShadow =
                                            "0 6px 16px rgba(40,167,69,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "0 2px 4px rgba(0,0,0,0.1)";
                                    }}
                                >
                                    <i className="fas fa-graduation-cap me-2"></i>
                                    Take Course
                                </button>
                            )}
                            {auth.completed_at && (
                                <button
                                    className="btn btn-info btn-sm shadow-sm fw-bold px-3 py-2"
                                    style={{
                                        borderRadius: "8px",
                                        background:
                                            "linear-gradient(45deg, #17a2b8, #117a8b)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() => handleCourseAction("review")}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(-2px)";
                                        e.currentTarget.style.boxShadow =
                                            "0 6px 16px rgba(23,162,184,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform =
                                            "translateY(0)";
                                        e.currentTarget.style.boxShadow =
                                            "0 2px 4px rgba(0,0,0,0.1)";
                                    }}
                                >
                                    <i className="fas fa-eye me-2"></i>
                                    Review
                                </button>
                            )}
                        </div>
                    );
                },
                enableSorting: false,
            }),
        ],
        [courseAuths]
    );

    // Create TanStack table instance
    const table = useReactTable({
        data: courseAuths,
        columns,
        state: {
            sorting,
            columnFilters,
            globalFilter,
        },
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        onGlobalFilterChange: setGlobalFilter,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
    });

    // Render classroom dashboard when a course is selected
    if (currentView === "course" && selectedCourseAuthId) {
        const selectedCourse = courseAuths.find(
            (auth) => auth.id === selectedCourseAuthId
        );

        if (!selectedCourse) {
            // Course not found, return to dashboard
            setCurrentView("dashboard");
            setSelectedCourseAuthId(null);
            return null;
        }

        // Convert to the format expected by SchoolDashboard
        const studentForClassroom = {
            id: student.id,
            fname: student.fname,
            lname: student.lname,
            email: student.email,
            name: `${student.fname} ${student.lname}`,
            fullname: `${student.fname} ${student.lname}`,
            is_active: true,
            role_id: 5,
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        // Mock instructor data (since this is student view)
        const instructor = {
            id: 1,
            fname: "Instructor",
            lname: "Name",
            email: "instructor@example.com",
            name: "Instructor Name",
            fullname: "Instructor Name",
            is_active: true,
            role_id: 2,
            use_gravatar: false,
            email_opt_in: false,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
        };

        // Convert selectedCourse to the format expected by SchoolDashboard
        const courseAuthForClassroom = {
            ...selectedCourse,
            course: selectedCourse.course
                ? {
                      ...selectedCourse.course,
                      price: 0,
                      total_minutes: 0,
                      policy_expire_days: 0,
                      is_active: true,
                      needs_range: false,
                  }
                : undefined,
            progress: 0,
            status: selectedCourse.completed_at
                ? "completed"
                : selectedCourse.start_date
                ? "in_progress"
                : "not_started",
            is_active: !selectedCourse.disabled_at,
            is_expired: false,
            is_failed: false,
        };

        return (
            <div className="classroom-container">
                {/* Back to Dashboard Button */}
                <div className="container-fluid mb-3">
                    <div className="row">
                        <div className="col-12">
                            <button
                                className="btn btn-outline-light me-3"
                                onClick={handleBackToDashboard}
                                style={{ borderRadius: "8px" }}
                            >
                                <i className="fas fa-arrow-left me-2"></i>
                                Back to Dashboard
                            </button>
                        </div>
                    </div>
                </div>

                {/* School Dashboard Component */}
                <SchoolDashboard
                    student={studentForClassroom}
                    instructor={instructor}
                    courseAuths={courseAuthForClassroom}
                    courseDates={[]} // Empty for now, can add course dates later
                />
            </div>
        );
    }

    // Default dashboard view
    return (
        <div className="container-lg py-4">
            {/* Header Section */}
            <div className="row mb-4 mt-3">
                <div className="col-12">
                    <div className="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 className="mb-1 text-white">
                                {student.fname} {student.lname}'s Dashboard
                            </h2>
                            <p className="text-white-50 mb-0">
                                Welcome back, {student.fname}
                            </p>
                        </div>
                        <div className="text-end">
                            <small className="text-success">
                                Student ID: {student.id}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {/* Stats Cards Section */}
            <StudentStatsBlock
                totalCourses={totalCourses}
                completedCourses={completedCourses}
                activeCourses={activeCourses}
                passedCourses={passedCourses}
            />

            {/* Course Purchases Table */}
            <div className="row">
                <div className="col-12">
                    <StudentPurchaseTable
                        table={table}
                        flexRender={flexRender}
                        globalFilter={globalFilter}
                        setGlobalFilter={setGlobalFilter}
                    />
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
