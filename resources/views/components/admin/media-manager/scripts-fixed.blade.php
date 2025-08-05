<script>
    let currentDisk = 'public'; // Start with public disk
    let currentPath = '/';
    let viewMode = 'grid';
    let currentFiles = [];
    let currentDirectories = [];

    $(document).ready(function() {
        // Initialize
        loadFiles();
        setupEventHandlers();

        // Set user role for frontend access control
        window.userRole = {{ auth()->user()->role_id ?? 5 }};
    });

    function setupEventHandlers() {
        // Modern tab switching with animations and role-based access
        $('.media-tabs .nav-link').on('click', function(e) {
            e.preventDefault();

            const diskName = $(this).data('disk');

            // Check role-based access on frontend as well
            if (!canAccessDiskOnFrontend(diskName)) {
                showNotification('Access denied to this storage disk', 'error');
                return;
            }

            // Remove active class from all tabs
            $('.media-tabs .nav-link').removeClass('active');
            $('.tab-pane').removeClass('show active');

            // Add active class to clicked tab
            $(this).addClass('active');

            const targetTab = $(this).attr('href').substring(1);

            // Update current disk
            currentDisk = diskName;
            currentPath = '/';

            // Update disk display
            const diskDisplayName = $(this).text().trim().split('\n')[0];
            $('#currentDiskDisplay').text(diskDisplayName);

            // Show target tab with animation
            $(`#${targetTab}`).addClass('show active');

            // Load files for the new disk
            loadFiles();
        });

        // Upload button with role-based restrictions
        $('#uploadBtn').on('click', function() {
            if (!canUploadToCurrentDisk()) {
                showNotification('Upload not allowed for your role on this storage location', 'warning');
                return;
            }
            $('#uploadModal').modal('show');
        });

        // Refresh button with loading animation
        $('#refreshBtn').on('click', function() {
            const $icon = $(this).find('i');
            $icon.addClass('fa-spin');
            loadFiles().finally(() => {
                setTimeout(() => $icon.removeClass('fa-spin'), 500);
            });
        });

        // View mode buttons
        $('#gridViewBtn').on('click', function() {
            viewMode = 'grid';
            $(this).addClass('active').siblings().removeClass('active');
            renderFiles(currentFiles);
        });

        $('#listViewBtn').on('click', function() {
            viewMode = 'list';
            $(this).addClass('active').siblings().removeClass('active');
            renderFiles(currentFiles);
        });

        // Search with debounce
        let searchTimeout;
        $('#searchInput').on('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (e.key === 'Enter' || $(this).val().length > 2 || $(this).val().length === 0) {
                    performSearch();
                }
            }, 300);
        });

        $('#searchBtn').on('click', function() {
            performSearch();
        });

        // Create folder
        $('#createFolderBtn').on('click', function() {
            if (!canUploadToCurrentDisk()) {
                showNotification('You do not have permission to create folders in this location', 'warning');
                return;
            }
            createFolder();
        });
    }

    function loadFiles() {
        showLoading();

        return $.ajax({
            url: '/admin/media-manager/files',
            method: 'GET',
            data: {
                disk: currentDisk,
                path: currentPath
            },
            success: function(response) {
                if (response.success) {
                    currentFiles = response.files || [];
                    currentDirectories = response.directories || [];
                    renderFiles(currentFiles);
                    updateDirectoryTree(currentDirectories);
                    updateFileStats(currentFiles.length, currentDirectories.length);
                } else {
                    showNotification('Failed to load files: ' + (response.error || 'Unknown error'), 'error');
                    renderEmptyState('error', response.error || 'Failed to load files');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Unknown error';
                if (xhr.status === 403) {
                    errorMessage = 'Access denied to this storage disk';
                } else if (xhr.responseJSON?.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred';
                }

                showNotification('Error loading files: ' + errorMessage, 'error');
                renderEmptyState('error', errorMessage);
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function renderFiles(files = []) {
        // Map disk names back to tab names for grid selection
        let gridSuffix;
        if (currentDisk === 'public') {
            gridSuffix = 'public';
        } else if (currentDisk === 'local') {
            gridSuffix = 'private';
        } else if (currentDisk === 's3') {
            gridSuffix = 's3';
        }

        const gridId = `${gridSuffix}Grid`;
        const uploadAreaId = `${gridSuffix}UploadArea`;
        const $grid = $(`#${gridId}`);
        const $uploadArea = $(`#${uploadAreaId}`);

        $grid.empty();

        if (files.length === 0) {
            renderEmptyState('empty');
            return;
        }

        $uploadArea.hide();
        files.forEach((file, index) => {
            const fileHtml = createFileElement(file, index);
            $grid.append(fileHtml);
        });

        // Add fade-in animation to loaded files
        setTimeout(() => {
            $('.media-item').addClass('animate-fade-in');
        }, 100);
    }

    function renderEmptyState(type = 'empty', message = null) {
        let gridSuffix;
        if (currentDisk === 'public') {
            gridSuffix = 'public';
        } else if (currentDisk === 'local') {
            gridSuffix = 'private';
        } else if (currentDisk === 's3') {
            gridSuffix = 's3';
        }

        const gridId = `${gridSuffix}Grid`;
        const uploadAreaId = `${gridSuffix}UploadArea`;
        const $grid = $(`#${gridId}`);
        const $uploadArea = $(`#${uploadAreaId}`);

        if (type === 'error') {
            $uploadArea.hide();
            $grid.html(`
                <div class="col-12 text-center p-5">
                    <div style="opacity: 0.8;">
                        <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                        <h5 class="text-warning">Error Loading Files</h5>
                        <p class="text-muted">${message || 'Unable to load files from this storage location'}</p>
                        <button class="btn btn-outline-primary" onclick="loadFiles()">
                            <i class="fas fa-retry mr-2"></i>Try Again
                        </button>
                    </div>
                </div>
            `);
        } else {
            // Show upload area for empty state if user can upload
            if (canUploadToCurrentDisk()) {
                $uploadArea.show();
            } else {
                $uploadArea.hide();
            }

            $grid.html(`
                <div class="col-12 text-center p-5">
                    <div style="opacity: 0.6;">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No files found in this directory</h5>
                        <p class="text-muted">
                            ${canUploadToCurrentDisk()
                                ? 'Upload some files to get started'
                                : 'This storage location is empty'}
                        </p>
                        ${canUploadToCurrentDisk()
                            ? '<button class="btn btn-outline-primary" id="uploadFromEmpty"><i class="fas fa-upload mr-2"></i>Upload Files</button>'
                            : ''}
                    </div>
                </div>
            `);

            // Bind upload button in empty state
            $('#uploadFromEmpty').on('click', function() {
                $('#uploadBtn').click();
            });
        }
    }

    function createFileElement(file, index = 0) {
        const isImage = file.is_image || file.type === 'image' || /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(file.name);
        const fileIcon = getFileIcon(file.name, file.mime_type || file.type);
        const animationDelay = (index % 6) * 0.1;
        const fileSize = file.size_formatted || formatFileSize(file.size || 0);
        const modifiedDate = file.modified_formatted || (file.modified ? formatDate(file.modified) : 'Unknown');

        // Use file path as unique identifier if no ID available
        const fileId = file.id || btoa(file.path || file.name);

        return `
            <div class="media-item" data-file-id="${fileId}" data-file-path="${file.path || file.name}" style="animation-delay: ${animationDelay}s;">
                <div class="media-preview">
                    ${isImage && file.url
                        ? `<img src="${file.url}" alt="${file.name}" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                           <div style="display: none; align-items: center; justify-content: center; height: 100%; background: #f8f9fa;">
                               <i class="file-icon ${fileIcon}"></i>
                           </div>`
                        : `<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa;">
                               <i class="file-icon ${fileIcon}"></i>
                           </div>`
                    }
                    <div class="media-actions">
                        ${file.url ? `
                        <button class="btn btn-primary btn-sm" onclick="previewFile('${fileId}', '${file.url}', '${file.name}')" title="Preview">
                            <i class="fas fa-eye"></i>
                        </button>` : ''}
                        <button class="btn btn-success btn-sm" onclick="downloadFile('${fileId}', '${file.path || file.name}')" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        ${canDeleteFiles() ? `
                        <button class="btn btn-danger btn-sm" onclick="deleteFile('${fileId}', '${file.name}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>` : ''}
                    </div>
                </div>
                <div class="media-info">
                    <div class="media-name" title="${file.name}">${truncateFilename(file.name, 20)}</div>
                    <div class="media-size text-muted">${fileSize}</div>
                    <div class="media-date text-muted small">${modifiedDate}</div>
                    ${file.type ? `<div class="media-type-badge badge badge-secondary">${file.type}</div>` : ''}
                </div>
            </div>
        `;
    }

    function getFileIcon(filename, mimeType = null) {
        // Use MIME type first if available
        if (mimeType) {
            if (mimeType.startsWith('image/')) return 'fas fa-file-image text-info';
            if (mimeType.startsWith('video/')) return 'fas fa-file-video text-primary';
            if (mimeType.startsWith('audio/')) return 'fas fa-file-audio text-success';
            if (mimeType === 'application/pdf') return 'fas fa-file-pdf text-danger';
            if (mimeType.includes('word')) return 'fas fa-file-word text-primary';
            if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel text-success';
            if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint text-warning';
            if (mimeType.includes('zip') || mimeType.includes('compressed')) return 'fas fa-file-archive text-warning';
            if (mimeType.startsWith('text/')) return 'fas fa-file-alt text-secondary';
        }

        // Fallback to extension-based detection
        const ext = filename.split('.').pop().toLowerCase();
        const iconMap = {
            pdf: 'fas fa-file-pdf text-danger',
            doc: 'fas fa-file-word text-primary',
            docx: 'fas fa-file-word text-primary',
            xls: 'fas fa-file-excel text-success',
            xlsx: 'fas fa-file-excel text-success',
            ppt: 'fas fa-file-powerpoint text-warning',
            pptx: 'fas fa-file-powerpoint text-warning',
            zip: 'fas fa-file-archive text-warning',
            rar: 'fas fa-file-archive text-warning',
            mp4: 'fas fa-file-video text-info',
            mp3: 'fas fa-file-audio text-info',
            txt: 'fas fa-file-alt text-secondary'
        };

        return iconMap[ext] || 'fas fa-file text-secondary';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function performSearch() {
        const query = $('#searchInput').val();
        if (query.length > 0) {
            showNotification('Search functionality coming soon', 'info');
        } else {
            loadFiles();
        }
    }

    function createFolder() {
        const folderName = prompt('Enter folder name:');
        if (folderName && folderName.trim()) {
            $.ajax({
                url: '/admin/media-manager/create-folder',
                method: 'POST',
                data: {
                    disk: currentDisk,
                    path: currentPath,
                    name: folderName.trim(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Folder created successfully', 'success');
                        loadFiles();
                    } else {
                        showNotification('Failed to create folder: ' + response.error, 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Failed to create folder: ' + (xhr.responseJSON?.error || 'Unknown error'), 'error');
                }
            });
        }
    }

    // Role-based access control functions
    function canAccessDiskOnFrontend(disk) {
        // Admin and System Admin (role_id <= 2): Full access
        if (window.userRole <= 2) {
            return true;
        }

        // All other roles: Only public disk
        return disk === 'public';
    }

    function canUploadToCurrentDisk() {
        // Students (role_id > 4): No upload access
        if (window.userRole > 4) {
            return false;
        }

        // Admin and System Admin (role_id <= 2): Can upload to all disks
        if (window.userRole <= 2) {
            return true;
        }

        // Instructors and Support (role_id 3-4): Can upload to public only
        return currentDisk === 'public';
    }

    function canDeleteFiles() {
        // Only admins can delete files
        return window.userRole <= 2;
    }

    // Utility functions
    function truncateFilename(filename, maxLength) {
        if (filename.length <= maxLength) {
            return filename;
        }

        const extension = filename.split('.').pop();
        const name = filename.substring(0, filename.lastIndexOf('.'));
        const truncatedName = name.substring(0, maxLength - extension.length - 4) + '...';

        return truncatedName + '.' + extension;
    }

    function formatDate(timestamp) {
        const date = new Date(timestamp * 1000); // Convert Unix timestamp to milliseconds
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function updateFileStats(fileCount, dirCount) {
        const statsText = `${fileCount} file${fileCount !== 1 ? 's' : ''}, ${dirCount} folder${dirCount !== 1 ? 's' : ''}`;
        $('#fileStats').removeClass('loading error').addClass('success')
            .html(`<i class="fas fa-info-circle mr-1"></i>${statsText}`);
    }

    function updateDirectoryTree(directories) {
        // TODO: Implement directory tree update
        console.log('Directory tree update:', directories);
    }

    // Loading and notification functions
    function showLoading() {
        $('#loadingIndicator').show();
        $('#fileStats').addClass('loading').html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...');
    }

    function hideLoading() {
        $('#loadingIndicator').hide();
        $('#fileStats').removeClass('loading error');
    }

    function showNotification(message, type = 'info') {
        // Simple notification system - can be enhanced with a proper toast library
        const alertClass = type === 'error' ? 'alert-danger' :
                          type === 'success' ? 'alert-success' :
                          type === 'warning' ? 'alert-warning' : 'alert-info';

        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed"
                 id="${alertId}"
                 style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;

        $('body').append(alertHtml);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $(`#${alertId}`).alert('close');
        }, 5000);
    }

    // Enhanced file action functions
    function previewFile(fileId, fileUrl, fileName) {
        if (!fileUrl) {
            showNotification('Preview not available for this file', 'warning');
            return;
        }

        // Simple preview - open in new tab for now
        window.open(fileUrl, '_blank');
    }

    function downloadFile(fileId, filePath) {
        const downloadUrl = `/admin/media-manager/download?disk=${currentDisk}&file=${encodeURIComponent(filePath)}`;
        window.open(downloadUrl, '_blank');
    }

    function deleteFile(fileId, fileName) {
        if (!canDeleteFiles()) {
            showNotification('You do not have permission to delete files', 'error');
            return;
        }

        if (confirm(`Are you sure you want to delete "${fileName}"?`)) {
            $.ajax({
                url: `/admin/media-manager/delete/${fileId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    disk: currentDisk,
                    path: filePath
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('File deleted successfully', 'success');
                        loadFiles();
                    } else {
                        showNotification('Delete failed: ' + response.error, 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Delete failed: ' + (xhr.responseJSON?.error || 'Unknown error'), 'error');
                }
            });
        }
    }
</script>
