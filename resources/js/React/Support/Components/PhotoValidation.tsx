import React, { useState } from "react";

interface PhotoValidationData {
    student: {
        id: number;
        name: string;
        email: string;
        student_number: string | null;
    };
    idcard: {
        validation_id: number | null;
        image_url: string | null;
        status: "missing" | "uploaded" | "approved" | "rejected";
        uploaded_at: string | null;
        reject_reason: string | null;
    };
    headshot: {
        validation_id: number | null;
        image_url: string | null;
        status: "missing" | "uploaded" | "approved" | "rejected";
        captured_at: string | null;
        reject_reason: string | null;
    };
    fully_verified: boolean;
}

interface PhotoValidationProps {
    photos: PhotoValidationData | null;
}

const PhotoValidation: React.FC<PhotoValidationProps> = ({ photos }) => {
    const [zoomedImage, setZoomedImage] = useState<string | null>(null);

    if (!photos) {
        return (
            <div className="alert alert-info">
                <i className="fas fa-info-circle mr-2"></i>
                No photo validation data available for this course.
            </div>
        );
    }

    const getStatusBadge = (status: string) => {
        switch (status) {
            case "approved":
                return (
                    <span className="badge badge-success">
                        <i className="fas fa-check mr-1"></i>Approved
                    </span>
                );
            case "rejected":
                return (
                    <span className="badge badge-danger">
                        <i className="fas fa-times mr-1"></i>Rejected
                    </span>
                );
            case "uploaded":
                return (
                    <span className="badge badge-warning">
                        <i className="fas fa-clock mr-1"></i>Pending Review
                    </span>
                );
            case "missing":
                return (
                    <span className="badge badge-secondary">
                        <i className="fas fa-question mr-1"></i>Not Uploaded
                    </span>
                );
            default:
                return (
                    <span className="badge badge-secondary">
                        <i className="fas fa-question mr-1"></i>Unknown
                    </span>
                );
        }
    };

    const formatDate = (dateString: string | null) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleString();
    };

    return (
        <div>
            {/* Overall Status */}
            <div className="alert alert-info mb-3">
                <div className="d-flex justify-content-between align-items-center">
                    <div>
                        <i className="fas fa-user-check mr-2"></i>
                        <strong>Verification Status:</strong>
                    </div>
                    <div>
                        {photos.fully_verified ? (
                            <span className="badge badge-success badge-lg">
                                <i className="fas fa-check-circle mr-1"></i>
                                Fully Verified
                            </span>
                        ) : (
                            <span className="badge badge-warning badge-lg">
                                <i className="fas fa-exclamation-triangle mr-1"></i>
                                Verification Incomplete
                            </span>
                        )}
                    </div>
                </div>
            </div>

            {/* ID Card Section */}
            <div className="card mb-3">
                <div className="card-header bg-primary text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-id-card mr-2"></i>
                        ID Card
                    </h5>
                </div>
                <div className="card-body">
                    <div className="row">
                        <div className="col-md-6">
                            {photos.idcard.image_url ? (
                                <div>
                                    <img
                                        src={photos.idcard.image_url}
                                        alt="ID Card"
                                        className="img-fluid rounded border cursor-pointer"
                                        onClick={() =>
                                            setZoomedImage(
                                                photos.idcard.image_url
                                            )
                                        }
                                        style={{
                                            cursor: "pointer",
                                            maxHeight: "300px",
                                        }}
                                    />
                                    <p className="text-center text-muted mt-2">
                                        <small>
                                            <i className="fas fa-search-plus mr-1"></i>
                                            Click to zoom
                                        </small>
                                    </p>
                                </div>
                            ) : (
                                <div className="text-center p-5 bg-light rounded">
                                    <i
                                        className="fas fa-id-card text-muted"
                                        style={{ fontSize: "48px" }}
                                    ></i>
                                    <p className="text-muted mt-2">
                                        No ID card uploaded
                                    </p>
                                </div>
                            )}
                        </div>
                        <div className="col-md-6">
                            <div className="mb-3">
                                <strong>Status:</strong>
                                <div className="mt-1">
                                    {getStatusBadge(photos.idcard.status)}
                                </div>
                            </div>
                            <div className="mb-3">
                                <strong>Uploaded:</strong>
                                <div className="text-muted">
                                    {formatDate(photos.idcard.uploaded_at)}
                                </div>
                            </div>
                            {photos.idcard.reject_reason && (
                                <div className="alert alert-danger">
                                    <strong>
                                        <i className="fas fa-exclamation-triangle mr-2"></i>
                                        Rejection Reason:
                                    </strong>
                                    <p className="mb-0 mt-1">
                                        {photos.idcard.reject_reason}
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Headshot Section */}
            <div className="card mb-3">
                <div className="card-header bg-info text-white">
                    <h5 className="mb-0">
                        <i className="fas fa-camera mr-2"></i>
                        Headshot Photo
                    </h5>
                </div>
                <div className="card-body">
                    <div className="row">
                        <div className="col-md-6">
                            {photos.headshot.image_url ? (
                                <div>
                                    <img
                                        src={photos.headshot.image_url}
                                        alt="Headshot"
                                        className="img-fluid rounded border cursor-pointer"
                                        onClick={() =>
                                            setZoomedImage(
                                                photos.headshot.image_url
                                            )
                                        }
                                        style={{
                                            cursor: "pointer",
                                            maxHeight: "300px",
                                        }}
                                    />
                                    <p className="text-center text-muted mt-2">
                                        <small>
                                            <i className="fas fa-search-plus mr-1"></i>
                                            Click to zoom
                                        </small>
                                    </p>
                                </div>
                            ) : (
                                <div className="text-center p-5 bg-light rounded">
                                    <i
                                        className="fas fa-user-circle text-muted"
                                        style={{ fontSize: "48px" }}
                                    ></i>
                                    <p className="text-muted mt-2">
                                        No headshot captured
                                    </p>
                                </div>
                            )}
                        </div>
                        <div className="col-md-6">
                            <div className="mb-3">
                                <strong>Status:</strong>
                                <div className="mt-1">
                                    {getStatusBadge(photos.headshot.status)}
                                </div>
                            </div>
                            <div className="mb-3">
                                <strong>Captured:</strong>
                                <div className="text-muted">
                                    {formatDate(photos.headshot.captured_at)}
                                </div>
                            </div>
                            {photos.headshot.reject_reason && (
                                <div className="alert alert-danger">
                                    <strong>
                                        <i className="fas fa-exclamation-triangle mr-2"></i>
                                        Rejection Reason:
                                    </strong>
                                    <p className="mb-0 mt-1">
                                        {photos.headshot.reject_reason}
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Zoom Modal */}
            {zoomedImage && (
                <div
                    className="modal fade show d-block"
                    style={{ backgroundColor: "rgba(0,0,0,0.8)" }}
                    onClick={() => setZoomedImage(null)}
                >
                    <div className="modal-dialog modal-lg modal-dialog-centered">
                        <div className="modal-content bg-transparent border-0">
                            <div className="modal-body text-center p-0">
                                <img
                                    src={zoomedImage}
                                    alt="Zoomed"
                                    className="img-fluid rounded"
                                    style={{ maxHeight: "90vh" }}
                                />
                                <button
                                    className="btn btn-light mt-3"
                                    onClick={() => setZoomedImage(null)}
                                >
                                    <i className="fas fa-times mr-2"></i>
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PhotoValidation;
