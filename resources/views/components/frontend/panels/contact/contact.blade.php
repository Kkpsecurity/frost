{{-- Contact Content Section --}}
<section class="contact-content py-5" style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 80vh;">
    <div class="container">
        <div class="row g-5">
            {{-- Contact Information --}}
            <div class="col-lg-6">
                <div class="contact-info h-100 p-4 rounded-3" style="background: rgba(29, 56, 84, 0.95); size: 14px; backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(253, 253, 253, 0.1); font-zie: 24px; color: #fff !important;"
                    <h2 class="mb-4" style="color: #fff !important; font-weight: 700;">Contact {{ setting('company_name', 'Us') }}</h2>
                    <p class="lead mb-4" style="color: #eee">Ready to start your security training journey? Reach out to us through any of the following methods:</p>

                    <div class="contact-details">
                        {{-- Company Name --}}
                        <div class="contact-item mb-4 p-3 rounded-3" style="background: var(--frost-accent-color-1); color: white; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-building" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">Company</h5>
                                    <p class="mb-0" style="color: rgba(255,255,255,0.9);">{{ setting('company_name', config('app.name')) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="contact-item mb-4 p-3 rounded-3" style="background: var(--frost-accent-color-4); color: white; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-phone" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">Phone</h5>
                                    <p class="mb-0">
                                        <a href="tel:{{ setting('support_phone', '(555) 123-4567') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                            {{ setting('support_phone', '(555) 123-4567') }}
                                        </a>
                                    </p>
                                    @if(setting('support_phone_hours'))
                                        <small style="color: rgba(255,255,255,0.8);">{{ setting('support_phone_hours') }}</small>
                                    @else
                                        <small style="color: rgba(255,255,255,0.8);">Call Center</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="contact-item mb-4 p-3 rounded-3" style="background: var(--frost-accent-color-2); color: white; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-envelope" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">Email</h5>
                                    <p class="mb-0">
                                        <a href="mailto:{{ setting('contact_email', 'info@example.com') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                            {{ setting('contact_email', 'info@example.com') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Support Email --}}
                        @if(setting('support_email') && setting('support_email') !== setting('contact_email'))
                        <div class="contact-item mb-4 p-3 rounded-3" style="background: var(--frost-accent-color-3); color: white; transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-center">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-life-ring" style="font-size: 1.5rem;"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: white;">Support</h5>
                                    <p class="mb-0">
                                        <a href="mailto:{{ setting('support_email') }}" class="text-decoration-none" style="color: rgba(255,255,255,0.9);">
                                            {{ setting('support_email') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Address --}}
                        @if(setting('company_address'))
                        <div class="contact-item mb-4 p-3 rounded-3" style="background: var(--frost-highlight-color); color: var(--frost-primary-color); transition: transform 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="d-flex align-items-start">
                                <div class="contact-icon me-3">
                                    <i class="fas fa-map-marker-alt" style="font-size: 1.5rem; color: var(--frost-primary-color);"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="color: var(--frost-primary-color); font-weight: 600;">Address</h5>
                                    <p class="mb-0" style="color: var(--frost-primary-color);">{!! nl2br(e(setting('company_address'))) !!}</p>
                                    @if(setting('google_map_url'))
                                        <a href="{{ setting('google_map_url') }}" target="_blank" class="btn btn-sm mt-2" style="background: var(--frost-primary-color); color: white; border: none;">
                                            <i class="fas fa-external-link-alt me-1"></i>View on Map
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
                <div class="contact-form-wrapper h-100">
                    <div class="card border-0" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                        <div class="card-body p-4">
                            <h3 class="mb-4" id="contact-form" style="color: var(--frost-primary-color); font-weight: 700;">Send Us a Message</h3>

                            {{-- Success Message --}}
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- Error Message --}}
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                                @csrf

                                {{-- Name Field --}}
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Phone Field --}}
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Subject Field --}}
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <select class="form-select @error('subject') is-invalid @enderror"
                                            id="subject"
                                            name="subject">
                                        <option value="">Select a topic...</option>
                                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                                        <option value="enrollment" {{ old('subject') == 'enrollment' ? 'selected' : '' }}>Course Enrollment</option>
                                        <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>Technical Support</option>
                                        <option value="licensing" {{ old('subject') == 'licensing' ? 'selected' : '' }}>Licensing Questions</option>
                                        <option value="partnership" {{ old('subject') == 'partnership' ? 'selected' : '' }}>Partnership Opportunities</option>
                                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Message Field --}}
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('message') is-invalid @enderror"
                                              id="message"
                                              name="message"
                                              rows="5"
                                              required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Privacy Agreement --}}
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('privacy_agree') is-invalid @enderror"
                                               type="checkbox"
                                               id="privacy_agree"
                                               name="privacy_agree"
                                               value="1"
                                               {{ old('privacy_agree') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label" for="privacy_agree">
                                            I agree to the <a href="{{ route('pages', 'privacy') }}" target="_blank">Privacy Policy</a> <span class="text-danger">*</span>
                                        </label>
                                        @error('privacy_agree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-lg" style="background: var(--frost-primary-color); color: white; border: none; transition: all 0.3s ease;" onmouseover="this.style.background='var(--frost-secondary-color)'" onmouseout="this.style.background='var(--frost-primary-color)'">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
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
