import React, { useEffect, useMemo, useState } from "react";
import axios from "axios";

interface ZoomStatusResponse {
  success: boolean;
  status?: "enabled" | "disabled" | string;
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
  onZoomReadyChange?: (ready: boolean) => void;
}

const ZoomSetupPanel: React.FC<ZoomSetupPanelProps> = ({
  instUnit,
  courseName,
  onZoomReadyChange,
}) => {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [zoom, setZoom] = useState<ZoomStatusResponse | null>(null);
  const [detailsExpanded, setDetailsExpanded] = useState(false);

  const isZoomReady = zoom?.status === "enabled";

  useEffect(() => {
    let mounted = true;

    const fetchZoomStatus = async () => {
      setLoading(true);
      setError(null);

      try {
        const res = await axios.get<ZoomStatusResponse>(
          "/admin/instructors/zoom/status"
        );
        if (!mounted) return;
        setZoom(res.data);
      } catch (err: any) {
        if (!mounted) return;
        setError(err?.message || "Failed to load Zoom status");
      } finally {
        if (!mounted) return;
        setLoading(false);
      }
    };

    fetchZoomStatus();

    return () => {
      mounted = false;
    };
  }, []);

  useEffect(() => {
    if (!onZoomReadyChange) return;
    onZoomReadyChange(isZoomReady);
  }, [isZoomReady, onZoomReadyChange]);

  const derivedCourseName = useMemo(() => {
    return courseName || zoom?.course_name || "Live Class";
  }, [courseName, zoom?.course_name]);

  const canComplete = useMemo(() => {
    if (isZoomReady) return false;
    if (loading) return false;
    if (saving) return false;
    return true;
  }, [isZoomReady, loading, saving]);

  const handleComplete = async () => {
    if (!canComplete) return;

    setSaving(true);
    setError(null);

    try {
      // Enable Zoom screen sharing state (existing endpoint)
      const toggleRes = await axios.post("/admin/instructors/zoom/toggle", {
        status: "enabled",
      });

      setZoom((prev) => ({
        ...(prev || {}),
        success: true,
        status: "enabled",
        meeting_id: toggleRes?.data?.meeting_id ?? prev?.meeting_id,
        passcode: toggleRes?.data?.passcode ?? prev?.passcode,
        password: toggleRes?.data?.password ?? prev?.password,
        email: toggleRes?.data?.email ?? prev?.email,
        course_name: toggleRes?.data?.course_name ?? prev?.course_name,
      }));

      // Collapse to a single-line status after completing.
      setDetailsExpanded(false);
    } catch (err: any) {
      setError(err?.response?.data?.message || err?.message || "Failed to complete Zoom setup");
    } finally {
      setSaving(false);
    }
  };

  if (isZoomReady) {
    return (
      <div className="w-100 p-0 m-0">
        <div className="bg-dark text-light px-3 py-2 border-bottom border-secondary d-flex align-items-center justify-content-between">
          <div className="d-flex align-items-center gap-2">
            <span className="badge bg-success">enabled</span>
            <strong>Active Screenshare Session</strong>
          </div>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary"
            onClick={() => setDetailsExpanded((v) => !v)}
          >
            {detailsExpanded ? (
              <>
                Hide <i className="fas fa-chevron-up ms-1" />
              </>
            ) : (
              <>
                Details <i className="fas fa-chevron-down ms-1" />
              </>
            )}
          </button>
        </div>

        {detailsExpanded && (
          <div className="card m-0 bg-dark text-light border-0 rounded-0">
            <div className="card-body py-2 px-3">
              <div className="row g-2">
                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Zoom Account</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.email || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Meeting ID (PMI)</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.meeting_id || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Passcode</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.passcode || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Host Password</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.password || ""}
                    readOnly
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
      <div className="card m-0 bg-dark text-light border-0 rounded-0">
        <div className="card-header d-flex justify-content-between align-items-center bg-dark text-light py-2 px-3 border-bottom border-secondary">
          <div>
            <strong>Zoom Setup</strong>
            <div className="text-muted small">{derivedCourseName}</div>
          </div>
          {zoom?.status && (
            <span
              className={`badge ${zoom.status === "enabled" ? "bg-success" : "bg-secondary"}`}
            >
              {zoom.status}
            </span>
          )}
        </div>

        <div className="card-body py-2 px-3">
          {loading && (
            <div className="text-center py-3 text-light">
              <i className="fas fa-spinner fa-spin me-2" /> Loading Zoom connection...
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
              <div className="row g-2">
                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Zoom Account</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.email || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Meeting ID (PMI)</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.meeting_id || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Passcode</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.passcode || ""}
                    readOnly
                  />
                </div>

                <div className="col-12 col-lg-6">
                  <label className="form-label small text-muted mb-1">Host Password</label>
                  <input
                    className="form-control form-control-sm bg-dark text-light border-secondary"
                    value={zoom?.password || ""}
                    readOnly
                  />
                </div>

              </div>

              <div className="d-flex justify-content-end mt-2">
                <button
                  className="btn btn-sm btn-primary"
                  disabled={!canComplete || saving}
                  onClick={handleComplete}
                >
                  {saving ? "Saving..." : "Completed"}
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
