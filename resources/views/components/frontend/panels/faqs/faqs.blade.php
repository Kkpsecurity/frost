{{-- FAQs Content Section --}}
<style>
    #faqsAccordion .accordion-item {
        background-color: #1a1f2e !important;
        border-color: rgba(255, 255, 255, .12) !important;
    }

    #faqsAccordion .accordion-button {
        background-color: #1a1f2e !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    #faqsAccordion .accordion-button:not(.collapsed) {
        background-color: #252b3b !important;
        color: #ffc107 !important;
        border-bottom: 1px solid rgba(255, 255, 255, .12) !important;
    }

    #faqsAccordion .accordion-button::after {
        filter: invert(1) brightness(2);
    }

    #faqsAccordion .accordion-body {
        background-color: #1a1f2e !important;
        color: rgba(255, 255, 255, .6) !important;
    }

    #faq-contact-cta .card {
        --bs-card-bg: #1a1f2e;
        background-color: #1a1f2e !important;
        border-color: rgba(255, 255, 255, .12) !important;
    }

    #faq-contact-cta .card-body {
        background-color: #1a1f2e !important;
    }
</style>
<section class="faqs-content py-4 mt-0 pt-3 frost-secondary-bg">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="faqsAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq1-heading">
                            <button class="accordion-button bg-dark text-white" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                {{ __('frontend.faqs.q1') }}
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" aria-labelledby="faq1-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a1') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq2-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false"
                                aria-controls="faq2">
                                {{ __('frontend.faqs.q2') }}
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" aria-labelledby="faq2-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a2') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq3-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false"
                                aria-controls="faq3">
                                {{ __('frontend.faqs.q3') }}
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faq3-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a3') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq4-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false"
                                aria-controls="faq4">
                                {{ __('frontend.faqs.q4') }}
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faq4-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a4') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq5-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false"
                                aria-controls="faq5">
                                {{ __('frontend.faqs.q5') }}
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" aria-labelledby="faq5-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a5') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq6-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false"
                                aria-controls="faq6">
                                {{ __('frontend.faqs.q6') }}
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" aria-labelledby="faq6-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a6') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 7 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq7-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq7" aria-expanded="false"
                                aria-controls="faq7">
                                {{ __('frontend.faqs.q7') }}
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" aria-labelledby="faq7-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a7') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 8 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq8-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq8" aria-expanded="false"
                                aria-controls="faq8">
                                {{ __('frontend.faqs.q8') }}
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" aria-labelledby="faq8-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a8') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 9 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq9-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq9" aria-expanded="false"
                                aria-controls="faq9">
                                {{ __('frontend.faqs.q9') }}
                            </button>
                        </h2>
                        <div id="faq9" class="accordion-collapse collapse" aria-labelledby="faq9-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a9') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 10 -->
                    <div class="accordion-item bg-dark border-secondary mb-3">
                        <h2 class="accordion-header" id="faq10-heading">
                            <button class="accordion-button collapsed bg-dark text-white" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faq10" aria-expanded="false"
                                aria-controls="faq10">
                                {{ __('frontend.faqs.q10') }}
                            </button>
                        </h2>
                        <div id="faq10" class="accordion-collapse collapse" aria-labelledby="faq10-heading"
                            data-bs-parent="#faqsAccordion">
                            <div class="accordion-body text-white-50">
                                {{ __('frontend.faqs.a10') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact CTA Section -->
                <div class="text-center mt-5" id="contact">
                    <div id="faq-contact-cta">
                        <div class="card border-secondary shadow-lg"
                            style="--bs-card-bg: #1a1f2e; background-color: #1a1f2e !important;">
                            <div class="card-body p-4">
                                <h3 class="text-warning mb-3">{{ __('frontend.faqs.still_questions') }}</h3>
                                <p class="text-white-50 mb-4">{{ __('frontend.faqs.still_questions_text') }}</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="contact-method">
                                            <i class="fas fa-phone-alt text-info mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="text-white">{{ __('frontend.faqs.call_us') }}</h5>
                                            <p class="text-white mb-2">(555) 123-4567</p>
                                            <small
                                                class="text-white-50">{{ __('frontend.faqs.call_us_hours') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="contact-method">
                                            <i class="fas fa-envelope text-info mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="text-white">{{ __('frontend.faqs.email_us') }}</h5>
                                            <p class="text-white mb-2">info@stg-florida.com</p>
                                            <small
                                                class="text-white-50">{{ __('frontend.faqs.email_response') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <a href="/contact"
                                    class="btn btn-primary btn-lg mt-3">{{ __('frontend.faqs.contact_us_today') }}</a>
                            </div>
                        </div>
                    </div>{{-- /#faq-contact-cta --}}
                </div>
            </div>
        </div>
    </div>
</section>
