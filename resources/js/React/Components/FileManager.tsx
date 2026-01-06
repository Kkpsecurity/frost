import React, { useRef } from 'react';
import useFileHandler, { FileData } from '../Hooks/useFileHandler';
import FileDisplay from './FileDisplay';

interface FileManagerProps {
    student?: { id: number; name: string };
    debug?: boolean;
}

const FileManager: React.FC<FileManagerProps> = ({
    student = { id: 1, name: 'Test User' },
    debug = false
}) => {
    const {
        files,
        uploading,
        error,
        uploadFile,
        removeFile,
        clearFiles,
        showIdCard,
        showHeadshot,
        showAllHeadshots,
        showAllIdCards,
        downloadFile,
        generateFileName
    } = useFileHandler();

    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleFileSelect = async (event: React.ChangeEvent<HTMLInputElement>, type: FileData['type']) => {
        const file = event.target.files?.[0];
        if (file) {
            const uploadedFile = await uploadFile(file, type);
            if (uploadedFile && debug) {
                console.log('File uploaded:', uploadedFile);
            }
        }
        // Reset input
        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    };

    const triggerFileSelect = (type: FileData['type']) => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = (e) => handleFileSelect(e as any, type);
        input.click();
    };

    return (
        <div style={{ padding: '20px', maxWidth: '1200px', margin: '0 auto' }}>
            <h2>File Manager</h2>

            {/* Error Display */}
            {error && (
                <div style={{
                    backgroundColor: '#fee',
                    color: '#c00',
                    padding: '10px',
                    borderRadius: '4px',
                    marginBottom: '20px',
                    border: '1px solid #fcc'
                }}>
                    ❌ {error}
                </div>
            )}

            {/* Upload Section */}
            <div style={{
                backgroundColor: '#f8f9fa',
                padding: '20px',
                borderRadius: '8px',
                marginBottom: '30px',
                border: '1px solid #e9ecef'
            }}>
                <h3>Upload Files</h3>
                <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                    <button
                        onClick={() => triggerFileSelect('headshot')}
                        disabled={uploading}
                        style={{
                            padding: '10px 20px',
                            backgroundColor: '#28a745',
                            color: 'white',
                            border: 'none',
                            borderRadius: '5px',
                            cursor: uploading ? 'not-allowed' : 'pointer',
                            opacity: uploading ? 0.6 : 1
                        }}
                    >
                        📸 Upload Headshot
                    </button>

                    <button
                        onClick={() => triggerFileSelect('id_card')}
                        disabled={uploading}
                        style={{
                            padding: '10px 20px',
                            backgroundColor: '#007bff',
                            color: 'white',
                            border: 'none',
                            borderRadius: '5px',
                            cursor: uploading ? 'not-allowed' : 'pointer',
                            opacity: uploading ? 0.6 : 1
                        }}
                    >
                        🆔 Upload ID Card
                    </button>

                    <button
                        onClick={clearFiles}
                        disabled={uploading || files.length === 0}
                        style={{
                            padding: '10px 20px',
                            backgroundColor: '#dc3545',
                            color: 'white',
                            border: 'none',
                            borderRadius: '5px',
                            cursor: (uploading || files.length === 0) ? 'not-allowed' : 'pointer',
                            opacity: (uploading || files.length === 0) ? 0.6 : 1
                        }}
                    >
                        🗑️ Clear All ({files.length})
                    </button>
                </div>

                {uploading && (
                    <div style={{ marginTop: '10px', color: '#007bff' }}>
                        ⏳ Uploading file...
                    </div>
                )}
            </div>

            {/* Current Files Section */}
            <div style={{
                display: 'grid',
                gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
                gap: '20px',
                marginBottom: '30px'
            }}>
                <FileDisplay
                    file={showIdCard()}
                    title="Current ID Card"
                    width={240}
                    height={150}
                    showDetails={true}
                    onRemove={removeFile}
                    onDownload={downloadFile}
                />

                <FileDisplay
                    file={showHeadshot()}
                    title="Current Headshot"
                    width={240}
                    height={150}
                    showDetails={true}
                    onRemove={removeFile}
                    onDownload={downloadFile}
                />
            </div>

            {/* All Files Sections */}
            {showAllIdCards().length > 0 && (
                <div style={{ marginBottom: '30px' }}>
                    <h3>All ID Cards ({showAllIdCards().length})</h3>
                    <div style={{
                        display: 'grid',
                        gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
                        gap: '15px'
                    }}>
                        {showAllIdCards().map(file => (
                            <FileDisplay
                                key={file.id}
                                file={file}
                                width={180}
                                height={120}
                                showDetails={true}
                                onRemove={removeFile}
                                onDownload={downloadFile}
                            />
                        ))}
                    </div>
                </div>
            )}

            {showAllHeadshots().length > 0 && (
                <div style={{ marginBottom: '30px' }}>
                    <h3>All Headshots ({showAllHeadshots().length})</h3>
                    <div style={{
                        display: 'grid',
                        gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
                        gap: '15px'
                    }}>
                        {showAllHeadshots().map(file => (
                            <FileDisplay
                                key={file.id}
                                file={file}
                                width={180}
                                height={120}
                                showDetails={true}
                                onRemove={removeFile}
                                onDownload={downloadFile}
                            />
                        ))}
                    </div>
                </div>
            )}

            {/* Debug Info */}
            {debug && (
                <div style={{
                    backgroundColor: '#f1f1f1',
                    padding: '15px',
                    borderRadius: '5px',
                    marginTop: '20px'
                }}>
                    <h4>Debug Info</h4>
                    <pre style={{ fontSize: '12px', overflow: 'auto' }}>
                        {JSON.stringify({
                            totalFiles: files.length,
                            idCards: showAllIdCards().length,
                            headshots: showAllHeadshots().length,
                            student,
                            exampleFileName: generateFileName(student, 'id_card')
                        }, null, 2)}
                    </pre>
                </div>
            )}
        </div>
    );
};

export default FileManager;
