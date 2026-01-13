import React from "react";

const DeleteCourseScheduleModal = ({
    courseName,
    confirmDelete,
    cancelDelete,
}: {
    courseName: { course_name: string };
    confirmDelete: () => void;
    cancelDelete: () => void;
}) => {
    return (
        <div
            className="modal fade show"
            style={{ display: "block", zIndex: 9999 }}
            tabIndex={-1}
        >
            <div className="modal-dialog">
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title">Delete Course</h5>
                        <button
                            type="button"
                            className="btn-close"
                            onClick={cancelDelete}
                        >
                            <i className="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div className="modal-body">
                        <p>
                            Are you sure you want to delete{" "}
                            <strong>{course.course_name}</strong>?
                        </p>
                        <p className="text-danger">
                            This action cannot be undone.
                        </p>
                    </div>
                    <div className="modal-footer">
                        <button
                            type="button"
                            className="btn btn-secondary"
                            onClick={cancelDelete}
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            className="btn btn-danger"
                            onClick={confirmDelete}
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DeleteCourseScheduleModal;
