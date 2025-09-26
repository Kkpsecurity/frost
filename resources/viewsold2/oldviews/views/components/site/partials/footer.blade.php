<footer class="footer-1">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-lg-2 col-md-3 col-sm-12">
                <div class="footer-logo d-flex justify-content-center">
                    <a href="{{ url('frontend.index') }}">
                        <img src="{{ asset('assets/img/logo/logo2.png') }}" alt="logo" width="100">
                    </a>
                </div>
            </div>

            <div class="col-lg-10 col-md-9 col-sm-12">
                <div class="row">
                    <!-- Start column-->
                    <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                       <x-site.partials.footer.description />
                    </div>
                    <div class="col-lg-7 col-md-6 mb-4 mb-md-0">
                        <div class="row g-4"> <!-- added g-4 for spacing between columns -->

                            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                                <x-site.partials.footer.company />
                                <x-site.partials.footer.courses />
                            </div>

                            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                                <x-site.partials.footer.privacy />
                            </div>
                            <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                                <x-site.partials.footer.support />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start footer bottom area -->
        <div class="row">
            <div class="col-12 text-center">
            <small class="text-muted">&copy; {{ date('Y') }} Your Company Name™. All rights reserved.</small>
            </div>
        </div>
        <!-- End footer bottom area -->
    </div>
</footer>
