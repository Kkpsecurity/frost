import React, { useEffect, useState, useCallback } from "react";
import BanEnrollmentModal, { CourseItem } from "../../../Support/Components/Modals/BanEnrollmentModal";
import EjectDayModal, { AttendanceRecord } from "../../../Support/Components/Modals/EjectDayModal";
import DayLessonsModal from "../../../Support/Components/Modals/DayLessonsModal";

interface StudentInfo {
    id: number;
    name: string;
    email: string;
    course_auth_id: number;
    student_unit_id?: number;
}

interface InstructorStudentToolsCardProps {
    student: StudentInfo;
    toolPermissions: Record<string, boolean>;
    onRefresh?: () => void;
}

const InstructorStudentToolsCard: React.FC<InstructorStudentToolsCardProps> = ({
    student,
    toolPermissions,
    onRefresh,
}) => {
    const [enrollment, setEnrollment] = useState<CourseItem | null>(null);
    const [attendanceRecord, setAttendanceRecord] = useState<AttendanceRecord | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const [showBanModal, setShowBanModal] = useState(false);
    const [showEjectModal, setShowEjectModal] = useState(false);
    const [showLessonsModal, setShowLessonsModal] = useState(false);

    const fetchData = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const params = student.student_unit_id
                ? `?student_unit_id=${student.student_unit_id}`
                : "";
            const res = await fetch(
                `/admin/instructors/data/student-tools/${student.course_auth_id}${params}`,
                {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                },
            );
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            setEnrollment(data.enrollment ?? null);
            setAttendanceRecord(data.attendanceRecord ?? null);
        } catch (err: any) {
            setError(err?.message || "Failed to load student tools data.");
        } finally {
            setLoading(false);
        }
    }, [student.course_auth_id, student.student_unit_id]);

    useEffect(() => {
        fetchData();
    }, [fetchData]);

    const handleRefresh = () => {
        fetchData();
        onRefresh?.();
    };

    const canBan = toolPermissions["ban-course-auth"] ?? false;
    const canDayBan = toolPermissions["day-ban"] ?? false;
    const canGrantLesson = toolPermissions["grant-lesson"] ?? false;
    const canReverseDnc = toolPermissions["reverse-dnc"] ?? false;

    return (
        <div
            className="card card-dark mb-0"
            style={{ background: "#2d3748", border: "1px solid #4a5568" }}
        >
            <div
                className="card-header py-2"
                style={{ background: "#1a202c", borderBottom: "1px solid #4a5568" }}
            >
                <h6 className="mb-0 text-light">
                    <i className="fas fa-tools mr-2 text-warning"></i>
                    Student Tools —{" "}
                    <span className="text-warning">{student.name}</span>
                </h6>
            </div>

            <div className="card-body p-0">
                {loading && (
                    <div className="p-3 text-center text-secondary">
                        <i className="fas fa-spinner fa-spin mr-1"></i> Loading…
                    </div>
                )}

                {!loading && error && (
                    <div className="p-3 text-danger small">
                        <i className="fas fa-exclamation-circle mr-1"></i>
                        {error}
                    </div>
                )}

                {!loading && !error && enrollment && (
                    <table className="table table-sm mb-0" style={{ color: "#cbd5e0" }}>
                        <tbody>
                            {/* Row 1: Course Enrollment */}
                            <tr style={{ borderColor: "#4a5568" }}>
                                <td className="pl-3 py-2" style={{ width: "40%" }}>
                                    <small className="text-secondary d-block">Enrollment</small>
                                    <span className="small text-light">{enrollment.name}</span>
                                </td>
                                <td className="py-2" style={{ width: "25%" }}>
                                    {enrollment.disabled_at ? (
                                        <span className="badge badge-danger">
                                            <i className="fas fa-ban mr-1"></i>Banned
                                        </span>
                                    ) : (
                                        <span className="badge badge-success">
                                            <i className="fas fa-check mr-1"></i>Active
                                        </span>
                                    )}
                                </td>
                                <td className="pr-3 py-2 text-right">
                                    {canBan && (
                                        <button
                                            className={`btn btn-sm ${enrollment.disabled_at ? "btn-outline-success" : "btn-outline-danger"}`}
                                            onClick={() => setShowBanModal(true)}
                                        >
                                            <i className={`fas fa-${enrollment.disabled_at ? "undo" : "ban"} mr-1`}></i>
                                            {enrollment.disabled_at ? "Reinstate" : "Ban"}
                                        </button>
                                    )}
                                </td>
                            </tr>

                            {/* Row 2: Day Access (only if student is in a unit today) */}
                            <tr style={{ borderColor: "#4a5568" }}>
                                <td className="pl-3 py-2">
                                    <small className="text-secondary d-block">Day Access</small>
                                    <span className="small text-light">
                                        {attendanceRecord
                                            ? `${attendanceRecord.day_name}, ${attendanceRecord.formatted_date}`
                                            : "Not in today's class"}
                                    </span>
                                </td>
                                <td className="py-2">
                                    {attendanceRecord ? (
                                        attendanceRecord.ejected_at ? (
                                            <span className="badge badge-danger">
                                                <i className="fas fa-user-slash mr-1"></i>Ejected
                                            </span>
                                        ) : (
                                            <span className="badge badge-success">
                                                <i className="fas fa-user-check mr-1"></i>Present
                                            </span>
                                        )
                                    ) : (
                                        <span className="badge badge-secondary">—</span>
                                    )}
                                </td>
                                <td className="pr-3 py-2 text-right">
                                    {canDayBan && attendanceRecord && (
                                        <button
                                            className={`btn btn-sm ${attendanceRecord.ejected_at ? "btn-outline-success" : "btn-outline-warning"}`}
                                            onClick={() => setShowEjectModal(true)}
                                        >
                                            <i className={`fas fa-${attendanceRecord.ejected_at ? "undo" : "user-slash"} mr-1`}></i>
                                            {attendanceRecord.ejected_at ? "Reinstate" : "Eject"}
                                        </button>
                                    )}
                                </td>
                            </tr>

                            {/* Row 3: Lessons (only if student_unit_id available) */}
                            {student.student_unit_id && (canGrantLesson || canReverseDnc) && (
                                <tr style={{ borderColor: "#4a5568" }}>
                                    <td className="pl-3 py-2">
                                        <small className="text-secondary d-block">Lesson Access</small>
                                        <span className="small text-light">Grant / Reverse DNC</span>
                                    </td>
                                    <td className="py-2">
                                        <span className="badge badge-secondary">—</span>
                                    </td>
                                    <td className="pr-3 py-2 text-right">
                                        <button
                                            className="btn btn-sm btn-outline-primary"
                                            onClick={() => setShowLessonsModal(true)}
                                        >
                                            <i className="fas fa-book-open mr-1"></i>Lessons
                                        </button>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                )}
            </div>

            {/* Modals */}
            {showBanModal && enrollment && (
                <BanEnrollmentModal
                    course={enrollment}
                    onClose={() => setShowBanModal(false)}
                    onSuccess={handleRefresh}
                />
            )}

            {showEjectModal && attendanceRecord && (
                <EjectDayModal
                    record={attendanceRecord}
                    onClose={() => setShowEjectModal(false)}
                    onSuccess={handleRefresh}
                />
            )}

            {showLessonsModal && student.student_unit_id && (
                <DayLessonsModal
                    studentUnitId={student.student_unit_id}
                    dayName={attendanceRecord?.day_name ?? "Today"}
                    formattedDate={attendanceRecord?.formatted_date ?? ""}
                    toolPermissions={toolPermissions}
                    onClose={() => setShowLessonsModal(false)}
                    onRefresh={handleRefresh}
                />
            )}
        </div>
    );
};

export default InstructorStudentToolsCard;
