import React from 'react';
import { FileData } from '../Hooks/useFileHandler';

interface FileDisplayProps {
    file: FileData | null;
    title?: string;
    width?: number;
    height?: number;
    showDetails?: boolean;
    onRemove?: (fileId: string) => void;
    onDownload?: (file: FileData) => void;
    className?: string;
    style?: React.CSSProperties;
}

const FileDisplay: React.FC<FileDisplayProps> = ({
    file,
    title,
    width = 200,
    height = 150,
    showDetails = false,
    onRemove,
    onDownload,
    className = '',
    style = {},
}) => {
    if (!file) {
        return (
            <div
                className={`file-display-empty ${className}`}
                style={{
                    width,
                    height,
                    border: '2px dashed #ccc',
                    borderRadius: '8px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    color: '#666',
                    backgroundColor: '#f9f9f9',
                    ...style
                }}
            >
                {title && <div style={{ marginBottom: '8px', fontWeight: 'bold' }}>{title}</div>}
                <div>No file uploaded</div>
            </div>
        );
    }

    const formatFileSize = (bytes: number): string => {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const formatDate = (date: Date): string => {
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    return (
        <div
            className={`file-display ${className}`}
            style={{
                border: '1px solid #ddd',
                borderRadius: '8px',
                padding: '8px',
                backgroundColor: '#fff',
                maxWidth: width + 16,
                ...style
            }}
        >
            {title && (
                <div style={{
                    marginBottom: '8px',
                    fontWeight: 'bold',
                    fontSize: '14px',
                    color: '#333'
                }}>
                    {title}
                </div>
            )}

            {/* Image Preview */}
            <div style={{
                width: '100%',
                height: height,
                border: '1px solid #eee',
                borderRadius: '4px',
                overflow: 'hidden',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                backgroundColor: '#f5f5f5'
            }}>
                <img
                    src={file.url}
                    alt={file.name}
                    style={{
                        maxWidth: '100%',
                        maxHeight: '100%',
                        objectFit: 'contain'
                    }}
                    onError={(e) => {
                        console.error('Failed to load image:', file.url);
                        (e.target as HTMLImageElement).style.display = 'none';
                    }}
                />
            </div>

            {/* File Details */}
            {showDetails && (
                <div style={{
                    marginTop: '8px',
                    fontSize: '12px',
                    color: '#666',
                    lineHeight: '1.4'
                }}>
                    <div><strong>Name:</strong> {file.name}</div>
                    <div><strong>Type:</strong> {file.type.replace('_', ' ').toUpperCase()}</div>
                    <div><strong>Size:</strong> {formatFileSize(file.size)}</div>
                    <div><strong>Uploaded:</strong> {formatDate(file.uploadedAt)}</div>
                </div>
            )}

            {/* Action Buttons */}
            {(onRemove || onDownload) && (
                <div style={{
                    marginTop: '8px',
                    display: 'flex',
                    gap: '4px',
                    flexWrap: 'wrap'
                }}>
                    {onDownload && (
                        <button
                            onClick={() => onDownload(file)}
                            style={{
                                padding: '4px 8px',
                                fontSize: '12px',
                                backgroundColor: '#0369a1',
                                color: 'white',
                                border: 'none',
                                borderRadius: '4px',
                                cursor: 'pointer',
                                flex: '1'
                            }}
                            onMouseEnter={(e) => {
                                (e.target as HTMLButtonElement).style.backgroundColor = '#0284c7';
                            }}
                            onMouseLeave={(e) => {
                                (e.target as HTMLButtonElement).style.backgroundColor = '#0369a1';
                            }}
                        >
                            📥 Download
                        </button>
                    )}

                    {onRemove && (
                        <button
                            onClick={() => onRemove(file.id)}
                            style={{
                                padding: '4px 8px',
                                fontSize: '12px',
                                backgroundColor: '#dc2626',
                                color: 'white',
                                border: 'none',
                                borderRadius: '4px',
                                cursor: 'pointer',
                                flex: '1'
                            }}
                            onMouseEnter={(e) => {
                                (e.target as HTMLButtonElement).style.backgroundColor = '#ef4444';
                            }}
                            onMouseLeave={(e) => {
                                (e.target as HTMLButtonElement).style.backgroundColor = '#dc2626';
                            }}
                        >
                            🗑️ Remove
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default FileDisplay;
