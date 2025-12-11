import React, { useState } from 'react';

interface AssignmentSubmissionProps {
    assignmentTitle?: string;
    dueDate?: string;
    maxFileSize?: string;
}

const AssignmentSubmission: React.FC<AssignmentSubmissionProps> = ({
    assignmentTitle = 'Assignment',
    dueDate = 'No due date',
    maxFileSize = '10MB'
}) => {
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [isSubmitted, setIsSubmitted] = useState(false);
    const [notes, setNotes] = useState('');

    const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setSelectedFile(e.target.files[0]);
        }
    };

    const handleSubmit = () => {
        if (selectedFile || notes.trim()) {
            setIsSubmitted(true);
            // Here you would typically upload the file and notes
            console.log('Submitting assignment:', { file: selectedFile, notes });
        }
    };

    return (
        <div className="assignment-submission">
            <div className="card">
                <div className="card-header">
                    <h6>{assignmentTitle}</h6>
                    <small className="text-muted">Due: {dueDate}</small>
                </div>
                <div className="card-body">
                    {!isSubmitted ? (
                        <>
                            <div className="mb-3">
                                <label htmlFor="fileUpload" className="form-label">
                                    Upload File (Max size: {maxFileSize})
                                </label>
                                <input
                                    type="file"
                                    className="form-control"
                                    id="fileUpload"
                                    onChange={handleFileSelect}
                                />
                                {selectedFile && (
                                    <small className="text-success">
                                        Selected: {selectedFile.name}
                                    </small>
                                )}
                            </div>

                            <div className="mb-3">
                                <label htmlFor="assignmentNotes" className="form-label">
                                    Notes (Optional)
                                </label>
                                <textarea
                                    className="form-control"
                                    id="assignmentNotes"
                                    rows={4}
                                    placeholder="Add any notes or comments about your submission..."
                                    value={notes}
                                    onChange={(e) => setNotes(e.target.value)}
                                />
                            </div>

                            <button
                                className="btn btn-success"
                                onClick={handleSubmit}
                                disabled={!selectedFile && !notes.trim()}
                            >
                                <i className="fas fa-paper-plane"></i> Submit Assignment
                            </button>
                        </>
                    ) : (
                        <div className="alert alert-success">
                            <i className="fas fa-check-circle"></i>
                            <strong> Assignment Submitted!</strong>
                            <p className="mb-0 mt-2">
                                Your assignment has been submitted successfully.
                                You will receive feedback once it's reviewed.
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default AssignmentSubmission;
