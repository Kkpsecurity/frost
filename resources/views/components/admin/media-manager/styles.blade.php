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

/* Breadcrumb Styles */
.media-manager-container .breadcrumb {
    background: transparent;
    padding: 0.25rem 0;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.media-manager-container .breadcrumb-link {
    color: #6c757d;
    text-decoration: none;
    cursor: pointer;
    transition: color 0.15s ease-in-out;
}

.media-manager-container .breadcrumb-link:hover {
    color: #007bff !important;
    text-decoration: underline;
}

.dark-mode .media-manager-container .breadcrumb-link {
    color: #adb5bd;
}

.dark-mode .media-manager-container .breadcrumb-link:hover {
    color: #6ea8fe !important;
}

/* Disk Status Indicators */
.disk-status-indicator {
    display: inline-block;
    margin-left: 0.25rem;
}

.disk-status-indicator.status-connected i {
    animation: none;
}

.disk-status-indicator.status-loading i {
    animation: fa-spin 2s infinite linear;
}

.disk-status-indicator.status-error i {
    animation: pulse-error 2s infinite;
}

@keyframes pulse-error {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* Tab Error States */
.nav-tabs .nav-link.text-danger {
    border-color: #dc3545 !important;
}

.nav-tabs .nav-link.text-danger:hover {
    border-color: #c82333 !important;
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.dark-mode .nav-tabs .nav-link.text-danger {
    color: #f1646a !important;
}

.dark-mode .nav-tabs .nav-link.text-danger:hover {
    color: #f5878c !important;
    background-color: rgba(241, 100, 106, 0.1) !important;
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
