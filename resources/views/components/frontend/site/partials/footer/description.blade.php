{{-- Footer Description Component --}}
<div class="row footer-content text-align-center p-3">
    <div class="col-lg-6 col-md-6 col-sm-12 footer-item">
        <h5 class="text-white"><i class="fas fa-map-marker-alt"></i> {{ __('frontend.footer.address') }}</h5>
        <p class="text-white">{!! nl2br(e(setting('company_address'))) !!}</p>

        <h5 class="text-white"><i class="fas fa-envelope"></i> {{ __('frontend.footer.email') }}</h5>
        <p class="text-white">
            <span class="email-truncated" title="{{ setting('support_email') }}" data-bs-toggle="tooltip"
                data-bs-placement="top" style="cursor: help; text-decoration: underline dotted;">
                {{ Str::limit(setting('support_email'), 20, '...') }}
            </span>
        </p>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 footer-item">
        <h5 class="text-white"><i class="fas fa-phone"></i> {{ __('frontend.footer.phone') }}</h5>
        <p class="text-white">{{ setting('support_phone') }}</p>

        <h5 class="text-white"><i class="fas fa-globe"></i> {{ __('frontend.footer.ds_license') }}</h5>
        <p class="text-white">{{ config('define.licenses.STG.DS') }}</p>
    </div>
</div>
