<script>
    let currentDisk = 'public'; // Start with public disk
    let currentPath = '/';
    let viewMode = 'grid';
    let currentFiles = [];
    let currentDirectories = [];

    // Map disk names to their corresponding grid IDs in the templates
    const diskToGridMap = {
        'public': 'public',
        'local': 'private',  // local disk uses 'private' as the grid ID
        's3': 's3'
    };

    $(document).ready(function() {
        // Initialize the media manager
        initializeMediaManager();

        // Set user role information for frontend access control
        window.userRole = {
            id: {{ auth('admin')->check() ? auth('admin')->user()->role_id : 6 }},
            isSysAdmin: {{ auth('admin')->check() && auth('admin')->user()->IsSysAdmin() ? 'true' : 'false' }},
            isAdministrator: {{ auth('admin')->check() && auth('admin')->user()->IsAdministrator() ? 'true' : 'false' }},
            isSupport: {{ auth('admin')->check() && auth('admin')->user()->IsSupport() ? 'true' : 'false' }},
            isInstructor: {{ auth('admin')->check() && auth('admin')->user()->IsInstructor() ? 'true' : 'false' }},
            isAnyAdmin: {{ auth('admin')->check() && auth('admin')->user()->IsAnyAdmin() ? 'true' : 'false' }}
        };

        console.log('Media Manager initialized with user role:', window.userRole);
    });


    function bindEventHandlers() {
        console.log('Binding event handlers...');

        // Handle tab switching
        $('.nav-tabs a[data-disk]').on('click', function (e) {
            e.preventDefault();
            const newDisk = $(this).data('disk');
            console.log(`Tab clicked - switching from ${currentDisk} to ${newDisk}`);

            if (newDisk && newDisk !== currentDisk) {
                currentDisk = newDisk;
                currentPath = '/'; // Reset path when switching disks

                // Update active tab
                $('.nav-tabs a').removeClass('active');
                $(this).addClass('active');

                // Update tab content - hide all, show active
                $('.tab-pane').removeClass('show active').hide();
                const targetTab = $(`#${diskToGridMap[currentDisk] || currentDisk}`);
                targetTab.addClass('show active').show();

                // Update current disk display
                $('#currentDiskDisplay').text(newDisk.charAt(0).toUpperCase() + newDisk.slice(1));

                console.log(`Switching to disk: ${currentDisk}`);
                loadFiles();
            }
        });

        // Handle upload button
        $('#uploadBtn').on('change', function() {
            handleFileUpload(this.files);
        });

        // Handle file actions
        $(document).on('click', '.download-file', function() {
            const filePath = $(this).data('file');
            downloadFile(filePath);
        });

        $(document).on('click', '.delete-file', function() {
            const filePath = $(this).data('file');
            deleteFile(filePath);
        });
    }

    function loadFiles() {
        showLoading();

        console.log(`Loading files for disk: ${currentDisk}, path: ${currentPath}`);

        return $.ajax({
            url: '/admin/media-manager/files',
            method: 'GET',
            data: {
                disk: currentDisk,
                path: currentPath
            },
            success: function(response) {
                console.log('Files loaded successfully:', response);
                hideLoading();

                if (response.success) {
                    currentFiles = response.files || [];
                    currentDirectories = response.directories || [];

                    console.log(`Found ${currentFiles.length} files and ${currentDirectories.length} directories`);

                    renderFiles(currentFiles);
                    updateDirectoryTree(currentDirectories);
                    updateFileStats(currentFiles.length, currentDirectories.length);

                    // Update disk status if provided
                    if (response.disk_status) {
                        updateDiskStatus(currentDisk, response.disk_status);
                    }
                } else {
                    console.error('File loading failed:', response);
                    // Check if we should show connection screen
                    if (response.show_connection_screen && response.disk_status) {
                        showConnectionScreen(currentDisk, response.disk_status);
                    } else {
                        showNotification('Failed to load files: ' + (response.error || 'Unknown error'), 'error');
                        renderEmptyState('error', response.error || 'Failed to load files');
                    }
                }
            },
            error: function(xhr) {
                console.error('AJAX error loading files:', xhr);

                let errorMessage = 'Unknown error';
                let showConnectionScreen = false;

                if (xhr.status === 503 && xhr.responseJSON?.show_connection_screen) {
                    // Storage connection issue
                    showConnectionScreen = true;
                } else if (xhr.status === 403) {
                    errorMessage = 'Access denied to this storage disk';
                } else if (xhr.responseJSON?.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred';
                }

                if (showConnectionScreen && xhr.responseJSON?.disk_status) {
                    showConnectionScreen(currentDisk, xhr.responseJSON.disk_status);
                } else {
                    showNotification('Error loading files: ' + errorMessage, 'error');
                    renderEmptyState('error', errorMessage);
                }
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    function renderFiles(files = []) {
        // Get the correct disk ID for templates (maps 'local' -> 'private')
        const templateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const gridId = `${templateDiskId}Grid`;
        const uploadAreaId = `${templateDiskId}UploadArea`;
        const emptyStateId = `${templateDiskId}EmptyState`;
        const loadingId = `${templateDiskId}Loading`;
        const $grid = $(`#${gridId}`);
        const $uploadArea = $(`#${uploadAreaId}`);
        const $emptyState = $(`#${emptyStateId}`);
        const $loading = $(`#${loadingId}`);

        console.log(`Rendering files for disk: ${currentDisk}, templateDiskId: ${templateDiskId}, using grid: #${gridId}`, files);

        // FORCE HIDE all other elements first
        $uploadArea.hide().css('display', 'none');
        $emptyState.hide().css('display', 'none');
        $loading.hide().css('display', 'none');

        $grid.empty();

        if (files.length === 0) {
            // Hide grid when empty and delegate to renderEmptyState
            $grid.hide().css('display', 'none');
            renderEmptyState('empty');
            return;
        }

        // Show grid for files and hide others
        files.forEach((file, index) => {
            const fileHtml = createFileElement(file, index);
            $grid.append(fileHtml);
        });

        $grid.show().css('display', 'block');

        // Add fade-in animation to loaded files
        setTimeout(() => {
            $('.media-item').addClass('animate-fade-in');
        }, 100);
    }

    function renderEmptyState(type = 'empty', message = null) {
        // Get the correct disk ID for templates (maps 'local' -> 'private')
        const templateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const gridId = `${templateDiskId}Grid`;
        const uploadAreaId = `${templateDiskId}UploadArea`;
        const emptyStateId = `${templateDiskId}EmptyState`;
        const loadingId = `${templateDiskId}Loading`;
        const $grid = $(`#${gridId}`);
        const $uploadArea = $(`#${uploadAreaId}`);
        const $emptyState = $(`#${emptyStateId}`);
        const $loading = $(`#${loadingId}`);

        console.log(`Rendering empty state for disk: ${currentDisk}, templateDiskId: ${templateDiskId}, type: ${type}, using grid: #${gridId}`);
        console.log(`Elements found - Grid: ${$grid.length}, UploadArea: ${$uploadArea.length}, EmptyState: ${$emptyState.length}, Loading: ${$loading.length}`);
        console.log(`Looking for IDs: #${gridId}, #${uploadAreaId}, #${emptyStateId}, #${loadingId}`);

        // FORCE HIDE ALL ELEMENTS FIRST using both jQuery and CSS
        $grid.hide().css('display', 'none');
        $uploadArea.hide().css('display', 'none');
        $emptyState.hide().css('display', 'none');
        $loading.hide().css('display', 'none');

        console.log('All elements forcibly hidden with display:none');

        if (type === 'error') {
            $grid.html(`
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                            <h4 class="text-danger">Error Loading Files</h4>
                            <p class="text-muted">${message || 'Unable to load files from this storage location'}</p>
                            <button class="btn btn-danger" onclick="loadFiles()">
                                <i class="fas fa-redo mr-2"></i>Try Again
                            </button>
                        </div>
                    </div>
                </div>
            `);
            $grid.show().css('display', 'block');
            console.log('Error state: showing grid with error message');
        } else {
            // Show upload area for empty state if user can upload
            if (canUploadToCurrentDisk()) {
                console.log('User can upload - showing upload area');
                $uploadArea.show().css('display', 'block');
            } else {
                console.log('User cannot upload - showing empty state');
                $emptyState.show().css('display', 'block');
            }
        }

        // Final verification - log what's actually visible
        setTimeout(() => {
            console.log('=== FINAL VISIBILITY CHECK ===');
            console.log(`Grid visible: ${$grid.is(':visible')}, display: ${$grid.css('display')}`);
            console.log(`UploadArea visible: ${$uploadArea.is(':visible')}, display: ${$uploadArea.css('display')}`);
            console.log(`EmptyState visible: ${$emptyState.is(':visible')}, display: ${$emptyState.css('display')}`);
            console.log(`Loading visible: ${$loading.is(':visible')}, display: ${$loading.css('display')}`);
            console.log('=== END VISIBILITY CHECK ===');
        }, 100);
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
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card media-item" data-file-id="${fileId}" data-file-path="${file.path || file.name}" style="animation-delay: ${animationDelay}s;">
                    <div class="card-body text-center">
                        <div class="file-icon mb-3">
                            ${isImage && file.url
                                ? `<img src="${file.url}" alt="${file.name}" class="img-fluid rounded" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                   <i class="${fileIcon}" style="display: none;"></i>`
                                : `<i class="${fileIcon}"></i>`
                            }
                        </div>
                        <h6 class="file-name" title="${file.name}">${truncateFilename(file.name, 25)}</h6>
                        <div class="file-meta">
                            <small class="text-muted d-block">Size: <span class="file-size">${fileSize}</span></small>
                            <small class="text-muted d-block">Modified: ${modifiedDate}</small>
                        </div>
                        <div class="file-actions mt-3">
                            ${file.url ? `
                            <button class="btn btn-sm btn-outline-primary file-action-btn mr-1" onclick="previewFile('${fileId}', '${file.url}', '${file.name}')" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>` : ''}
                            <button class="btn btn-sm btn-outline-info file-action-btn mr-1" onclick="downloadFile('${fileId}', '${file.path || file.name}')" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            ${canDeleteFiles() ? `
                            <button class="btn btn-sm btn-outline-danger file-action-btn delete" onclick="deleteFile('${fileId}', '${file.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>` : ''}
                        </div>
                    </div>
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
        // Public disk: accessible to all authenticated users
        if (disk === 'public') {
            return true;
        }

        // Private disk: accessible to Instructor level and higher
        if (disk === 'local') {
            return window.userRole.isInstructor;
        }

        // S3 disk: accessible to Administrator level and higher
        if (disk === 's3') {
            return window.userRole.isAdministrator;
        }

        return false;
    }

    function canUploadToCurrentDisk() {
        // Guest users: No upload access
        if (!window.userRole.isAnyAdmin) {
            return false;
        }

        // Administrator level: Can upload to all accessible disks
        if (window.userRole.isAdministrator) {
            return true;
        }

        // Instructor and Support: Can upload to public and private only
        if (window.userRole.isInstructor) {
            return currentDisk === 'public' || currentDisk === 'local';
        }

        return false;
    }

    function canDeleteFiles() {
        // Only Administrator level and higher can delete files
        return window.userRole.isAdministrator;
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
        // Get the correct disk ID for templates (maps 'local' -> 'private')
        const templateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const loadingElement = $(`#${templateDiskId}Loading`);
        const gridElement = $(`#${templateDiskId}Grid`);
        const uploadAreaElement = $(`#${templateDiskId}UploadArea`);
        const emptyStateElement = $(`#${templateDiskId}EmptyState`);

        console.log(`showLoading() - currentDisk: ${currentDisk}, templateDiskId: ${templateDiskId}`);
        console.log(`Loading element found: ${loadingElement.length > 0}, Grid element found: ${gridElement.length > 0}`);

        // FORCE HIDE all other elements using both jQuery and CSS
        if (gridElement.length > 0) {
            gridElement.hide().css('display', 'none');
        }
        if (uploadAreaElement.length > 0) {
            uploadAreaElement.hide().css('display', 'none');
        }
        if (emptyStateElement.length > 0) {
            emptyStateElement.hide().css('display', 'none');
        }

        // Show loading with force
        if (loadingElement.length > 0) {
            loadingElement.show().css('display', 'block');
        }

        $('#currentDiskDisplay').text(currentDisk.charAt(0).toUpperCase() + currentDisk.slice(1));
    }

    function hideLoading() {
        // Get the correct disk ID for templates (maps 'local' -> 'private')
        const templateDiskId = diskToGridMap[currentDisk] || currentDisk;
        const loadingElement = $(`#${templateDiskId}Loading`);

        console.log(`hideLoading() - currentDisk: ${currentDisk}, templateDiskId: ${templateDiskId}`);

        if (loadingElement.length > 0) {
            loadingElement.hide().css('display', 'none');
        }
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

    // Connection Status Management
    function showConnectionScreen(disk, diskStatus) {
        hideAllConnectionScreens();

        if (disk === 's3') {
            showS3ConnectionScreen(diskStatus);
        } else if (disk === 'local') {
            showLocalConnectionScreen(diskStatus);
        }
    }

    function hideAllConnectionScreens() {
        $('.connection-screen').hide().removeClass('show');
    }

    function showS3ConnectionScreen(diskStatus) {
        const $screen = $('#s3ConnectionScreen');

        // Update status indicator
        const $indicator = $('#s3StatusIndicator .status-item');
        if (diskStatus.connected) {
            $indicator.find('i').removeClass('text-danger').addClass('text-success');
            $indicator.find('.status-text').text('Connected');
        } else {
            $indicator.find('i').removeClass('text-success').addClass('text-danger');
            $indicator.find('.status-text').text('Disconnected');
        }

        // Update configuration details
        const $details = $('#s3ConfigDetails');
        $details.empty();

        if (diskStatus.config_status) {
            const config = diskStatus.config_status;

            $details.append('<h6 class="mb-2">Configuration Status:</h6>');

            // Show configured fields
            if (config.configured_fields && config.configured_fields.length > 0) {
                $details.append('<div class="mb-2"><small class="text-success"><i class="fas fa-check mr-1"></i>Configured: ' +
                    config.configured_fields.join(', ') + '</small></div>');
            }

            // Show missing fields
            if (config.missing_fields && config.missing_fields.length > 0) {
                $details.append('<div class="mb-2"><small class="text-danger"><i class="fas fa-times mr-1"></i>Missing: ' +
                    config.missing_fields.join(', ') + '</small></div>');
            }

            // Show bucket and region if available
            if (config.bucket) {
                $details.append('<div class="config-item"><span>Bucket:</span><code class="config-value">' + config.bucket + '</code></div>');
            }
            if (config.region) {
                $details.append('<div class="config-item"><span>Region:</span><code class="config-value">' + config.region + '</code></div>');
            }
        }

        // Show error if available
        if (diskStatus.error) {
            $details.append('<div class="alert alert-danger mt-2"><small><strong>Error:</strong> ' + diskStatus.error + '</small></div>');
        }

        $screen.show().addClass('show');
    }

    function showLocalConnectionScreen(diskStatus) {
        const $screen = $('#localConnectionScreen');

        // Update status indicator
        const $indicator = $('#localStatusIndicator .status-item');
        if (diskStatus.connected) {
            $indicator.find('i').removeClass('text-danger').addClass('text-success');
            $indicator.find('.status-text').text('Connected');
        } else {
            $indicator.find('i').removeClass('text-success').addClass('text-danger');
            $indicator.find('.status-text').text('Storage Unavailable');
        }

        // Update error details
        const $details = $('#localErrorDetails');
        $details.empty();

        if (diskStatus.error) {
            $details.append('<div class="alert alert-danger"><small><strong>Error:</strong> ' + diskStatus.error + '</small></div>');
        }

        $screen.show().addClass('show');
    }

    function updateDiskStatus(disk, diskStatus) {
        // Update tab indicators based on disk status
        const $tab = $(`.media-tabs a[data-disk="${disk}"]`);
        const $indicator = $(`.disk-status-indicator[data-disk="${disk}"]`);

        if (diskStatus.connected) {
            $tab.removeClass('disk-disconnected').addClass('disk-connected');
            $indicator.show().find('i').removeClass('text-danger text-warning').addClass('text-success');
            $tab.find('.status-indicator').remove(); // Remove old style indicators
        } else {
            $tab.removeClass('disk-connected').addClass('disk-disconnected');
            $indicator.show().find('i').removeClass('text-success text-warning').addClass('text-danger');

            // Add warning indicator if not present
            if ($tab.find('.status-indicator').length === 0) {
                $tab.append(' <span class="status-indicator text-warning"><i class="fas fa-exclamation-triangle"></i></span>');
            }
        }

        // Update tooltip with status message
        $tab.attr('title', diskStatus.message);
    }

    function testConnection(disk) {
        showLoading();

        $.ajax({
            url: '/admin/media-manager/disk-statuses',
            method: 'GET',
            success: function(response) {
                if (response.success && response.disk_statuses[disk]) {
                    const diskStatus = response.disk_statuses[disk];
                    updateDiskStatus(disk, diskStatus);

                    if (diskStatus.connected) {
                        hideAllConnectionScreens();
                        loadFiles();
                        showNotification('Connection successful!', 'success');
                    } else {
                        showConnectionScreen(disk, diskStatus);
                        showNotification('Connection failed: ' + diskStatus.message, 'error');
                    }
                }
            },
            error: function(xhr) {
                showNotification('Failed to test connection', 'error');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    // Event handlers for connection screens
    $(document).ready(function() {
        // S3 connection actions
        $('#testS3Connection').on('click', function() {
            testConnection('s3');
        });

        $('#refreshS3Status').on('click', function() {
            testConnection('s3');
        });

        // Local connection actions
        $('#testLocalConnection').on('click', function() {
            testConnection('local');
        });

        $('#refreshLocalStatus').on('click', function() {
            testConnection('local');
        });

        // Close connection screens when clicking outside
        $('.connection-screen').on('click', function(e) {
            if (e.target === this) {
                hideAllConnectionScreens();
            }
        });
    });

    // Initial disk status check
    function checkAllDiskStatuses() {
        $.ajax({
            url: '/admin/media-manager/disk-statuses',
            method: 'GET',
            success: function(response) {
                if (response.success && response.disk_statuses) {
                    Object.keys(response.disk_statuses).forEach(disk => {
                        updateDiskStatus(disk, response.disk_statuses[disk]);
                    });
                }
            },
            error: function(xhr) {
                console.warn('Failed to check disk statuses:', xhr);
            }
        });
    }
</script>
