<script>
/**
 * Advanced Upload Modal - Consolidated working version
 */

// Wait for document to be fully loaded
$(document).ready(function() {
    console.log('Upload modal initializing...');

    // Simple file browser functionality
    $(document).on('click', '#browseFilesBtn', function(e) {
        console.log('Browse files button clicked');
        e.preventDefault();
        e.stopPropagation();

        const fileInput = document.getElementById('fileUploadInput');
        console.log('File input element:', fileInput);

        if (fileInput) {
            console.log('Triggering file input click...');
            fileInput.click();
        } else {
            console.error('File input element not found!');
        }
    });

    // Alternative approach - also bind to modal content area
    $(document).on('click', '#fileUploadInput', function() {
        console.log('File input clicked directly');
    });

    // Handle file selection
    $(document).on('change', '#fileUploadInput', function(e) {
        const files = e.target.files;
        console.log('Files selected:', files.length);

        if (files.length > 0) {
            // Simple file processing - just show file names
            handleFileSelection(files);
        }
    });

    function handleFileSelection(files) {
        console.log('Processing', files.length, 'files');

        // Hide file selection area
        $('#fileSelectionArea').hide();

        // Show simple file list
        const fileList = $('#fileEditingArea');
        fileList.show();

        // Clear previous content
        $('#selectedFilesList').empty();

        // Add files to list
        Array.from(files).forEach((file, index) => {
            const fileItem = $(`
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${file.name}</h6>
                            <small class="text-muted">${formatFileSize(file.size)}</small>
                        </div>
                        <span class="badge badge-primary">Ready</span>
                    </div>
                </div>
            `);
            $('#selectedFilesList').append(fileItem);
        });

        // Show upload button
        $('#startUpload').show().off('click').on('click', function() {
            startSimpleUpload(files);
        });
    }

    function startSimpleUpload(files) {
        console.log('Starting upload of', files.length, 'files');

        const formData = new FormData();
        Array.from(files).forEach((file, index) => {
            formData.append('files', file);
        });

        // Get current disk and folder from existing functions
        const currentDisk = window.getCurrentDisk ? window.getCurrentDisk() : 'public';
        const currentFolder = 'images'; // Default folder

        formData.append('disk', currentDisk);
        formData.append('folder', currentFolder);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show progress
        $('#fileEditingArea').hide();
        $('#uploadProgressArea').show();

        $.ajax({
            url: '/admin/media-manager/upload',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                console.log('Upload successful:', response);
                $('#uploadModal').modal('hide');

                // Show success message
                if (typeof showNotification === 'function') {
                    showNotification('success', 'Files uploaded successfully');
                } else {
                    alert('Files uploaded successfully');
                }

                // Refresh files if function exists
                if (typeof loadFiles === 'function') {
                    loadFiles(currentDisk);
                }
            },
            error: function(xhr) {
                console.error('Upload failed:', xhr);

                let errorMessage = 'Upload failed';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                if (typeof showNotification === 'function') {
                    showNotification('error', errorMessage);
                } else {
                    alert(errorMessage);
                }

                $('#uploadModal').modal('hide');
            }
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Reset modal when it's hidden
    $('#uploadModal').on('hidden.bs.modal', function() {
        $('#fileSelectionArea').show();
        $('#fileEditingArea').hide();
        $('#uploadProgressArea').hide();
        $('#fileUploadInput').val('');
        $('#selectedFilesList').empty();
        $('#startUpload').hide();
    });

    // When modal is shown, ensure file input is accessible
    $('#uploadModal').on('shown.bs.modal', function() {
        console.log('Modal shown - checking file input availability');
        const fileInput = document.getElementById('fileUploadInput');
        console.log('File input on modal show:', fileInput);
    });

    console.log('Upload modal initialization complete');
});
</script>    bindEvents() {
        const self = this;

        console.log('AdvancedUploadModal: Binding events...');

        // Upload button click - show modal
        $(document).on('click', '#uploadFileBtn', function(e) {
            console.log('Upload button clicked - opening advanced modal');
            e.preventDefault();
            e.stopPropagation();
            self.openModal();
        });

        // Browse files button - use event delegation
        $(document).on('click', '#browseFilesBtn', function() {
            console.log('Browse files button clicked');
            $('#fileUploadInput').click();
        });

        // Make the entire dropzone clickable - use event delegation
        $(document).on('click', '#uploadDropzone', function(e) {
            if (e.target === this || $(e.target).hasClass('dropzone-content') || $(e.target).closest('.dropzone-content').length) {
                console.log('Dropzone clicked - opening file dialog');
                $('#fileUploadInput').click();
            }
        });

        // File input change - use event delegation
        $(document).on('change', '#fileUploadInput', function(e) {
            console.log('File input changed:', e.target.files.length, 'files selected');
            self.handleFileSelection(e.target.files);
        });

        // Drag and drop
        $('#uploadDropzone').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });

        $('#uploadDropzone').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });

        $('#uploadDropzone').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            self.handleFileSelection(e.originalEvent.dataTransfer.files);
        });

        // File list item click
        $(document).on('click', '.selected-file-item', function() {
            const index = $(this).data('index');
            self.selectFile(index);
        });

        // Image editing controls
        $('#imageWidth, #imageHeight').on('input', function() {
            self.handleDimensionChange();
        });

        $('#maintainAspectRatio').on('change', function() {
            self.handleAspectRatioToggle();
        });

        $('.crop-preset').on('click', function() {
            const ratio = $(this).data('ratio');
            self.applyCropPreset(ratio);
        });

        $('#imageQuality').on('input', function() {
            self.updateImageQuality();
        });

        $('#rotateLeft').on('click', () => self.rotateImage(-90));
        $('#rotateRight').on('click', () => self.rotateImage(90));
        $('#flipHorizontal').on('click', () => self.flipImage('horizontal'));
        $('#flipVertical').on('click', () => self.flipImage('vertical'));

        // Modal actions
        $('#addMoreFiles').on('click', function() {
            $('#fileUploadInput').click();
        });

        $('#startUpload').on('click', function() {
            self.startUpload();
        });

        // File name editing
        $('#fileName').on('input', function() {
            self.updateFileName();
        });
    }

    openModal() {
        console.log('AdvancedUploadModal: Opening modal...');
        console.log('Modal element:', this.modal);
        console.log('Modal length:', this.modal.length);

        this.resetModal();

        if (this.modal.length > 0) {
            console.log('AdvancedUploadModal: Calling modal show...');
            this.modal.modal('show');
        } else {
            console.error('AdvancedUploadModal: Modal element not found!');
        }
    }

    resetModal() {
        this.selectedFiles = [];
        this.currentFileIndex = 0;
        this.fileSelectionArea.show();
        this.fileEditingArea.hide();
        this.uploadProgressArea.hide();
        $('#addMoreFiles, #startUpload').hide();
        $('#fileUploadInput').val('');
    }

    handleFileSelection(files) {
        if (files.length === 0) return;

        // Add new files to the selection
        Array.from(files).forEach(file => {
            const fileData = {
                file: file,
                originalName: file.name,
                editedName: file.name,
                type: file.type,
                size: file.size,
                edited: false,
                canvas: null
            };
            this.selectedFiles.push(fileData);
        });

        this.showFileEditingArea();
        this.populateFileList();
        this.selectFile(0);
    }

    showFileEditingArea() {
        this.fileSelectionArea.hide();
        this.fileEditingArea.show();
        $('#addMoreFiles, #startUpload').show();
    }

    populateFileList() {
        this.selectedFilesList.empty();

        this.selectedFiles.forEach((fileData, index) => {
            const fileItem = this.createFileListItem(fileData, index);
            this.selectedFilesList.append(fileItem);
        });
    }

    createFileListItem(fileData, index) {
        const isImage = fileData.type.startsWith('image/');
        const icon = this.getFileIcon(fileData.type);
        const sizeFormatted = this.formatFileSize(fileData.size);

        return $(`
            <div class="selected-file-item list-group-item" data-index="${index}">
                <div class="d-flex align-items-center">
                    <div class="file-thumbnail mr-3">
                        ${isImage ? '<img class="file-thumbnail" alt="thumbnail">' : `<i class="${icon} fa-2x text-muted"></i>`}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${fileData.editedName}</h6>
                        <small class="text-muted">${sizeFormatted}</small>
                        ${fileData.edited ? '<span class="badge badge-primary ml-2">Edited</span>' : ''}
                    </div>
                    <div class="file-actions">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
    }

    selectFile(index) {
        if (index >= this.selectedFiles.length) return;

        this.currentFileIndex = index;
        const fileData = this.selectedFiles[index];

        // Update UI
        $('.selected-file-item').removeClass('active');
        $(`.selected-file-item[data-index="${index}"]`).addClass('active');

        // Show appropriate editor
        this.showFileEditor(fileData);
    }

    showFileEditor(fileData) {
        $('#currentFileTitle').html(`<i class="fas fa-edit mr-2"></i>Edit: ${fileData.editedName}`);
        $('#fileName').val(fileData.editedName);

        if (fileData.type.startsWith('image/')) {
            this.showImageEditor(fileData);
        } else {
            this.showDocumentPreview(fileData);
        }
    }

    showImageEditor(fileData) {
        this.imageEditor.show();
        this.documentPreview.hide();

        if (!fileData.canvas) {
            this.loadImageToCanvas(fileData);
        } else {
            this.displayCanvasImage(fileData.canvas);
        }
    }

    loadImageToCanvas(fileData) {
        const img = new Image();
        const reader = new FileReader();

        reader.onload = (e) => {
            img.onload = () => {
                this.originalImageData[this.currentFileIndex] = {
                    width: img.width,
                    height: img.height,
                    data: e.target.result
                };

                this.resizeCanvas(img.width, img.height);
                this.ctx.drawImage(img, 0, 0);

                // Update dimension inputs
                $('#imageWidth').val(img.width);
                $('#imageHeight').val(img.height);

                fileData.canvas = this.canvas.toDataURL();
                this.currentImage = img;
            };
            img.src = e.target.result;
        };

        reader.readAsDataURL(fileData.file);
    }

    showDocumentPreview(fileData) {
        this.imageEditor.hide();
        this.documentPreview.show();

        $('#documentName').text(fileData.editedName);
        $('#documentSize').text(this.formatFileSize(fileData.size));
    }

    resizeCanvas(width, height) {
        this.canvas.width = width;
        this.canvas.height = height;

        // Limit display size while maintaining quality
        const maxDisplayWidth = 600;
        if (width > maxDisplayWidth) {
            const ratio = maxDisplayWidth / width;
            $(this.canvas).css({
                width: maxDisplayWidth + 'px',
                height: (height * ratio) + 'px'
            });
        } else {
            $(this.canvas).css({
                width: width + 'px',
                height: height + 'px'
            });
        }
    }

    handleDimensionChange() {
        const width = parseInt($('#imageWidth').val());
        const height = parseInt($('#imageHeight').val());
        const maintainRatio = $('#maintainAspectRatio').is(':checked');

        if (!width || !height || !this.currentImage) return;

        if (maintainRatio) {
            const aspectRatio = this.originalImageData[this.currentFileIndex].width /
                               this.originalImageData[this.currentFileIndex].height;

            if (width !== this.canvas.width) {
                $('#imageHeight').val(Math.round(width / aspectRatio));
            } else if (height !== this.canvas.height) {
                $('#imageWidth').val(Math.round(height * aspectRatio));
            }
        }

        this.resizeImage(parseInt($('#imageWidth').val()), parseInt($('#imageHeight').val()));
    }

    resizeImage(newWidth, newHeight) {
        if (!this.currentImage) return;

        this.resizeCanvas(newWidth, newHeight);
        this.ctx.drawImage(this.currentImage, 0, 0, newWidth, newHeight);

        this.selectedFiles[this.currentFileIndex].canvas = this.canvas.toDataURL();
        this.selectedFiles[this.currentFileIndex].edited = true;
        this.updateFileListItem();
    }

    rotateImage(degrees) {
        if (!this.currentImage) return;

        const radians = degrees * Math.PI / 180;
        const cos = Math.cos(radians);
        const sin = Math.sin(radians);

        const newWidth = Math.abs(this.canvas.width * cos) + Math.abs(this.canvas.height * sin);
        const newHeight = Math.abs(this.canvas.width * sin) + Math.abs(this.canvas.height * cos);

        this.resizeCanvas(newWidth, newHeight);

        this.ctx.translate(newWidth / 2, newHeight / 2);
        this.ctx.rotate(radians);
        this.ctx.drawImage(this.currentImage, -this.currentImage.width / 2, -this.currentImage.height / 2);
        this.ctx.setTransform(1, 0, 0, 1, 0, 0);

        this.selectedFiles[this.currentFileIndex].canvas = this.canvas.toDataURL();
        this.selectedFiles[this.currentFileIndex].edited = true;
        this.updateFileListItem();
    }

    flipImage(direction) {
        if (!this.currentImage) return;

        this.ctx.save();

        if (direction === 'horizontal') {
            this.ctx.scale(-1, 1);
            this.ctx.drawImage(this.currentImage, -this.canvas.width, 0);
        } else {
            this.ctx.scale(1, -1);
            this.ctx.drawImage(this.currentImage, 0, -this.canvas.height);
        }

        this.ctx.restore();

        this.selectedFiles[this.currentFileIndex].canvas = this.canvas.toDataURL();
        this.selectedFiles[this.currentFileIndex].edited = true;
        this.updateFileListItem();
    }

    updateFileName() {
        const newName = $('#fileName').val();
        this.selectedFiles[this.currentFileIndex].editedName = newName;
        this.updateFileListItem();
    }

    updateFileListItem() {
        const fileData = this.selectedFiles[this.currentFileIndex];
        const $item = $(`.selected-file-item[data-index="${this.currentFileIndex}"]`);

        $item.find('h6').text(fileData.editedName);

        if (fileData.edited && !$item.find('.badge-primary').length) {
            $item.find('.flex-grow-1').append('<span class="badge badge-primary ml-2">Edited</span>');
        }
    }

    async startUpload() {
        this.fileEditingArea.hide();
        this.uploadProgressArea.show();
        $('#startUpload').prop('disabled', true);

        const uploadPromises = this.selectedFiles.map((fileData, index) => {
            return this.uploadSingleFile(fileData, index);
        });

        try {
            await Promise.all(uploadPromises);
            this.showUploadSuccess();
        } catch (error) {
            this.showUploadError(error);
        }
    }

    async uploadSingleFile(fileData, index) {
        const progressItem = this.createProgressItem(fileData, index);
        $('#uploadProgressList').append(progressItem);

        let fileToUpload = fileData.file;

        // If image was edited, convert canvas to blob
        if (fileData.edited && fileData.canvas) {
            fileToUpload = await this.canvasToBlob(fileData.canvas, fileData.editedName);
        }

        const formData = new FormData();
        formData.append(`files`, fileToUpload);
        formData.append('disk', getCurrentDisk());
        formData.append('folder', determineUploadFolder([fileToUpload]));
        formData.append('_token', window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        return $.ajax({
            url: '/admin/media-manager/upload',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const progress = (e.loaded / e.total) * 100;
                        $(`.upload-progress-item[data-index="${index}"] .progress-bar`)
                            .css('width', progress + '%')
                            .attr('aria-valuenow', progress);
                    }
                });
                return xhr;
            }
        });
    }

    canvasToBlob(canvasDataUrl, filename) {
        return new Promise((resolve) => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);

                canvas.toBlob((blob) => {
                    const file = new File([blob], filename, { type: blob.type });
                    resolve(file);
                }, 'image/jpeg', 0.9);
            };

            img.src = canvasDataUrl;
        });
    }

    createProgressItem(fileData, index) {
        return $(`
            <div class="upload-progress-item" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-medium">${fileData.editedName}</span>
                    <span class="text-muted">${this.formatFileSize(fileData.size)}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        `);
    }

    showUploadSuccess() {
        showNotification('success', `${this.selectedFiles.length} file(s) uploaded successfully`);
        this.modal.modal('hide');
        loadFiles(getCurrentDisk()); // Refresh file list
    }

    showUploadError(error) {
        showNotification('error', 'Upload failed: ' + error.message);
        $('#startUpload').prop('disabled', false);
    }

    getFileIcon(mimeType) {
        if (mimeType.startsWith('image/')) return 'fas fa-image';
        if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
        if (mimeType.includes('word')) return 'fas fa-file-word';
        if (mimeType.includes('text')) return 'fas fa-file-alt';
        return 'fas fa-file';
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Initialize the advanced upload modal when document is ready
$(document).ready(function() {
    console.log('Document ready - initializing AdvancedUploadModal...');

    // Small delay to ensure all DOM elements are fully rendered
    setTimeout(function() {
        try {
            console.log('Checking for upload modal element...');
            const modalElement = $('#uploadModal');
            console.log('Upload modal found:', modalElement.length > 0);

            if (modalElement.length > 0) {
                window.advancedUploadModal = new AdvancedUploadModal();
                console.log('AdvancedUploadModal initialized successfully');
            } else {
                console.error('Upload modal element not found in DOM');
            }
        } catch (error) {
            console.error('Error initializing AdvancedUploadModal:', error);
        }
    }, 100);
});

// Helper functions to integrate with existing media manager
function getCurrentDisk() {
    return window.currentDisk || 'public';
}

function determineUploadFolder(files) {
    return window.currentFolder || '';
}

function showNotification(type, message) {
    // Create a simple notification if no notification system exists
    if (typeof window.showToast === 'function') {
        window.showToast(type, message);
    } else {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('body').append(alertHtml);

        // Auto dismiss after 3 seconds
        setTimeout(() => {
            $('.alert').alert('close');
        }, 3000);
    }
}

function loadFiles(disk) {
    // Refresh the media manager files if the function exists
    if (typeof window.loadMediaFiles === 'function') {
        window.loadMediaFiles(disk);
    } else {
        // Fallback: reload the page
        window.location.reload();
    }
}
</script>
