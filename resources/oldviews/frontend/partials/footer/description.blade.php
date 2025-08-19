<div class="row footer-content text-align-center p-3">
    <div class="col-lg-6 col-md-6 col-sm-12 footer-item">
        <h5 class="text-white"><i class="fas fa-map-marker-alt"></i> Address</h5>
        <p class="text-white"> @nl2br(\App\RCache::SiteConfig('site_company_address'))</p>

        <h5 class="text-white"><i class="fas fa-envelope"></i> Email</h5>
        <p class="text-white">{{ \App\RCache::SiteConfig('site_support_email') }}</p>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 footer-item">
        <h5 class="text-white"><i class="fas fa-phone"></i> Phone</h5>
        <p class="text-white">{{ \App\RCache::SiteConfig('site_support_phone') }}</p>

        <h5 class="text-white"><i class="fas fa-globe"></i> DS License</h5>
        <p class="text-white">{{ config('define.licenses.STG.DS') }}</p>
    </div>
</div>
