{{-- Profile Section --}}
<div class="profile-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-user-circle me-2"></i> Profile Information
    </h3>

    {{-- Profile Avatar --}}
    <div class="mb-5 pb-4 border-bottom border-secondary">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            @if ($data['avatar']['current_avatar'])
                <img src="{{ $data['avatar']['current_avatar'] }}" alt="{{ $data['basic_info']['full_name'] }}"
                    class="rounded-circle"
                    style="width: 100px; height: 100px; object-fit: cover; border: 4px solid rgba(52, 152, 219, 0.3);">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 4px solid rgba(52, 152, 219, 0.3);">
                    <i class="fas fa-user fa-3x"></i>
                </div>
            @endif
            <div class="flex-grow-1">
                <h4 class="text-white mb-2 fw-bold">{{ $data['basic_info']['full_name'] }}</h4>
                <p class="text-white-50 mb-2 d-flex align-items-center">
                    <i class="fas fa-graduation-cap me-2"></i>
                    {{ $data['basic_info']['role'] }}
                </p>
                @if ($data['is_active'])
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>Active
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>Inactive
                    </span>
                @endif
            </div>
            <div>
                <button type="button" class="btn btn-outline-light px-4" data-bs-toggle="modal"
                    data-bs-target="#avatarUploadModal">
                    <i class="fas fa-camera me-2"></i>Change Photo
                </button>
            </div>
        </div>
    </div>

    {{-- Basic Information --}}
    <form action="{{ route('account.profile.update') }}" method="POST" class="profile-form">
        @csrf

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-user me-2"></i>First Name
                </label>
                <input type="text" name="first_name" class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['basic_info']['first_name'] }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-user me-2"></i>Last Name
                </label>
                <input type="text" name="last_name" class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['basic_info']['last_name'] }}" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-white-50">
                <i class="fas fa-envelope me-2"></i>Email Address
            </label>
            <input type="email" name="email" class="form-control p-2 bg-dark text-white border-secondary"
                value="{{ $data['basic_info']['email'] }}" required>
            @if ($data['email_verified'])
                <small class="text-success d-flex align-items-center mt-2">
                    <i class="fas fa-check-circle me-1"></i>Verified
                </small>
            @else
                <small class="text-warning d-flex align-items-center mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>Not Verified
                </small>
            @endif
        </div>

        {{-- Student Info --}}
        <h5 class="text-white mb-3 mt-5">
            <i class="fas fa-info-circle me-2"></i>Additional Information
        </h5>
        <div class="row g-4 mb-4">
            <div class="col-md-2 col-sm-6">
                <label class="form-label text-white-50">Initials</label>
                <input type="text" name="student_info[initials]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['initials'] ?? '' }}" maxlength="5">
                <small class="text-white-50">Middle initial</small>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label text-white-50">Suffix</label>
                <input type="text" name="student_info[suffix]"
                    class="form-control bg-dark p-2 text-white border-secondary"
                    value="{{ $data['student_info']['suffix'] ?? '' }}" maxlength="10">
                <small class="text-white-50">Jr, Sr, III</small>
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-calendar me-2"></i>Date of Birth
                </label>
                <input type="date" name="student_info[dob]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['dob'] ?? '' }}">
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-phone me-2"></i>Phone Number
                </label>
                <input type="tel" name="student_info[phone]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['phone'] ?? '' }}" placeholder="(123) 456-7890">
            </div>
        </div>

        {{-- Account Info --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-4 mt-4">
                <i class="fas fa-id-card me-2"></i>Account Information
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-label">Member Since</div>
                        <div class="info-card-value">
                            <i class="fas fa-clock me-2"></i>{{ $data['basic_info']['member_since'] }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-label">Last Login</div>
                        <div class="info-card-value">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ $data['basic_info']['last_login'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 flex-wrap mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Save Changes
            </button>
            <button type="reset" class="btn btn-outline-secondary text-white px-4">
                <i class="fas fa-undo me-2"></i>Reset
            </button>
        </div>
    </form>

    {{-- Avatar Upload Modal (Outside main form) --}}
    <div class="modal fade" id="avatarUploadModal" tabindex="-1" aria-labelledby="avatarUploadModalLabel"
        aria-hidden="true" style="z-index: 99999999 !important;">
        <div class="modal-dialog modal-dialog-centered" style="z-index: 99999999 !important;">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="avatarUploadModalLabel">
                        <i class="fas fa-camera me-2"></i>Update Profile Photo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Current Avatar Preview --}}
                    @if ($data['avatar']['current_avatar'])
                        <div class="text-center mb-4">
                            <p class="text-white-50 mb-2">Current Photo:</p>
                            <img src="{{ $data['avatar']['current_avatar'] }}" alt="Current Avatar"
                                class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(52, 152, 219, 0.3);">
                            @if ($data['avatar']['has_custom_avatar'])
                                <p class="text-success mt-2 mb-0"><i class="fas fa-check-circle me-1"></i>Custom
                                    Upload</p>
                            @elseif($data['avatar']['use_gravatar'])
                                <p class="text-info mt-2 mb-0"><i class="fas fa-globe me-1"></i>Gravatar</p>
                            @else
                                <p class="text-white-50 mt-2 mb-0"><i class="fas fa-user me-1"></i>Generated
                                    Avatar</p>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('account.profile.update') }}" method="POST"
                        enctype="multipart/form-data" id="avatarUploadForm">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-white-50">
                                <i class="fas fa-upload me-2"></i>Upload New Photo
                            </label>
                            <input type="file" name="avatar" id="avatarInput"
                                class="form-control bg-dark text-white border-secondary"
                                accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-white-50 mt-2 d-block">
                                Accepted formats: JPG, PNG, GIF (max 2MB)
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="useGravatar" name="use_gravatar"
                                    {{ $data['avatar']['use_gravatar'] ? 'checked' : '' }}>
                                <label class="form-check-label text-white" for="useGravatar">
                                    <i class="fas fa-globe me-2"></i>Use Gravatar instead
                                </label>
                            </div>
                            <small class="text-white-50 ms-4 ps-2">
                                Gravatar uses your email to display a profile picture from
                                <a href="https://gravatar.com" target="_blank" class="text-info">gravatar.com</a>
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="avatarUploadForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Photo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fix modal z-index when shown
            const avatarModal = document.getElementById('avatarUploadModal');
            if (avatarModal) {
                avatarModal.addEventListener('show.bs.modal', function() {
                    // Set modal z-index
                    this.style.zIndex = '99999999';

                    // Find and set backdrop z-index after a short delay
                    setTimeout(function() {
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.style.zIndex = '99999998';
                        }
                    }, 10);
                });

                avatarModal.addEventListener('shown.bs.modal', function() {
                    // Double-check z-index after modal is fully shown
                    this.style.zIndex = '99999999';
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.style.zIndex = '99999998';
                    }
                });
            }
        });
    </script>
@endpush
