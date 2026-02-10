{{-- Settings Section --}}
<div class="settings-section">
    <h3 class="text-white mb-4">
        <i class="fas fa-cog me-2"></i>Account Settings
    </h3>

    <form action="{{ route('account.settings.update') }}" method="POST" class="settings-form">
        @csrf

        {{-- Email Preferences --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-envelope me-2"></i>Email Preferences
            </h5>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="emailOptIn" name="email_opt_in"
                    {{ $data['email_preferences']['email_opt_in'] ? 'checked' : '' }}>
                <label class="form-check-label text-white" for="emailOptIn">
                    <strong>Receive promotional emails and updates</strong>
                    <small class="d-block text-white-50 mt-1">Get news about courses, special offers, and platform
                        updates</small>
                </label>
            </div>
        </div>

        {{-- Privacy Settings --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-shield-alt me-2"></i>Privacy Settings
            </h5>
            <div class="mb-3">
                <label class="form-label text-white-50">
                    <i class="fas fa-eye me-2"></i>Profile Visibility
                </label>
                <select name="profile_visibility" class="form-select bg-dark text-white border-secondary p-2">
                    <option value="private"
                        {{ $data['privacy_settings']['profile_visibility'] === 'private' ? 'selected' : '' }}>
                        🔒 Private - Only you can see your profile
                    </option>
                    <option value="instructors"
                        {{ $data['privacy_settings']['profile_visibility'] === 'instructors' ? 'selected' : '' }}>
                        👨‍🏫 Instructors Only - Visible to your instructors
                    </option>
                    <option value="public"
                        {{ $data['privacy_settings']['profile_visibility'] === 'public' ? 'selected' : '' }}>
                        🌐 Public - Visible to all users
                    </option>
                </select>
                <small class="text-white-50 mt-2 d-block">Control who can view your profile information</small>
            </div>
        </div>

        {{-- Display Preferences --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-palette me-2"></i>Display Preferences
            </h5>

            <div class="mb-3">
                <label class="form-label text-white-50">
                    <i class="fas fa-clock me-2"></i>Timezone
                </label>
                <select name="preferences[timezone]" class="form-select bg-dark text-white border-secondary p-2">
                    <option value="America/New_York"
                        {{ ($data['preferences']['timezone'] ?? 'America/New_York') === 'America/New_York' ? 'selected' : '' }}>
                        Eastern Time (ET)
                    </option>
                    <option value="America/Chicago"
                        {{ ($data['preferences']['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' }}>
                        Central Time (CT)
                    </option>
                    <option value="America/Denver"
                        {{ ($data['preferences']['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' }}>
                        Mountain Time (MT)
                    </option>
                    <option value="America/Los_Angeles"
                        {{ ($data['preferences']['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>
                        Pacific Time (PT)
                    </option>
                    <option value="America/Anchorage"
                        {{ ($data['preferences']['timezone'] ?? '') === 'America/Anchorage' ? 'selected' : '' }}>
                        Alaska Time (AKT)
                    </option>
                    <option value="Pacific/Honolulu"
                        {{ ($data['preferences']['timezone'] ?? '') === 'Pacific/Honolulu' ? 'selected' : '' }}>
                        Hawaii Time (HT)
                    </option>
                </select>
                <small class="text-white-50 mt-2 d-block">All dates and times will be displayed in your selected
                    timezone</small>
            </div>

            <div class="mb-3">
                <label class="form-label text-white-50">
                    <i class="fas fa-language me-2"></i>Language
                </label>
                <select name="preferences[language]" class="form-select bg-dark text-white border-secondary p-2">
                    <option value="en" {{ ($data['preferences']['language'] ?? 'en') === 'en' ? 'selected' : '' }}>
                        English
                    </option>
                    <option value="es" {{ ($data['preferences']['language'] ?? '') === 'es' ? 'selected' : '' }}>
                        Español (Spanish)
                    </option>
                </select>
                <small class="text-white-50 mt-2 d-block">Choose your preferred language for the interface</small>
            </div>

            <div class="mb-3">
                <label class="form-label text-white-50">
                    <i class="fas fa-calendar-alt me-2"></i>Date Format
                </label>
                <select name="preferences[date_format]" class="form-select bg-dark text-white border-secondary p-2">
                    <option value="m/d/Y"
                        {{ ($data['preferences']['date_format'] ?? 'm/d/Y') === 'm/d/Y' ? 'selected' : '' }}>
                        MM/DD/YYYY (12/31/2026)
                    </option>
                    <option value="d/m/Y"
                        {{ ($data['preferences']['date_format'] ?? '') === 'd/m/Y' ? 'selected' : '' }}>
                        DD/MM/YYYY (31/12/2026)
                    </option>
                    <option value="Y-m-d"
                        {{ ($data['preferences']['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' }}>
                        YYYY-MM-DD (2026-12-31)
                    </option>
                </select>
                <small class="text-white-50 mt-2 d-block">How dates should be displayed throughout the platform</small>
            </div>
        </div>

        {{-- Learning Preferences --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-graduation-cap me-2"></i>Learning Preferences
            </h5>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="autoplayVideos" name="preferences[autoplay_videos]"
                    {{ $data['preferences']['autoplay_videos'] ?? false ? 'checked' : '' }}>
                <label class="form-check-label text-white" for="autoplayVideos">
                    <strong>Autoplay videos</strong>
                    <small class="d-block text-white-50 mt-1">Automatically play lesson videos when opened</small>
                </label>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="showSubtitles" name="preferences[show_subtitles]"
                    {{ $data['preferences']['show_subtitles'] ?? true ? 'checked' : '' }}>
                <label class="form-check-label text-white" for="showSubtitles">
                    <strong>Show subtitles by default</strong>
                    <small class="d-block text-white-50 mt-1">Enable subtitles/captions automatically on videos</small>
                </label>
            </div>

            <div class="mb-3">
                <label class="form-label text-white-50">
                    <i class="fas fa-volume-up me-2"></i>Default Video Volume
                </label>
                <input type="range" class="form-range" id="videoVolume" name="preferences[video_volume]"
                    min="0" max="100" value="{{ $data['preferences']['video_volume'] ?? 75 }}"
                    oninput="document.getElementById('volumeValue').textContent = this.value + '%'">
                <small class="text-white-50 d-block mt-2">
                    Volume: <span id="volumeValue">{{ $data['preferences']['video_volume'] ?? 75 }}%</span>
                </small>
            </div>
        </div>

        {{-- Security Settings --}}
        <div class="mb-4 pb-4 border-bottom border-secondary">
            <h5 class="text-white mb-3">
                <i class="fas fa-lock me-2"></i>Security Settings
            </h5>

            <div class="mb-3">
                <a href="{{ route('password.request') }}" class="btn btn-outline-light">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
                <small class="d-block text-white-50 mt-2">Update your account password</small>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="sessionTimeout" name="preferences[auto_logout]"
                    {{ $data['preferences']['auto_logout'] ?? true ? 'checked' : '' }}>
                <label class="form-check-label text-white" for="sessionTimeout">
                    <strong>Auto-logout after inactivity</strong>
                    <small class="d-block text-white-50 mt-1">Automatically log out after 30 minutes of
                        inactivity</small>
                </label>
            </div>
        </div>

        {{-- Custom Preferences --}}
        @if (
            !empty($data['preferences']) &&
                count(array_diff_key(
                        $data['preferences'],
                        array_flip([
                            'timezone',
                            'language',
                            'date_format',
                            'autoplay_videos',
                            'show_subtitles',
                            'video_volume',
                            'auto_logout',
                        ]))) > 0)
            <div class="mb-4 pb-4 border-bottom border-secondary">
                <h5 class="text-white mb-3">
                    <i class="fas fa-sliders-h me-2"></i>Additional Preferences
                </h5>
                @foreach ($data['preferences'] as $key => $value)
                    @if (
                        !in_array($key, [
                            'timezone',
                            'language',
                            'date_format',
                            'autoplay_videos',
                            'show_subtitles',
                            'video_volume',
                            'auto_logout',
                        ]))
                        <div class="mb-3">
                            <label class="form-label text-white-50">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            <input type="text" name="preferences[{{ $key }}]"
                                class="form-control bg-dark text-white border-secondary p-2"
                                value="{{ $value }}">
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="d-flex gap-3 flex-wrap">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
            <button type="reset" class="btn btn-outline-secondary text-white px-4">
                <i class="fas fa-undo me-2"></i>Reset Changes
            </button>
        </div>
    </form>
</div>

<style>
    .settings-form .form-check-input {
        width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }

    .settings-form .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }

    .settings-form .form-range {
        height: 0.5rem;
    }

    .settings-form .form-range::-webkit-slider-thumb {
        background: #3498db;
    }

    .settings-form .form-range::-moz-range-thumb {
        background: #3498db;
    }

    .settings-form h5::after {
        content: '';
        display: block;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #3498db, #2980b9);
        margin-top: 0.75rem;
        border-radius: 2px;
    }

    .settings-section {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
</style>
