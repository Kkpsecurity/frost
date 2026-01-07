import React, { useState } from "react";
import axios from "axios";
import StudentAgreement from "./Onboarding/StudentAgreement";
import ClassRules from "./Onboarding/ClassRules";
import CaptureIDForValidation from "./Onboarding/Video/CaptureIDForValidation";

interface OnboardingFlowProps {
    courseAuthId: number;
    courseDateId: number;
    studentUnitId: number;
    studentUnit: any;
    student: any;
    course: any;
    courseAuth?: any; // Pass courseAuth to check agreed_at
    validations?: any;
    onComplete: () => void;
}

interface OnboardingState {
    currentStep: number;
    termsAccepted: boolean;
    rulesAccepted: boolean;
    idCardUploaded: boolean;
    headshotUploaded: boolean;
    loading: boolean;
    error: string | null;
}

/**
 * OnboardingFlow - Multi-step onboarding process for students
 *
 * Steps:
 * 1. Student Agreement (Terms of Service)
 * 2. Classroom Rules
 * 3. Identity Verification (ID Card + Headshot)
 * 4. Completion
 *
 * Gates classroom access until all steps complete
 */
const OnboardingFlow: React.FC<OnboardingFlowProps> = ({
    courseAuthId,
    courseDateId,
    studentUnitId,
    studentUnit,
    student,
    course,
    courseAuth,
    validations,
    onComplete,
}) => {
    const getTodayKey = () => {
        try {
            return new Date().toLocaleString('en-US', { weekday: 'long' }).toLowerCase();
        } catch {
            return 'monday';
        }
    };

    const getHeadshotUrlFromValidations = (): string | null => {
        const headshot = validations?.headshot;
        if (!headshot) return null;

        if (typeof headshot === 'string') return headshot;
        if (Array.isArray(headshot)) return headshot.find(Boolean) || null;

        // Backend sends { monday: url|null, ... } (object)
        if (typeof headshot === 'object') {
            const todayKey = getTodayKey();
            const todayUrl = headshot?.[todayKey];
            if (typeof todayUrl === 'string' && todayUrl.length > 0) return todayUrl;
            const firstUrl = Object.values(headshot).find((v: any) => typeof v === 'string' && v.length > 0);
            return (firstUrl as string) || null;
        }

        return null;
    };

    const derivedIdCardUploaded = !!(validations?.idcard);
    const derivedHeadshotUploaded = !!getHeadshotUrlFromValidations();

    const idCardUrl: string | null = typeof validations?.idcard === 'string' ? validations.idcard : null;
    const todayHeadshotUrl: string | null = getHeadshotUrlFromValidations();

    // Check agreement status from courseAuth (one-time per course)
    const hasAgreedToTerms = courseAuth?.agreed_at !== null && courseAuth?.agreed_at !== undefined;

    const [state, setState] = useState<OnboardingState>({
        currentStep: 1,
        termsAccepted: hasAgreedToTerms, // Use courseAuth.agreed_at
        rulesAccepted: studentUnit?.rules_accepted || false,
        // ID card: once per courseAuth (use poll validations.idcard)
        idCardUploaded: derivedIdCardUploaded,
        // Headshot: per-day (use poll validations.headshot[today])
        headshotUploaded: derivedHeadshotUploaded,
        loading: false,
        error: null,
    });

    // Calculate which step to show based on completion status
    const getInitialStep = (): number => {
        if (!state.termsAccepted) return 1;
        if (!state.rulesAccepted) return 2;
        if (!state.idCardUploaded || !state.headshotUploaded) return 3;
        return 4;
    };

    React.useEffect(() => {
        const initialStep = getInitialStep();
        if (initialStep !== state.currentStep) {
            setState(prev => ({ ...prev, currentStep: initialStep }));
        }
    }, []);

    // Keep onboarding status in sync with the classroom poll so refresh + polling reflect completion.
    React.useEffect(() => {
        const nextIdCardUploaded = !!(validations?.idcard);
        const nextHeadshotUploaded = !!getHeadshotUrlFromValidations();

        setState(prev => {
            const shouldUpdate =
                prev.idCardUploaded !== nextIdCardUploaded ||
                prev.headshotUploaded !== nextHeadshotUploaded;

            if (!shouldUpdate) return prev;

            return {
                ...prev,
                idCardUploaded: nextIdCardUploaded,
                headshotUploaded: nextHeadshotUploaded,
            };
        });
    }, [validations]);

    const handleNextStep = () => {
        setState((prev) => ({
            ...prev,
            currentStep: Math.min(prev.currentStep + 1, 4),
            error: null,
        }));
    };

    const handlePrevStep = () => {
        setState((prev) => ({
            ...prev,
            currentStep: Math.max(prev.currentStep - 1, 1),
            error: null,
        }));
    };

    const handleAcceptTerms = async () => {
        // StudentAgreement component handles the API call
        // Just update state and move to next step
        setState((prev) => ({
            ...prev,
            termsAccepted: true,
            currentStep: 2,
            error: null,
        }));
    };

    const handleAcceptRules = async () => {
        setState((prev) => ({ ...prev, loading: true, error: null }));

        try {
            await axios.post("/classroom/portal/student/rules", {
                course_date_id: courseDateId,
            });

            setState((prev) => ({
                ...prev,
                rulesAccepted: true,
                loading: false,
            }));

            handleNextStep();
        } catch (error: any) {
            setState((prev) => ({
                ...prev,
                loading: false,
                error: error.response?.data?.message || "Failed to accept rules",
            }));
        }
    };

    const handleUploadIdCard = async (file: File) => {
        setState((prev) => ({ ...prev, loading: true, error: null }));

        try {
            const formData = new FormData();
            formData.append("course_date_id", courseDateId.toString());
            formData.append("id_document", file);

            await axios.post("/classroom/id-verification/start", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            setState((prev) => ({
                ...prev,
                idCardUploaded: true,
                loading: false,
            }));
        } catch (error: any) {
            setState((prev) => ({
                ...prev,
                loading: false,
                error: error.response?.data?.message || "Failed to upload ID card",
            }));
        }
    };

    const handleUploadHeadshot = async (file: File) => {
        setState((prev) => ({ ...prev, loading: true, error: null }));

        try {
            const formData = new FormData();
            formData.append("course_date_id", courseDateId.toString());
            formData.append("headshot", file);

            await axios.post("/classroom/id-verification/upload-headshot", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            setState((prev) => ({
                ...prev,
                headshotUploaded: true,
                loading: false,
            }));

            handleNextStep();
        } catch (error: any) {
            setState((prev) => ({
                ...prev,
                loading: false,
                error: error.response?.data?.message || "Failed to upload headshot",
            }));
        }
    };

    const handleCompleteOnboarding = async () => {
        setState((prev) => ({ ...prev, loading: true, error: null }));

        try {
            await axios.post("/classroom/student/onboarding/complete", {
                course_date_id: courseDateId,
            });

            setState((prev) => ({ ...prev, loading: false }));
            onComplete(); // Trigger refresh/redirect to classroom
        } catch (error: any) {
            setState((prev) => ({
                ...prev,
                loading: false,
                error:
                    error.response?.data?.message ||
                    "Failed to complete onboarding. Please ensure all steps are finished.",
            }));
        }
    };

    const renderProgressDots = () => {
        return (
            <div className="d-flex justify-content-center align-items-center gap-2 mb-4">
                {[1, 2, 3, 4].map((step) => (
                    <div
                        key={step}
                        style={{
                            width: "12px",
                            height: "12px",
                            borderRadius: "50%",
                            backgroundColor:
                                step === state.currentStep
                                    ? "#3498db"
                                    : step < state.currentStep
                                    ? "#2ecc71"
                                    : "#95a5a6",
                            transition: "all 0.3s",
                        }}
                    />
                ))}
            </div>
        );
    };

    return (
        <div
            style={{
                backgroundColor: "#1a1f2e",
                minHeight: "100vh",
                display: "flex",
                alignItems: "flex-start", // Changed from "center" to "flex-start"
                justifyContent: "center",
                padding: "2rem",
                paddingTop: "4rem", // Added more top padding
            }}
        >
            <div
                className="card"
                style={{
                    backgroundColor: "#2c3e50",
                    border: "2px solid #3498db",
                    borderRadius: "0.75rem",
                    maxWidth: "800px",
                    marginTop: "2rem", // Increased from 1rem and removed !important
                    width: "100%",
                    boxShadow: "0 4px 6px rgba(0,0,0,0.3)",
                }}
            >
                <div
                    className="card-header"
                    style={{
                        backgroundColor: "#34495e",
                        borderBottom: "1px solid rgba(255,255,255,0.1)",
                        padding: "1.5rem",
                    }}
                >
                    <h4 className="mb-1" style={{ color: "white" }}>
                        <i className="fas fa-clipboard-check me-2" style={{ color: "#3498db" }}></i>
                        Classroom Onboarding
                    </h4>
                    <p className="mb-0" style={{ color: "#95a5a6", fontSize: "0.875rem" }}>
                        Step {state.currentStep} of 4
                    </p>
                </div>

                <div className="card-body" style={{ padding: "2rem" }}>
                    {renderProgressDots()}

                    {state.error && (
                        <div
                            className="alert alert-danger"
                            style={{
                                backgroundColor: "rgba(231, 76, 60, 0.1)",
                                border: "1px solid #e74c3c",
                                color: "#e74c3c",
                                marginBottom: "1.5rem",
                            }}
                        >
                            <i className="fas fa-exclamation-triangle me-2"></i>
                            {state.error}
                        </div>
                    )}

                    {/* Step 1: Terms of Service - Show validation if already agreed */}
                    {state.currentStep === 1 && state.termsAccepted && (
                        <div className="text-center" style={{ padding: "2rem" }}>
                            <i
                                className="fas fa-check-circle"
                                style={{ fontSize: "3rem", color: "#2ecc71", marginBottom: "1rem" }}
                            ></i>
                            <h5 style={{ color: "white", marginBottom: "0.5rem" }}>
                                Validating Step 1
                            </h5>
                            <p style={{ color: "#95a5a6" }}>
                                Agreement already on file. Moving to next step...
                            </p>
                            <div className="spinner-border text-primary mt-2" role="status">
                                <span className="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    )}

                    {/* Step 1: Terms of Service - Show form if not agreed */}
                    {state.currentStep === 1 && !state.termsAccepted && (
                        <StudentAgreement
                            student={{
                                ...student,
                                course_auth_id: courseAuthId,
                                course_date_id: courseDateId,
                            }}
                            course={course}
                            onSuccess={handleAcceptTerms}
                        />
                    )}

                    {/* Step 2: Classroom Rules */}
                    {state.currentStep === 2 && (
                        <ClassRules
                            onAgree={handleAcceptRules}
                            loading={state.loading}
                        />
                    )}

                    {/* Step 3: Identity Verification */}
                    {state.currentStep === 3 && (
                        <CaptureIDForValidation
                            data={{
                                ...(course || {}),
                                course_date_id: courseDateId,
                                course_auth_id: courseAuthId,
                            }}
                            student={{
                                ...student,
                                course_auth_id: courseAuthId,
                                course_date_id: courseDateId,
                            }}
                            validations={{
                                headshot: validations?.headshot ?? null,
                                idcard: validations?.idcard ?? null,
                            }}
                            onComplete={handleNextStep}
                            debug={false}
                        />
                    )}

                    {/* Step 4: Completion */}
                    {state.currentStep === 4 && (
                        <div>
                            <div className="text-center mb-4">
                                <i
                                    className="fas fa-check-circle"
                                    style={{ fontSize: "4rem", color: "#2ecc71" }}
                                ></i>
                            </div>

                            <h5
                                className="text-center"
                                style={{ color: "white", marginBottom: "1rem" }}
                            >
                                Onboarding Complete!
                            </h5>

                            <p
                                className="text-center"
                                style={{ color: "#ecf0f1", marginBottom: "2rem" }}
                            >
                                You're all set to enter the classroom.
                            </p>

                            {/* Confirmation images */}
                            <div
                                style={{
                                    display: "flex",
                                    gap: "1rem",
                                    flexWrap: "wrap",
                                    marginBottom: "2rem",
                                }}
                            >
                                <div
                                    style={{
                                        flex: "1 1 320px",
                                        backgroundColor: "rgba(255,255,255,0.04)",
                                        border: "1px solid rgba(255,255,255,0.1)",
                                        borderRadius: "0.75rem",
                                        padding: "1rem",
                                    }}
                                >
                                    <div style={{ color: "#ecf0f1", marginBottom: "0.75rem" }}>
                                        <i className="fas fa-id-card me-2" style={{ color: "#3498db" }}></i>
                                        ID Card
                                    </div>
                                    {idCardUrl ? (
                                        <img
                                            src={idCardUrl}
                                            alt="ID Card"
                                            style={{
                                                width: "100%",
                                                maxHeight: "260px",
                                                objectFit: "contain",
                                                backgroundColor: "rgba(0,0,0,0.25)",
                                                borderRadius: "0.5rem",
                                            }}
                                        />
                                    ) : (
                                        <div style={{ color: "#95a5a6" }}>No ID card image found.</div>
                                    )}
                                </div>

                                <div
                                    style={{
                                        flex: "1 1 320px",
                                        backgroundColor: "rgba(255,255,255,0.04)",
                                        border: "1px solid rgba(255,255,255,0.1)",
                                        borderRadius: "0.75rem",
                                        padding: "1rem",
                                    }}
                                >
                                    <div style={{ color: "#ecf0f1", marginBottom: "0.75rem" }}>
                                        <i className="fas fa-user-circle me-2" style={{ color: "#3498db" }}></i>
                                        Headshot (Today)
                                    </div>
                                    {todayHeadshotUrl ? (
                                        <img
                                            src={todayHeadshotUrl}
                                            alt="Headshot"
                                            style={{
                                                width: "100%",
                                                maxHeight: "260px",
                                                objectFit: "contain",
                                                backgroundColor: "rgba(0,0,0,0.25)",
                                                borderRadius: "0.5rem",
                                            }}
                                        />
                                    ) : (
                                        <div style={{ color: "#95a5a6" }}>No headshot image found for today.</div>
                                    )}
                                </div>
                            </div>

                            <div style={{ marginBottom: "2rem" }}>
                                <div
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        padding: "0.75rem",
                                        backgroundColor: "rgba(46, 204, 113, 0.1)",
                                        borderRadius: "0.5rem",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle me-3"
                                        style={{ color: "#2ecc71", fontSize: "1.25rem" }}
                                    ></i>
                                    <span style={{ color: "#ecf0f1" }}>Terms Accepted</span>
                                </div>

                                <div
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        padding: "0.75rem",
                                        backgroundColor: "rgba(46, 204, 113, 0.1)",
                                        borderRadius: "0.5rem",
                                        marginBottom: "0.5rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle me-3"
                                        style={{ color: "#2ecc71", fontSize: "1.25rem" }}
                                    ></i>
                                    <span style={{ color: "#ecf0f1" }}>Rules Acknowledged</span>
                                </div>

                                <div
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        padding: "0.75rem",
                                        backgroundColor: "rgba(46, 204, 113, 0.1)",
                                        borderRadius: "0.5rem",
                                    }}
                                >
                                    <i
                                        className="fas fa-check-circle me-3"
                                        style={{ color: "#2ecc71", fontSize: "1.25rem" }}
                                    ></i>
                                    <span style={{ color: "#ecf0f1" }}>Identity Verified</span>
                                </div>
                            </div>

                            <button
                                className="btn btn-success w-100"
                                onClick={handleCompleteOnboarding}
                                disabled={state.loading}
                                style={{ fontSize: "1.1rem", padding: "0.75rem" }}
                            >
                                {state.loading ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin me-2"></i>
                                        Completing...
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-door-open me-2"></i>
                                        Enter Classroom
                                    </>
                                )}
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default OnboardingFlow;
