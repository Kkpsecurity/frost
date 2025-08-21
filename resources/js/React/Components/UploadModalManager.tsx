/**
 * Upload Modal Manager
 * React component for managing file upload modals
 */

import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';

interface UploadModalProps {
  id: string;
  title?: string;
  acceptedTypes?: string[];
  maxFileSize?: number;
  multiple?: boolean;
  onUploadSuccess?: (files: File[]) => void;
  onUploadError?: (error: string) => void;
}

interface UploadProgress {
  file: File;
  progress: number;
  status: 'pending' | 'uploading' | 'success' | 'error';
  error?: string;
}

const UploadModal: React.FC<UploadModalProps> = ({
  id,
  title = 'Upload Files',
  acceptedTypes = ['*'],
  maxFileSize = 10 * 1024 * 1024, // 10MB
  multiple = false,
  onUploadSuccess,
  onUploadError
}) => {
  const [files, setFiles] = useState<UploadProgress[]>([]);
  const [isDragging, setIsDragging] = useState(false);
  const [isUploading, setIsUploading] = useState(false);

  const handleFileSelect = (selectedFiles: FileList | null) => {
    if (!selectedFiles) return;

    const fileArray = Array.from(selectedFiles);
    const validFiles: UploadProgress[] = [];

    fileArray.forEach(file => {
      // Check file size
      if (file.size > maxFileSize) {
        onUploadError?.(`File ${file.name} is too large. Maximum size is ${maxFileSize / (1024 * 1024)}MB`);
        return;
      }

      // Check file type if specified
      if (acceptedTypes[0] !== '*') {
        const fileType = file.type || file.name.split('.').pop();
        const isValidType = acceptedTypes.some(type => 
          type === fileType || file.name.toLowerCase().endsWith(type.toLowerCase())
        );
        
        if (!isValidType) {
          onUploadError?.(`File ${file.name} is not an accepted type. Accepted types: ${acceptedTypes.join(', ')}`);
          return;
        }
      }

      validFiles.push({
        file,
        progress: 0,
        status: 'pending'
      });
    });

    if (!multiple && validFiles.length > 1) {
      onUploadError?.('Only one file can be uploaded at a time');
      return;
    }

    setFiles(multiple ? [...files, ...validFiles] : validFiles);
  };

  const handleDragOver = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(true);
  };

  const handleDragLeave = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    setIsDragging(false);
    handleFileSelect(e.dataTransfer.files);
  };

  const uploadFiles = async () => {
    if (files.length === 0) return;

    setIsUploading(true);
    const formData = new FormData();
    
    files.forEach((fileProgress, index) => {
      if (fileProgress.status === 'pending') {
        formData.append(multiple ? `files[${index}]` : 'file', fileProgress.file);
      }
    });

    try {
      // Add CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      if (csrfToken) {
        formData.append('_token', csrfToken);
      }

      const response = await fetch('/api/upload', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        throw new Error(`Upload failed: ${response.statusText}`);
      }

      const result = await response.json();
      
      setFiles(prev => prev.map(f => ({ ...f, status: 'success', progress: 100 })));
      onUploadSuccess?.(files.map(f => f.file));
      
    } catch (error) {
      setFiles(prev => prev.map(f => ({ 
        ...f, 
        status: 'error', 
        error: error instanceof Error ? error.message : 'Upload failed'
      })));
      onUploadError?.(error instanceof Error ? error.message : 'Upload failed');
    } finally {
      setIsUploading(false);
    }
  };

  const removeFile = (index: number) => {
    setFiles(prev => prev.filter((_, i) => i !== index));
  };

  const clearAll = () => {
    setFiles([]);
  };

  return (
    <div className="modal fade" id={id} tabIndex={-1} aria-hidden="true">
      <div className="modal-dialog modal-lg">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">{title}</h5>
            <button
              type="button"
              className="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            />
          </div>
          <div className="modal-body">
            {/* Upload Area */}
            <div
              className={`upload-area border-2 border-dashed rounded p-4 text-center ${
                isDragging ? 'border-primary bg-light' : 'border-secondary'
              }`}
              onDragOver={handleDragOver}
              onDragLeave={handleDragLeave}
              onDrop={handleDrop}
            >
              <i className="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
              <p className="mb-2">
                Drag and drop files here, or{' '}
                <label className="text-primary" style={{ cursor: 'pointer' }}>
                  browse
                  <input
                    type="file"
                    className="d-none"
                    multiple={multiple}
                    accept={acceptedTypes.join(',')}
                    onChange={(e) => handleFileSelect(e.target.files)}
                  />
                </label>
              </p>
              <small className="text-muted">
                Max file size: {maxFileSize / (1024 * 1024)}MB
                {acceptedTypes[0] !== '*' && (
                  <>
                    <br />
                    Accepted types: {acceptedTypes.join(', ')}
                  </>
                )}
              </small>
            </div>

            {/* File List */}
            {files.length > 0 && (
              <div className="mt-4">
                <div className="d-flex justify-content-between align-items-center mb-3">
                  <h6 className="mb-0">Selected Files ({files.length})</h6>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-secondary"
                    onClick={clearAll}
                  >
                    Clear All
                  </button>
                </div>
                <div className="list-group">
                  {files.map((fileProgress, index) => (
                    <div key={index} className="list-group-item">
                      <div className="d-flex justify-content-between align-items-center">
                        <div className="flex-grow-1">
                          <div className="fw-medium">{fileProgress.file.name}</div>
                          <small className="text-muted">
                            {(fileProgress.file.size / (1024 * 1024)).toFixed(2)} MB
                          </small>
                          {fileProgress.status === 'error' && fileProgress.error && (
                            <div className="text-danger small mt-1">
                              {fileProgress.error}
                            </div>
                          )}
                        </div>
                        <div className="ms-3">
                          {fileProgress.status === 'pending' && (
                            <button
                              type="button"
                              className="btn btn-sm btn-outline-danger"
                              onClick={() => removeFile(index)}
                            >
                              <i className="fas fa-times"></i>
                            </button>
                          )}
                          {fileProgress.status === 'uploading' && (
                            <div className="spinner-border spinner-border-sm" role="status">
                              <span className="visually-hidden">Uploading...</span>
                            </div>
                          )}
                          {fileProgress.status === 'success' && (
                            <i className="fas fa-check-circle text-success"></i>
                          )}
                          {fileProgress.status === 'error' && (
                            <i className="fas fa-exclamation-circle text-danger"></i>
                          )}
                        </div>
                      </div>
                      {fileProgress.status === 'uploading' && (
                        <div className="progress mt-2" style={{ height: '4px' }}>
                          <div
                            className="progress-bar"
                            role="progressbar"
                            style={{ width: `${fileProgress.progress}%` }}
                            aria-valuenow={fileProgress.progress}
                            aria-valuemin={0}
                            aria-valuemax={100}
                          />
                        </div>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
          <div className="modal-footer">
            <button
              type="button"
              className="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Cancel
            </button>
            <button
              type="button"
              className="btn btn-primary"
              onClick={uploadFiles}
              disabled={files.length === 0 || isUploading || files.every(f => f.status !== 'pending')}
            >
              {isUploading ? (
                <>
                  <span className="spinner-border spinner-border-sm me-2" role="status" />
                  Uploading...
                </>
              ) : (
                'Upload Files'
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// Initialize upload modals
export const initializeUploadModals = () => {
  const uploadModalElements = document.querySelectorAll('[data-upload-modal]');
  
  uploadModalElements.forEach(element => {
    const config = element.getAttribute('data-upload-config');
    const modalId = element.getAttribute('data-upload-modal') || 'uploadModal';
    
    let props: UploadModalProps = { id: modalId };
    
    if (config) {
      try {
        const parsedConfig = JSON.parse(config);
        props = { ...props, ...parsedConfig };
      } catch (e) {
        console.error('Invalid upload modal configuration:', config);
      }
    }
    
    const container = document.createElement('div');
    element.appendChild(container);
    
    const root = createRoot(container);
    root.render(<UploadModal {...props} />);
  });
};

// Auto-initialize when DOM is loaded
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeUploadModals);
} else {
  initializeUploadModals();
}

export default UploadModal;
