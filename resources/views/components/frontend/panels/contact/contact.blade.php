{{-- Contact Content Section --}}
<style>
    .contact-form-dark .form-control,
    .contact-form-dark .form-select {
        background-color: #252b3b !important;
        color: #fff !important;
        border-color: rgba(255, 255, 255, .2) !important;
    }

    .contact-form-dark .form-control::placeholder {
        color: rgba(255, 255, 255, .4) !important;
    }

    .contact-form-dark .form-control:focus,
    .contact-form-dark .form-select:focus {
        background-color: #2e354a !important;
        color: #fff !important;
        border-color: var(--frost-highlight-color, #ffc107) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, .15) !important;
    }

    .contact-form-dark .form-label {
        color: rgba(255, 255, 255, .85) !important;
    }

    .contact-form-dark .form-check-label {
        color: rgba(255, 255, 255, .75) !important;
    }

    .contact-form-dark .form-check-input {
        background-color: #252b3b !important;
        border-color: rgba(255, 255, 255, .3) !important;
    }

    .contact-form-dark select option {
        background-color: #252b3b;
        color: #fff;
    }

    /* force card dark */
    .contact-form-dark,
    .contact-form-dark .card,
    .contact-form-dark .card-body {
        --bs-card-bg: #1a1f2e !important;
        background-color: #1a1f2e !important;
    }
</style>
<section class="contact-content py-5 mt-0 pt-4 frost-secondary-bg" style="margin-top:0 !important;">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Information --}}
            <div class="col-lg-6">
                <div class="contact-info h-100 p-4 rounded-3"
                    style="background: rgba(29, 56, 84, 0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); color: #fff !important;">
                    <h2 class="mb-4" style="color: #fff !important; font-weight: 700;">
                        {{ __('frontend.contact.info_heading') }} {{ setting('company_name', '') }}</h2>
                    <p class="lead mb-4" style="color: #eee">{{ __('frontend.contact.info_subtext') }}</p>

                    <div class="contact-details">
                        {{-- Company Name --}}
                        <div class="contact-item mb-4 p-3 rounded-3"
                            style="background: var(--frost-accent-color-1); color: white; transition: transform 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-building" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">{{ __('frontend.contact.company_label') }}
                                    </h5>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">
                                        {{ setting('company_name', config('app.name')) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="contact-item mb-4 p-3 rounded-3"
                            style="background: var(--frost-accent-color-4); color: white; transition: transform 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-phone" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">{{ __('frontend.contact.phone_label') }}
                                    </h5>
                                    <p class="mb-0">
                                        <a href="tel:{{ setting('support_phone', '(555) 123-4567') }}"
                                            class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                            {{ setting('support_phone', '(555) 123-4567') }}
                                        </a>
                                    </p>
                                    @if (setting('support_phone_hours'))
                                        <small
                                            style="color: rgba(255,255,255,0.8);">{{ setting('support_phone_hours') }}</small>
                                    @else
                                        <small
                                            style="color: rgba(255,255,255,0.8);">{{ __('frontend.contact.call_center') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="contact-item mb-4 p-3 rounded-3"
                            style="background: var(--frost-accent-color-2); color: white; transition: transform 0.3s ease;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-envelope" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">{{ __('frontend.contact.email_label') }}
                                    </h5>
                                    <p class="mb-0">
                                        <a href="mailto:{{ setting('contact_email', 'info@example.com') }}"
                                            class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                            {{ setting('contact_email', 'info@example.com') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Support Email --}}
                        @if (setting('support_email') && setting('support_email') !== setting('contact_email'))
                            <div class="contact-item mb-4 p-3 rounded-3"
                                style="background: var(--frost-accent-color-3); color: white; transition: transform 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.transform='translateY(0)'">
                                <div class="d-flex align-items-center">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-life-ring" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" style="color: white;">
                                            {{ __('frontend.contact.support_label') }}</h5>
                                        <p class="mb-0">
                                            <a href="mailto:{{ setting('support_email') }}"
                                                class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                                {{ setting('support_email') }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Address --}}
                        @if (setting('company_address'))
                            <div class="contact-item mb-4 p-3 rounded-3"
                                style="background: rgba(255,193,7,.15); border: 1px solid rgba(255,193,7,.3); transition: transform 0.3s ease;"
                                onmouseover="this.style.transform='translateY(-2px)'"
                                onmouseout="this.style.transform='translateY(0)'">
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon me-3">
                                        <i class="fas fa-map-marker-alt text-warning" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-warning fw-600">{{ __('frontend.contact.address_label') }}
                                        </h5>
                                        <p class="mb-0 text-white-50">{!! nl2br(e(setting('company_address'))) !!}</p>
                                        @if (setting('google_map_url'))
                                            <a href="{{ setting('google_map_url') }}" target="_blank"
                                                class="btn btn-sm btn-warning mt-2">
                                                <i
                                                    class="fas fa-external-link-alt me-1"></i>{{ __('frontend.contact.view_on_map') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contact Form --}}
            <div class="col-lg-6">
                <div class="contact-form-wrapper h-100 contact-form-dark">
                    <div class="card border-secondary shadow-lg"
                        style="--bs-card-bg: #1a1f2e; background-color: #1a1f2e !important;">
                        <div class="card-body p-4" style="background-color: #1a1f2e !important;">
                            <h3 class="mb-4 text-warning fw-bold" id="contact-form">
                                {{ __('frontend.contact.form_heading') }}</h3>

                            {{-- Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Error Message --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                                @csrf

                                {{-- Name Field --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('frontend.contact.name_label') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <label for="email"
                                        class="form-label">{{ __('frontend.contact.email_field_label') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone Field --}}
                                <div class="mb-3">
                                    <label for="phone"
                                        class="form-label">{{ __('frontend.contact.phone_field_label') }}</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Subject Field --}}
                                <div class="mb-3">
                                    <label for="subject"
                                        class="form-label">{{ __('frontend.contact.subject_label') }}</label>
                                    <select class="form-select @error('subject') is-invalid @enderror" id="subject"
                                        name="subject">
                                        <option value="">{{ __('frontend.contact.subject_placeholder') }}
                                        </option>
                                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_general') }}</option>
                                        <option value="enrollment"
                                            {{ old('subject') == 'enrollment' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_enrollment') }}</option>
                                        <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_support') }}</option>
                                        <option value="licensing"
                                            {{ old('subject') == 'licensing' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_licensing') }}</option>
                                        <option value="partnership"
                                            {{ old('subject') == 'partnership' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_partnership') }}</option>
                                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>
                                            {{ __('frontend.contact.subject_other') }}</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Message Field --}}
                                <div class="mb-3">
                                    <label for="message"
                                        class="form-label">{{ __('frontend.contact.message_label') }} <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                                        required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Privacy Agreement --}}
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('privacy_agree') is-invalid @enderror"
                                            type="checkbox" id="privacy_agree" name="privacy_agree" value="1"
                                            {{ old('privacy_agree') ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="privacy_agree">
                                            {{ __('frontend.contact.privacy_agree_text') }} <a
                                                href="{{ route('pages', 'privacy') }}" target="_blank"
                                                class="text-warning">{{ __('frontend.contact.privacy_policy') }}</a>
                                            <span class="text-danger">*</span>
                                        </label>
                                        @error('privacy_agree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-warning btn-lg fw-bold">
                                        <i class="fas fa-paper-plane me-2"></i>{{ __('frontend.contact.submit_btn') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
