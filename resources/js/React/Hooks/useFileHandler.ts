import { useState, useCallback } from 'react';

export interface FileData {
    id: string;
    name: string;
    type: 'id_card' | 'headshot' | 'document';
    url: string;
    blob?: Blob;
    file?: File;
    uploadedAt: Date;
    size: number;
    mimeType: string;
}

export interface UseFileHandlerReturn {
    files: FileData[];
    uploading: boolean;
    error: string | null;

    // File operations
    uploadFile: (file: File, type: FileData['type']) => Promise<FileData | null>;
    removeFile: (fileId: string) => void;
    clearFiles: () => void;
    getFilesByType: (type: FileData['type']) => FileData[];

    // Display helpers
    showIdCard: () => FileData | null;
    showHeadshot: () => FileData | null;
    showAllHeadshots: () => FileData[];
    showAllIdCards: () => FileData[];

    // File validation
    validateFile: (file: File) => { isValid: boolean; error?: string };

    // Utilities
    downloadFile: (fileData: FileData) => void;
    generateFileName: (student: any, type: FileData['type']) => string;
}

export const useFileHandler = (): UseFileHandlerReturn => {
    const [files, setFiles] = useState<FileData[]>([]);
    const [uploading, setUploading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    // Validate file before upload
    const validateFile = useCallback((file: File): { isValid: boolean; error?: string } => {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        if (file.size > maxSize) {
            return { isValid: false, error: 'File size must be less than 10MB' };
        }

        if (!allowedTypes.includes(file.type)) {
            return { isValid: false, error: 'File must be JPEG, PNG, or WebP format' };
        }

        return { isValid: true };
    }, []);

    // Generate standardized filename
    const generateFileName = useCallback((student: any, type: FileData['type']): string => {
        const studentName = student?.name || 'unknown';
        const nameParts = studentName.toLowerCase().split(' ');
        const firstName = nameParts[0] || 'unknown';
        const lastName = nameParts.slice(1).join('_') || 'user';
        const courseAuthId = student?.id || '0';

        const typePrefix = type === 'headshot' ? 'headshot' : 'idcard';
        const timestamp = new Date().getTime();

        return `${courseAuthId}_${firstName}_${lastName}_${typePrefix}_${timestamp}.jpg`;
    }, []);

    // Upload/Add file
    const uploadFile = useCallback(async (file: File, type: FileData['type']): Promise<FileData | null> => {
        setUploading(true);
        setError(null);

        try {
            // Validate file
            const validation = validateFile(file);
            if (!validation.isValid) {
                throw new Error(validation.error);
            }

            // Create file URL for preview
            const url = URL.createObjectURL(file);

            // Create file data object
            const fileData: FileData = {
                id: `${type}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
                name: file.name,
                type,
                url,
                file,
                uploadedAt: new Date(),
                size: file.size,
                mimeType: file.type,
            };

            // Add to files array
            setFiles(prev => [...prev, fileData]);

            console.log('✅ File processed successfully:', fileData);
            return fileData;

        } catch (err) {
            const errorMessage = err instanceof Error ? err.message : 'Failed to process file';
            setError(errorMessage);
            console.error('❌ File processing error:', errorMessage);
            return null;
        } finally {
            setUploading(false);
        }
    }, [validateFile]);

    // Remove file
    const removeFile = useCallback((fileId: string) => {
        setFiles(prev => {
            const fileToRemove = prev.find(f => f.id === fileId);
            if (fileToRemove?.url) {
                URL.revokeObjectURL(fileToRemove.url);
            }
            return prev.filter(f => f.id !== fileId);
        });
        console.log('🗑️ File removed:', fileId);
    }, []);

    // Clear all files
    const clearFiles = useCallback(() => {
        files.forEach(file => {
            if (file.url) {
                URL.revokeObjectURL(file.url);
            }
        });
        setFiles([]);
        setError(null);
        console.log('🧹 All files cleared');
    }, [files]);

    // Get files by type
    const getFilesByType = useCallback((type: FileData['type']): FileData[] => {
        return files.filter(file => file.type === type);
    }, [files]);

    // Display helpers
    const showIdCard = useCallback((): FileData | null => {
        const idCards = getFilesByType('id_card');
        return idCards.length > 0 ? idCards[idCards.length - 1] : null; // Return latest
    }, [getFilesByType]);

    const showHeadshot = useCallback((): FileData | null => {
        const headshots = getFilesByType('headshot');
        return headshots.length > 0 ? headshots[headshots.length - 1] : null; // Return latest
    }, [getFilesByType]);

    const showAllHeadshots = useCallback((): FileData[] => {
        return getFilesByType('headshot').sort((a, b) =>
            b.uploadedAt.getTime() - a.uploadedAt.getTime()
        ); // Sort by newest first
    }, [getFilesByType]);

    const showAllIdCards = useCallback((): FileData[] => {
        return getFilesByType('id_card').sort((a, b) =>
            b.uploadedAt.getTime() - a.uploadedAt.getTime()
        ); // Sort by newest first
    }, [getFilesByType]);

    // Download file
    const downloadFile = useCallback((fileData: FileData) => {
        try {
            const link = document.createElement('a');
            link.href = fileData.url;
            link.download = fileData.name;
            link.style.display = 'none';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log('📥 File downloaded:', fileData.name);
        } catch (err) {
            console.error('❌ Download error:', err);
            setError('Failed to download file');
        }
    }, []);

    return {
        files,
        uploading,
        error,

        // File operations
        uploadFile,
        removeFile,
        clearFiles,
        getFilesByType,

        // Display helpers
        showIdCard,
        showHeadshot,
        showAllHeadshots,
        showAllIdCards,

        // File validation
        validateFile,

        // Utilities
        downloadFile,
        generateFileName,
    };
};

export default useFileHandler;
