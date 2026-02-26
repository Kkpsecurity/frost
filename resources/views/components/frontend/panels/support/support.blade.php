{{-- Support Center Panel --}}
<section class="support-page py-5" style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 100vh;">
    <div class="container-fluid px-4">
        <div class="row g-4">

            {{-- Sidebar Nav --}}
            <div class="col-lg-3 col-md-4">
                <div class="sticky-top pt-2" style="top: 80px;">
                    <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                        <h5 class="text-white mb-3 pb-2 border-bottom border-secondary fw-bold">
                            <i class="fas fa-headset me-2" style="color: var(--frost-accent-color-1);"></i>
                            {{ __('frontend.support.heading') }}
                        </h5>
                        <nav class="d-flex flex-column gap-1">
                            <a href="#security-training-program" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-shield-alt me-2"></i>{{ __('frontend.support.nav_program') }}
                            </a>
                            <a href="#class-d-course" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-id-card me-2"></i>{{ __('frontend.support.nav_class_d') }}
                            </a>
                            <a href="#class-g-course" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-crosshairs me-2"></i>{{ __('frontend.support.nav_class_g') }}
                            </a>
                            <a href="#about-stg" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-building me-2"></i>{{ __('frontend.support.nav_about') }}
                            </a>
                            <a href="#refund-policy" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-undo me-2"></i>{{ __('frontend.support.nav_refund') }}
                            </a>
                            <a href="#k-partners" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-handshake me-2"></i>{{ __('frontend.support.nav_kpartners') }}
                            </a>
                            <a href="#webcam-troubleshooting" class="support-nav-link px-3 py-2 rounded text-white-50 text-decoration-none small">
                                <i class="fas fa-video me-2"></i>{{ __('frontend.support.nav_webcam') }}
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9 col-md-8">

                {{-- Section 1: Security Training Program --}}
                <div id="security-training-program" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s1_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s1_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s1_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s1_body') }}</p>
                </div>

                {{-- Section 2: Class D --}}
                <div id="class-d-course" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s2_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s2_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s2_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s2_body') }}</p>
                </div>

                {{-- Section 3: Class G --}}
                <div id="class-g-course" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s3_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s3_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s3_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s3_body') }}</p>
                </div>

                {{-- Section 4: About STG --}}
                <div id="about-stg" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s4_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s4_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s4_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s4_body') }}</p>
                </div>

                {{-- Section 5: Refund Policy --}}
                <div id="refund-policy" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s5_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s5_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s5_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s5_body') }}</p>
                </div>

                {{-- Section 6: K Partners --}}
                <div id="k-partners" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s6_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s6_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s6_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50" style="line-height: 1.8;">{{ __('frontend.support.s6_body') }}</p>
                </div>

                {{-- Section 7: Webcam Troubleshooting --}}
                <div id="webcam-troubleshooting" class="support-section p-4 p-lg-5 rounded-3 mb-4" style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-uppercase small mb-1" style="color: var(--frost-accent-color-1); letter-spacing: 0.1em;">{{ __('frontend.support.s7_tag') }}</p>
                    <h2 class="text-white fw-bold mb-1">{{ __('frontend.support.s7_heading') }}</h2>
                    <h5 class="mb-3" style="color: var(--frost-accent-color-2);">{{ __('frontend.support.s7_subheading') }}</h5>
                    <hr class="border-secondary">
                    <p class="text-white-50 mb-4" style="line-height: 1.8;">{{ __('frontend.support.s7_body') }}</p>

                    <div class="row g-3">
                        @foreach ([
                            ['browser' => 'Firefox',  'icon' => 'fab fa-firefox-browser', 'color' => 'var(--frost-accent-color-1)', 'key' => 's7_firefox'],
                            ['browser' => 'Chrome',   'icon' => 'fab fa-chrome',           'color' => 'var(--frost-accent-color-2)', 'key' => 's7_chrome'],
                            ['browser' => 'Edge',     'icon' => 'fab fa-edge',             'color' => 'var(--frost-accent-color-3)', 'key' => 's7_edge'],
                        ] as $browser)
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 h-100" style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.08);">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="{{ $browser['icon'] }} fa-lg me-2" style="color: {{ $browser['color'] }};"></i>
                                    <strong class="text-white">{{ $browser['browser'] }}</strong>
                                </div>
                                <p class="text-white-50 small mb-0" style="line-height: 1.7;">{{ __('frontend.support.' . $browser['key']) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
.support-nav-link:hover {
    background: rgba(255,255,255,0.08) !important;
    color: #fff !important;
}
.support-section {
    scroll-margin-top: 90px;
}
</style>
