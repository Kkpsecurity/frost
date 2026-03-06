import React from "react";
import { Modal } from "react-bootstrap";
import CaptureDevices from "../Classroom/Onboarding/Video/CaptureDevices";

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

interface IdCardSectionProps {
    validations: any;
    student: any;
    courseAuthId?: number | null;
}

const IdCardSection: React.FC<IdCardSectionProps> = ({
    validations,
    student,
    courseAuthId = null,
}) => {
    type CaptureType = "upload" | "webcam" | "preview" | null;
    const [showCaptureType, setShowCaptureType] =
        React.useState<CaptureType>(null);
    const [currentStep, setCurrentStep] = React.useState<number>(2);
    const [isModalOpen, setIsModalOpen] = React.useState(false);

    const idCardUrl: string | null = React.useMemo(() => {
        const raw = (validations as any)?.idcard;
        if (typeof raw !== "string") return null;
        const trimmed = raw.trim();
        if (!trimmed || trimmed.includes("no-image")) return null;
        return trimmed;
    }, [validations]);

    const idCardStatus: string = React.useMemo(() => {
        const status = (validations as any)?.idcard_status;
        if (typeof status === "string" && status.trim()) return status;
        return idCardUrl ? "uploaded" : "missing";
    }, [validations, idCardUrl]);

    const shouldShowUploadPanel = React.useMemo(() => {
        const normalized = String(idCardStatus || "").toLowerCase();
        if (!idCardUrl) return true;
        if (normalized === "rejected") return true;
        return false;
    }, [idCardUrl, idCardStatus]);

    return (
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
                            onClick={() => setIsModalOpen(true)}
                            title="Preview ID Card"
                            style={{
                                display: "block",
                                width: "100%",
                                padding: 0,
                                border: 0,
                                background: "transparent",
                                cursor: "pointer",
                                overflow: "hidden",
                            }}
                        >
                            <img
                                src={idCardUrl}
                                alt="ID Card"
                                style={{
                                    display: "block",
                                    width: "100%",
                                    maxWidth: "100%",
                                    height: "auto",
                                    maxHeight: "180px",
                                    objectFit: "contain",
                                    borderRadius: "0.5rem",
                                    backgroundColor: "rgba(0,0,0,0.25)",
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
                    <div className="mt-2" style={{ color: "#95a5a6" }}>
                        {shouldShowUploadPanel
                            ? "Upload a clear photo of your ID card."
                            : "Your ID card is on file."}
                    </div>
                </div>

                {shouldShowUploadPanel && (
                    <div className="mt-3">
                        <div style={{ width: "100%", maxWidth: "620px" }}>
                            <CaptureDevices
                                data={{
                                    course_date_id: null,
                                    student_unit_id: null,
                                    course_auth_id: courseAuthId ?? null,
                                }}
                                photoType="idcard"
                                student={(student as any) || ({} as any)}
                                validations={
                                    validations
                                        ? {
                                            headshot:
                                                (validations as any)
                                                    ?.headshot ?? null,
                                            idcard:
                                                (validations as any)
                                                    ?.idcard ?? null,
                                        }
                                        : null
                                }
                                showCaptureType={showCaptureType as any}
                                setShowCaptureType={setShowCaptureType as any}
                                setCurrentStep={setCurrentStep}
                                currentStep={currentStep}
                                onUploaded={() => setShowCaptureType(null)}
                                debug={false}
                            />
                        </div>
                    </div>
                )}

                <Modal
                    show={Boolean(idCardUrl) && isModalOpen}
                    onHide={() => setIsModalOpen(false)}
                    centered
                    size="lg"
                >
                    <Modal.Header closeButton>
                        <Modal.Title>ID Card Preview</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        {idCardUrl && (
                            <img
                                src={idCardUrl}
                                alt="ID Card Preview"
                                style={{
                                    width: "100%",
                                    height: "auto",
                                    maxHeight: "70vh",
                                    objectFit: "contain",
                                    display: "block",
                                    backgroundColor: "rgba(0,0,0,0.05)",
                                    borderRadius: "0.5rem",
                                }}
                            />
                        )}
                    </Modal.Body>
                </Modal>
            </div>
        </div>
    );
};

export default IdCardSection;
