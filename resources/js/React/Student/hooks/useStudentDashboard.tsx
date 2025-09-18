import { useState, useEffect, useMemo } from "react";
import {
    useReactTable,
    getCoreRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    createColumnHelper,
    SortingState,
    ColumnFiltersState,
    flexRender,
} from "@tanstack/react-table";
import { Student, CourseAuth, LessonsData } from "../types/LaravelProps";

interface UseStudentDashboardProps {
    student: Student;
    courseAuths: CourseAuth[];
    lessons?: LessonsData;
    hasLessons?: boolean;
}

export const useStudentDashboard = ({
    student,
    courseAuths = [],
    lessons,
    hasLessons = false,
}: UseStudentDashboardProps) => {
    // TanStack Table state
    const [sorting, setSorting] = useState<SortingState>([]);
    const [columnFilters, setColumnFilters] = useState<ColumnFiltersState>([]);
    const [globalFilter, setGlobalFilter] = useState("");

    // View state with localStorage
    const [currentView, setCurrentView] = useState<"dashboard" | "course">(() => {
        return (
            (localStorage.getItem("studentDashboardView") as
                | "dashboard"
                | "course") || "dashboard"
        );
    });

    const [selectedCourseAuthId, setSelectedCourseAuthId] = useState<number | null>(() => {
        const stored = localStorage.getItem("selectedCourseAuthId");
        return stored ? parseInt(stored) : null;
    });

    // Save view state to localStorage whenever it changes
    useEffect(() => {
        localStorage.setItem("studentDashboardView", currentView);
        if (selectedCourseAuthId) {
            localStorage.setItem("selectedCourseAuthId", selectedCourseAuthId.toString());
        } else {
            localStorage.removeItem("selectedCourseAuthId");
        }
    }, [currentView, selectedCourseAuthId]);

    // Debug logging
    useEffect(() => {
        console.log("🎓 StudentDashboard: Course auths received:", courseAuths);
        console.log("🎓 StudentDashboard: Course auths type:", typeof courseAuths);
        console.log("🎓 StudentDashboard: Course auths length:", courseAuths?.length);
        console.log("🎓 StudentDashboard: Is array?:", Array.isArray(courseAuths));
        console.log("🎓 StudentDashboard: First course auth:", courseAuths[0]);
        console.log("🎓 StudentDashboard: Student data:", student);
        console.log("🎓 StudentDashboard: Lessons received:", lessons);
        console.log("🎓 StudentDashboard: Has lessons:", hasLessons);
        console.log("🎓 StudentDashboard: Lessons type:", typeof lessons);
    }, [courseAuths, student, lessons, hasLessons]);

    // Navigation functions
    const handleBackToDashboard = () => {
        console.log("🎓 Navigating back to dashboard");
        setSelectedCourseAuthId(null);
        setCurrentView("dashboard");
    };

    const handleCourseAction = (courseAuthId: number) => {
        console.log(`🎓 Course action clicked for course auth: ${courseAuthId}`);
        setSelectedCourseAuthId(courseAuthId);
        setCurrentView("course");
        console.log(`🎓 Switching to classroom view for course auth: ${courseAuthId}`);
    };

    // Calculate stats from course data
    const stats = useMemo(() => ({
        totalCourses: courseAuths.length,
        completedCourses: courseAuths.filter((auth) => auth.completed_at).length,
        activeCourses: courseAuths.filter((auth) => auth.start_date && !auth.completed_at).length,
        passedCourses: courseAuths.filter((auth) => auth.is_passed).length,
    }), [courseAuths]);

    // Utility functions
    const formatDate = (dateInput?: string | number | null) => {
        if (!dateInput) return "N/A";

        // Handle Unix timestamp (number)
        if (typeof dateInput === "number") {
            return new Date(dateInput * 1000).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        }

        // Handle date string
        return new Date(dateInput).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    const getStatusBadge = (auth: CourseAuth) => {
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

    const calculateProgress = (auth: CourseAuth): { percentage: number; completed: number; total: number } => {
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
                            (lesson: any) => lesson.is_completed || lesson.completed_at
                        ).length;
                    }
                });
            }

            // Calculate percentage
            const percentage = totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;

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

    // TanStack Table setup
    const columnHelper = createColumnHelper<CourseAuth>();

    const columns = useMemo(
        () => [
            columnHelper.accessor((row) => row.course?.title || "Unknown Course", {
                id: "course",
                header: () => (
                    <div className="d-flex align-items-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-book me-2"></i>
                        Course
                    </div>
                ),
                cell: (info) => (
                    <div>
                        <div className="fw-bold mb-1" style={{ fontSize: "1rem", color: "#f1f5f9" }}>
                            <i className="fas fa-certificate me-2" style={{ color: "#3b82f6" }}></i>
                            {info.getValue()}
                        </div>
                        {info.row.original.course?.description && (
                            <div className="small fw-medium" style={{ color: "#94a3b8", fontSize: "0.8rem" }}>
                                {info.row.original.course.description}
                            </div>
                        )}
                    </div>
                ),
                enableSorting: true,
                enableColumnFilter: true,
            }),
            columnHelper.accessor("created_at", {
                id: "purchase_date",
                header: () => (
                    <div className="d-flex align-items-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-calendar-plus me-2"></i>
                        Purchase Date
                    </div>
                ),
                cell: (info) => (
                    <div className="text-center">
                        <div className="fw-medium" style={{ color: "#f1f5f9", fontSize: "0.9rem" }}>
                            {formatDate(info.getValue())}
                        </div>
                    </div>
                ),
                enableSorting: true,
            }),
            columnHelper.accessor("start_date", {
                id: "start_date",
                header: () => (
                    <div className="d-flex align-items-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-play me-2"></i>
                        Started
                    </div>
                ),
                cell: (info) => (
                    <div className="text-center">
                        <div className="fw-medium" style={{ color: "#f1f5f9", fontSize: "0.9rem" }}>
                            {formatDate(info.getValue())}
                        </div>
                    </div>
                ),
                enableSorting: true,
            }),
            columnHelper.display({
                id: "status",
                header: () => (
                    <div className="d-flex align-items-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-flag me-2"></i>
                        Status
                    </div>
                ),
                cell: (info) => (
                    <div className="text-center">{getStatusBadge(info.row.original)}</div>
                ),
                enableSorting: false,
            }),
            columnHelper.display({
                id: "progress",
                header: () => (
                    <div className="d-flex align-items-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-chart-line me-2"></i>
                        Progress
                    </div>
                ),
                cell: (info) => {
                    const progress = calculateProgress(info.row.original);
                    return (
                        <div className="text-center">
                            <div className="d-flex align-items-center justify-content-center mb-1">
                                <div className="progress me-2" style={{ width: "60px", height: "8px" }}>
                                    <div
                                        className="progress-bar bg-success"
                                        role="progressbar"
                                        style={{ width: `${progress.percentage}%` }}
                                        aria-valuenow={progress.percentage}
                                        aria-valuemin={0}
                                        aria-valuemax={100}
                                    ></div>
                                </div>
                                <small className="text-success fw-bold" style={{ fontSize: "0.75rem" }}>
                                    {progress.percentage}%
                                </small>
                            </div>
                            <small className="text-muted" style={{ fontSize: "0.7rem" }}>
                                {progress.completed}/{progress.total} lessons
                            </small>
                        </div>
                    );
                },
                enableSorting: false,
            }),
            columnHelper.display({
                id: "actions",
                header: () => (
                    <div className="d-flex align-items-center justify-content-center" style={{ color: "#f1f5f9" }}>
                        <i className="fas fa-cogs me-2"></i>
                        Actions
                    </div>
                ),
                cell: (info) => {
                    const auth = info.row.original;

                    return (
                        <div className="text-center">
                            {auth.start_date && !auth.completed_at && (
                                <button
                                    className="btn btn-primary btn-sm shadow-sm fw-bold px-3 py-2"
                                    style={{
                                        borderRadius: "8px",
                                        background: "linear-gradient(45deg, #007bff, #0056b3)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() => handleCourseAction(auth.id)}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform = "translateY(-2px)";
                                        e.currentTarget.style.boxShadow = "0 6px 16px rgba(0,123,255,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform = "translateY(0)";
                                        e.currentTarget.style.boxShadow = "0 2px 4px rgba(0,0,0,0.1)";
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
                                        background: "linear-gradient(45deg, #28a745, #1e7e34)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() => handleCourseAction(auth.id)}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform = "translateY(-2px)";
                                        e.currentTarget.style.boxShadow = "0 6px 16px rgba(40,167,69,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform = "translateY(0)";
                                        e.currentTarget.style.boxShadow = "0 2px 4px rgba(0,0,0,0.1)";
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
                                        background: "linear-gradient(45deg, #17a2b8, #117a8b)",
                                        border: "none",
                                        transition: "all 0.3s ease",
                                    }}
                                    onClick={() => handleCourseAction(auth.id)}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform = "translateY(-2px)";
                                        e.currentTarget.style.boxShadow = "0 6px 16px rgba(23,162,184,0.4)";
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform = "translateY(0)";
                                        e.currentTarget.style.boxShadow = "0 2px 4px rgba(0,0,0,0.1)";
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

    // Get selected course data for classroom view
    const selectedCourse = useMemo(() => {
        if (!selectedCourseAuthId) return null;
        return courseAuths.find((auth) => auth.id === selectedCourseAuthId) || null;
    }, [selectedCourseAuthId, courseAuths]);

    return {
        // State
        currentView,
        selectedCourseAuthId,
        selectedCourse,

        // Table state
        sorting,
        setSorting,
        columnFilters,
        setColumnFilters,
        globalFilter,
        setGlobalFilter,
        table,

        // Stats
        stats,

        // Functions
        handleBackToDashboard,
        handleCourseAction,
        formatDate,
        getStatusBadge,
        calculateProgress,

        // Utils
        flexRender,
    };
};
