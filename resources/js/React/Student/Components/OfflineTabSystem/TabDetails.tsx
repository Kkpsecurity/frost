import React from "react";
import { Modal } from "react-bootstrap";
import OfflineTabsQuickStats from "../OfflineTabsQuickStats";
import { useStudent } from "../../context/StudentContext";
import CaptureDevices from "../Classroom/Onboarding/Video/CaptureDevices";

type OfflineLessonLike = {
    is_completed?: boolean;
    duration_minutes?: number;
};

interface TabDetailsProps {
    courseAuthId: number;
    lessons: OfflineLessonLike[];
}

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

const mutedText: React.CSSProperties = { color: "#95a5a6" };

const TabDetails: React.FC<TabDetailsProps> = ({ courseAuthId, lessons }) => {
    const student = useStudent();

    type CaptureType = "upload" | "webcam" | "preview" | null;
    const [showCaptureType, setShowCaptureType] =
        React.useState<CaptureType>(null);
    // CaptureDevices expects onboarding step setters; keep local state for compatibility.
    const [currentStep, setCurrentStep] = React.useState<number>(2);

    const [isIdCardModalOpen, setIsIdCardModalOpen] = React.useState(false);

    const validations = student?.validationsByCourseAuth
        ? student.validationsByCourseAuth[courseAuthId]
        : null;

    const completedCount = React.useMemo(() => {
        return lessons.filter((l) => l.is_completed).length;
    }, [lessons]);

    const selectedCourse = React.useMemo(() => {
        const courses = student?.courses ?? [];
        return (
            courses.find((c: any) => {
                const candidateCourseAuthId =
                    c?.course_auth_id ?? c?.courseAuthId ?? c?.id;
                return Number(candidateCourseAuthId) === Number(courseAuthId);
            }) ?? null
        );
    }, [student?.courses, courseAuthId]);

    const courseName =
        (selectedCourse as any)?.course_name ||
        (selectedCourse as any)?.courseName ||
        (selectedCourse as any)?.name ||
        (selectedCourse as any)?.course?.name ||
        "—";

    const studentDisplayName = React.useMemo(() => {
        const s: any = student?.student ?? null;
        if (!s) return "—";

        if (typeof s.name === "string" && s.name.trim()) return s.name;
        if (typeof s.full_name === "string" && s.full_name.trim())
            return s.full_name;
        if (typeof s.fullName === "string" && s.fullName.trim())
            return s.fullName;

        // Common field names across backend variants
        const first =
            (typeof s.fname === "string" ? s.fname : "") ||
            (typeof s.first_name === "string" ? s.first_name : "") ||
            (typeof s.firstName === "string" ? s.firstName : "") ||
            (typeof s.first === "string" ? s.first : "") ||
            "";
        const last =
            (typeof s.lname === "string" ? s.lname : "") ||
            (typeof s.last_name === "string" ? s.last_name : "") ||
            (typeof s.lastName === "string" ? s.lastName : "") ||
            (typeof s.last === "string" ? s.last : "") ||
            "";
        const combined = `${first} ${last}`.trim();
        return combined || "—";
    }, [student?.student]);

    const studentEmail = React.useMemo(() => {
        const s: any = student?.student ?? null;
        return s?.email || s?.user?.email || s?.username || "—";
    }, [student?.student]);

    const idCardUrl: string | null = React.useMemo(() => {
        const raw = (validations as any)?.idcard;
        if (typeof raw !== "string") return null;
        const trimmed = raw.trim();
        if (!trimmed) return null;
        if (trimmed.includes("no-image")) return null;
        return trimmed;
    }, [validations]);

    const idCardStatus: string = React.useMemo(() => {
        const status = (validations as any)?.idcard_status;
        if (typeof status === "string" && status.trim()) return status;
        return idCardUrl ? "uploaded" : "missing";
    }, [validations, idCardUrl]);

    const shouldShowIdUploadPanel = React.useMemo(() => {
        const normalized = String(idCardStatus || "").toLowerCase();
        // Show upload controls only when missing or explicitly rejected.
        if (!idCardUrl) return true;
        if (normalized === "rejected") return true;
        return false;
    }, [idCardUrl, idCardStatus]);

    return (
        <div className="details-tab">
            <h4
                className="mb-2"
                style={{
                    color: "white",
                    fontSize: "1.75rem",
                    fontWeight: "600",
                }}
            >
                <i
                    className="fas fa-tachometer-alt me-2"
                    style={{ color: "#3498db" }}
                ></i>
                Learning Dashboard
            </h4>

            <p className="mb-4" style={mutedText}>
                Course overview, progress stats, and what to do next.
            </p>

            <OfflineTabsQuickStats lessons={lessons} />

            <div className="row g-3">
                {/* Student Profile */}
                <div className="col-12 col-lg-5">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-user me-2"
                                    style={{ color: "#9b59b6" }}
                                ></i>
                                Student Profile
                            </h6>

                            <div style={{ color: "#ecf0f1" }}>
                                <div>
                                    <strong>Name:</strong> {studentDisplayName}
                                </div>
                                <div>
                                    <strong>Email:</strong> {studentEmail}
                                </div>
                                <div>
                                    <strong>Progress:</strong> {completedCount}/
                                    {lessons.length} lessons
                                </div>
                            </div>

                            <div className="mt-3" style={mutedText}>
                                Keep your ID and onboarding items up to date so
                                you’re ready when class is live.
                            </div>
                        </div>
                    </div>
                </div>

                {/* Course Details */}
                <div className="col-12 col-lg-7">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-book me-2"
                                    style={{ color: "#3498db" }}
                                ></i>
                                Course Details
                            </h6>

                            <div style={{ color: "#ecf0f1" }}>
                                <div>
                                    <strong>Course:</strong> {courseName}
                                </div>
                                <div>
                                    <strong>Mode:</strong> Self‑Study (Offline)
                                </div>
                                <div>
                                    <strong>Progress:</strong> {completedCount}/
                                    {lessons.length} lessons
                                </div>
                                <div>
                                    <strong>Remaining:</strong>{" "}
                                    {Math.max(
                                        0,
                                        lessons.length - completedCount,
                                    )}
                                </div>
                            </div>

                            <div className="mt-3" style={mutedText}>
                                Choose any lesson from the left sidebar to
                                continue.
                            </div>
                        </div>
                    </div>
                </div>

                {/* ID Card */}
                <div className="col-12 col-lg-6">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-id-badge me-2"
                                    style={{ color: "#2ecc71" }}
                                ></i>
                                ID Card
                            </h6>

                            <div className="mt-2" style={{ color: "#ecf0f1" }}>
                                <div
                                    style={{
                                        color: "#95a5a6",
                                        fontSize: "0.8rem",
                                        marginBottom: "0.35rem",
                                    }}
                                >
                                    Current ID Card
                                </div>
                                {idCardUrl ? (
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setIsIdCardModalOpen(true)
                                        }
                                        title="Preview ID Card"
                                        style={{
                                            display: "block",
                                            width: "100%",
                                            padding: 0,
                                            border: 0,
                                            background: "transparent",
                                            cursor: "pointer",
                                        }}
                                    >
                                        <img
                                            src={idCardUrl}
                                            alt="ID Card"
                                            style={{
                                                width: "100%",
                                                height: "160px",
                                                objectFit: "contain",
                                                borderRadius: "0.5rem",
                                                backgroundColor:
                                                    "rgba(0,0,0,0.25)",
                                                display: "block",
                                            }}
                                        />
                                        <div
                                            className="mt-1"
                                            style={{
                                                color: "#95a5a6",
                                                fontSize: "0.8rem",
                                                textAlign: "right",
                                            }}
                                        >
                                            Click to enlarge
                                        </div>
                                    </button>
                                ) : (
                                    <div
                                        style={{
                                            height: "140px",
                                            borderRadius: "0.5rem",
                                            backgroundColor: "rgba(0,0,0,0.15)",
                                            color: "#95a5a6",
                                            display: "flex",
                                            alignItems: "center",
                                            justifyContent: "center",
                                            fontSize: "0.9rem",
                                        }}
                                    >
                                        Missing
                                    </div>
                                )}

                                <div className="mt-2">
                                    <strong>Status:</strong> {idCardStatus}
                                </div>
                                <div className="mt-2" style={mutedText}>
                                    {shouldShowIdUploadPanel
                                        ? "Upload a clear photo of your ID card."
                                        : "Your ID card is on file."}
                                </div>
                            </div>

                            {shouldShowIdUploadPanel && (
                                <div className="mt-3">
                                    <div
                                        style={{
                                            width: "100%",
                                            maxWidth: "620px",
                                        }}
                                    >
                                        <CaptureDevices
                                            data={{
                                                course_date_id: null,
                                                student_unit_id: null,
                                            }}
                                            photoType="idcard"
                                            student={
                                                (student?.student as any) ||
                                                ({} as any)
                                            }
                                            validations={
                                                validations
                                                    ? {
                                                          headshot:
                                                              (
                                                                  validations as any
                                                              )?.headshot ??
                                                              null,
                                                          idcard:
                                                              (
                                                                  validations as any
                                                              )?.idcard ?? null,
                                                      }
                                                    : null
                                            }
                                            showCaptureType={
                                                showCaptureType as any
                                            }
                                            setShowCaptureType={
                                                setShowCaptureType as any
                                            }
                                            setCurrentStep={setCurrentStep}
                                            currentStep={currentStep}
                                            onUploaded={() => {
                                                // Poll should refresh and bring back validations.idcard URL
                                                setShowCaptureType(null);
                                            }}
                                            debug={false}
                                        />
                                    </div>
                                </div>
                            )}

                            <Modal
                                show={Boolean(idCardUrl) && isIdCardModalOpen}
                                onHide={() => setIsIdCardModalOpen(false)}
                                centered
                                size="lg"
                            >
                                <Modal.Header closeButton>
                                    <Modal.Title>ID Card Preview</Modal.Title>
                                </Modal.Header>
                                <Modal.Body>
                                    {idCardUrl ? (
                                        <img
                                            src={idCardUrl}
                                            alt="ID Card Preview"
                                            style={{
                                                width: "100%",
                                                height: "auto",
                                                maxHeight: "70vh",
                                                objectFit: "contain",
                                                display: "block",
                                                backgroundColor:
                                                    "rgba(0,0,0,0.05)",
                                                borderRadius: "0.5rem",
                                            }}
                                        />
                                    ) : null}
                                </Modal.Body>
                            </Modal>
                        </div>
                    </div>
                </div>

                {/* Signatures (placeholder) */}
                <div className="col-12 col-lg-6">
                    <div className="card" style={cardStyle}>
                        <div className="card-body">
                            <h6 style={{ color: "white", fontWeight: 600 }}>
                                <i
                                    className="fas fa-pen-nib me-2"
                                    style={{ color: "#f39c12" }}
                                ></i>
                                Signatures
                            </h6>

                            <div className="mt-2" style={{ color: "#ecf0f1" }}>
                                <div style={mutedText}>
                                    Signature capture will appear here.
                                </div>
                                <div className="mt-2" style={mutedText}>
                                    This panel is reserved for the student
                                    signature box.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TabDetails;
