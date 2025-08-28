<div class="course-details-header mb-4">
    <div class="d-flex align-items-center mb-3">
        <div class="course-icon-large me-3">
            <i class="{{ $course['icon'] ?? 'fas fa-shield-alt' }} fa-3x text-info"></i>
        </div>
        <div>
            <div class="course-badge-large mb-2">
                <span class="badge bg-info fs-6 text-dark">{{ $course['badge'] ?? 'Security Course' }}</span>
                @if ($course['popular'] ?? false)
                    <span class="badge bg-warning text-dark ms-2">Most Popular</span>
                @endif
            </div>
            <h1 class="course-title-large mb-2 text-white">{{ $course['title'] ?? 'Security Training Course' }}</h1>
            <p class="course-type-large text-white-50">
                {{ $course['type'] ?? 'Professional Security Training' }}</p>
        </div>
    </div>

    {{-- Course Meta Information --}}
    <div class="course-meta-details row g-3 p-3 rounded"
        style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
        <div class="col-md-3 col-sm-6">
            <div class="meta-item text-center">
                <i class="fas fa-clock text-info mb-2"></i>
                <div class="meta-label text-white-50">Duration</div>
                <div class="meta-value text-white">{{ $course['duration'] ?? '3-5 Days' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="meta-item text-center">
                <i class="fas fa-laptop text-info mb-2"></i>
                <div class="meta-label text-white-50">Format</div>
                <div class="meta-value text-white">{{ $course['format'] ?? 'Online + Live' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="meta-item text-center">
                <i class="fas fa-certificate text-info mb-2"></i>
                <div class="meta-label text-white-50">Certification</div>
                <div class="meta-value text-white">{{ $course['certification'] ?? 'State Approved' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="meta-item text-center">
                <i class="fas fa-users text-info mb-2"></i>
                <div class="meta-label text-white-50">Class Size</div>
                <div class="meta-value text-white">{{ $course['classSize'] ?? '12 Students Max' }}</div>
            </div>
        </div>
    </div>
</div>
