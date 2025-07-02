@extends('layouts.app')
{{-- 
    // RCache::SiteConfig('site_tech_phone'),
    // Rcache::SiteConfig('site_refund_phone'),
    // RCache::SiteConfig('site_tech_email'),     
--}}

@php
    $pageTitle = $content['title'];
    $pageKeywords = $content['keywords'];
    $pageDescription = $content['description'];
    $googleMapUrl = RCache::SiteConfig('site_google_map_url');
    $contactDetails = [
        [
            'icon' => 'fa-map',
            'content' => RCache::SiteConfig('site_company_address'),
        ],
        [
            'icon' => 'fa-phone-open',
            'content' => RCache::SiteConfig('site_support_phone'),                        
        ],
        [
            'icon' => 'fa-envelope',
            'content' => RCache::SiteConfig('site_support_email'),
        ],
    ];

    $cards = [
        [
            'icon' => 'fas fa-mobile-alt',
            'title' => 'Call',
            'content' => $contactDetails[1]['content'],
            'subtitle' => RCache::SiteConfig('site_support_phone_hours'),
        ],
        [
            'icon' => 'fas fa-envelope',
            'title' => 'Email',
            'content' => $contactDetails[2]['content'],
            'subtitle' => '',
        ],
        [
            'icon' => 'fas fa-map-marker-alt',
            'title' => 'Location',
            'content' => $contactDetails[0]['content'],
            'subtitle' => '',
        ],
    ];
@endphp

@include('frontend.partials.meta-tags', [
    'title' => $pageTitle,
    'keywords' => $pageKeywords,
    'description' => $pageDescription
])

@section('content')
    @include('frontend.partials.breadcrumbs')

    <!-- 1 Contact Start -->
    <div class="contact-area area-padding frost-secondary-bg">
        <div class="container contact-panels">
            <div class="row">
                @foreach ($cards as $card)
                    @include('frontend.partials.contact_card', ['card' => $card])
                @endforeach
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="contact-images">
                        <div class="position-relative h-100">
                            <iframe src="{!! $googleMapUrl !!}" width="100%" height="450" style="border:0;"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Start Left contact -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="Scard contact-form-container">
                        <div class="row">
                            @include('frontend.forms.contact_us_form')
                        </div>
                    </div>
                </div>
                <!-- End Left contact -->
            </div>
        </div>
    </div>
@endsection
