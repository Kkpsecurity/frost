<style>
/* ===================================
   AdminLTE-Compatible Media Manager Styles
   Dark Mode Support
   =================================== */

/* Main Container */
.media-manager-container {
    background: transparent;
    border-radius: 0;
    padding: 0;
    box-shadow: none;
    border: none;
    margin: 0;
}

/* Tab Content Management */
.media-manager-container .tab-pane:not(.active) {
    display: none !important;
}

.media-manager-container .tab-pane.active {
    display: block !important;
}

/* Dark Mode Adjustments */
.dark-mode .media-manager-container .nav-tabs {
    background: var(--bs-dark, #343a40) !important;
    border-bottom-color: var(--bs-gray-700, #495057) !important;
}

.dark-mode .media-manager-container .nav-tabs .nav-link {
    color: var(--bs-gray-300, #dee2e6) !important;
    border-color: transparent !important;
}

.dark-mode .media-manager-container .nav-tabs .nav-link:hover {
    color: var(--bs-primary, #6ea8fe) !important;
    background: rgba(110, 168, 254, 0.1) !important;
    border-color: var(--bs-gray-600, #6c757d) !important;
}

.dark-mode .media-manager-container .nav-tabs .nav-link.active {
    color: var(--bs-primary, #6ea8fe) !important;
    background: var(--bs-gray-800, #495057) !important;
    border-color: var(--bs-gray-700, #495057) var(--bs-gray-700, #495057) var(--bs-gray-800, #495057) !important;
}

.dark-mode .media-manager-container .tab-content {
    background: var(--bs-gray-800, #495057) !important;
    border-color: var(--bs-gray-700, #495057) !important;
}

.dark-mode .media-manager-container .upload-area {
    background: var(--bs-gray-800, #495057) !important;
    border-color: var(--bs-gray-600, #6c757d) !important;
}

.dark-mode .media-manager-container .upload-area:hover {
    border-color: var(--bs-primary, #6ea8fe) !important;
    background: rgba(110, 168, 254, 0.05) !important;
}

.dark-mode .media-manager-container .upload-area h4 {
    color: var(--bs-gray-100, #f8f9fa) !important;
}

.dark-mode .media-manager-container .upload-area p {
    color: var(--bs-gray-400, #adb5bd) !important;
}

.dark-mode .media-manager-container .upload-area i {
    color: var(--bs-gray-400, #adb5bd) !important;
}

.dark-mode .media-manager-container .upload-area:hover i {
    color: var(--bs-primary, #6ea8fe) !important;
}

.dark-mode .media-manager-container .empty-state {
    background: var(--bs-gray-800, #495057) !important;
    border-color: var(--bs-gray-700, #495057) !important;
    color: var(--bs-gray-400, #adb5bd) !important;
}

.dark-mode .media-manager-container .empty-state h4 {
    color: var(--bs-gray-100, #f8f9fa) !important;
}

.dark-mode .media-manager-container .empty-state i {
    color: var(--bs-gray-600, #6c757d) !important;
}

.dark-mode .media-manager-container .media-item {
    background: var(--bs-gray-700, #495057) !important;
    border-color: var(--bs-gray-600, #6c757d) !important;
}

.dark-mode .media-manager-container .media-item:hover {
    border-color: var(--bs-primary, #6ea8fe) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.5) !important;
}

.dark-mode .media-manager-container .media-item .file-name {
    color: var(--bs-gray-100, #f8f9fa) !important;
}

.dark-mode .media-manager-container .media-item .file-meta {
    color: var(--bs-gray-400, #adb5bd) !important;
}

.dark-mode .media-manager-container .media-item .file-meta .file-size {
    color: var(--bs-gray-300, #dee2e6) !important;
}

.dark-mode .media-manager-container .file-action-btn {
    background: var(--bs-gray-600, #6c757d) !important;
    border-color: var(--bs-gray-500, #adb5bd) !important;
    color: var(--bs-gray-300, #dee2e6) !important;
}

.dark-mode .media-manager-container .file-action-btn:hover {
    background: var(--bs-primary, #6ea8fe) !important;
    border-color: var(--bs-primary, #6ea8fe) !important;
}

.dark-mode .media-manager-container .loading-indicator {
    background: var(--bs-gray-800, #495057) !important;
    border-color: var(--bs-gray-700, #495057) !important;
}

.dark-mode .media-manager-container .loading-spinner i {
    color: var(--bs-primary, #6ea8fe) !important;
}

.dark-mode .media-manager-container .loading-spinner p {
    color: var(--bs-gray-400, #adb5bd) !important;
}

.dark-mode .media-manager-container .media-sidebar {
    background: var(--bs-gray-700, #495057) !important;
    border-color: var(--bs-gray-600, #6c757d) !important;
}

.dark-mode .media-manager-container .sidebar-section h6 {
    color: var(--bs-gray-200, #e9ecef) !important;
    border-bottom-color: var(--bs-gray-600, #6c757d) !important;
}

/* Badge adjustments for dark mode */
.dark-mode .badge-primary {
    background-color: var(--bs-primary, #6ea8fe) !important;
    color: var(--bs-dark, #212529) !important;
}

.dark-mode .badge-secondary {
    background-color: var(--bs-gray-600, #6c757d) !important;
    color: var(--bs-gray-100, #f8f9fa) !important;
}

/* Icon color adjustments */
.dark-mode .text-primary {
    color: var(--bs-primary, #6ea8fe) !important;
}

.dark-mode .text-muted {
    color: var(--bs-gray-400, #adb5bd) !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .media-manager-container .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
    }
    
    .media-manager-container .nav-tabs .nav-link small {
        font-size: 0.7rem !important;
    }
}

@media (max-width: 576px) {
    .media-manager-container .nav-tabs .nav-link small {
        display: none !important;
    }
}
</style>
