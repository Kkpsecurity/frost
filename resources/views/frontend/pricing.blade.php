@extends('layouts.frontend')

@section('title', 'Pricing - Frost')

@section('content')
<!-- Page Header -->
<section class="page-header py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pricing</li>
                    </ol>
                </nav>
                <h1 class="page-title">Pricing Plans</h1>
                <p class="page-subtitle">Choose the plan that works best for you</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="pricing-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Basic</h3>
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">29</span>
                            <span class="period">/month</span>
                        </div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check text-success"></i> 5 Courses</li>
                            <li><i class="fas fa-check text-success"></i> Basic Support</li>
                            <li><i class="fas fa-check text-success"></i> Mobile Access</li>
                            <li><i class="fas fa-times text-muted"></i> Certificate</li>
                            <li><i class="fas fa-times text-muted"></i> Priority Support</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="#" class="btn btn-outline-primary btn-block">Choose Plan</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card featured">
                    <div class="badge">Most Popular</div>
                    <div class="pricing-header">
                        <h3>Pro</h3>
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">59</span>
                            <span class="period">/month</span>
                        </div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check text-success"></i> Unlimited Courses</li>
                            <li><i class="fas fa-check text-success"></i> Priority Support</li>
                            <li><i class="fas fa-check text-success"></i> Mobile Access</li>
                            <li><i class="fas fa-check text-success"></i> Certificates</li>
                            <li><i class="fas fa-check text-success"></i> Offline Downloads</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="#" class="btn btn-primary btn-block">Choose Plan</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Enterprise</h3>
                        <div class="price">
                            <span class="currency">$</span>
                            <span class="amount">99</span>
                            <span class="period">/month</span>
                        </div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check text-success"></i> Everything in Pro</li>
                            <li><i class="fas fa-check text-success"></i> Custom Branding</li>
                            <li><i class="fas fa-check text-success"></i> API Access</li>
                            <li><i class="fas fa-check text-success"></i> Advanced Analytics</li>
                            <li><i class="fas fa-check text-success"></i> Dedicated Support</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="#" class="btn btn-outline-primary btn-block">Contact Sales</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
