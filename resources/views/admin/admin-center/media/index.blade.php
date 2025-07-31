@extends('adminlte::page')

@section('title', 'Media Manager')

@section('css')
    @vite('resources/css/admin.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Media Manager Styles */
        .media-tabs .nav-link {
            border: 1px solid #dee2e6;
            color: #495057;
            background-color: #f8f9fa;
            border-radius: 0;
        }
        
        .media-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .media-toolbar {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
        }
        
        .media-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .media-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .media-preview {
            height: 150px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .media-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        
        .media-preview .file-icon {
            font-size: 48px;
            color: #6c757d;
        }
        
        .media-info {
            padding: 10px;
        }
        
        .media-name {
            font-weight: 500;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .media-size {
            font-size: 0.85em;
            color: #6c757d;
        }
        
        .media-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
        }
        
        .media-item:hover .media-actions {
            display: block;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            margin: 20px;
            transition: border-color 0.3s;
        }
        
        .upload-area.dragover {
            border-color: #007bff;
            background: #e3f2fd;
        }
        
        .directory-tree {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .tree-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .tree-item:hover {
            background: #f8f9fa;
        }
        
        .tree-item.active {
            background: #e3f2fd;
            color: #007bff;
        }
    </style>
@stop
@section('content_header')
    <x-admin.widgets.admin-header />
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <!-- Tab Navigation -->
                    <x-admin.media-manager.partials.media-tabs />

                    <!-- Media Toolbar -->
                    <div class="media-toolbar">
                        <div class="row align-items-center">
                            <x-admin.media-manager.partials.media-toolbar />
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="mediaTabsContent">
                        <!-- Public Tab -->
                        <div class="tab-pane fade show active" id="public" role="tabpanel" aria-labelledby="public-tab">
                            <x-admin.media-manager.partials.public-media-disk id="publicDisk" />
                        </div>

                        <!-- Private Tab -->
                        <div class="tab-pane fade" id="private" role="tabpanel" aria-labelledby="private-tab">
                            <x-admin.media-manager.partials.private-media-disk id="privateDisk" />
                        </div>

                        <!-- S3 Tab -->
                        <div class="tab-pane fade" id="s3" role="tabpanel" aria-labelledby="s3-tab">
                            <x-admin.media-manager.partials.s3-media-disk id="s3Disk" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <x-admin.media-manager.modals.upload-modal />
@stop

@section('js')
    <script>
        let currentDisk = 'public'; // Start with public disk
        let currentPath = '/';
        let viewMode = 'grid';

        $(document).ready(function() {
            // Initialize
            loadFiles();
            setupEventHandlers();
        });

        function setupEventHandlers() {
            // Tab switching
            $('.nav-tabs a').on('click', function(e) {
                e.preventDefault();
                const targetTab = $(this).attr('href').substring(1);

                // Map tab names to actual disk names
                if (targetTab === 'public') {
                    currentDisk = 'public';
                } else if (targetTab === 'private') {
                    currentDisk = 'local'; // Private tab uses local disk
                } else if (targetTab === 's3') {
                    currentDisk = 'media_s3'; // S3 tab uses media_s3 disk
                }

                currentPath = '/';
                loadFiles();
            });

            // Upload button
            $('#uploadBtn').on('click', function() {
                $('#uploadModal').modal('show');
            });

            // File input change
            $('#fileInput').on('change', function() {
                if (this.files.length > 0) {
                    $('#uploadSubmit').prop('disabled', false);
                }
            });

            // Upload submit
            $('#uploadSubmit').on('click', function() {
                uploadFiles();
            });

            // Drag and drop
            $('.upload-area').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            $('.upload-area').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            $('.upload-area').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    handleFileUpload(files);
                }
            });

            // Click to upload
            $('.upload-area').on('click', function() {
                // Get the current active tab to determine which file input to trigger
                const activeTab = $('.nav-tabs .nav-link.active').attr('href').substring(1);
                $(`#${activeTab}FileInput`).click();
            });

            // Direct file input change
            $('[id$="FileInput"]').on('change', function() {
                if (this.files.length > 0) {
                    handleFileUpload(this.files);
                }
            });

            // Refresh button
            $('#refreshBtn').on('click', function() {
                loadFiles();
            });

            // View mode buttons
            $('#gridViewBtn').on('click', function() {
                viewMode = 'grid';
                $(this).addClass('active').siblings().removeClass('active');
                renderFiles();
            });

            $('#listViewBtn').on('click', function() {
                viewMode = 'list';
                $(this).addClass('active').siblings().removeClass('active');
                renderFiles();
            });

            // Search
            $('#searchInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            $('#searchBtn').on('click', function() {
                performSearch();
            });

            // Create folder
            $('#createFolderBtn').on('click', function() {
                createFolder();
            });
        }

        function loadFiles() {
            showLoading();

            $.ajax({
                url: '/admin/media-manager/files',
                method: 'GET',
                data: {
                    disk: currentDisk,
                    path: currentPath
                },
                success: function(response) {
                    if (response.success) {
                        renderFiles(response.files);
                        updateDirectoryTree(response.directories);
                    } else {
                        showNotification('Failed to load files: ' + response.error, 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Error loading files: ' + (xhr.responseJSON?.error || 'Unknown error'),
                        'error');
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
            } else if (currentDisk === 'media_s3') {
                gridSuffix = 's3';
            }

            const gridId = `${gridSuffix}Grid`;
            const $grid = $(`#${gridId}`);

            $grid.empty();

            if (files.length === 0) {
                $grid.html(`
                    <div class="col-12 text-center p-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No files found in this directory</p>
                        <button class="btn btn-primary" onclick="$('#uploadBtn').click()">
                            <i class="fas fa-upload mr-1"></i>Upload Files
                        </button>
                    </div>
                `);
                return;
            }

            files.forEach(file => {
                const fileHtml = createFileElement(file);
                $grid.append(fileHtml);
            });
        }

        function createFileElement(file) {
            const isImage = /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(file.name);
            const fileIcon = getFileIcon(file.name);

            return `
                <div class="media-item" data-file-id="${file.id}">
                    <div class="media-preview">
                        ${isImage 
                            ? `<img src="${file.url}" alt="${file.name}" loading="lazy">` 
                            : `<i class="file-icon ${fileIcon}"></i>`
                        }
                        <div class="media-actions">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary" onclick="previewFile('${file.id}')" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-success" onclick="downloadFile('${file.id}')" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-danger" onclick="deleteFile('${file.id}')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="media-info">
                        <div class="media-name" title="${file.name}">${file.name}</div>
                        <div class="media-size">${formatFileSize(file.size)}</div>
                    </div>
                </div>
            `;
        }

        function getFileIcon(filename) {
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

        function uploadFiles() {
            const formData = new FormData();
            const files = $('#fileInput')[0].files;

            if (files.length === 0) {
                showNotification('Please select files to upload', 'error');
                return;
            }

            Array.from(files).forEach(file => {
                formData.append('files[]', file);
            });

            formData.append('disk', currentDisk);
            formData.append('path', currentPath);
            formData.append('collection', $('#collectionInput').val() || 'uploads');
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $('#uploadProgress').show();
            $('#uploadSubmit').prop('disabled', true);

            $.ajax({
                url: '/admin/media-manager/upload',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            $('#uploadProgress .progress-bar').css('width', percentComplete + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Files uploaded successfully', 'success');
                        $('#uploadModal').modal('hide');
                        loadFiles();
                        resetUploadForm();
                    } else {
                        showNotification('Upload failed: ' + response.error, 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Upload failed: ' + (xhr.responseJSON?.error || 'Unknown error'), 'error');
                },
                complete: function() {
                    $('#uploadProgress').hide();
                    $('#uploadSubmit').prop('disabled', false);
                }
            });
        }

        function handleFileUpload(files) {
            // Auto-populate the upload modal with dropped files
            const dt = new DataTransfer();
            Array.from(files).forEach(file => dt.items.add(file));
            $('#fileInput')[0].files = dt.files;
            $('#uploadModal').modal('show');
        }

        function resetUploadForm() {
            $('#uploadForm')[0].reset();
            $('#uploadProgress .progress-bar').css('width', '0%');
            $('#uploadSubmit').prop('disabled', true);
        }

        function previewFile(fileId) {
            // Implementation for file preview
            showNotification('Preview functionality coming soon', 'info');
        }

        function downloadFile(fileId) {
            window.open(`/admin/media-manager/download/${fileId}`, '_blank');
        }

        function deleteFile(fileId) {
            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    url: `/admin/media-manager/delete/${fileId}`,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
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
                        showNotification('Delete failed: ' + (xhr.responseJSON?.error || 'Unknown error'),
                            'error');
                    }
                });
            }
        }

        function createFolder() {
            const folderName = prompt('Enter folder name:');
            if (folderName) {
                $.ajax({
                    url: '/admin/media-manager/create-folder',
                    method: 'POST',
                    data: {
                        disk: currentDisk,
                        path: currentPath,
                        name: folderName,
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
                        showNotification('Failed to create folder: ' + (xhr.responseJSON?.error ||
                            'Unknown error'), 'error');
                    }
                });
            }
        }

        function performSearch() {
            const query = $('#searchInput').val();
            // Implementation for search functionality
            showNotification('Search functionality coming soon', 'info');
        }

        function updateDirectoryTree(directories) {
            // Implementation for directory tree update
            // This would populate the directory trees with folder structure
        }

        function showLoading() {
            // Map disk names back to tab names for grid selection
            let gridSuffix;
            if (currentDisk === 'public') {
                gridSuffix = 'public';
            } else if (currentDisk === 'local') {
                gridSuffix = 'private';
            } else if (currentDisk === 'media_s3') {
                gridSuffix = 's3';
            }

            const $grid = $(`#${gridSuffix}Grid`);
            $grid.html(`
                <div class="col-12 text-center p-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="text-muted mt-2">Loading files...</p>
                </div>
            `);
        }

        function hideLoading() {
            // Loading is hidden when files are rendered
        }

        function showNotification(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' :
                type === 'error' ? 'alert-danger' :
                type === 'info' ? 'alert-info' : 'alert-warning';
            const icon = type === 'success' ? 'fa-check' :
                type === 'error' ? 'fa-exclamation-triangle' :
                type === 'info' ? 'fa-info-circle' : 'fa-exclamation';

            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} mr-2"></i>${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;

            // Remove existing alerts
            $('.alert').remove();

            // Add new alert at the top
            $('.container-fluid').prepend(alertHtml);

            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
@stop
