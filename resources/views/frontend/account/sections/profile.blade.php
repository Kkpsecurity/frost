{{-- Profile Section --}}
<div class="profile-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-user-circle me-2"></i> {{ __('frontend.account.profile_information') }}
    </h3>

    {{-- Profile Avatar --}}
    <div class="mb-5 pb-4 border-bottom border-secondary">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            @if ($data['avatar']['current_avatar'])
                @php
                    $gravatarHash = md5(strtolower(trim($data['basic_info']['email'] ?? '')));
                    $gravatarUrl = "https://www.gravatar.com/avatar/{$gravatarHash}?s=220&d=identicon&r=pg";
                @endphp

                <img id="profileAvatarImg" src="{{ $data['avatar']['current_avatar'] }}"
                    data-original-src="{{ $data['avatar']['current_avatar'] }}" data-gravatar-src="{{ $gravatarUrl }}"
                    alt="{{ $data['basic_info']['full_name'] }}" class="rounded-circle"
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
                <p class="mb-2">
                    @if ($data['avatar']['has_custom_avatar'])
                        <span class="badge bg-success"><i
                                class="fas fa-check-circle me-1"></i>{{ __('frontend.account.custom_upload') }}</span>
                    @elseif($data['avatar']['use_gravatar'])
                        <span class="badge bg-info text-dark"><i
                                class="fas fa-globe me-1"></i>{{ __('frontend.account.gravatar') }}</span>
                    @else
                        <span class="badge bg-secondary"><i
                                class="fas fa-user me-1"></i>{{ __('frontend.account.generated_avatar') }}</span>
                    @endif
                </p>
                @if ($data['is_active'])
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>{{ __('frontend.account.active') }}
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>{{ __('frontend.account.inactive') }}
                    </span>
                @endif
            </div>
            <div>
                <button id="changePhotoBtn" type="button" class="btn btn-outline-light px-4" data-bs-toggle="modal"
                    data-bs-target="#avatarUploadModal">
                    <i class="fas fa-camera me-2"></i>{{ __('frontend.account.change_photo') }}
                </button>

                <div class="mt-3">
                    <form action="{{ route('account.avatar.update') }}" method="POST" id="gravatarToggleForm">
                        @csrf
                        <div class="form-check form-switch">
                            <input type="hidden" name="use_gravatar" value="0">
                            <input class="form-check-input" type="checkbox" id="useGravatarToggle" name="use_gravatar"
                                value="1" {{ $data['avatar']['use_gravatar'] ? 'checked' : '' }}>
                            <label class="form-check-label text-white" for="useGravatarToggle">
                                <i class="fas fa-globe ms-2 me-2"></i>{{ __('frontend.account.use_gravatar') }}
                            </label>
                        </div>
                        <small class="text-white-50 d-block ms-4 ps-2">
                            {!! __('frontend.account.use_gravatar_hint') !!}
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Basic Information --}}
    <form action="{{ route('account.profile.update') }}" method="POST" class="profile-form">
        @csrf

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-user me-2"></i>{{ __('frontend.account.first_name') }}
                </label>
                <input type="text" name="first_name" class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['basic_info']['first_name'] }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-user me-2"></i>{{ __('frontend.account.last_name') }}
                </label>
                <input type="text" name="last_name" class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['basic_info']['last_name'] }}" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label text-white-50">
                <i class="fas fa-envelope me-2"></i>{{ __('frontend.account.email_address') }}
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
            <i class="fas fa-info-circle me-2"></i>{{ __('frontend.account.additional_information') }}
        </h5>
        <div class="row g-4 mb-4">
            <div class="col-md-2 col-sm-6">
                <label class="form-label text-white-50">{{ __('frontend.account.initials') }}</label>
                <input type="text" name="student_info[initials]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['initials'] ?? '' }}" maxlength="5">
                <small class="text-white-50">{{ __('frontend.account.initials_hint') }}</small>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label text-white-50">{{ __('frontend.account.suffix') }}</label>
                <input type="text" name="student_info[suffix]"
                    class="form-control bg-dark p-2 text-white border-secondary"
                    value="{{ $data['student_info']['suffix'] ?? '' }}" maxlength="10">
                <small class="text-white-50">{{ __('frontend.account.suffix_hint') }}</small>
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-calendar me-2"></i>{{ __('frontend.account.date_of_birth') }}
                </label>
                <input type="date" name="student_info[dob]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['dob'] ?? '' }}">
            </div>
            <div class="col-md-4 col-sm-6">
                <label class="form-label text-white-50">
                    <i class="fas fa-phone me-2"></i>{{ __('frontend.account.phone_number') }}
                </label>
                <input type="tel" name="student_info[phone]"
                    class="form-control p-2 bg-dark text-white border-secondary"
                    value="{{ $data['student_info']['phone'] ?? '' }}" placeholder="(123) 456-7890">
            </div>
        </div>

        {{-- Account Info --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-4 mt-4">
                <i class="fas fa-id-card me-2"></i>{{ __('frontend.account.account_information') }}
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-label">{{ __('frontend.account.member_since') }}</div>
                        <div class="info-card-value">
                            <i class="fas fa-clock me-2"></i>{{ $data['basic_info']['member_since'] }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-label">{{ __('frontend.account.last_login') }}</div>
                        <div class="info-card-value">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ $data['basic_info']['last_login'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 flex-wrap mt-4">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>{{ __('frontend.account.save_changes') }}
            </button>
            <button type="reset" class="btn btn-outline-secondary text-white px-4">
                <i class="fas fa-undo me-2"></i>{{ __('frontend.account.reset') }}
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
                        <i class="fas fa-camera me-2"></i>{{ __('frontend.account.update_profile_photo') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Current Avatar Preview --}}
                    @if ($data['avatar']['current_avatar'])
                        <div class="text-center mb-4">
                            <p class="text-white-50 mb-2">{{ __('frontend.account.current_photo') }}</p>
                            <img src="{{ $data['avatar']['current_avatar'] }}" alt="Current Avatar"
                                class="rounded-circle"
                                style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(52, 152, 219, 0.3);">
                            @if ($data['avatar']['has_custom_avatar'])
                                <p class="text-success mt-2 mb-0"><i
                                        class="fas fa-check-circle me-1"></i>{{ __('frontend.account.custom_upload') }}
                                </p>
                            @elseif($data['avatar']['use_gravatar'])
                                <p class="text-info mt-2 mb-0"><i
                                        class="fas fa-globe me-1"></i>{{ __('frontend.account.gravatar') }}</p>
                            @else
                                <p class="text-white-50 mt-2 mb-0"><i
                                        class="fas fa-user me-1"></i>{{ __('frontend.account.generated_avatar') }}</p>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('account.avatar.update') }}" method="POST" enctype="multipart/form-data"
                        id="avatarUploadForm">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-white-50">
                                <i class="fas fa-upload me-2"></i>{{ __('frontend.account.upload_new_photo') }}
                            </label>
                            <input type="file" name="avatar" id="avatarInput"
                                class="form-control bg-dark text-white border-secondary"
                                accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="text-white-50 mt-2 d-block">
                                {{ __('frontend.account.upload_formats_hint') }}
                            </small>

                            <div class="mt-3 d-none" id="avatarPreviewWrap">
                                <p class="text-white-50 mb-2">{{ __('frontend.account.preview') }}</p>
                                <img id="avatarPreview" alt="Avatar preview" class="rounded-circle"
                                    style="width: 80px; height: 80px; object-fit: cover; border: 3px solid rgba(52, 152, 219, 0.3);">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('frontend.account.cancel') }}</button>
                    @if ($data['avatar']['has_custom_avatar'] || $data['avatar']['use_gravatar'])
                        <button type="submit" form="avatarUploadForm" class="btn btn-outline-danger"
                            id="removeAvatarBtn" name="clear_avatar" value="1">
                            <i class="fas fa-trash me-2"></i>{{ __('frontend.account.remove_photo') }}
                        </button>
                    @endif
                    <button type="submit" form="avatarUploadForm" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ __('frontend.account.save_photo') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // In-page Gravatar toggle: show preview immediately and persist to backend.
            const gravatarToggle = document.getElementById('useGravatarToggle');
            const gravatarToggleForm = document.getElementById('gravatarToggleForm');
            const profileAvatarImg = document.getElementById('profileAvatarImg');

            if (gravatarToggle && gravatarToggleForm) {
                gravatarToggle.addEventListener('change', function() {
                    const gravatarOn = gravatarToggle.checked;

                    if (profileAvatarImg) {
                        const gravatarSrc = profileAvatarImg.getAttribute('data-gravatar-src');
                        const originalSrc = profileAvatarImg.getAttribute('data-original-src');

                        if (gravatarOn && gravatarSrc) {
                            profileAvatarImg.src = gravatarSrc;
                        } else if (!gravatarOn && originalSrc) {
                            profileAvatarImg.src = originalSrc;
                        }
                    }

                    gravatarToggleForm.submit();
                });
            }

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

            // Avatar modal UX: file preview + deterministic remove.
            const avatarInput = document.getElementById('avatarInput');
            const previewWrap = document.getElementById('avatarPreviewWrap');
            const previewImg = document.getElementById('avatarPreview');

            const removeBtn = document.getElementById('removeAvatarBtn');
            if (removeBtn && avatarInput) {
                removeBtn.addEventListener('click', function() {
                    avatarInput.value = '';
                    if (previewWrap) previewWrap.classList.add('d-none');
                    if (previewImg) previewImg.removeAttribute('src');
                });
            }

            if (avatarInput) {
                avatarInput.addEventListener('change', function() {
                    if (!previewWrap || !previewImg) return;
                    const file = avatarInput.files && avatarInput.files[0];
                    if (!file) {
                        previewWrap.classList.add('d-none');
                        previewImg.removeAttribute('src');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target && e.target.result ? e.target.result : '';
                        previewWrap.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
@endpush
