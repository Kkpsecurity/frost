{{-- Terms & Conditions Content Section --}}
<section class="terms-content py-5"
    style="background: linear-gradient(135deg, var(--frost-primary-color) 0%, var(--frost-secondary-color) 100%); min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="p-5 rounded-3"
                    style="background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 8px 32px rgba(0,0,0,0.3);">

                    <div class="mb-4 pb-3 border-bottom border-secondary">
                        <h1 class="display-5 text-white fw-bold">
                            <i class="fas fa-file-contract me-3" style="color: var(--frost-accent-color-2);"></i>
                            {{ __('frontend.terms.heading') }}
                        </h1>
                        <p class="text-white-50 mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            {{ __('frontend.terms.last_updated') }}: {{ date('F j, Y') }}
                        </p>
                    </div>

                    <div class="policy-intro mb-5">
                        <p class="text-white-50 lead">
                            {{ __('frontend.terms.intro', ['company' => setting('company_name', config('app.name'))]) }}
                        </p>
                    </div>

                    @foreach (['acceptance', 'enrollment', 'payments', 'refund_policy', 'conduct', 'intellectual_property', 'limitation', 'governing_law'] as $section)
                        <div class="policy-section mb-4">
                            <h3 class="text-white mb-3">
                                <i class="fas fa-chevron-right me-2" style="color: var(--frost-accent-color-1);"></i>
                                {{ __('frontend.terms.' . $section . '_title') }}
                            </h3>
                            <p class="text-white-50" style="line-height: 1.8;">
                                {{ __('frontend.terms.' . $section . '_text', ['company' => setting('company_name', config('app.name'))]) }}
                            </p>
                        </div>
                    @endforeach

                    <div class="mt-5 pt-3 border-top border-secondary d-flex flex-wrap gap-3">
                        <a href="{{ route('pages', 'privacy') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-shield-alt me-2"></i>{{ __('frontend.footer.privacy_policy') }}
                        </a>
                        <a href="{{ route('pages', 'student_agreement') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-user-graduate me-2"></i>{{ __('frontend.footer.student_agreement') }}
                        </a>
                        <a href="{{ route('pages', 'contact') }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-envelope me-2"></i>{{ __('frontend.footer.contact_us') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
