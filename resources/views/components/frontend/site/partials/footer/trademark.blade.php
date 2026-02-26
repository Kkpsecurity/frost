{{-- Footer Trademark/Copyright Component --}}
<div class="border-top border-secondary mt-3 pt-3 pb-2 d-flex flex-wrap justify-content-between align-items-center">
    <p class="text-white-50 mb-0 small">
        &copy; {{ date('Y') }} {{ setting('company_name', config('app.name')) }}.
        {{ __('frontend.footer.copyright') }}
    </p>
    <p class="text-white-50 mb-0 small">
        {{ __('frontend.footer.powered_by') }} <a href="https://kkpsolutions.com" target="_blank"
            class="text-warning text-decoration-none">KKP Solutions</a>
    </p>
</div>
