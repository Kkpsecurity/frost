import React from "react";
import SignaturePad from "./SignaturePad";

const cardStyle: React.CSSProperties = {
    backgroundColor: "#2c3e50",
    border: "1px solid #34495e",
    borderRadius: "0.5rem",
};

const SignaturesSection: React.FC = () => {
    const [savedSignature, setSavedSignature] = React.useState<string | null>(
        null,
    );

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
                                    onClick={() => setSavedSignature(null)}
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
                            <SignaturePad
                                onSave={(dataUrl) => {
                                    setSavedSignature(dataUrl);
                                    console.log(
                                        "Signature saved:",
                                        dataUrl.substring(0, 50) + "...",
                                    );
                                }}
                                width={350}
                                height={150}
                            />
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
