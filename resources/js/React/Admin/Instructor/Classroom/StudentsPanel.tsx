import React, { useState, useMemo } from "react";
import { useQuery } from "@tanstack/react-query";

interface StudentsPanelProps {
    courseDateId?: number;
    instUnitId?: number;
    onStudentClick?: (student: {
        id: number;
        name: string;
        email: string;
        course_auth_id: number;
        student_unit_id?: number;
    }) => void;
}

interface Student {
    id: number;
    student_id: number;
    student_name: string;
    student_email: string;
    avatar?: string;
    course_auth_id?: number; // Course enrollment ID
    student_unit_id?: number; // Current session enrollment
    status: "online" | "offline" | "away";
    joined_at?: string;
    verified: boolean;
    progress_percent: number;
}

/**
 * StudentsPanel - Right sidebar panel showing active students with real-time status
 *
 * Features:
 * - Shows active students in current class
 * - Real-time status indicators (online, offline, away)
 * - Identity verification status
 * - Student progress tracking
 * - Instructor actions (message, eject, ban)
 * - Live count updates
 */
const StudentsPanel: React.FC<StudentsPanelProps> = ({
    courseDateId,
    instUnitId,
    onStudentClick,
}) => {
    // Pagination and search state
    const [searchQuery, setSearchQuery] = useState("");
    const [currentPage, setCurrentPage] = useState(1);
    const studentsPerPage = 10;

    // Fetch active students for current class
    const {
        data: studentsData,
        isLoading,
        error,
    } = useQuery({
        queryKey: ["students", courseDateId],
        queryFn: async () => {
            if (!courseDateId) return null;

            const response = await fetch(
                `/admin/instructors/data/students/active?courseDateId=${courseDateId}`,
                {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                }
            );

            if (!response.ok) {
                throw new Error(
                    `Failed to fetch students: ${response.statusText}`
                );
            }

            return response.json();
        },
        staleTime: 5 * 1000, // 5 seconds - high refresh rate for live status
        gcTime: 1 * 60 * 1000, // 1 minute
        refetchInterval: 3000, // Poll every 3 seconds for live updates
        enabled: !!courseDateId,
        retry: 2,
    });

    const students: Student[] = studentsData?.students || [];
    const onlineCount = students.filter((s) => s.status === "online").length;

    // Filter students by search query
    const filteredStudents = useMemo(() => {
        if (!searchQuery.trim()) return students;

        const query = searchQuery.toLowerCase();
        return students.filter(
            (student) =>
                student.student_name.toLowerCase().includes(query) ||
                student.student_email.toLowerCase().includes(query)
        );
    }, [students, searchQuery]);

    // Calculate pagination
    const totalPages = Math.ceil(filteredStudents.length / studentsPerPage);
    const startIndex = (currentPage - 1) * studentsPerPage;
    const endIndex = startIndex + studentsPerPage;
    const paginatedStudents = filteredStudents.slice(startIndex, endIndex);

    // Reset to page 1 when search changes
    React.useEffect(() => {
        setCurrentPage(1);
    }, [searchQuery]);

    if (isLoading) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-users mr-2"></i>
                        Students
                    </h5>
                </div>
                <div
                    className="card-body d-flex justify-content-center align-items-center"
                    style={{ minHeight: "400px" }}
                >
                    <div className="text-center">
                        <div
                            className="spinner-border spinner-border-sm text-primary"
                            role="status"
                        >
                            <span className="visually-hidden">
                                Loading students...
                            </span>
                        </div>
                        <p className="mt-2 text-muted">
                            <small>Loading students...</small>
                        </p>
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
                        <i className="fas fa-users mr-2"></i>
                        Students
                    </h5>
                </div>
                <div className="card-body">
                    <div className="alert alert-danger alert-sm mb-0">
                        <small>
                            <i className="fas fa-exclamation-circle mr-2"></i>
                            Failed to load students
                        </small>
                    </div>
                </div>
            </div>
        );
    }

    if (!students || students.length === 0) {
        return (
            <div className="card">
                <div className="card-header bg-secondary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-users mr-2"></i>
                        Students (0)
                    </h5>
                </div>
                <div className="card-body">
                    <p className="text-muted small mb-0">
                        No students in this class yet
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="card h-100 d-flex flex-column">
            <div className="card-header bg-secondary text-white py-2 px-3">
                <h5 className="mb-0">
                    <i className="fas fa-users mr-2"></i>
                    Students ({students.length})
                </h5>
                <small className="text-white-50">
                    {onlineCount} online / {students.length} total
                </small>
            </div>

            {/* Search Bar */}
            <div className="p-2 bg-dark border-bottom">
                <div className="input-group input-group-sm">
                    <span className="input-group-text bg-secondary border-secondary">
                        <i className="fas fa-search text-white-50"></i>
                    </span>
                    <input
                        type="text"
                        className="form-control bg-dark text-light border-secondary"
                        placeholder="Search students..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        style={{ fontSize: "0.875rem" }}
                    />
                    {searchQuery && (
                        <button
                            className="btn btn-sm btn-secondary"
                            onClick={() => setSearchQuery("")}
                            title="Clear search"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    )}
                </div>
                {searchQuery && (
                    <small className="text-white-50 d-block mt-1">
                        Found {filteredStudents.length} student
                        {filteredStudents.length !== 1 ? "s" : ""}
                    </small>
                )}
            </div>

            <div
                className="list-group list-group-flush m-0 flex-grow-1"
                style={{ overflow: "auto" }}
            >
                {paginatedStudents.map((student, index) => (
                    <div
                        key={student.id}
                        className={`list-group-item list-group-item-action bg-transparent px-3 py-2 ${
                            index === 0 ? "border-top-0" : ""
                        }`}
                    >
                        <div className="d-flex align-items-start gap-2">
                            {student.avatar ? (
                                <img
                                    src={student.avatar}
                                    alt={student.student_name}
                                    className="rounded-circle flex-shrink-0 mr-2"
                                    style={{
                                        width: 32,
                                        height: 32,
                                        objectFit: "cover",
                                    }}
                                />
                            ) : (
                                <div
                                    className="rounded-circle bg-secondary text-white-50 d-flex align-items-center justify-content-center flex-shrink-0 mr-2"
                                    style={{
                                        width: 32,
                                        height: 32,
                                        fontSize: "0.8rem",
                                    }}
                                    aria-hidden="true"
                                >
                                    <i className="fas fa-user"></i>
                                </div>
                            )}
                            <div
                                className="flex-grow-1"
                                style={{ minWidth: 0 }}
                            >
                                <button
                                    onClick={() =>
                                        onStudentClick?.({
                                            id: student.student_id,
                                            name: student.student_name,
                                            email: student.student_email,
                                            course_auth_id:
                                                student.course_auth_id || 0,
                                            student_unit_id:
                                                student.student_unit_id,
                                        })
                                    }
                                    className="btn btn-link p-0 text-start d-block fw-semibold link-light text-decoration-none text-truncate"
                                    title={`View ${student.student_name}'s validation images`}
                                    style={{
                                        border: "none",
                                        background: "none",
                                    }}
                                >
                                    {student.student_name}
                                </button>
                                <div
                                    className="small text-white-50 text-truncate"
                                    title={student.student_email}
                                >
                                    {student.student_email}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Pagination Controls */}
            {totalPages > 1 && (
                <div className="card-footer bg-dark border-top border-secondary py-2 px-3">
                    <div className="d-flex justify-content-between align-items-center">
                        <small className="text-white-50">
                            Page {currentPage} of {totalPages}
                        </small>
                        <div className="btn-group btn-group-sm">
                            <button
                                className="btn btn-secondary"
                                onClick={() =>
                                    setCurrentPage((p) => Math.max(1, p - 1))
                                }
                                disabled={currentPage === 1}
                                title="Previous page"
                            >
                                <i className="fas fa-chevron-left"></i>
                            </button>
                            <button
                                className="btn btn-secondary"
                                onClick={() =>
                                    setCurrentPage((p) =>
                                        Math.min(totalPages, p + 1)
                                    )
                                }
                                disabled={currentPage === totalPages}
                                title="Next page"
                            >
                                <i className="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <small className="text-white-50 d-block mt-1">
                        Showing {startIndex + 1}-
                        {Math.min(endIndex, filteredStudents.length)} of{" "}
                        {filteredStudents.length}
                    </small>
                </div>
            )}
        </div>
    );
};

export default StudentsPanel;
