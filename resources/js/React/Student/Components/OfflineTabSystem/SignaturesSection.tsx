import React from "react";
import SignaturePad from "./SignaturePad";
import apiClient from "../../../Config/axios";

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

interface SignaturesSectionProps {
    courseAuthId: number | null;
    studentId: number | null;
    existingSignatureUrl?: string | null;
}

const SignaturesSection: React.FC<SignaturesSectionProps> = ({
    courseAuthId,
    studentId,
    existingSignatureUrl = null,
}) => {
    const [savedSignature, setSavedSignature] = React.useState<string | null>(
        existingSignatureUrl ?? null,
    );
    const [isSaving, setIsSaving] = React.useState(false);
    const [saveError, setSaveError] = React.useState<string | null>(null);

    // Sync if poll refreshes and provides an existing URL
    React.useEffect(() => {
        if (existingSignatureUrl && !savedSignature) {
            setSavedSignature(existingSignatureUrl);
        }
    }, [existingSignatureUrl]);

    const handleSave = async (dataUrl: string) => {
        if (!courseAuthId || !studentId) {
            setSavedSignature(dataUrl);
            setSaveError("Missing enrollment context — signature not saved to server.");
            return;
        }

        setIsSaving(true);
        setSaveError(null);

        try {
            const response = await apiClient.post("/classroom/save-student-signature", {
                course_auth_id: courseAuthId,
                student_id: studentId,
                signature: dataUrl,
            });

            if (response.data?.success) {
                // Use the server-returned URL if available, otherwise use the dataUrl for preview
                const url = response.data?.data?.signature_url ?? dataUrl;
                setSavedSignature(url);
            } else {
                setSavedSignature(dataUrl);
                setSaveError(response.data?.message ?? "Signature saved locally only.");
            }
        } catch (err: any) {
            setSavedSignature(dataUrl);
            const msg = err?.response?.data?.message ?? err?.message ?? "Upload failed";
            setSaveError(msg);
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <div className="card" style={cardStyle}>
            <div className="card-body">
                <h6 style={{ color: "white", fontWeight: 600 }}>
                    <i
                        className="fas fa-pen-nib me-2"
                        style={{ color: "#f39c12" }}
                    ></i>
                    Signatures
                </h6>

                <div className="mt-2">
                    {savedSignature ? (
                        <div>
                            <div
                                style={{
                                    color: "#ecf0f1",
                                    marginBottom: "0.5rem",
                                }}
                            >
                                <strong>Saved Signature:</strong>
                            </div>
                            <div
                                style={{
                                    border: "2px solid #34495e",
                                    borderRadius: "0.5rem",
                                    padding: "0.5rem",
                                    backgroundColor: "#ecf0f1",
                                    display: "inline-block",
                                    marginBottom: "0.75rem",
                                }}
                            >
                                <img
                                    src={savedSignature}
                                    alt="Saved Signature"
                                    style={{
                                        display: "block",
                                        maxWidth: "100%",
                                    }}
                                />
                            </div>
                            <div>
                                <button
                                    className="btn btn-sm btn-outline-light"
                                    onClick={() => {
                                        setSavedSignature(null);
                                        setSaveError(null);
                                    }}
                                >
                                    <i className="fas fa-edit me-1"></i>
                                    Create New Signature
                                </button>
                            </div>
                        </div>
                    ) : (
                        <div>
                            <div
                                style={{
                                    color: "#ecf0f1",
                                    marginBottom: "0.75rem",
                                }}
                            >
                                Please sign below:
                            </div>
                            {isSaving ? (
                                <div style={{ color: "#f39c12" }}>
                                    <i className="fas fa-spinner fa-spin me-2"></i>
                                    Saving signature...
                                </div>
                            ) : (
                                <SignaturePad
                                    onSave={handleSave}
                                    width={350}
                                    height={150}
                                />
                            )}
                        </div>
                    )}

                    {saveError && (
                        <div className="mt-2" style={{ color: "#e74c3c", fontSize: "0.85rem" }}>
                            <i className="fas fa-exclamation-triangle me-1"></i>
                            {saveError}
                        </div>
                    )}
                </div>

                <div className="mt-3" style={{ color: "#95a5a6" }}>
                    <i className="fas fa-info-circle me-1"></i>
                    Your signature will be used for course completion
                    certificates.
                </div>
            </div>
        </div>
    );
};

export default SignaturesSection;

