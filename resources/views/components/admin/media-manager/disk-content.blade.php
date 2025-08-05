@props(['diskId', 'diskName', 'icon', 'isActive' => false])

<!-- {{ ucfirst($diskName) }} Disk -->
<div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="{{ $diskId }}" role="tabpanel" style="{{ !$isActive ? 'display: none;' : '' }}">
    <!-- Loading indicator -->
    <div class="loading-indicator" id="{{ $diskId }}Loading" style="display: none;">
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
            <p class="mt-3 text-muted">Loading {{ $diskName }} files...</p>
        </div>
    </div>

    <!-- Upload Drop Area -->
    <div class="upload-area" id="{{ $diskId }}UploadArea" style="display: none;">
        <div class="upload-content">
            <i class="{{ $icon }} fa-4x mb-3"></i>
            <h4 class="mb-2">Drop files here or click to upload</h4>
            <p class="text-muted mb-3">{{ $slot ?? 'Supports images, documents, and other file types accessible to all users' }}</p>
            <button class="btn btn-primary upload-btn" onclick="$('#{{ $diskId }}FileInput').click()">
                <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Files
            </button>
        </div>
        <input type="file" id="{{ $diskId }}FileInput" multiple style="display: none;">
    </div>

    <!-- Empty State -->
    <div class="empty-state" id="{{ $diskId }}EmptyState" style="display: none;">
        <i class="{{ $icon }} fa-4x mb-3"></i>
        <h4 class="mb-2">No files found</h4>
        <p class="text-muted mb-3">Start by uploading some files to this storage location</p>
        <button class="btn btn-primary" onclick="$('#{{ $diskId }}FileInput').click()">
            <i class="fas fa-plus mr-2"></i>Upload First File
        </button>
    </div>

    <!-- Files Grid - AdminLTE Gallery Style -->
    <div class="row media-grid" id="{{ $diskId }}Grid" style="display: none;">
        <!-- Files will be loaded here dynamically -->
    </div>
</div>
