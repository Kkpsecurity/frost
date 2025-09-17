import React, { useMemo } from 'react';
import {
    useReactTable,
    getCoreRowModel,
    getSortedRowModel,
    getFilteredRowModel,
    flexRender,
    createColumnHelper,
    SortingState,
    ColumnFiltersState
} from '@tanstack/react-table';
import { Student, CourseAuth } from '../types/LaravelProps';

interface StudentDashboardProps {
    student: Student;
    courseAuths: CourseAuth[];
    onEnterClassroom?: (courseAuth: CourseAuth) => void;
}

const StudentDashboard: React.FC<StudentDashboardProps> = ({
    student,
    courseAuths = [],
    onEnterClassroom,
}) => {
    // TanStack Table state
    const [sorting, setSorting] = React.useState<SortingState>([]);
    const [columnFilters, setColumnFilters] =
        React.useState<ColumnFiltersState>([]);
    const [globalFilter, setGlobalFilter] = React.useState("");

    // Debug logging to see what course data we're getting
    console.log("🎓 StudentDashboard: Course auths received:", courseAuths);
    console.log("🎓 StudentDashboard: Course auths type:", typeof courseAuths);
    console.log(
        "🎓 StudentDashboard: Course auths length:",
        courseAuths?.length
    );
    console.log("🎓 StudentDashboard: Is array?:", Array.isArray(courseAuths));
    console.log("🎓 StudentDashboard: First course auth:", courseAuths[0]);
    console.log("🎓 StudentDashboard: Student data:", student);

    // Navigation handlers for classroom entry (SPA style)
    const handleContinueCourse = (
        courseAuth: CourseAuth,
        event?: React.MouseEvent
    ) => {
        console.log(
            "🚀 CONTINUE BUTTON CLICKED - Continuing course:",
            courseAuth
        );
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (onEnterClassroom) {
            console.log("🚀 Using SPA navigation to classroom");
            onEnterClassroom(courseAuth);
        } else {
            console.log("🚀 Fallback to page navigation");
            const url = `/classroom/enter/${courseAuth.id}`;
            window.location.href = url;
        }
    };

    const handleStartCourse = (
        courseAuth: CourseAuth,
        event?: React.MouseEvent
    ) => {
        console.log("🚀 START BUTTON CLICKED - Starting course:", courseAuth);
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (onEnterClassroom) {
            console.log("🚀 Using SPA navigation to classroom");
            onEnterClassroom(courseAuth);
        } else {
            console.log("🚀 Fallback to page navigation");
            const url = `/classroom/enter/${courseAuth.id}`;
            window.location.href = url;
        }
    };

    const handleReviewCourse = (
        courseAuth: CourseAuth,
        event?: React.MouseEvent
    ) => {
        console.log("👁️ REVIEW BUTTON CLICKED - Reviewing course:", courseAuth);
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        if (onEnterClassroom) {
            console.log("👁️ Using SPA navigation to classroom");
            onEnterClassroom(courseAuth);
        } else {
            console.log("👁️ Fallback to page navigation");
            const url = `/classroom/enter/${courseAuth.id}`;
            window.location.href = url;
        }
    };

    // Calculate stats from course data
    const totalCourses = courseAuths.length;
    const completedCourses = courseAuths.filter(
        (auth) => auth.completed_at
    ).length;
    const activeCourses = courseAuths.filter(
        (auth) => auth.start_date && !auth.completed_at
    ).length;
    const passedCourses = courseAuths.filter((auth) => auth.is_passed).length;

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
                                    onClick={(e) =>
                                        handleContinueCourse(auth, e)
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
                                    <i className="fas fa-play me-2"></i>
                                    Continue
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
                                    onClick={(e) => handleStartCourse(auth, e)}
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
                                    <i className="fas fa-rocket me-2"></i>
                                    Start
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
                                    onClick={(e) => handleReviewCourse(auth, e)}
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
            <div className="row mb-4">
                <div className="col-md-3 col-sm-6 mb-3">
                    <div className="card frost-primary-bg text-white h-100">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 className="mb-0">{totalCourses}</h3>
                                    <p className="mb-0">Total Courses</p>
                                </div>
                                <div className="fs-2">
                                    <i className="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3 col-sm-6 mb-3">
                    <div className="card bg-success text-white h-100">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 className="mb-0">{completedCourses}</h3>
                                    <p className="mb-0">Completed</p>
                                </div>
                                <div className="fs-2">
                                    <i className="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3 col-sm-6 mb-3">
                    <div className="card bg-info text-white h-100">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 className="mb-0">{activeCourses}</h3>
                                    <p className="mb-0">In Progress</p>
                                </div>
                                <div className="fs-2">
                                    <i className="fas fa-play-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3 col-sm-6 mb-3">
                    <div className="card bg-warning text-white h-100">
                        <div className="card-body">
                            <div className="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 className="mb-0">{passedCourses}</h3>
                                    <p className="mb-0">Passed</p>
                                </div>
                                <div className="fs-2">
                                    <i className="fas fa-trophy"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Course Purchases Table */}
            <div className="row">
                <div className="col-12">
                    <div
                        className="card frost-primary-bg shadow-lg border-0"
                        style={{ borderRadius: "15px", overflow: "hidden" }}
                    >
                        <div
                            className="card-header bg-gradient text-white py-4"
                            style={{
                                background:
                                    "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                            }}
                        >
                            <h5 className="card-title mb-1 fw-bold fs-4">
                                <i className="fas fa-shopping-cart me-3"></i>
                                My Course Purchases
                            </h5>
                            <p className="mb-0 opacity-90 small">
                                Track your learning progress and continue your
                                courses
                            </p>
                        </div>
                        <div className="card-body p-0">
                            {courseAuths.length === 0 ? (
                                <div className="text-center py-5">
                                    <i
                                        className="fas fa-graduation-cap fs-1 mb-3"
                                        style={{ color: "#64748b" }}
                                    ></i>
                                    <h4 style={{ color: "#94a3b8" }}>
                                        No course authorizations found
                                    </h4>
                                    <p style={{ color: "#64748b" }}>
                                        Contact your administrator to get
                                        enrolled in courses.
                                    </p>
                                </div>
                            ) : (
                                <>
                                    {/* Search/Filter Bar */}
                                    <div
                                        className="row mb-4 p-4"
                                        style={{
                                            background:
                                                "linear-gradient(135deg, #1e293b 0%, #334155 100%)",
                                            borderRadius: "12px",
                                            margin: "0 0 1rem 0",
                                            boxShadow:
                                                "0 4px 16px rgba(0,0,0,0.2)",
                                        }}
                                    >
                                        <div className="col-md-6">
                                            <div className="input-group">
                                                <span
                                                    className="input-group-text text-white"
                                                    style={{
                                                        background:
                                                            "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)",
                                                        border: "none",
                                                    }}
                                                >
                                                    <i className="fas fa-search"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    placeholder="Search courses..."
                                                    value={globalFilter ?? ""}
                                                    onChange={(e) =>
                                                        setGlobalFilter(
                                                            e.target.value
                                                        )
                                                    }
                                                    style={{
                                                        backgroundColor:
                                                            "#334155",
                                                        border: "none",
                                                        color: "#f1f5f9",
                                                        fontSize: "0.9rem",
                                                    }}
                                                />
                                            </div>
                                        </div>
                                        <div className="col-md-6 d-flex justify-content-end align-items-center">
                                            <small
                                                style={{
                                                    color: "#94a3b8",
                                                    fontSize: "0.8rem",
                                                }}
                                            >
                                                Showing{" "}
                                                {
                                                    table.getRowModel().rows
                                                        .length
                                                }{" "}
                                                of {courseAuths.length} courses
                                            </small>
                                        </div>
                                    </div>

                                    {/* TanStack Table - Dark Mode */}
                                    <div
                                        className="table-responsive"
                                        style={{
                                            background:
                                                "linear-gradient(135deg, #1e293b 0%, #334155 100%)",
                                            borderRadius: "12px",
                                            overflow: "hidden",
                                            boxShadow:
                                                "0 8px 32px rgba(0,0,0,0.3)",
                                        }}
                                    >
                                        <table className="table table-hover mb-0 table-dark">
                                            <thead
                                                style={{
                                                    background:
                                                        "linear-gradient(135deg, #0f172a 0%, #1e293b 100%)",
                                                    borderBottom:
                                                        "2px solid #3b82f6",
                                                }}
                                            >
                                                {table
                                                    .getHeaderGroups()
                                                    .map((headerGroup) => (
                                                        <tr
                                                            key={headerGroup.id}
                                                            className="text-white"
                                                        >
                                                            {headerGroup.headers.map(
                                                                (header) => (
                                                                    <th
                                                                        key={
                                                                            header.id
                                                                        }
                                                                        className="py-3 px-3 fw-bold border-0"
                                                                        style={{
                                                                            fontSize:
                                                                                "0.85rem",
                                                                            letterSpacing:
                                                                                "0.8px",
                                                                            textTransform:
                                                                                "uppercase",
                                                                            cursor: header.column.getCanSort()
                                                                                ? "pointer"
                                                                                : "default",
                                                                            userSelect:
                                                                                "none",
                                                                            transition:
                                                                                "all 0.2s ease",
                                                                            fontWeight:
                                                                                "600",
                                                                        }}
                                                                        onClick={header.column.getToggleSortingHandler()}
                                                                        onMouseEnter={(
                                                                            e
                                                                        ) => {
                                                                            if (
                                                                                header.column.getCanSort()
                                                                            ) {
                                                                                e.currentTarget.style.backgroundColor =
                                                                                    "rgba(59, 130, 246, 0.2)";
                                                                            }
                                                                        }}
                                                                        onMouseLeave={(
                                                                            e
                                                                        ) => {
                                                                            if (
                                                                                header.column.getCanSort()
                                                                            ) {
                                                                                e.currentTarget.style.backgroundColor =
                                                                                    "transparent";
                                                                            }
                                                                        }}
                                                                    >
                                                                        <div className="d-flex align-items-center justify-content-between">
                                                                            {flexRender(
                                                                                header
                                                                                    .column
                                                                                    .columnDef
                                                                                    .header,
                                                                                header.getContext()
                                                                            )}
                                                                            {header.column.getCanSort() && (
                                                                                <span className="ms-2">
                                                                                    {header.column.getIsSorted() ===
                                                                                    "asc" ? (
                                                                                        <i className="fas fa-sort-up"></i>
                                                                                    ) : header.column.getIsSorted() ===
                                                                                      "desc" ? (
                                                                                        <i className="fas fa-sort-down"></i>
                                                                                    ) : (
                                                                                        <i className="fas fa-sort opacity-50"></i>
                                                                                    )}
                                                                                </span>
                                                                            )}
                                                                        </div>
                                                                    </th>
                                                                )
                                                            )}
                                                        </tr>
                                                    ))}
                                            </thead>
                                            <tbody>
                                                {table
                                                    .getRowModel()
                                                    .rows.map((row, index) => (
                                                        <tr
                                                            key={row.id}
                                                            className="border-0"
                                                            style={{
                                                                background:
                                                                    index %
                                                                        2 ===
                                                                    0
                                                                        ? "linear-gradient(135deg, #1e293b 0%, #334155 100%)"
                                                                        : "linear-gradient(135deg, #334155 0%, #475569 100%)",
                                                                transition:
                                                                    "all 0.3s ease",
                                                                borderLeft: `3px solid ${
                                                                    row.original
                                                                        .completed_at
                                                                        ? "#10b981"
                                                                        : row
                                                                              .original
                                                                              .start_date
                                                                        ? "#3b82f6"
                                                                        : "#f59e0b"
                                                                }`,
                                                                color: "#f8fafc",
                                                            }}
                                                            onMouseEnter={(
                                                                e
                                                            ) => {
                                                                e.currentTarget.style.background =
                                                                    "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)";
                                                                e.currentTarget.style.transform =
                                                                    "translateX(3px)";
                                                                e.currentTarget.style.boxShadow =
                                                                    "0 6px 20px rgba(59, 130, 246, 0.4)";
                                                            }}
                                                            onMouseLeave={(
                                                                e
                                                            ) => {
                                                                e.currentTarget.style.background =
                                                                    index %
                                                                        2 ===
                                                                    0
                                                                        ? "linear-gradient(135deg, #1e293b 0%, #334155 100%)"
                                                                        : "linear-gradient(135deg, #334155 0%, #475569 100%)";
                                                                e.currentTarget.style.transform =
                                                                    "translateX(0)";
                                                                e.currentTarget.style.boxShadow =
                                                                    "none";
                                                            }}
                                                        >
                                                            {row
                                                                .getVisibleCells()
                                                                .map((cell) => (
                                                                    <td
                                                                        key={
                                                                            cell.id
                                                                        }
                                                                        className="py-2 px-3 border-0"
                                                                        style={{
                                                                            fontSize:
                                                                                "0.9rem",
                                                                        }}
                                                                    >
                                                                        {flexRender(
                                                                            cell
                                                                                .column
                                                                                .columnDef
                                                                                .cell,
                                                                            cell.getContext()
                                                                        )}
                                                                    </td>
                                                                ))}
                                                        </tr>
                                                    ))}
                                            </tbody>
                                        </table>
                                    </div>

                                    {/* Table Footer with Row Count */}
                                    {table.getRowModel().rows.length === 0 &&
                                        globalFilter && (
                                            <div className="text-center py-4">
                                                <i
                                                    className="fas fa-search fs-1 mb-3"
                                                    style={{ color: "#64748b" }}
                                                ></i>
                                                <h5
                                                    style={{ color: "#94a3b8" }}
                                                >
                                                    No courses match your search
                                                </h5>
                                                <button
                                                    className="btn btn-sm text-white"
                                                    onClick={() =>
                                                        setGlobalFilter("")
                                                    }
                                                    style={{
                                                        background:
                                                            "linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)",
                                                        border: "none",
                                                        borderRadius: "8px",
                                                        padding: "8px 16px",
                                                    }}
                                                >
                                                    Clear Search
                                                </button>
                                            </div>
                                        )}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDashboard;
