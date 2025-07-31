import * as FilePond from 'filepond';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

// Import FilePond styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';

// Register FilePond plugins
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType
);

/**
 * Initialize FilePond for file uploads
 * @param {string} selector - CSS selector for the input element
 * @param {object} options - FilePond configuration options
 * @returns {object} FilePond instance
 */
export function createFilePond(selector, options = {}) {
    const defaultOptions = {
        allowMultiple: false,
        allowRevert: true,
        allowRemove: true,
        allowProcess: true,
        allowBrowse: true,
        allowDrop: true,
        allowPaste: false,
        allowReplace: true,
        dropOnPage: false,
        dropOnElement: true,
        dropValidation: true,
        ignoredFiles: ['.ds_store', 'thumbs.db', 'desktop.ini'],
        instantUpload: false,
        allowImagePreview: true,
        allowImageFilter: true,
        allowImageExifOrientation: true,
        allowImageCrop: false,
        allowImageResize: true,
        imageResizeTargetWidth: 1920,
        imageResizeTargetHeight: 1080,
        imageResizeMode: 'contain',
        imageResizeUpscale: false,
        acceptedFileTypes: ['image/*'],
        maxFileSize: '10MB',
        maxFiles: 1,
        checkValidity: true,
        server: {
            process: '/admin/upload',
            revert: '/admin/upload/revert',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            }
        },
        labelIdle: `
            <div class="filepond-drop-area">
                <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                <div class="h5 mb-2">Drag & Drop your files or <span class="filepond-label-action text-primary">Browse</span></div>
                <div class="text-muted small">Supports: Images up to 10MB</div>
            </div>
        `,
        labelInvalidField: 'Field contains invalid files',
        labelFileWaitingForSize: 'Waiting for size',
        labelFileSizeNotAvailable: 'Size not available',
        labelFileLoading: 'Loading',
        labelFileLoadError: 'Error during load',
        labelFileProcessing: 'Uploading',
        labelFileProcessingComplete: 'Upload complete',
        labelFileProcessingAborted: 'Upload cancelled',
        labelFileProcessingError: 'Error during upload',
        labelFileProcessingRevertError: 'Error during revert',
        labelFileRemoveError: 'Error during remove',
        labelTapToCancel: 'tap to cancel',
        labelTapToRetry: 'tap to retry',
        labelTapToUndo: 'tap to undo',
        labelButtonRemoveItem: 'Remove',
        labelButtonAbortItemLoad: 'Abort',
        labelButtonRetryItemLoad: 'Retry',
        labelButtonAbortItemProcessing: 'Cancel',
        labelButtonUndoItemProcessing: 'Undo',
        labelButtonRetryItemProcessing: 'Retry',
        labelButtonProcessItem: 'Upload',
        // Custom styling classes
        className: 'filepond-modern',
        stylePanelLayout: 'compact',
        stylePanelAspectRatio: '16:9',
        styleButtonRemoveItemPosition: 'center bottom',
        styleButtonProcessItemPosition: 'right bottom',
        styleLoadIndicatorPosition: 'center bottom',
        styleProgressIndicatorPosition: 'right bottom',
        // Event handlers
        onupdatefiles: (fileItems) => {
            console.log('FilePond files updated:', fileItems.map(item => item.file));
        },
        onprocessfile: (error, file) => {
            if (error) {
                console.error('FilePond process error:', error);
                return;
            }
            console.log('FilePond file processed:', file);
        },
        onremovefile: (error, file) => {
            if (error) {
                console.error('FilePond remove error:', error);
                return;
            }
            console.log('FilePond file removed:', file);
        }
    };

    // Merge default options with user options
    const finalOptions = { ...defaultOptions, ...options };

    // Get the input element
    const inputElement = document.querySelector(selector);
    if (!inputElement) {
        console.error(`FilePond: Element not found for selector: ${selector}`);
        return null;
    }

    // Create FilePond instance
    const pond = FilePond.create(inputElement, finalOptions);

    return pond;
}

/**
 * Initialize FilePond for image uploads with specific settings
 * @param {string} selector - CSS selector for the input element
 * @param {object} options - Additional options
 * @returns {object} FilePond instance
 */
export function createImageUpload(selector, options = {}) {
    const imageOptions = {
        acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        allowImagePreview: true,
        allowImageCrop: true,
        allowImageResize: true,
        imageResizeTargetWidth: 1200,
        imageResizeTargetHeight: 800,
        imageResizeMode: 'contain',
        imageCropAspectRatio: '1:1',
        maxFileSize: '5MB',
        labelIdle: `
            <div class="filepond-drop-area">
                <i class="fas fa-image fa-3x text-success mb-3"></i>
                <div class="h5 mb-2">Drag & Drop your image or <span class="filepond-label-action text-success">Browse</span></div>
                <div class="text-muted small">Supports: JPEG, PNG, GIF, WebP up to 5MB</div>
            </div>
        `,
        ...options
    };

    return createFilePond(selector, imageOptions);
}

/**
 * Initialize FilePond for document uploads
 * @param {string} selector - CSS selector for the input element
 * @param {object} options - Additional options
 * @returns {object} FilePond instance
 */
export function createDocumentUpload(selector, options = {}) {
    const documentOptions = {
        acceptedFileTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'],
        allowImagePreview: false,
        maxFileSize: '25MB',
        labelIdle: `
            <div class="filepond-drop-area">
                <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                <div class="h5 mb-2">Drag & Drop your document or <span class="filepond-label-action text-info">Browse</span></div>
                <div class="text-muted small">Supports: PDF, DOC, DOCX, TXT up to 25MB</div>
            </div>
        `,
        ...options
    };

    return createFilePond(selector, documentOptions);
}

// Auto-initialize FilePond on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize elements with data-filepond attribute
    document.querySelectorAll('[data-filepond]').forEach(element => {
        const type = element.getAttribute('data-filepond');
        const selector = `#${element.id}`;

        switch(type) {
            case 'image':
                createImageUpload(selector);
                break;
            case 'document':
                createDocumentUpload(selector);
                break;
            default:
                createFilePond(selector);
                break;
        }
    });
});

// Make FilePond available globally
window.FilePond = {
    create: createFilePond,
    createImageUpload,
    createDocumentUpload
};
