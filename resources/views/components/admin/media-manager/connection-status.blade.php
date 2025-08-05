<!-- S3 Connection Status Screen -->
<div id="s3ConnectionScreen" class="connection-screen" style="display: none;">
    <div class="connection-container">
        <div class="connection-header text-center mb-4">
            <div class="connection-icon">
                <i class="fab fa-aws fa-3x text-warning mb-3"></i>
            </div>
            <h3 class="connection-title">Amazon S3 Connection</h3>
            <p class="connection-subtitle text-muted">Configure your AWS S3 storage to access archived files</p>
        </div>

        <div class="connection-status-card">
            <div id="s3StatusIndicator" class="status-indicator">
                <div class="status-item">
                    <i class="fas fa-circle text-danger"></i>
                    <span class="status-text">Disconnected</span>
                </div>
            </div>

            <div class="connection-details mt-3">
                <div id="s3ConfigDetails" class="config-details">
                    <!-- Configuration details will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <div class="connection-actions mt-4">
            <div class="btn-group-vertical w-100">
                <button id="testS3Connection" class="btn btn-primary mb-2">
                    <i class="fas fa-plug mr-2"></i>Test Connection
                </button>
                <button id="refreshS3Status" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-sync mr-2"></i>Refresh Status
                </button>
                <a href="{{ route('admin.settings.storage') }}" class="btn btn-outline-info">
                    <i class="fas fa-cog mr-2"></i>Configure S3 Settings
                </a>
            </div>
        </div>

        <div class="connection-help mt-4">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle mr-2"></i>Configuration Required</h6>
                <p class="mb-2">To connect to Amazon S3, you need to configure the following settings:</p>
                <ul class="mb-0">
                    <li>AWS Access Key ID</li>
                    <li>AWS Secret Access Key</li>
                    <li>S3 Bucket Name</li>
                    <li>AWS Region</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Local Storage Connection Screen -->
<div id="localConnectionScreen" class="connection-screen" style="display: none;">
    <div class="connection-container">
        <div class="connection-header text-center mb-4">
            <div class="connection-icon">
                <i class="fas fa-hdd fa-3x text-danger mb-3"></i>
            </div>
            <h3 class="connection-title">Local Storage Issue</h3>
            <p class="connection-subtitle text-muted">There's an issue accessing the local storage disk</p>
        </div>

        <div class="connection-status-card">
            <div id="localStatusIndicator" class="status-indicator">
                <div class="status-item">
                    <i class="fas fa-circle text-danger"></i>
                    <span class="status-text">Storage Unavailable</span>
                </div>
            </div>

            <div class="connection-details mt-3">
                <div id="localErrorDetails" class="error-details">
                    <!-- Error details will be populated by JavaScript -->
                </div>
            </div>
        </div>

        <div class="connection-actions mt-4">
            <div class="btn-group-vertical w-100">
                <button id="testLocalConnection" class="btn btn-primary mb-2">
                    <i class="fas fa-sync mr-2"></i>Retry Connection
                </button>
                <button id="refreshLocalStatus" class="btn btn-outline-secondary">
                    <i class="fas fa-redo mr-2"></i>Refresh Status
                </button>
            </div>
        </div>

        <div class="connection-help mt-4">
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle mr-2"></i>Storage Issue</h6>
                <p class="mb-0">The local storage disk is not accessible. This could be due to permissions or disk space issues. Please contact your system administrator.</p>
            </div>
        </div>
    </div>
</div>

<style>
.connection-screen {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.connection-container {
    max-width: 500px;
    width: 100%;
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid #e3e6f0;
}

.connection-icon {
    opacity: 0.8;
}

.connection-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.connection-subtitle {
    font-size: 0.95rem;
}

.connection-status-card {
    background: #f8f9fc;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e3e6f0;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
}

.config-details {
    font-size: 0.9rem;
}

.config-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e3e6f0;
}

.config-item:last-child {
    border-bottom: none;
}

.config-value {
    font-family: monospace;
    background: #f1f3f4;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.85rem;
}

.connection-actions .btn {
    border-radius: 6px;
    font-weight: 500;
}

.connection-help .alert {
    border-radius: 8px;
    font-size: 0.9rem;
}

.connection-help ul {
    font-size: 0.85rem;
}

/* Animation for connection screen */
.connection-screen.show {
    animation: fadeInConnectionScreen 0.3s ease-out;
}

@keyframes fadeInConnectionScreen {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
