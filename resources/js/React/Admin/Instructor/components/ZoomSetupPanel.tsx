import React, { useEffect, useMemo, useState } from "react";
import axios from "axios";
import { ZoomStatusData } from "../types";

interface ZoomStatusResponse {
  success: boolean;
  status?: "enabled" | "disabled" | string;
  is_active?: boolean;
  meeting_id?: string;
  passcode?: string;
  password?: string;
  email?: string;
  course_name?: string;
  inst_unit_id?: number;
  message?: string;
}

interface ZoomSetupPanelProps {
    instUnit: any;
    courseName?: string;
    zoomStatusFromPoll?: ZoomStatusData | null; // NEW: Zoom status from instructor poll
    onZoomReadyChange?: (ready: boolean) => void;
}

const ZoomSetupPanel: React.FC<ZoomSetupPanelProps> = ({
    instUnit,
    courseName,
    zoomStatusFromPoll, // NEW
    onZoomReadyChange,
}) => {
    const [loading, setLoading] = useState(false); // Initially false since we have data from poll
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [zoom, setZoom] = useState<ZoomStatusResponse | null>(null);
    const [detailsExpanded, setDetailsExpanded] = useState(false);

    // Check if Zoom is active - credentials are enabled for sharing
    const isZoomActive = zoom?.is_active === true;

    // NEW: Use zoom status from instructor poll instead of separate API call
    useEffect(() => {
        if (zoomStatusFromPoll) {
            setZoom({
                success: true,
                status: zoomStatusFromPoll.status,
                is_active: zoomStatusFromPoll.is_active,
                email: zoomStatusFromPoll.email,
                meeting_id: zoomStatusFromPoll.meeting_id,
                course_name: zoomStatusFromPoll.course_name,
                message: zoomStatusFromPoll.message,
            });
            setLoading(false);
        }
    }, [zoomStatusFromPoll]);

    useEffect(() => {
        if (!onZoomReadyChange) return;
        onZoomReadyChange(isZoomActive);
    }, [isZoomActive, onZoomReadyChange]);

    const derivedCourseName = useMemo(() => {
        return courseName || zoom?.course_name || "Live Class";
    }, [courseName, zoom?.course_name]);

    const canStartSharing = useMemo(() => {
        if (isZoomActive) return false; // Already active
        if (loading) return false;
        if (saving) return false;
        return true;
    }, [isZoomActive, loading, saving]);

    const handleComplete = async () => {
        if (!canStartSharing) return;

        setSaving(true);
        setError(null);

        try {
            // Enable Zoom screen sharing state (existing endpoint)
            const toggleRes = await axios.post(
                "/admin/instructors/zoom/toggle",
                {
                    status: "enabled",
                }
            );

            setZoom((prev) => ({
                ...(prev || {}),
                success: true,
                status: "enabled",
                is_active: true,
                meeting_id: toggleRes?.data?.meeting_id ?? prev?.meeting_id,
                passcode: toggleRes?.data?.passcode ?? prev?.passcode,
                password: toggleRes?.data?.password ?? prev?.password,
                email: toggleRes?.data?.email ?? prev?.email,
                course_name: toggleRes?.data?.course_name ?? prev?.course_name,
            }));

            // Collapse to a single-line status after completing.
            setDetailsExpanded(false);
        } catch (err: any) {
            setError(
                err?.response?.data?.message ||
                    err?.message ||
                    "Failed to activate Zoom sharing"
            );
        } finally {
            setSaving(false);
        }
    };

    if (isZoomActive) {
        return (
            <div className="w-100 p-0 m-0">
                <div className="bg-success text-white px-3 py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                    <div className="d-flex align-items-center gap-3">
                        <i className="fas fa-check-circle fa-2x mr-2"></i>
                        <div>
                            <strong
                                className="d-block"
                                style={{ fontSize: "1.1rem" }}
                            >
                                Zoom Screen Sharing Active
                            </strong>
                            <small className="text-white-50">
                                Students can now see your screen
                            </small>
                        </div>
                    </div>
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-light"
                        onClick={() => setDetailsExpanded((v) => !v)}
                    >
                        {detailsExpanded ? (
                            <>
                                Hide <i className="fas fa-chevron-up ms-1" />
                            </>
                        ) : (
                            <>
                                Details{" "}
                                <i className="fas fa-chevron-down ms-1" />
                            </>
                        )}
                    </button>
                </div>

                {detailsExpanded && (
                    <div className="card m-0 bg-dark text-light border-0 rounded-0">
                        <div className="card-body py-3 px-3">
                            <h6 className="text-muted mb-3">
                                <i className="fas fa-info-circle mr-2"></i>
                                Zoom Session Details
                            </h6>
                            <div className="row g-3">
                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Zoom Account
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.email || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Meeting ID (PMI)
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.meeting_id || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Passcode
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.passcode || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Host Password
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.password || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        );
    }

    return (
        <div className="w-100 p-0 m-0">
            <div className="card m-0 bg-dark text-light border-secondary rounded shadow-sm">
                <div className="card-header d-flex justify-content-between align-items-center bg-dark text-light py-3 px-3 border-bottom border-secondary">
                    <div>
                        <h5 className="mb-1">
                            <i
                                className="fas fa-video mr-2"
                                style={{ color: "#3498db" }}
                            ></i>
                            <strong>Zoom Setup & Review</strong>
                        </h5>
                        <div className="text-muted small">
                            {derivedCourseName}
                        </div>
                    </div>
                    {zoom?.status && (
                        <span
                            className={`badge ${
                                zoom.status === "enabled"
                                    ? "bg-success"
                                    : "bg-warning"
                            }`}
                        >
                            {zoom.status === "enabled" ? "Active" : "Pending"}
                        </span>
                    )}
                </div>

                <div className="card-body py-3 px-3">
                    {loading && (
                        <div className="text-center py-4 text-light">
                            <i
                                className="fas fa-spinner fa-spin fa-2x mb-3"
                                style={{ color: "#3498db" }}
                            />
                            <p>Loading Zoom credentials...</p>
                        </div>
                    )}

                    {error && !loading && (
                        <div className="alert alert-danger">
                            <i className="fas fa-exclamation-triangle me-2" />
                            {error}
                        </div>
                    )}

                    {!loading && !error && (
                        <>
                            <div className="alert alert-info mb-3">
                                <i className="fas fa-info-circle mr-2"></i>
                                <strong>
                                    Review your Zoom credentials
                                </strong>{" "}
                                before starting screen sharing. Make sure the
                                details are correct, then click "Start Sharing"
                                to activate.
                            </div>

                            <div className="row g-3">
                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Zoom Account
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.email || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Meeting ID (PMI)
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.meeting_id || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Passcode
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.passcode || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>

                                <div className="col-12 col-lg-6">
                                    <label className="form-label small text-muted mb-1">
                                        Host Password
                                    </label>
                                    <input
                                        className="form-control form-control-sm bg-dark text-light border-secondary"
                                        value={zoom?.password || ""}
                                        readOnly
                                        disabled={!isZoomActive}
                                        style={{
                                            opacity: isZoomActive ? 1 : 0.5,
                                        }}
                                    />
                                </div>
                            </div>

                            <div className="d-flex justify-content-end mt-3 gap-2">
                                <button
                                    className="btn btn-sm btn-success"
                                    disabled={!canStartSharing || saving}
                                    onClick={handleComplete}
                                >
                                    {saving ? (
                                        <>
                                            <i className="fas fa-spinner fa-spin mr-2" />
                                            Activating...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-share-square mr-2" />
                                            Start Sharing
                                        </>
                                    )}
                                </button>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ZoomSetupPanel;
