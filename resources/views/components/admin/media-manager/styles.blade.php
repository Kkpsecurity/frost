<style>
/* ===================================
   AdminLTE-Compatible Media Manager Styles
   =================================== */

/* Main Container - Using AdminLTE card styling */
.media-manager-container {
    background: transparent !important;
    border-radius: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    border: none !important;
    margin: 0 !important;
}

/* Content Card - Main AdminLTE Card */
.media-content-card {
    margin-bottom: 0 !important;
}

/* Tab Navigation - AdminLTE Nav Tabs */
.media-manager-container .nav-tabs {
    background: #fff !important;
    border-bottom: 1px solid #dee2e6 !important;
    margin-bottom: 0 !important;
}

.media-manager-container .nav-tabs .nav-link {
    color: #495057 !important;
    background: transparent !important;
    border: 1px solid transparent !important;
    border-radius: 0.25rem 0.25rem 0 0 !important;
    padding: 0.75rem 1rem !important;
    margin-bottom: -1px !important;
    transition: all 0.15s ease-in-out !important;
    font-weight: 500 !important;
}

.media-manager-container .nav-tabs .nav-link:hover {
    color: #6f42c1 !important;
    background: rgba(111, 66, 193, 0.05) !important;
    border-color: #e9ecef #e9ecef #dee2e6 !important;
    text-decoration: none !important;
}

.media-manager-container .nav-tabs .nav-link.active {
    color: #6f42c1 !important;
    background: #fff !important;
    border-color: #dee2e6 #dee2e6 #fff !important;
    font-weight: 600 !important;
}

.media-manager-container .nav-tabs .nav-link i {
    margin-right: 0.5rem !important;
}

/* Disk Status Indicators */
.disk-status-indicator {
    position: absolute !important;
    top: 8px !important;
    right: 8px !important;
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: #28a745 !important;
    border: 2px solid white !important;
    box-shadow: 0 0 3px rgba(0,0,0,0.2) !important;
}

.disk-status-indicator.disconnected {
    background: #dc3545 !important;
}

.disk-status-indicator.warning {
    background: #ffc107 !important;
}

/* Tab Content */
.media-manager-container .tab-content {
    background: #fff !important;
    border: 1px solid #dee2e6 !important;
    border-top: none !important;
    border-radius: 0 0 0.25rem 0.25rem !important;
    min-height: 500px !important;
}

.media-manager-container .tab-pane {
    padding: 1.5rem !important;
    min-height: 500px !important;
}

/* Ensure inactive tabs are hidden */
.media-manager-container .tab-pane:not(.active) {
    display: none !important;
}

.media-manager-container .tab-pane.active {
    display: block !important;
}

/* Upload Area - AdminLTE Card Style */
.media-manager-container .upload-area {
    border: 2px dashed #6f42c1 !important;
    border-radius: 0.25rem !important;
    padding: 3rem 2rem !important;
    text-align: center !important;
    background: #f8f9fa !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    margin-bottom: 1.5rem !important;
}

.media-manager-container .upload-area:hover {
    border-color: #563d7c !important;
    background: rgba(111, 66, 193, 0.05) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.media-manager-container .upload-area.dragover {
    border-color: #28a745 !important;
    background: rgba(40, 167, 69, 0.05) !important;
    transform: scale(1.02) !important;
}

.media-manager-container .upload-area i {
    color: #6c757d !important;
    margin-bottom: 1rem !important;
    transition: all 0.3s ease !important;
}

.media-manager-container .upload-area:hover i {
    color: #6f42c1 !important;
    transform: scale(1.1) !important;
}

.media-manager-container .upload-area.dragover i {
    color: #28a745 !important;
    transform: scale(1.2) !important;
}

.media-manager-container .upload-area h4 {
    color: #495057 !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
    font-size: 1.25rem !important;
}

.media-manager-container .upload-area p {
    color: #6c757d !important;
    margin-bottom: 1.5rem !important;
    font-size: 0.95rem !important;
}

/* Upload Button - AdminLTE Button Style */
.media-manager-container .upload-btn {
    background: linear-gradient(180deg, #6f42c1, #563d7c) !important;
    border: none !important;
    border-radius: 0.25rem !important;
    padding: 0.75rem 2rem !important;
    color: white !important;
    font-weight: 500 !important;
    font-size: 1rem !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 4px rgba(111, 66, 193, 0.3) !important;
}

.media-manager-container .upload-btn:hover {
    background: linear-gradient(180deg, #563d7c, #452a5c) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(111, 66, 193, 0.4) !important;
    color: white !important;
}

/* Media Grid - AdminLTE Gallery Style */
.media-manager-container .media-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
    gap: 1rem !important;
    padding: 0 !important;
}

/* Empty State - AdminLTE Card */
.media-manager-container .empty-state {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 300px !important;
    text-align: center !important;
    color: #6c757d !important;
    background: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    padding: 2rem !important;
}

.media-manager-container .empty-state i {
    font-size: 4rem !important;
    color: #dee2e6 !important;
    margin-bottom: 1rem !important;
}

.media-manager-container .empty-state h4 {
    color: #495057 !important;
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
    font-size: 1.25rem !important;
}

.media-manager-container .empty-state p {
    color: #6c757d !important;
    margin-bottom: 1.5rem !important;
    max-width: 400px !important;
}

/* File Items - AdminLTE Card Components */
.media-manager-container .media-item {
    background: #fff !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    padding: 1rem !important;
    transition: all 0.3s ease !important;
    position: relative !important;
    overflow: hidden !important;
}

.media-manager-container .media-item:hover {
    border-color: #6f42c1 !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    transform: translateY(-2px) !important;
}

.media-manager-container .media-item .file-icon {
    text-align: center !important;
    margin-bottom: 1rem !important;
}

.media-manager-container .media-item .file-icon i {
    font-size: 2.5rem !important;
    color: #6f42c1 !important;
    transition: all 0.3s ease !important;
}

.media-manager-container .media-item:hover .file-icon i {
    color: #563d7c !important;
    transform: scale(1.1) !important;
}

.media-manager-container .media-item .file-icon img {
    width: 100% !important;
    height: 80px !important;
    object-fit: cover !important;
    border-radius: 0.25rem !important;
    border: 1px solid #dee2e6 !important;
}

.media-manager-container .media-item .file-name {
    font-weight: 600 !important;
    color: #495057 !important;
    margin-bottom: 0.5rem !important;
    font-size: 0.9rem !important;
    line-height: 1.2 !important;
    text-align: center !important;
    word-break: break-word !important;
}

.media-manager-container .media-item .file-meta {
    font-size: 0.8rem !important;
    color: #6c757d !important;
    margin-bottom: 1rem !important;
    text-align: center !important;
}

.media-manager-container .media-item .file-meta .file-size {
    font-weight: 500 !important;
    color: #495057 !important;
}

.media-manager-container .media-item .file-actions {
    display: flex !important;
    gap: 0.25rem !important;
    justify-content: center !important;
    opacity: 0 !important;
    transition: all 0.3s ease !important;
}

.media-manager-container .media-item:hover .file-actions {
    opacity: 1 !important;
}

/* File Action Buttons - AdminLTE Button Style */
.media-manager-container .file-action-btn {
    padding: 0.25rem 0.5rem !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    background: #fff !important;
    color: #6c757d !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    font-size: 0.8rem !important;
}

.media-manager-container .file-action-btn:hover {
    background: #6f42c1 !important;
    border-color: #6f42c1 !important;
    color: white !important;
}

.media-manager-container .file-action-btn.delete:hover {
    background: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

/* Loading States - AdminLTE Style */
.media-manager-container .loading-indicator {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 300px !important;
    background: #f8f9fa !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
}

.media-manager-container .loading-spinner {
    text-align: center !important;
    padding: 2rem !important;
}

.media-manager-container .loading-spinner i {
    color: #6f42c1 !important;
    margin-bottom: 1rem !important;
}

.media-manager-container .loading-spinner p {
    color: #6c757d !important;
    font-weight: 500 !important;
}

/* Sidebar - AdminLTE Style */
.media-manager-container .media-sidebar {
    background: #fff !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    padding: 1rem !important;
    margin-bottom: 1rem !important;
}

.media-manager-container .sidebar-section h6 {
    color: #495057 !important;
    font-weight: 600 !important;
    margin-bottom: 0.75rem !important;
    padding-bottom: 0.5rem !important;
    border-bottom: 1px solid #dee2e6 !important;
}

/* Access Level Indicator */
.access-level-indicator {
    background: #e9ecef !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.25rem !important;
    padding: 0.5rem 1rem !important;
    margin-bottom: 1rem !important;
    font-size: 0.9rem !important;
}

.access-level-indicator i {
    color: #6f42c1 !important;
    margin-right: 0.5rem !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .media-manager-container .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
        gap: 0.75rem !important;
    }

    .media-manager-container .tab-pane {
        padding: 1rem !important;
    }

    .media-manager-container .upload-area {
        padding: 2rem 1rem !important;
    }

    .media-manager-container .media-item {
        padding: 0.75rem !important;
    }
}

@media (max-width: 576px) {
    .media-manager-container .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)) !important;
    }

    .media-manager-container .media-item .file-icon i {
        font-size: 2rem !important;
    }
}
</style>
