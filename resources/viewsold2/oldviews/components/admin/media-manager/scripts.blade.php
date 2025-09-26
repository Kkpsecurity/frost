<script>
    // CSRF Token for AJAX requests
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    // FilePond variable (if using FilePond library)
    let FilePond = null;

    let currentDisk = 'public';
    let currentPath = '/';
    let currentFolder = '';
    let selectedFiles = [];
    let currentFiles = [];
    let currentDirectories = [];
    let currentViewMode = 'grid'; // Default view mode

    // Map disk names to their corresponding template IDs
    const diskToGridMap = {
        'public': 'public',
        'local': 'private',
        's3': 's3'
    };

    $(document).ready(function() {
        console.log('Initializing Media Manager...');
        initializeMediaManager();
    });

    function initializeMediaManager() {
        // Initialize tab functionality
        initializeTabs();

        // Set initial state
        showLoadingState(currentDisk);

        // Bind event handlers
        bindEventHandlers();

        // Bind breadcrumb navigation
        bindBreadcrumbNavigation();

        // Load initial files
        loadFiles(currentDisk);
    }

    function initializeTabs() {
        console.log('Initializing tab functionality...');

        // Bind tab click handlers
        $('.media-tabs .nav-link').on('click', function(e) {
            e.preventDefault();
            const targetDisk = $(this).data('disk');

            if (targetDisk && targetDisk !== currentDisk) {
                switchToDisk(targetDisk);
            }
        });

        // Set initial active tab
        setActiveTab(currentDisk);
    }

    function switchToDisk(diskName) {
        console.log(`Switching from ${currentDisk} to ${diskName}`);

        // Update current disk
        currentDisk = diskName;
        currentPath = '/';
        currentFolder = '';

        // Update UI
        setActiveTab(diskName);
        showActiveTabContent(diskName);
        updateCurrentDiskDisplay(diskName);
        updateBreadcrumbs(diskName, 'media', '/');
        showLoadingState(diskName);

        // Load files for new disk
        loadFiles(diskName);
    }

    function setActiveTab(diskName) {
        // Remove active class from all tabs
        $('.media-tabs .nav-link').removeClass('active');

        // Add active class to current tab
        $(`.media-tabs .nav-link[data-disk="${diskName}"]`).addClass('active');

        console.log(`Set active tab: ${diskName}`);
    }

    function showActiveTabContent(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // Hide all tab panes
        $('.tab-pane').removeClass('show active').hide();

        // Show current tab pane
        $(`#${templateDiskId}`).addClass('show active').show();

        console.log(`Showing tab content for: ${diskName} (${templateDiskId})`);
    }

    function updateCurrentDiskDisplay(diskName) {
        const displayName = diskName.charAt(0).toUpperCase() + diskName.slice(1);
        $('#currentDiskDisplay').text(displayName);
    }

    function showLoadingState(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // NUCLEAR APPROACH - Hide ALL upload areas first, then show loading
        $('.upload-area').hide().removeClass('show').css({
            'display': 'none !important',
            'visibility': 'hidden !important',
            'height': '0 !important',
            'min-height': '0 !important',
            'max-height': '0 !important',
            'padding': '0 !important',
            'margin': '0 !important',
            'overflow': 'hidden !important'
        });


        // Force hide specific content states
        $(`#${templateDiskId}Grid`).hide().css('display', 'none');

        // Show loading with explicit styling
        $(`#${templateDiskId}Loading`).show().css('display', 'block');

        console.log(`Showing loading state for: ${diskName} - ALL upload areas hidden`);
    }

    function showUploadState(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // First hide ALL upload areas everywhere
        $('.upload-area').hide().removeClass('show').css({
            'display': 'none !important',
            'visibility': 'hidden !important',
            'height': '0 !important',
            'min-height': '0 !important',
            'max-height': '0 !important',
            'padding': '0 !important',
            'margin': '0 !important',
            'overflow': 'hidden !important'
        });

        // Force hide other states for this disk
        $(`#${templateDiskId}Loading`).hide().css('display', 'none');
        $(`#${templateDiskId}Grid`).hide().css('display', 'none');

        // Now show ONLY the current disk's upload area with FULL restoration
        $(`#${templateDiskId}UploadArea`).addClass('show').show().css({
            'display': 'flex !important',
            'visibility': 'visible !important',
            'justify-content': 'center',
            'align-items': 'center',
            'width': '100%',
            'min-height': '400px',
            'height': 'auto !important',
            'max-height': 'none !important',
            'padding': '4rem 2rem !important',
            'margin': '2rem auto !important',
            'overflow': 'visible !important',
            'pointer-events': 'auto !important',
            'cursor': 'pointer !important'
        });

        // Ensure buttons inside are clickable
        $(`#${templateDiskId}UploadArea .upload-btn`).css({
            'pointer-events': 'auto !important',
            'cursor': 'pointer !important',
            'display': 'inline-block !important'
        });

        console.log(`Showing upload state for: ${diskName} - upload area visible and clickable`);
    }

    function showFileGrid(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // NUCLEAR APPROACH - Hide ALL upload areas first
        $('.upload-area').hide().removeClass('show').css({
            'display': 'none !important',
            'visibility': 'hidden !important',
            'height': '0 !important',
            'min-height': '0 !important',
            'max-height': '0 !important',
            'padding': '0 !important',
            'margin': '0 !important',
            'overflow': 'hidden !important'
        });

        // Force hide other states
        $(`#${templateDiskId}Loading`).hide().css('display', 'none');

        // Show file grid with block display
        $(`#${templateDiskId}Grid`).show().css({
            'display': 'block',
            'width': '100%'
        });

        console.log(`Showing file grid for: ${diskName} - ALL upload areas hidden`);
    }

    function showErrorState(diskName, errorMessage, showConnectionScreen = false) {
        console.log(`showErrorState called for ${diskName}:`, errorMessage, showConnectionScreen);
        const templateDiskId = diskToGridMap[diskName] || diskName;
        console.log(`Using template disk ID: ${templateDiskId}`);

        // Update disk status indicator for connection errors
        updateDiskStatusIndicator(diskName, 'error', errorMessage);

        // Clear selected files
        selectedFiles = [];
        updateDeleteButtonState();

        // Hide all other states
        $(`#${templateDiskId}Loading`).hide();
        $(`#${templateDiskId}Grid`).hide();
        $('.upload-area').hide();

        // Create error display content
        const connectionInfo = showConnectionScreen ? `
            <div class="alert alert-warning mt-3">
                <h6><i class="fas fa-exclamation-triangle mr-2"></i>Connection Required</h6>
                <p class="mb-2">S3 storage connection is not available. This may be due to:</p>
                <ul class="text-left mb-2">
                    <li>Network connectivity issues</li>
                    <li>DNS resolution problems</li>
                    <li>S3 service configuration</li>
                </ul>
                <small class="text-muted">Contact your system administrator to resolve this issue.</small>
            </div>
        ` : '';

        const errorContent = `
            <div class="error-state text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h4 class="text-danger">Storage Error</h4>
                    <p class="text-muted mb-3">${errorMessage}</p>
                    ${connectionInfo}
                </div>
                <div class="mt-4">
                    <button class="btn btn-outline-primary" onclick="loadFiles('${diskName}')">
                        <i class="fas fa-sync-alt mr-2"></i>Retry Connection
                    </button>
                </div>
            </div>
        `;

        // Show error in the grid area
        $(`#${templateDiskId}Grid`).html(errorContent).show();
        console.log(`Error content inserted into #${templateDiskId}Grid`);

        // Update directory tree to show disconnected state
        updateDirectoryTreeError(diskName, errorMessage);

        console.log(`Showing error state for: ${diskName} - ${errorMessage}`);
    }

    function updateDirectoryTreeError(diskName, errorMessage) {
        const $tree = $('#directoryTree');
        $tree.empty();

        $tree.append(`
            <div class="tree-item error-item">
                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Connection Error
                <small class="text-muted d-block ml-4">${errorMessage.substring(0, 50)}...</small>
            </div>
        `);
    }

    function updateDiskStatusIndicator(diskName, status, message = '') {
        const $indicator = $(`.disk-status-indicator[data-disk="${diskName}"]`);
        const $tabLink = $(`.nav-link[data-disk="${diskName}"]`);
        const $tabIcon = $tabLink.find('i').first();
        const $tabText = $tabLink.contents().filter(function() {
            return this.nodeType === 3; // Text nodes only
        });

        // Reset all status classes
        $indicator.removeClass('status-connected status-error status-loading');
        $tabLink.removeClass('text-danger text-success text-warning');

        switch(status) {
            case 'connected':
                $indicator.addClass('status-connected');
                $tabLink.removeClass('text-danger text-warning').addClass('text-success');
                $indicator.html('<i class="fas fa-check-circle text-success ml-1" title="Connected"></i>');
                console.log(`Updated ${diskName} status: connected`);
                break;

            case 'error':
                $indicator.addClass('status-error');
                $tabLink.removeClass('text-success text-warning').addClass('text-danger');

                // Change the main tab icon to warning triangle
                $tabIcon.removeClass('fas fa-globe fas fa-shield-alt fas fa-archive')
                       .addClass('fas fa-exclamation-triangle');

                // Clear the indicator since we're using the main tab icon for error state
                $indicator.empty();
                console.log(`Updated ${diskName} status: error - ${message}`);
                break;

            case 'loading':
                $indicator.addClass('status-loading');
                $tabLink.removeClass('text-danger text-success').addClass('text-warning');
                $indicator.html('<i class="fas fa-spinner fa-spin text-warning ml-1" title="Connecting..."></i>');
                console.log(`Updated ${diskName} status: loading`);
                break;

            default:
                $indicator.empty();
                $tabLink.removeClass('text-danger text-success text-warning');
                console.log(`Updated ${diskName} status: cleared`);
        }
    }

    function resetDiskStatusIndicator(diskName) {
        const $tabLink = $(`.nav-link[data-disk="${diskName}"]`);
        const $tabIcon = $tabLink.find('i').first();

        // Reset tab icon to original based on disk type
        $tabIcon.removeClass('fas fa-exclamation-triangle');

        switch(diskName) {
            case 'public':
                $tabIcon.addClass('fas fa-globe');
                break;
            case 'local':
                $tabIcon.addClass('fas fa-shield-alt');
                break;
            case 's3':
                $tabIcon.addClass('fas fa-archive');
                break;
        }

        // Reset status to clear
        updateDiskStatusIndicator(diskName, 'clear');
    }

    function bindEventHandlers() {
        console.log('Binding event handlers...');

        // File upload handlers (original)
        $(document).on('click', '.upload-btn', function(e) {
            const diskId = $(this).closest('.tab-pane').attr('id');
            $(`#${diskId}FileInput`).click();
            console.log(`Upload button clicked for disk: ${diskId}`);
        });

        // File Management Button Handlers
        $(document).on('click', '#createFolderBtn', function() {
            console.log('Create folder button clicked');
            showCreateFolderModal();
        });

        // Upload file button handler is now handled by advanced upload modal
        // See upload-modal-scripts.blade.php

        $(document).on('click', '#refreshBtn', function() {
            console.log('Refresh button clicked');
            loadFiles(currentDisk);
        });

        $(document).on('click', '#deleteSelectedBtn', function() {
            console.log('Delete selected button clicked');
            deleteSelectedFiles();
        });

        // File selection handlers
        $(document).on('change', '.file-checkbox', function() {
            const fileName = $(this).val();
            const isChecked = $(this).prop('checked');

            if (isChecked) {
                if (!selectedFiles.includes(fileName)) {
                    selectedFiles.push(fileName);
                }
            } else {
                selectedFiles = selectedFiles.filter(f => f !== fileName);
            }

            updateDeleteButtonState();
            console.log(`File selection changed. Selected files:`, selectedFiles);
        });

        // Individual file delete
        $(document).on('click', '.delete-single-btn', function(e) {
            e.stopPropagation();
            const fileName = $(this).closest('.file-item').data('file');
            if (confirm(`Are you sure you want to delete "${fileName}"?`)) {
                deleteSingleFile(fileName);
            }
        });

        // File download
        $(document).on('click', '.download-btn', function(e) {
            e.stopPropagation();
            const fileName = $(this).closest('.file-item').data('file');
            downloadFile(fileName);
        });

        // FOLDER-ONLY VIEW HANDLERS
        // Folder click for selection (single click)
        $(document).on('click', '.folder-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $folder = $(this);
            const folderName = $folder.data('folder');
            const $checkbox = $folder.find('.folder-checkbox');

            // Toggle selection state
            const isSelected = $folder.hasClass('selected');

            if (e.ctrlKey || e.metaKey) {
                // Multi-select with Ctrl/Cmd
                if (isSelected) {
                    $folder.removeClass('selected');
                    $checkbox.prop('checked', false);
                    selectedFiles = selectedFiles.filter(f => f !== folderName);
                } else {
                    $folder.addClass('selected');
                    $checkbox.prop('checked', true);
                    if (!selectedFiles.includes(folderName)) {
                        selectedFiles.push(folderName);
                    }
                }
            } else {
                // Single select (clear others first)
                $('.folder-item').removeClass('selected');
                $('.folder-checkbox').prop('checked', false);
                selectedFiles = [];

                // Select this one
                $folder.addClass('selected');
                $checkbox.prop('checked', true);
                selectedFiles.push(folderName);
            }

            updateDeleteButtonState();
            console.log(`Folder selection changed. Selected folders:`, selectedFiles);
        });

        // Folder double-click for navigation
        $(document).on('dblclick', '.folder-item', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const folderPath = $(this).data('path');
            const diskName = $(this).data('disk');
            const folderName = $(this).data('folder');

            console.log(`Double-clicked folder: ${folderName} at path: ${folderPath} on disk: ${diskName}`);

            // Navigate into the folder
            navigateToFolder(diskName, folderPath, folderName);
        });

        // Folder selection handlers (kept for programmatic access)
        $(document).on('change', '.folder-checkbox', function() {
            const folderName = $(this).val();
            const isChecked = $(this).prop('checked');
            const $folder = $(this).closest('.folder-item');

            if (isChecked) {
                $folder.addClass('selected');
                if (!selectedFiles.includes(folderName)) {
                    selectedFiles.push(folderName);
                }
            } else {
                $folder.removeClass('selected');
                selectedFiles = selectedFiles.filter(f => f !== folderName);
            }

            updateDeleteButtonState();
            console.log(`Folder checkbox changed. Selected folders:`, selectedFiles);
        });

        // Create first folder button (in empty state)
        $(document).on('click', '#createFirstFolderBtn', function() {
            showCreateFolderModal();
        });

        // General file input change handler
        $(document).on('change', '#generalFileInput', function() {
            const files = this.files;
            if (files.length > 0) {
                console.log(`Selected ${files.length} files for upload to current folder`);
                uploadFilesToCurrentFolder(files);
            }
        });

        // Original file input change handlers (for backward compatibility)
        $(document).on('change', 'input[type="file"]', function() {
            const files = this.files;
            if (files.length > 0 && $(this).attr('id') !== 'generalFileInput') {
                console.log(`Selected ${files.length} files for upload`);
                const diskId = $(this).attr('id').replace('FileInput', '');
                uploadFiles(files, diskId);
            }
        });
    }

    function updateDeleteButtonState() {
        const deleteBtn = $('#deleteSelectedBtn');
        if (selectedFiles.length > 0) {
            deleteBtn.prop('disabled', false).attr('title', `Delete ${selectedFiles.length} selected file(s)`);
        } else {
            deleteBtn.prop('disabled', true).attr('title', 'No files selected');
        }
    }

    function deleteSingleFile(fileName) {
        $.ajax({
            url: `/admin/media-manager/delete/${encodeURIComponent(fileName)}`,
            method: 'DELETE',
            data: {
                disk: currentDisk,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            console.log(`File deleted successfully: ${fileName}`);
            loadFiles(currentDisk); // Refresh file list
        })
        .fail(function(xhr) {
            console.error(`Delete failed for file: ${fileName}`, xhr.responseText);
            alert(`Failed to delete file: ${xhr.responseJSON?.error || 'Unknown error'}`);
        });
    }

    function deleteSingleFolder(folderName) {
        $.ajax({
            url: `/admin/media-manager/delete/${encodeURIComponent(folderName)}`,
            method: 'DELETE',
            data: {
                disk: currentDisk,
                folder: true, // Indicate this is a folder deletion
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            console.log(`Folder deleted successfully: ${folderName}`);
            loadFiles(currentDisk); // Refresh folder list
        })
        .fail(function(xhr) {
            console.error(`Delete failed for folder: ${folderName}`, xhr.responseText);
            alert(`Failed to delete folder: ${xhr.responseJSON?.error || 'Unknown error'}`);
        });
    }

    function downloadFile(fileName) {
        window.open(`/admin/media-manager/download/${encodeURIComponent(fileName)}?disk=${currentDisk}`, '_blank');
    }

    // Breadcrumb functions
    function updateBreadcrumbs(disk, folder, path) {
        const diskNames = {
            'public': 'Public Storage',
            'local': 'Private Storage',
            's3': 'S3 Archive Storage'
        };

        const diskName = diskNames[disk] || disk;

        // Clear existing breadcrumbs
        const breadcrumbContainer = $('#mediaBreadcrumb');
        breadcrumbContainer.empty();

        // Add disk breadcrumb (always first)
        breadcrumbContainer.append(`
            <li class="breadcrumb-item" data-disk="${disk}">
                <i class="fas fa-hdd mr-1 text-muted"></i>
                <span id="currentDiskBreadcrumb" class="breadcrumb-link">${diskName}</span>
            </li>
        `);

        // Normalize path
        if (!path || path === '') path = '/';
        if (!path.startsWith('/')) path = '/' + path;

        // Split path into segments
        const pathSegments = path.split('/').filter(segment => segment !== '');

        if (pathSegments.length === 0 || (pathSegments.length === 1 && pathSegments[0] === 'media')) {
            // Root level - show only Media Root
            breadcrumbContainer.append(`
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-folder mr-1 text-primary"></i>
                    <span>Media Root</span>
                </li>
            `);
            console.log(`Breadcrumbs updated: ${diskName} > Media Root`);
        } else {
            // Add Media Root as clickable breadcrumb
            breadcrumbContainer.append(`
                <li class="breadcrumb-item" data-path="/">
                    <i class="fas fa-folder mr-1 text-muted"></i>
                    <span class="breadcrumb-link">Media Root</span>
                </li>
            `);

            // Build path hierarchy
            let currentPath = '';
            for (let i = 0; i < pathSegments.length; i++) {
                const segment = pathSegments[i];
                if (segment === 'media') continue; // Skip 'media' folder in display

                currentPath += '/' + segment;
                const isLast = (i === pathSegments.length - 1);

                if (isLast) {
                    // Last segment is active (current location)
                    breadcrumbContainer.append(`
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-folder mr-1 text-primary"></i>
                            <span>${segment}</span>
                        </li>
                    `);
                } else {
                    // Intermediate segments are clickable
                    breadcrumbContainer.append(`
                        <li class="breadcrumb-item" data-path="${currentPath}">
                            <i class="fas fa-folder mr-1 text-muted"></i>
                            <span class="breadcrumb-link">${segment}</span>
                        </li>
                    `);
                }
            }

            const displayPath = pathSegments.filter(s => s !== 'media').join(' > ');
            console.log(`Breadcrumbs updated: ${diskName} > Media Root > ${displayPath}`);
        }
    }

    function bindBreadcrumbNavigation() {
        // Navigate to disk root when clicking disk breadcrumb
        $(document).on('click', '#currentDiskBreadcrumb', function(e) {
            e.preventDefault();
            const disk = $(this).closest('li').attr('data-disk') || currentDisk;
            console.log(`Breadcrumb: Navigating to disk root for ${disk}`);

            // Reset to root state
            currentPath = '/';
            currentFolder = '';

            // If switching to different disk, use switchToDisk
            if (disk !== currentDisk) {
                switchToDisk(disk);
            } else {
                // Same disk, just go to root
                updateBreadcrumbs(disk, 'media', '/');
                updateSidebarSelection('root');
                loadFiles(disk);
            }
        });

        // Navigate to specific path when clicking breadcrumb segments
        $(document).on('click', '.breadcrumb-link', function(e) {
            e.preventDefault();
            const breadcrumbItem = $(this).closest('li');
            const path = breadcrumbItem.attr('data-path') || '/';
            const disk = breadcrumbItem.attr('data-disk');

            console.log(`Breadcrumb: Navigating to path ${path}`);

            if (disk && disk !== currentDisk) {
                // Switching to different disk
                switchToDisk(disk);
            } else if (path && path !== currentPath) {
                // Navigate to specific path on same disk
                navigateToPath(path);
            } else {
                // Navigate to root
                currentPath = '/';
                currentFolder = '';
                updateBreadcrumbs(currentDisk, 'media', '/');
                updateSidebarSelection('root');
                loadFiles(currentDisk);
            }
        });

        // Visual feedback for clickable breadcrumbs
        $(document).on('mouseenter', '.breadcrumb-link', function() {
            $(this).addClass('text-primary').css('cursor', 'pointer');
        });

        $(document).on('mouseleave', '.breadcrumb-link', function() {
            $(this).removeClass('text-primary').css('cursor', 'auto');
        });
    }

    // Navigate to a specific path
    function navigateToPath(path) {
        console.log(`Navigating to path: ${path}`);

        // Normalize path
        if (!path.startsWith('/')) path = '/' + path;

        // Update current state
        currentPath = path;

        // Determine folder from path
        const pathSegments = path.split('/').filter(s => s !== '');
        if (pathSegments.length > 0 && pathSegments[0] === 'media') {
            pathSegments.shift(); // Remove 'media' prefix
        }

        currentFolder = pathSegments.length > 0 ? pathSegments[pathSegments.length - 1] : '';

        // Update UI
        updateBreadcrumbs(currentDisk, currentFolder, path);
        updateSidebarSelection(currentFolder || 'root');

        // Load files for this path
        loadFilesForPath(currentDisk, path);
    }

    function loadFiles(diskName) {
        console.log(`Loading files for disk: ${diskName}`);

        // Reset disk status and show loading state
        resetDiskStatusIndicator(diskName);
        updateDiskStatusIndicator(diskName, 'loading');
        showLoadingState(diskName);

        // Make actual API call to load files
        $.ajax({
            url: '/admin/media-manager/files',
            method: 'GET',
            data: {
                disk: diskName,
                path: '/'
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(`API Response for ${diskName}:`, response);

                if (response.success) {
                    const files = response.files || [];
                    const directories = response.directories || [];

                    // Update disk status to connected
                    updateDiskStatusIndicator(diskName, 'connected');

                    // Update current data
                    currentFiles = files;
                    currentDirectories = directories;

                    // Update breadcrumbs for current location
                    updateBreadcrumbs(diskName, 'media', '/');

                    // Update sidebar with directories and upload tools
                    updateDirectoryTree(diskName, directories);
                    updateUploadTools(diskName);

                    // Ensure sidebar shows root as selected for initial load
                    updateSidebarSelection('root');

                    // FOLDER-ONLY VIEW: Show folders in main content area
                    if (directories.length > 0) {
                        // Show folder grid in main content (Step 1: Folder-Only View)
                        populateFolderGrid(diskName, directories);
                        showFolderGrid(diskName);
                        console.log(`Showing ${directories.length} folders for ${diskName}`);
                    } else {
                        // No folders available - show empty state
                        showEmptyFolderState(diskName);
                        console.log(`No folders found for ${diskName}`);
                    }

                    console.log(`Loaded ${files.length} files and ${directories.length} directories for ${diskName}`);
                } else {
                    console.error('Failed to load files:', response.error);
                    console.log('Response details:', response);
                    showErrorState(diskName, response.error, response.show_connection_screen);
                }
            },
            error: function(xhr, status, error) {
                console.error(`Failed to load files for ${diskName}:`, xhr.responseText);

                let errorMessage = 'Failed to connect to storage';
                let showConnectionScreen = false;

                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.error || errorMessage;
                    showConnectionScreen = xhr.responseJSON.show_connection_screen || false;
                }

                showErrorState(diskName, errorMessage, showConnectionScreen);
            }
        });
    }

    function populateFileGrid(diskName, files) {
        const templateDiskId = diskToGridMap[diskName] || diskName;
        const $grid = $(`#${templateDiskId}Grid`);

        // Clear existing content and add grid class
        $grid.empty().addClass('media-files-grid');

        if (!files || files.length === 0) {
            $grid.removeClass('media-files-grid').html('<div class="no-files-message text-center py-4"><i class="fas fa-folder-open text-muted fa-3x mb-3"></i><p class="text-muted">No files found</p></div>');
            return;
        }

        // Add files to grid
        files.forEach(file => {
            const fileItem = createFileItem(file, diskName);
            $grid.append(fileItem);
        });

        console.log(`Populated grid with ${files.length} files`);
    }

    function createFileItem(file, diskName) {
        const isImage = /\.(jpg|jpeg|png|gif)$/i.test(file.name);
        const icon = getFileIcon(file.name);

        return $(`
            <div class=" file-item" data-file="${file.name}" data-disk="${diskName}">
                <div class="file-selector">
                    <input type="checkbox" class="file-checkbox" value="${file.name}">
                </div>
                <div class="file-preview">
                    ${isImage ?
                        `<img src="${file.url}" alt="${file.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                         <i class="file-icon ${icon}" style="display: none;"></i>` :
                        `<i class="file-icon ${icon}"></i>`
                    }
                </div>
                <div class="file-info">
                    <div class="file-name" title="${file.name}">${file.name}</div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                </div>
                <div class="file-actions">
                    <button class="btn btn-sm btn-outline-primary download-btn" title="Download">
                        <i class="fas fa-download"></i>
                    </button>finf trash
                    <button class="btn btn-sm btn-outline-danger delete-single-btn" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `);
    }

    function getFileIcon(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fas fa-file-pdf text-danger',
            'doc': 'fas fa-file-word text-primary',
            'docx': 'fas fa-file-word text-primary',
            'jpg': 'fas fa-file-image text-success',
            'jpeg': 'fas fa-file-image text-success',
            'png': 'fas fa-file-image text-success',
            'gif': 'fas fa-file-image text-success',
            'css': 'fas fa-file-code text-info',
            'js': 'fas fa-file-code text-warning',
            'json': 'fas fa-file-code text-secondary'
        };
        return iconMap[ext] || 'fas fa-file text-muted';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // FOLDER-ONLY VIEW FUNCTIONS
    function populateFolderGrid(diskName, directories) {
        const templateDiskId = diskToGridMap[diskName] || diskName;
        const $grid = $(`#${templateDiskId}Grid`);

        console.log(`PopulateFolderGrid: diskName=${diskName}, currentViewMode=${currentViewMode}, templateDiskId=${templateDiskId}`);

        // Clear selected files when populating new grid
        selectedFiles = [];
        updateDeleteButtonState();

        // Clear existing content and add Bootstrap row class with folder grid class
        $grid.empty().addClass('media-folders-grid row');

        // Force apply grid view class
        if (currentViewMode === 'grid') {
            $grid.addClass('view-grid');
        } else {
            $grid.addClass('view-list');
        }

        console.log(`Grid classes applied:`, $grid.attr('class'));

        if (!directories || directories.length === 0) {
            showEmptyFolderState(diskName);
            return;
        }

        // Add folders to grid
        directories.forEach(directory => {
            const folderItem = createFolderItem(directory, diskName);
            $grid.append(folderItem);
        });

        console.log(`Populated folder grid with ${directories.length} directories in ${currentViewMode} view`);
    }

    function createFolderItem(directory, diskName) {
        return $(`
            <div class="col-3 folder-item" data-folder="${directory.name}" data-path="${directory.path}" data-disk="${diskName}">
                <div class="folder-selector" style="display: none;">
                    <input type="checkbox" class="folder-checkbox" value="${directory.name}">
                </div>
                <div class="folder-preview">
                    <i class="fas fa-folder fa-3x text-warning"></i>
                </div>
                <div class="folder-info">
                    <div class="folder-name" title="${directory.name}">${directory.name}</div>
                    <div class="folder-details">
                        <small class="text-muted">Folder</small>
                    </div>
                </div>
            </div>
        `);
    }

    function showFolderGrid(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // Hide all other states
        $('.upload-area').hide();
        $(`#${templateDiskId}Loading`).hide();

        // Show folder grid with smooth animation
        const $grid = $(`#${templateDiskId}Grid`);
        $grid.hide().show().css('opacity', 0).animate({opacity: 1}, 300);

        // Update action buttons for folder-only view
        updateFolderViewButtons(true);

        console.log(`Showing folder grid for: ${diskName}`);
    }

    function showEmptyFolderState(diskName) {
        const templateDiskId = diskToGridMap[diskName] || diskName;

        // Hide other states
        $('.upload-area').hide();
        $(`#${templateDiskId}Loading`).hide();

        // Show empty folder message
        const emptyContent = `
            <div class="empty-folder-state text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Folders Found</h4>
                <p class="text-muted mb-4">This storage location doesn't contain any folders yet.</p>
                <button class="btn btn-primary" id="createFirstFolderBtn">
                    <i class="fas fa-folder-plus mr-2"></i>Create First Folder
                </button>
            </div>
        `;

        $(`#${templateDiskId}Grid`).html(emptyContent).show();

        // Update action buttons for empty state
        updateFolderViewButtons(false);

        console.log(`Showing empty folder state for: ${diskName}`);
    }

    function updateFolderViewButtons(hasFolders) {
        // Create Folder: always enabled
        $('#createFolderBtn').prop('disabled', false);

        // Refresh: always enabled
        $('#refreshBtn').prop('disabled', false);

        // Upload File: enabled for uploading to current disk/path
        $('#uploadFileBtn').prop('disabled', false).attr('title', `Upload files to ${currentDisk} storage`);

        // Delete: enabled only when folders are selected
        updateDeleteButtonState();

        console.log(`Updated folder view buttons. Has folders: ${hasFolders}`);
    }

    function canUploadToCurrentDisk(diskName) {
        // Simple permission check - can be expanded based on user roles
        // For now, allow upload to all disks for testing
        return true; // Changed from diskName === 'public' to allow upload on all disks
    }

    /**
     * UNIFIED UPLOAD FUNCTION - Handles all media manager uploads
     * @param {FileList|Array} files - Files to upload
     * @param {string} disk - Target disk (public, local, s3)
     * @param {string} folder - Target folder (images, documents, etc.) or path
     * @param {object} options - Additional options like progress callbacks
     */
    function unifiedUpload(files, disk, folder, options = {}) {
        console.log(`Starting upload of ${files.length} files to ${disk}/${folder}`);

        // Normalize folder parameter - handle both folder names and paths
        const targetFolder = folder.startsWith('/') ? folder.replace(/^\/+/, '') || 'general' : folder;

        const formData = new FormData();

        // Add files to FormData with consistent naming
        Array.from(files).forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        // Determine folder based on file types in the upload
        let folder = determineUploadFolder(files);

        console.log(`handleMediaManagerUpload: Determined folder: ${folder}`);

        // Add metadata
        formData.append('disk', disk);
        formData.append('folder', targetFolder);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Show loading state
        updateDiskStatusIndicator(disk, 'loading');
        if (options.showProgress !== false) {
            showHeaderUploadProgress(0);
        }

        return $.ajax({
            url: '/admin/media-manager/upload',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                const xhr = new XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const progress = (e.loaded / e.total) * 100;
                        console.log(`Upload progress: ${Math.round(progress)}%`);
                        if (options.showProgress !== false) {
                            updateHeaderUploadProgress(progress);
                        }
                        if (options.onProgress) {
                            options.onProgress(progress);
                        }
                    }
                }, false);
                return xhr;
            }
        })
        .done(function(response) {
            console.log('Upload successful:', response);
            updateDiskStatusIndicator(disk, 'connected');
            hideHeaderUploadProgress();

            if (response.success) {
                if (options.onSuccess) {
                    options.onSuccess(response);
                } else {
                    showNotification('success', response.message || `${files.length} file(s) uploaded successfully`);
                    loadFiles(disk); // Refresh current view
                }
            } else {
                const errorMsg = response.error || 'Upload failed';
                if (options.onError) {
                    options.onError(null, errorMsg);
                } else {
                    showNotification('error', errorMsg);
                }
            }
        })
        .fail(function(xhr) {
            console.error('Upload failed:', xhr.responseText);
            updateDiskStatusIndicator(disk, 'error', 'Upload failed');
            hideHeaderUploadProgress();

            let errorMessage = 'Upload failed';
            if (xhr.status === 422) {
                errorMessage = 'Validation failed';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join(', ');
                }
            } else if (xhr.responseJSON) {
                errorMessage = xhr.responseJSON.error || xhr.responseJSON.message || errorMessage;
            } else if (xhr.responseText) {
                try {
                    const parsed = JSON.parse(xhr.responseText);
                    errorMessage = parsed.error || parsed.message || errorMessage;
                } catch (e) {
                    errorMessage = `Server error (${xhr.status})`;
                }
            }

            if (options.onError) {
                options.onError(xhr, errorMessage);
            } else {
                showNotification('error', errorMessage);
            }
        });
    }

    // LEGACY WRAPPER FUNCTIONS - For backward compatibility
    function handleMediaManagerUpload(files, disk, path = '/') {
        return unifiedUpload(files, disk, path);
    }

    /**
     * Get current path for navigation and uploads
     */
    function getCurrentPath() {
        return currentPath || '/';
    }

    /**
     * Get current disk
     */
    function getCurrentDisk() {
        return currentDisk || 'public';
    }

    /**
     * Update files list in the current view
     */
    function updateFilesList() {
        console.log('updateFilesList: Refreshing current view');
        loadFiles(getCurrentDisk());
    }

    /**
     * Upload files to the current folder
     */
    function uploadFilesToCurrentFolder(files) {
        if (!files || files.length === 0) {
            console.log('uploadFilesToCurrentFolder: No files selected');
            return;
        }

        const disk = getCurrentDisk();
        const path = getCurrentPath();

        console.log(`uploadFilesToCurrentFolder: Uploading ${files.length} files to ${disk}:${path}`);

        // Show progress indicator in header
        showUploadProgress();

        // Use the existing standardized upload function
        handleMediaManagerUpload(files, disk, path)
            .always(function() {
                // Hide progress indicator
                hideUploadProgress();
                // Clear the file input
                $('#generalFileInput').val('');
            });
    }

    /**
     * Show upload progress in header
     */
    function showUploadProgress() {
        $('#headerProgressContainer').show();
        $('#headerProgressBar').css('width', '0%').attr('aria-valuenow', 0);
        $('#progressText').text('Uploading...');
    }

    /**
     * Update upload progress
     */
    function updateUploadProgress(disk, progress) {
        $('#headerProgressBar').css('width', progress + '%').attr('aria-valuenow', progress);
        $('#progressText').text(`Uploading... ${Math.round(progress)}%`);
    }

    /**
     * Hide upload progress in header
     */
    function hideUploadProgress() {
        $('#headerProgressContainer').hide();
    }

    /**
     * Determine the appropriate folder for upload based on file types
     */
    function determineUploadFolder(files) {
        if (!files || files.length === 0) {
            return 'images'; // Default fallback
        }

        // File type mappings based on controller validation rules
        const fileTypeMap = {
            'images': ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            'documents': ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'assets': ['text/css', 'application/javascript', 'application/json']
        };

        // Check the first file's type to determine folder
        const firstFile = files[0];
        const fileType = firstFile.type || '';

        // Find matching folder
        for (const [folder, mimeTypes] of Object.entries(fileTypeMap)) {
            if (mimeTypes.includes(fileType)) {
                console.log(`Determined folder '${folder}' for file type '${fileType}'`);
                return folder;
            }
        }

        // Default to images for unknown types
        console.log(`Unknown file type '${fileType}', defaulting to 'images' folder`);
        return 'images';
    }
    function updateDirectoryTree(diskName, directories) {
        const $tree = $('#directoryTree');
        $tree.empty();

        // Add root/media folder with proper data attributes
        $tree.append(`
            <div class="tree-item tree-root" data-path="/" data-disk="${diskName}" data-folder="">
                <i class="fas fa-home mr-2 text-primary"></i>Media Root
            </div>
        `);

        // Show the same directories that appear in the main content area
        directories.forEach(dir => {
            $tree.append(`
                <div class="tree-item tree-folder" data-path="${dir.path}" data-disk="${diskName}" data-folder="${dir.name}">
                    <i class="fas fa-folder mr-2 text-warning"></i>${dir.name}
                </div>
            `);
        });

        // Set initial active state for root
        updateSidebarSelection('root');

        // Bind click handlers for directory navigation
        bindSidebarNavigation();
    }

    /**
     * Bind sidebar navigation event handlers
     */
    function bindSidebarNavigation() {
        // Remove existing handlers to prevent duplicates
        $('.tree-item').off('click.sidebar');

        // Bind click handlers with namespace
        $(document).on('click.sidebar', '.tree-item', function(e) {
            e.preventDefault();

            const $item = $(this);
            const path = $item.data('path');
            const disk = $item.data('disk');
            const folder = $item.data('folder');

            console.log(`Sidebar: Clicked ${folder || 'root'} - path: ${path}, disk: ${disk}`);

            // Update active state immediately for responsive feedback
            updateSidebarSelection(folder || 'root');

            // Navigate based on whether it's root or a folder
            if (!folder || folder === '' || path === '/') {
                // Root folder - load disk root
                navigateToRoot(disk);
            } else {
                // Specific folder - navigate into it
                navigateToFolder(disk, path, folder);
            }
        });
    }

    /**
     * Update sidebar selection state with scroll preservation
     */
    function updateSidebarSelection(selectedFolder) {
        // Preserve scroll position
        const $tree = $('#directoryTree');
        const scrollTop = $tree.scrollTop();

        // Remove active class from all tree items
        $('.tree-item').removeClass('active');

        if (selectedFolder === 'root' || selectedFolder === '') {
            // Highlight root item
            $('.tree-root').addClass('active');
            console.log('Sidebar: Selected root folder');
        } else {
            // Highlight specific folder
            $(`.tree-folder[data-folder="${selectedFolder}"]`).addClass('active');
            console.log(`Sidebar: Selected folder "${selectedFolder}"`);
        }

        // Restore scroll position
        $tree.scrollTop(scrollTop);
    }

    /**
     * Navigate to disk root
     */
    function navigateToRoot(diskName) {
        console.log(`Navigating to root of disk: ${diskName}`);

        // Update current state
        currentDisk = diskName;
        currentPath = '/';
        currentFolder = '';

        // Clear selected files
        selectedFiles = [];
        updateDeleteButtonState();

        // Update breadcrumbs for root
        updateBreadcrumbs(diskName, 'media', '/');

        // Show loading state
        showLoadingState(diskName);

        // Load root folders
        loadFiles(diskName);
    }

    /**
     * Update upload tools in the sidebar
     */
    function updateUploadTools(diskName) {
        const $toolsContainer = $('#folderTools');
        const $toolsList = $('#uploadToolsList');

        $toolsList.empty();

        if (diskName === 'public') {
            $toolsContainer.show();

            const uploadFolders = [
                {
                    folder: 'images',
                    icon: 'fas fa-images',
                    label: 'Upload Images',
                    accept: '.jpg,.jpeg,.png,.gif',
                    description: 'Max 10MB each'
                },
                {
                    folder: 'documents',
                    icon: 'fas fa-file-pdf',
                    label: 'Upload Documents',
                    accept: '.pdf,.doc,.docx',
                    description: 'Max 25MB each'
                },
                {
                    folder: 'assets',
                    icon: 'fas fa-code',
                    label: 'Upload Assets',
                    accept: '.css,.js,.json',
                    description: 'Max 5MB each'
                },
                {
                    folder: 'validations/headshots',
                    icon: 'fas fa-user-circle',
                    label: 'Upload Headshots',
                    accept: '.jpg,.jpeg,.png',
                    description: 'Max 8MB each'
                },
                {
                    folder: 'validations/idcard',
                    icon: 'fas fa-id-card',
                    label: 'Upload ID Cards',
                    accept: '.jpg,.jpeg,.png',
                    description: 'Max 8MB each'
                }
            ];

            uploadFolders.forEach(tool => {
                $toolsList.append(`
                    <div class="upload-tool-item mb-2">
                        <button class="btn btn-outline-primary btn-sm w-100 text-left folder-upload-btn"
                                data-folder="${tool.folder}"
                                data-disk="${diskName}"
                                data-accept="${tool.accept}">
                            <i class="${tool.icon} mr-2"></i>${tool.label}
                            <small class="text-muted d-block">${tool.description}</small>
                        </button>
                        <input type="file"
                               id="upload-${tool.folder.replace('/', '-')}"
                               class="d-none folder-file-input"
                               multiple
                               accept="${tool.accept}"
                               data-folder="${tool.folder}"
                               data-disk="${diskName}">
                    </div>
                `);
            });

            // Bind upload button handlers
            $('.folder-upload-btn').off('click').on('click', function() {
                const folder = $(this).data('folder');
                const inputId = 'upload-' + folder.replace('/', '-');
                $(`#${inputId}`).click();
            });

            // Bind file input handlers
            $('.folder-file-input').off('change').on('change', function() {
                const files = this.files;
                const folder = $(this).data('folder');
                const disk = $(this).data('disk');

                if (files.length > 0) {
                    console.log(`Selected ${files.length} files for upload to ${disk}/${folder}`);
                    uploadFilesToFolder(files, disk, folder);
                }
            });
        } else {
            $toolsContainer.hide();
        }
    }

    /**
     * Load files for a specific path with enhanced state management
     */
    function loadFilesForPath(diskName, path) {
        console.log(`Loading files for ${diskName} at path: ${path}`);

        // Update disk status indicators
        updateDiskStatusIndicator(diskName, 'loading');

        // Add loading class to relevant sidebar item
        const $sidebarItem = $(`.tree-item[data-path="${path}"][data-disk="${diskName}"]`);
        $sidebarItem.addClass('loading');

        showLoadingState(diskName);

        $.ajax({
            url: '/admin/media-manager/files',
            method: 'GET',
            data: {
                disk: diskName,
                path: path
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Remove loading state
                $sidebarItem.removeClass('loading');

                if (response.success) {
                    const files = response.files || [];
                    const directories = response.directories || [];

                    // Update disk status to connected
                    updateDiskStatusIndicator(diskName, 'connected');

                    // Update current data
                    currentFiles = files;
                    currentDirectories = directories;

                    // If we have directories (subfolder view), show them
                    if (directories.length > 0) {
                        populateFolderGrid(diskName, directories);
                        showFolderGrid(diskName);
                        console.log(`Loaded ${directories.length} subdirectories for ${path}`);
                    } else if (files.length > 0) {
                        // Show files if no subdirectories
                        populateFileGrid(diskName, files);
                        showFileGrid(diskName);
                        console.log(`Loaded ${files.length} files for ${path}`);
                    } else {
                        // Empty folder
                        showEmptyFolderState(diskName);
                        console.log(`Empty folder at ${path}`);
                    }
                } else {
                    console.error('Failed to load files for path:', response.error);
                    updateDiskStatusIndicator(diskName, 'error', response.error);
                    showErrorState(diskName, response.error, response.show_connection_screen);
                }
            },
            error: function(xhr) {
                // Remove loading state
                $sidebarItem.removeClass('loading');

                console.error('Failed to load files for path:', xhr.responseText);

                let errorMessage = 'Failed to connect to storage';
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.error || errorMessage;
                }

                updateDiskStatusIndicator(diskName, 'error', errorMessage);
                showErrorState(diskName, errorMessage, xhr.responseJSON?.show_connection_screen);
            }
        });
    }

    /**
     * Show progress section in sidebar
     */
    function showProgressSection() {
        $('#progressSection').show();
    }

    function uploadFilesToFolder(files, diskId, folder) {
        return uploadFiles(files, diskId, folder);
    }

    /**
     * Upload files to the current disk and folder (legacy wrapper)
     */
    function uploadFiles(files, diskId) {
        const currentFolder = getCurrentFolder(diskId);
        return unifiedUpload(files, diskId, currentFolder, {
            onProgress: (progress) => updateUploadProgress(diskId, progress),
            onSuccess: (response) => handleUploadSuccess(response, diskId),
            onError: (xhr, message) => handleUploadError(xhr, diskId)
        });
    }

    /**
     * Get current folder for the disk based on selected folder or default
     */
    function getCurrentFolder(diskId) {
        // For now, default to 'images' folder for all uploads
        // This can be extended to support folder selection in the UI
        return 'images';
    }    /**
     * Show upload progress
     */
    function showUploadProgress(diskId, progress) {
        const templateDiskId = diskToGridMap[diskId] || diskId;
        const $progressContainer = $(`#${templateDiskId}ProgressContainer`);
        const $progressBar = $(`#${templateDiskId}ProgressBar`);

        // Show progress container if hidden
        $progressContainer.show();

        // Update progress bar
        $progressBar.css('width', progress + '%');
        $progressBar.attr('aria-valuenow', progress);
        $progressBar.text(Math.round(progress) + '%');
    }

    /**
     * Update upload progress
     */
    function updateUploadProgress(diskId, progress) {
        showUploadProgress(diskId, progress);
    }

    /**
     * Handle successful upload
     */
    function handleUploadSuccess(response, diskId) {
        console.log('Upload success for disk:', diskId, response);

        // Hide progress
        hideUploadProgress(diskId);

        if (response.success) {
            // Show success message
            showNotification('success', response.message);

            // Refresh file list
            loadFiles(diskId);

            // Clear file input
            clearFileInput(diskId);

        } else {
            showNotification('error', response.message || 'Upload failed');
        }
    }

    /**
     * Handle upload error
     */
    function handleUploadError(xhr, diskId) {
        console.error('Upload error for disk:', diskId, xhr);

        // Hide progress
        hideUploadProgress(diskId);

        let errorMessage = 'Upload failed';

        if (xhr.responseJSON) {
            errorMessage = xhr.responseJSON.message || errorMessage;
        }

        showNotification('error', errorMessage);

        // Clear file input
        clearFileInput(diskId);
    }

    /**
     * Hide upload progress
     */
    function hideUploadProgress(diskId) {
        const templateDiskId = diskToGridMap[diskId] || diskId;
        const $progressContainer = $(`#${templateDiskId}ProgressContainer`);

        // Hide progress container
        $progressContainer.hide();

        // Reset progress bar
        const $progressBar = $(`#${templateDiskId}ProgressBar`);
        $progressBar.css('width', '0%');
        $progressBar.attr('aria-valuenow', 0);
        $progressBar.text('0%');
    }

    /**
     * Clear file input
     */
    function clearFileInput(diskId) {
        const templateDiskId = diskToGridMap[diskId] || diskId;
        $(`#${templateDiskId}FileInput`).val('');
    }

    /**
     * Show notification message
     * @param {string} type - 'success' or 'error'
     * @param {string} message - The message to display
     * @param {boolean} persistent - Whether the notification should stay visible (default: false)
     */
    function showNotification(type, message, persistent = false) {
        // Create notification element
        const notification = $(`
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

        // Add to notification container (create if doesn't exist)
        let $container = $('#notification-container');
        if ($container.length === 0) {
            $container = $('<div id="notification-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;"></div>');
            $('body').append($container);
        }

        $container.prepend(notification);

        // Auto-hide after 5 seconds only if not persistent
        if (!persistent) {
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        }
    }

    /**
     * Header upload progress functions
     */
    function showHeaderUploadProgress(progress) {
        const $progressContainer = $('#headerProgressContainer');
        const $progressBar = $('#headerProgressBar');

        // Show progress container
        $progressContainer.show();

        // Update progress bar
        $progressBar.css('width', progress + '%');
        $progressBar.attr('aria-valuenow', progress);
    }

    function updateHeaderUploadProgress(progress) {
        showHeaderUploadProgress(progress);
    }

    function hideHeaderUploadProgress() {
        const $progressContainer = $('#headerProgressContainer');
        const $progressBar = $('#headerProgressBar');

        // Hide progress container after a short delay
        setTimeout(() => {
            $progressContainer.hide();
            $progressBar.css('width', '0%');
            $progressBar.attr('aria-valuenow', 0);
        }, 1000);
    }

    function handleHeaderUploadSuccess(response, diskId) {
        console.log('Header upload success for disk:', diskId, response);

        // Hide progress
        hideHeaderUploadProgress();

        if (response.success) {
            // Show success message
            showNotification('success', response.message);

            // Refresh file list for current disk
            loadFiles(diskId);

        } else {
            showNotification('error', response.message || 'Upload failed');
        }
    }

    function handleHeaderUploadError(xhr, diskId) {
        console.error('Header upload error for disk:', diskId, xhr);

        // Hide progress
        hideHeaderUploadProgress();

        let errorMessage = 'Upload failed';

        if (xhr.responseJSON) {
            errorMessage = xhr.responseJSON.message || errorMessage;
        }

        showNotification('error', errorMessage);
    }

    // Upload files to current folder/disk (legacy wrapper)
    function uploadFilesToCurrentFolder(files) {
        const uploadPath = currentPath || '/';
        return unifiedUpload(files, currentDisk, uploadPath, {
            showProgress: false, // This function doesn't show header progress
            onSuccess: function(response) {
                console.log(`Successfully uploaded ${response.files?.length || files.length} files`);
                loadFiles(currentDisk); // Refresh current view
                $('#generalFileInput').val(''); // Reset file input
            },
            onError: function(xhr, errorMessage) {
                alert(errorMessage);
                $('#generalFileInput').val(''); // Reset file input
            }
        });
    }

    // Show create folder modal
    function showCreateFolderModal() {
        const folderName = prompt('Enter folder name:');
        if (folderName && folderName.trim()) {
            createFolder(folderName.trim());
        }
    }

    // Create folder function
    function createFolder(folderName) {
        $.ajax({
            url: '{{ route("admin.media-manager.create-folder") }}',
            method: 'POST',
            data: {
                disk: currentDisk,
                folder: currentFolder,
                name: folderName,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(response) {
            console.log('Folder created successfully:', response);
            loadFiles(currentDisk); // Refresh file list
        })
        .fail(function(xhr) {
            console.error('Folder creation failed:', xhr.responseText);
            alert('Failed to create folder: ' + (xhr.responseJSON?.message || 'Unknown error'));
        });
    }

    // Delete selected files
    function deleteSelectedFiles() {
        if (selectedFiles.length === 0) {
            alert('No files selected for deletion');
            return;
        }

        if (!confirm(`Are you sure you want to delete ${selectedFiles.length} selected item(s)?`)) {
            return;
        }

        // Show progress
        const totalFiles = selectedFiles.length;
        let deletedCount = 0;
        let errors = [];

        // Delete each file individually
        const deletePromises = selectedFiles.map(file => {
            return $.ajax({
                url: `/admin/media-manager/delete/${encodeURIComponent(file)}`,
                method: 'DELETE',
                data: {
                    disk: currentDisk,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            })
            .done(function(response) {
                deletedCount++;
                console.log(`File deleted successfully: ${file}`);
            })
            .fail(function(xhr) {
                console.error(`Delete failed for file: ${file}`, xhr.responseText);
                errors.push(`${file}: ${xhr.responseJSON?.error || 'Unknown error'}`);
            });
        });

        // Wait for all deletion requests to complete
        Promise.allSettled(deletePromises).then(() => {
            selectedFiles = [];
            loadFiles(currentDisk); // Refresh file list

            // Show results
            if (errors.length > 0) {
                alert(`Deletion completed with errors:\n${errors.join('\n')}`);
            } else {
                console.log(`Successfully deleted ${deletedCount} files`);
            }
        });
    }

    // Initialize when document is ready
    $(document).ready(function() {
        console.log('Document ready - initializing Media Manager');

        // Set initial tab content visibility
        showActiveTabContent(currentDisk);

        // Initialize breadcrumbs and navigation
        updateBreadcrumbs(currentDisk, 'media', '/');
        bindBreadcrumbNavigation();

        // Initialize view toggle functionality
        initializeViewToggle();

        // Ensure sidebar shows root as selected initially
        updateSidebarSelection('root');
    });

    // VIEW TOGGLE FUNCTIONALITY
    function getStoredViewMode() {
        return localStorage.getItem('mediaManagerViewMode') || 'grid';
    }

    function initializeViewToggle() {
        // Load stored view mode preference
        currentViewMode = getStoredViewMode();

        // Bind view toggle button events
        $('#gridViewBtn').on('click', function() {
            setViewMode('grid');
        });

        $('#listViewBtn').on('click', function() {
            setViewMode('list');
        });

        // Set initial view mode
        setViewMode(currentViewMode);
    }

    function setViewMode(mode) {
        currentViewMode = mode;

        // Update button states
        $('.btn[data-view]').removeClass('active');
        $(`[data-view="${mode}"]`).addClass('active');

        // Update all folder grids with new view mode
        $('.media-folders-grid').removeClass('view-grid view-list').addClass(`view-${mode}`);

        // For list view, remove Bootstrap row class and add it back for grid view
        if (mode === 'list') {
            $('.media-folders-grid').removeClass('row');
        } else {
            $('.media-folders-grid').addClass('row');
        }

        // Store preference in localStorage
        localStorage.setItem('mediaManagerViewMode', mode);

        console.log(`View mode changed to: ${mode}`);
    }

    // FOLDER NAVIGATION FUNCTIONALITY
    function navigateToFolder(diskName, folderPath, folderName) {
        console.log(`Navigating to folder: ${folderName} at path: ${folderPath} on disk: ${diskName}`);

        // Update current state
        currentDisk = diskName;
        currentPath = folderPath;
        currentFolder = folderName;

        // Clear selected files
        selectedFiles = [];
        updateDeleteButtonState();

        // Update breadcrumbs for the specific folder
        updateBreadcrumbs(diskName, folderName, folderPath);

        // Update sidebar selection
        updateSidebarSelection(folderName);

        // Show loading state
        showLoadingState(diskName);

        // Load files for the selected folder
        loadFilesForPath(diskName, folderPath);
    }

    function getStoredViewMode() {
        return localStorage.getItem('mediaManagerViewMode') || 'grid';
    }
</script>

<!-- React Upload Modal will be loaded separately -->
